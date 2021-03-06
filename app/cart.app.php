<?php

/**
 *    购物车控制器，负责会员购物车的管理工作，她与下一步售货员的接口是：购物车告诉售货员，我要买的商品是我购物车内的商品
 *
 *    @author    Garbin
 */
class CartApp extends MallbaseApp {

    /**
     *    列出购物车中的商品
     *
     *    @author    Garbin
     *    @return    void
     */
    function index() {
        $store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
        $carts = $this->_get_carts($store_id);

        $this->_curlocal(
                LANG::get('cart')
        );
        $this->_config_seo('title', Lang::get('confirm_goods') . ' - ' . Conf::get('site_title'));

        if (empty($carts)) {
            $this->_cart_empty();

            return;
        }

        /*  tyioocom  感兴趣的商品 */
		$goods_mod = &m('goods');
//		$gst_mod = &m('goodsstatistics');
//		$interest = $goods_mod->find(array(
//		   'conditions'=>'',
//		   'join'=>'has_goodsstatistics',
//		   'order' => 'views desc,collects desc, sales desc',
//		   'fields' =>'g.goods_id,goods_name,price,sales,default_image',
//		   'limit'=>6
//		));
        $interest = $goods_mod->get_Mem_list(array(
            'conditions' => '',
            'order' => 'views desc,collects desc, sales desc',
            'fields' => 'g.goods_id,goods_name,g.price,sales,default_image,',
            'limit' => 6,
                ), null, false, true, $total_found);
        $this->assign('interest', $interest);
        /* end */
		$db = &db();
		foreach($carts as $k=>$v){
			$sql = 'SELECT `tel`,`im_qq`,`mk_name`,`dangkou_address` FROM '.DB_PREFIX.'store WHERE store_id='.$k;
			$res = $db->getrow($sql);	
			$carts[$k]['tel'] = $res['tel'];
			$carts[$k]['im_qq'] = $res['im_qq'];
			$carts[$k]['mk_name'] = $res['mk_name'];
			$carts[$k]['dangkou_address'] = $res['dangkou_address'];
		}

		$this->assign('cartscss', true);
		$this->assign('count', count($carts));
        $this->assign('carts', $carts);
        $this->display('cart.index.wind.html');
    }

    /**
     *    放入商品(根据不同的请求方式给出不同的返回结果)
     *
     *    @author    Garbin
     *    @return    void
     */
    function add() {
        $spec_id = isset($_GET['spec_id']) ? intval($_GET['spec_id']) : 0;
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
        if (!$spec_id || !$quantity) {
            return;
        }

        /* 店铺所在市场是否有代发 和  店铺本身提供配送。如果都没有，则报错！ */
        $mod_goodsspec = & m('goodsspec');
        $goodsspec = $mod_goodsspec->get($spec_id);

        $goods_id = $goodsspec ? $goodsspec['goods_id'] : 0;
        $mod_goods = & m('goods');
        $store = $mod_goods->find(array(
            'conditions' => 'g.goods_id = ' . $goods_id,
            'join' => 'belongs_to_store',
            'fields' => 's.store_id,s.mk_id,s.store_name',
                ));
        //查看店铺是否有运费模板
        $mod_delivery_template = & m('delivery_template');
        $delivery_templates = $mod_delivery_template->find(array(
            'conditions' => 'delivery_template.store_id =' . $store[$goods_id]['store_id'],
            'fields' => 'template_id'
                ));

        //查看店铺所在市场是否有代发
        $mod_market = & m('market');
        $layer = $mod_market->get_layer($store[$goods_id]['mk_id']);
        if ($layer == 2) {
            $mk_id = $store[$goods_id]['mk_id']; //市场名
        }
        if ($layer == 3) {
            $market = $mod_market->get($store[$goods_id]['mk_id']); //楼层名，须得到parent_id市场名
            $mk_id = $market['parent_id'];
        }
        if (!in_array($layer, array(2, 3))) {
            $market_behalfs = null;
        } else {
            $market_behalfs = $mod_market->getRelatedData('belongs_to_behalf', $mk_id);
        }

        if (empty($delivery_templates) && empty($market_behalfs)) {
            $this->json_error('goods_no_logistics');
            return;
        }
        
        // //既不能档口发货 也不在代发区内  本地屏蔽
        if(empty($delivery_templates) && !allow_behalf_open($store[$goods_id]['store_id']))
        {            
            $this->json_error(sprintf(Lang::get('not_allow_behalf_open'),$store[$goods_id]['store_name']));
            return;
        } 



        /* 是否有商品 */
        $spec_model = & m('goodsspec');
        $spec_info = $spec_model->get(array(
            'fields' => 'g.store_id, g.goods_id, g.goods_name, g.spec_name_1, g.spec_name_2, g.default_image, gs.spec_1, gs.spec_2, gs.stock, gs.price',
            'conditions' => $spec_id,
            'join' => 'belongs_to_goods',
                ));

        if (!$spec_info) {
            $this->json_error('no_such_goods');
            /* 商品不存在 */
            return;
        }
        //价格少于5元，不能加入购物车
        if($spec_info['price'] < 5)
        {
        	$this->json_error('price_less_5');
        	return;
        }

        /* 如果是自己店铺的商品，则不能购买 */
        if ($this->visitor->get('manage_store')) {
            if ($spec_info['store_id'] == $this->visitor->get('manage_store')) {
                $this->json_error('can_not_buy_yourself');

                return;
            }
        }

        /* 是否添加过 */
        $model_cart = & m('cart');
        $item_info = $model_cart->get("spec_id={$spec_id} AND session_id='" . SESS_ID . "'");
        if (!empty($item_info)) {
            $this->json_error('goods_already_in_cart');

            return;
        }
		
        if ($quantity > $spec_info['stock']) {
            $this->json_error('no_enough_goods');
            return;
        }

        $spec_1 = $spec_info['spec_name_1'] ? $spec_info['spec_name_1'] . ':' . $spec_info['spec_1'] : $spec_info['spec_1'];
        $spec_2 = $spec_info['spec_name_2'] ? $spec_info['spec_name_2'] . ':' . $spec_info['spec_2'] : $spec_info['spec_2'];

		$specification = $spec_1 . ' ' . $spec_2;
		$cart_model = & m('cart');
		$info = $cart_model->get("specification='{$specification}' AND user_id=".$this->visitor->get('user_id'));
		//商品存在就添加数量
		if(!empty($info)){
			
			$this->update($spec_id, $info['quantity']+$quantity);
		}else{
			
			/* 将商品加入购物车 */
			$cart_item = array(
				'user_id' => $this->visitor->get('user_id'),
				'session_id' => SESS_ID,
				'store_id' => $spec_info['store_id'],
				'spec_id' => $spec_id,
				'goods_id' => $spec_info['goods_id'],
				'goods_name' => addslashes($spec_info['goods_name']),
				'specification' => addslashes(trim($specification)),
				'price' => $spec_info['price'],
				'quantity' => $quantity,
				'goods_image' => addslashes($spec_info['default_image']),
				);

			/* 添加并返回购物车统计即可 */
			$cart_model->add($cart_item);
		}

        $cart_status = $this->_get_cart_status();

        /* 更新被添加进购物车的次数 */
        $model_goodsstatistics = & m('goodsstatistics');
        $model_goodsstatistics->edit($spec_info['goods_id'], 'carts=carts+1');

        $this->json_result(array(
            'cart' => $cart_status['status'], //返回购物车状态
                ), 'addto_cart_successed');
    }

    /**
     *    放入更多商品(根据不同的请求方式给出不同的返回结果)
     *
     *    @author    Garbin
     *    @return    void
     */
    function adds() {
        $spec_arr = isset($_GET['spec_id']) ? ($_GET['spec_id']) : array();
        $quantity_arr = isset($_GET['quantity']) ? ($_GET['quantity']) : array();

        if (count($spec_arr) != count($quantity_arr)) {
            $this->json_error('hack_attempt');
            return;
        }
        foreach ($spec_arr as $value) {
            if (!intval($value)) {
                $this->json_error('hack_attempt');
                return;
            }
        }
        foreach ($quantity_arr as $value) {
            if (!intval($value)) {
                $this->json_error('hack_attempt');
                return;
            }
        }

        /* 店铺所在市场是否有代发 和  店铺本身提供配送。如果都没有，则报错！ */
        $mod_goodsspec = & m('goodsspec');
        $goodsspec = $mod_goodsspec->get($spec_arr[0]);

        $goods_id = $goodsspec ? $goodsspec['goods_id'] : 0;
        $mod_goods = & m('goods');
        $store = $mod_goods->find(array(
            'conditions' => 'g.goods_id = ' . $goods_id,
            'join' => 'belongs_to_store',
            'fields' => 's.store_id,s.mk_id,s.store_name',
                ));
        //查看店铺是否有运费模板
        $mod_delivery_template = & m('delivery_template');
        $delivery_templates = $mod_delivery_template->find(array(
            'conditions' => 'delivery_template.store_id =' . $store[$goods_id]['store_id'],
            'fields' => 'template_id'
                ));

        //查看店铺所在市场是否有代发
        $mod_market = & m('market');
        $layer = $mod_market->get_layer($store[$goods_id]['mk_id']);
        if ($layer == 2) {
            $mk_id = $store[$goods_id]['mk_id']; //市场名
        }
        if ($layer == 3) {
            $market = $mod_market->get($store[$goods_id]['mk_id']); //楼层名，须得到parent_id市场名
            $mk_id = $market['parent_id'];
        }
        if (!in_array($layer, array(2, 3))) {
            $market_behalfs = null;
        } else {
            $market_behalfs = $mod_market->getRelatedData('belongs_to_behalf', $mk_id);
        }

        if (empty($delivery_templates) && empty($market_behalfs)) {
            $this->json_error('goods_no_logistics');
            return;
        }
        
        //既不能档口发货 也不在代发区内
        if(empty($delivery_templates) && !allow_behalf_open($store[$goods_id]['store_id']))
        {
            $this->json_error(sprintf(Lang::get('not_allow_behalf_open'),$store[$goods_id]['store_name']));
            return;
        }


        /* 是否有商品 */
        $spec_model = & m('goodsspec');
        $model_cart = & m('cart');

        for ($i = 0; $i < count($spec_arr); $i++) {
            $spec_id = $spec_arr[$i];
            $quantity = $quantity_arr[$i];

            $spec_info = $spec_model->get(array(
                'fields' => 'g.store_id, g.goods_id, g.goods_name, g.spec_name_1, g.spec_name_2, g.default_image, gs.spec_1, gs.spec_2, gs.stock, gs.price',
                'conditions' => $spec_id,
                'join' => 'belongs_to_goods',
                    ));

            if (!$spec_info) {
                $this->json_error('no_such_goods');
                /* 商品不存在 */
                return;
            }
	        //价格少于5元，不能加入购物车
	        if($spec_info['price'] < 5)
	        {
	        	$this->json_error('price_less_5');
	        	return;
	        }

            /* 如果是自己店铺的商品，则不能购买 */
            if ($this->visitor->get('manage_store')) {
                if ($spec_info['store_id'] == $this->visitor->get('manage_store')) {
                    $this->json_error('can_not_buy_yourself');

                    return;
                }
            }

            /* 是否添加过 */

            $item_info = $model_cart->get("spec_id={$spec_id} AND session_id='" . SESS_ID . "'");
            if (!empty($item_info)) {
                $this->json_error('goods_already_in_cart');

                return;
            }

            if ($quantity > $spec_info['stock']) {
                $this->json_error('no_enough_goods');
                return;
            }
        }

        for ($i = 0; $i < count($spec_arr); $i++) {
            $spec_id = $spec_arr[$i];
            $quantity = $quantity_arr[$i];

            $spec_info = $spec_model->get(array(
                'fields' => 'g.store_id, g.goods_id, g.goods_name, g.spec_name_1, g.spec_name_2, g.default_image, gs.spec_1, gs.spec_2, gs.stock, gs.price',
                'conditions' => $spec_id,
                'join' => 'belongs_to_goods',
                    ));

            $spec_1 = $spec_info['spec_name_1'] ? $spec_info['spec_name_1'] . ':' . $spec_info['spec_1'] : $spec_info['spec_1'];
            $spec_2 = $spec_info['spec_name_2'] ? $spec_info['spec_name_2'] . ':' . $spec_info['spec_2'] : $spec_info['spec_2'];

            $specification = $spec_1 . ' ' . $spec_2;

            /* 将商品加入购物车 */
            $cart_item = array(
                'user_id' => $this->visitor->get('user_id'),
                'session_id' => SESS_ID,
                'store_id' => $spec_info['store_id'],
                'spec_id' => $spec_id,
                'goods_id' => $spec_info['goods_id'],
                'goods_name' => addslashes($spec_info['goods_name']),
                'specification' => addslashes(trim($specification)),
                'price' => $spec_info['price'],
                'quantity' => $quantity,
                'goods_image' => addslashes($spec_info['default_image']),
            );

            /* 添加并返回购物车统计即可 */
            $cart_model = & m('cart');
            $cart_model->add($cart_item);
        }

        $cart_status = $this->_get_cart_status();

        /* 更新被添加进购物车的次数 */
        $model_goodsstatistics = & m('goodsstatistics');
        $model_goodsstatistics->edit($spec_info['goods_id'], 'carts=carts+1');

        $this->json_result(array(
            'cart' => $cart_status['status'], //返回购物车状态
                ), 'addto_cart_successed');
    }

    /**
     *    丢弃商品
     *
     *    @author    Garbin
     *    @return    void
     */
    function drop() {
        /* 传入rec_id，删除并返回购物车统计即可 */
        $rec_id = isset($_GET['rec_id']) ? intval($_GET['rec_id']) : 0;
        if (!$rec_id) {
            return;
        }

        /* 从购物车中删除 */
        $model_cart = & m('cart');
        $droped_rows = $model_cart->drop('rec_id=' . $rec_id . ' AND session_id=\'' . SESS_ID . '\'', 'store_id');
        if (!$droped_rows) {
            return;
        }

        /* 返回结果 */
        $dropped_data = $model_cart->getDroppedData();
        $store_id = $dropped_data[$rec_id]['store_id'];
        $cart_status = $this->_get_cart_status();
        $this->json_result(array(
            'cart' => $cart_status['status'], //返回总的购物车状态
            'amount' => $cart_status['carts'][$store_id]['amount']   //返回指定店铺的购物车状态
                ), 'drop_item_successed');
    }

    /**
     *    更新购物车中商品的数量，以商品为单位，AJAX更新
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function update($spec='', $num='') {
		if(empty($spec) && empty($num)){
			$spec_id = isset($_GET['spec_id']) ? intval($_GET['spec_id']) : 0;
			$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
		}else{
			$spec_id = $spec;
			$quantity = $num;
		}

        if (!$spec_id || !$quantity) {
            /* 不合法的请求 */
            return;
        }

        /* 判断库存是否足够 */
        $model_spec = & m('goodsspec');
        $spec_info = $model_spec->get($spec_id);
        if (empty($spec_info)) {
            /* 没有该规格 */
            $this->json_error('no_such_spec');
            return;
        }

        if ($quantity > $spec_info['stock']) {
            /* 数量有限 */
            $this->json_error('no_enough_goods');
            return;
        }

        /* 修改数量 */
        $where = "spec_id={$spec_id} AND session_id='" . SESS_ID . "'";
        $model_cart = & m('cart');


        /* 获取购物车中的信息，用于获取价格并计算小计 */
        $cart_spec_info = $model_cart->get($where);
        if (empty($cart_spec_info)) {
            /* 并没有添加该商品到购物车 */
            return;
        }

        $store_id = $cart_spec_info['store_id'];

        /* 修改数量 */
        $model_cart->edit($where, array(
            'quantity' => $quantity,
        ));
		
		if(!empty($spec) && !empty($num)){
			$cart_status = $this->_get_cart_status();

			$this->json_result(array(
				'cart' => $cart_status['status'], //返回购物车状态
					), 'addto_cart_successed');
					exit;
		}
		
        /* 小计 */
        $subtotal = $quantity * $cart_spec_info['price'];

        /* 返回JSON结果 */
        $cart_status = $this->_get_cart_status();
        $this->json_result(array(
            'cart' => $cart_status['status'], //返回总的购物车状态
            'subtotal' => $subtotal, //小计
            'amount' => $cart_status['carts'][$store_id]['amount']  //店铺购物车总计
                ), 'update_item_successed');
    }

    /**
     *    获取购物车状态
     *
     *    @author    Garbin
     *    @return    array
     */
    function _get_cart_status() {
        /* 默认的返回格式 */
        $data = array(
            'status' => array(
                'quantity' => 0, //总数量
                'amount' => 0, //总金额
                'kinds' => 0, //总种类
            ),
            'carts' => array(), //购物车列表，包含每个购物车的状态
        );

        /* 获取所有购物车 */
        $carts = $this->_get_carts();
        if (empty($carts)) {
            return $data;
        }
        $data['carts'] = $carts;
        foreach ($carts as $store_id => $cart) {
            $data['status']['quantity'] += $cart['quantity'];
            $data['status']['amount'] += $cart['amount'];
            $data['status']['kinds'] += $cart['kinds'];
        }

        return $data;
    }

    /**
     *    购物车为空
     *
     *    @author    Garbin
     *    @return    void
     */
    function _cart_empty() {
        $goods_mod = &m('goods');
        $gst_mod = &m('goodsstatistics');
       /*  $interest = $goods_mod->find(array(
            'conditions' => '',
            'join' => 'has_goodsstatistics',
            'order' => 'views desc,collects desc, sales desc',
            'fields' => 'g.goods_id,goods_name,price,sales,default_image',
            'limit' => 6
                )); */
        $interest = $goods_mod->get_Mem_list(array(
            'conditions' => '',
            'order' => 'views desc,collects desc, sales desc',
            'fields' => 'g.goods_id,goods_name,g.price,sales,default_image,',
            'limit' => 6,
        ), null, false, true, $total_found);
        
        $this->assign('interest', $interest);
        $this->display('cart.empty.html');
    }

    /**
     *    以购物车为单位获取购物车列表及商品项
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_carts($store_id = 0) {
        $carts = array();

        /* 获取所有购物车中的内容 */
        $where_store_id = $store_id ? ' AND cart.store_id=' . $store_id : '';

        /* 只有是自己购物车的项目才能购买 */
        $where_user_id = $this->visitor->get('user_id') ? " AND cart.user_id=" . $this->visitor->get('user_id') : '';
        $cart_model = & m('cart');
        $cart_items = $cart_model->find(array(
            'conditions' => 'session_id = \'' . SESS_ID . "'" . $where_store_id . $where_user_id,
            'fields' => 'this.*,store.store_name',
            'join' => 'belongs_to_store',
                ));
		//$db = &db();
		//SELECT cart.*,s.store_name,goods_attr.attr_name,cart.rec_id FROM ecm_cart cart LEFT JOIN ecm_store s ON cart.store_id = s.store_id WHERE session_id = '40edee8ca52e0b59bd21277950c4691c' AND cart.user_id=33595
		//$sql = 'SELECT cart.*,a.attr_value,store.store_name FROM '.DB_PREFIX.'cart cart JOIN '.DB_PREFIX.'goods_attr a ON cart.goods_id= a.goods_id  JOIN '.DB_PREFIX.'store store ON cart.store_id= store.store_id  WHERE a.attr_name="货号" AND  cart.session_id = \'' . SESS_ID . "'" . $where_store_id . $where_user_id;
		//$cart_items = $db->getall($sql);
		
        if (empty($cart_items)) {
            return $carts;
        }
        $kinds = array();
        foreach ($cart_items as $k=>$item) {
            /* 小计 */
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $kinds[$item['store_id']][$item['goods_id']] = 1;
		
            /* 以店铺ID为索引 */
            empty($item['goods_image']) && $item['goods_image'] = Conf::get('default_goods_image');
            $carts[$item['store_id']]['store_name'] = $item['store_name'];
            $carts[$item['store_id']]['amount'] += $item['subtotal'];   //各店铺的总金额
            $carts[$item['store_id']]['quantity'] += $item['quantity'];   //各店铺的总数量
            $carts[$item['store_id']]['goods'][] = $item;
        }
        foreach ($carts as $_store_id => $cart) {
            $carts[$_store_id]['kinds'] = count(array_keys($kinds[$_store_id]));  //各店铺的商品种类数
        }

        return $carts;
    }

}

?>
