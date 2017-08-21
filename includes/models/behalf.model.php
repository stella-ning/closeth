<?php

/* 代发 behalf */
class BehalfModel extends BaseModel
{
    var $table  = 'behalf';
    var $prikey = 'bh_id';
    var $_name  = 'behalf';
    
    var $_relation = array(
    		// 一个代发属于一个会员
    		'belongs_to_user' => array(
    				'model'         => 'member',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'bh_id',
    				'reverse'       => 'has_behalf',
    		),
    		// 代发和市场是多对多的关系
    		'has_market' => array(
    				'model'         => 'market',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'market_behalf',
    				'foreign_key'   => 'bh_id',
    				'reverse'       => 'belongs_to_behalf',
    		),
    		// 代发和快递是多对多的关系
    		'has_delivery' => array(
    				'model'         => 'delivery',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'behalf_delivery',
    				'foreign_key'   => 'bh_id',
    				'reverse'       => 'belongs_to_behalf',
    		),
    		// 代发和会员是多对多的关系（会员收藏代发）
    		'be_collect' => array(
    				'model'         => 'member',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'collect',
    				'foreign_key'   => 'item_id',
    				'ext_limit'     => array('type' => 'behalf'),
    				'reverse'       => 'collect_behalf',
    		),
    		// 代发和会员是多对多的关系（会员签约代发）
    		'be_signed' => array(
    				'model'         => 'member',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'collect',
    				'foreign_key'   => 'item_id',
    				'ext_limit'     => array('type' => 'sbehalf'),
    				'reverse'       => 'collect_sbehalf',
    		),
    		//一个代发有多个拿货员
    		'has_membertaker'=>array(
    				'model'         => 'member',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'behalf_goods_taker',
    				'dependent' => true
    		),
    		//一个代发有多个拿货市场
    		'has_markettakers'=>array(
    				'model'         => 'markettaker',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'bh_id',
    				'dependent' => true
    		),
    		//一个代发有多个拿货单
    		'has_goodstakerinventory'=>array(
    				'model'         => 'goodstakerinventory',
    				'type'          => HAS_MANY,
    				'foreign_key'   => 'bh_id',
    				'dependent' => true
    		),
            // 代发黑名单和店铺是多对多的关系
            'has_blacklist_stores' => array(
                'model'         => 'store',
                'type'          => HAS_AND_BELONGS_TO_MANY,
                'middle_table'  => 'behalf_store_blacklist',
                'foreign_key'   => 'bh_id',
                'reverse'       => 'belongs_to_behalf_blacklist',
            ),
    		// 代发和分类是多对多的关系
    		/* 'belongs_to_gcategory' => array(
    				'model'         => 'gcategory',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'category_behalf',
    				'foreign_key'   => 'bh_id',
    				'reverse'       => 'has_behalf',
    		), */
            //一个代发拥有多个角色
            'has_role' => array(
                'model' =>'role',
                'type' =>HAS_MANY,
                'foreign_key' => 'bh_id',
                'reverse' => 'belongs_to_behalf' 
            )
    		
    );
    
    /*
     * 判断名称是否唯一
    */
    function unique($bh_name, $bh_id = 0)
    {
    	$conditions = "bh_name = '" . $bh_name . "'";
    	$bh_id && $conditions .= " AND bh_id <> '" . $bh_id . "'";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    /**
     * 代发待发货订单数是否小于设置值
     * @param 代发 $bh_id
     */
    function usable_behalf_by_max_orders($bh_id)
    {
        if(!$bh_id) return false;
        $behalf = $this->get($bh_id);
        if(!$behalf) return false;
        if($behalf['max_orders'] == 0) return true;
        $model_order =& m('order');
        $count = $model_order->getOne("SELECT count(*) from ".$model_order->table." WHERE bh_id=".$bh_id." AND status=".ORDER_ACCEPTED);
        if($count >= $behalf['max_orders']) return false;
        return true;
            
    }
    
    /**
     * 
     * @param 代发id  $bh_id
     * @param 快递id  $dl_id
     * @param 订单商品总数  $goods_quantity
     * @return 返回  订单 中  某个快递费
     */
    function calculate_behalf_delivery_fee($bh_id,$dl_id,$goods_quantity)
    {
    	//$behalf_mod =& m('behalf');
    	if(!$bh_id || !$dl_id ||!$goods_quantity)
    	{
    		return 0;
    	}
    	$deliveries = $this->getRelatedData('has_delivery',$bh_id,array(
    			'order'=>'sort_order'
    	));
    	foreach($deliveries as $delivery)
    	{
    		 
    		if(intval($delivery['dl_id']) == intval($dl_id))
    		{
    			if($goods_quantity <= $delivery['first_amount'])
    			{
    				$shipping_fee = $delivery['first_price'];
    			}
    			else 
    			{
    				if($delivery['step_amount'] > 0)
    				{
    					$shipping_fee =$delivery['first_price'] + (ceil(($goods_quantity - $delivery['first_amount'])/$delivery['step_amount']))*$delivery['step_price'];
    				}
    				else 
    				{
    					$shipping_fee = $delivery['first_price'];
    				}
    			}
    		}
    	}
    	return $shipping_fee;
    }
    
    /**
     * 
     * @param 代发 $bh_id
     * @param 订单商品数量 $goods_quantity
     * @return 返回  订单中代发  所有快递费
     */
    function calculate_delivery_fee_bybehalf_old($bh_id,$goods_quantity)
    {
    	//$behalf_mod =& m('behalf');
    	if(!$bh_id || !$goods_quantity)
    	{
    		return 0;
    	}
    	$deliveries = $this->getRelatedData('has_delivery',$bh_id,array(
    			'order'=>'sort_order'
    	));
    	if(empty($deliveries))
    	{
    		return 0;
    	}
    	foreach($deliveries as $key=>$delivery)
    	{
    		 
    		
    			if($goods_quantity <= $delivery['first_amount'])
    			{
    				$deliveries[$key]['dl_fee'] = $delivery['first_price'];
    			}
    			else
    			{
    				if($delivery['step_amount'] > 0)
    				{
    					$deliveries[$key]['dl_fee'] =$delivery['first_price'] + (ceil(($goods_quantity - $delivery['first_amount'])/$delivery['step_amount']))*$delivery['step_price'];
    				}
    				else
    				{
    					$deliveries[$key]['dl_fee'] = $delivery['first_price'];
    				}
    			}
    		
    	}
    	return $deliveries;
    }

    /**
     * 
     * @param 代发ID $bh_id
     * @param  $gids  形如  "store_id:rec_id:goods_id, ..." 店铺ID：购物车商品rec_id:商品ID
     * @param 收货区县ID $region_id
     * @return number|void
     * @author mr.z  
     */
    function calculate_delivery_fee_bybehalf($bh_id,$gids,$region_id)
    {

        // 取买家所在区域
        $region_model = &m ('region');
   
        $region = $region_model->get_parents($region_id);

        

        // 取对应的快递
        $deliveries = $this->getRelatedData('has_delivery',$bh_id,array(
                'order'=>'sort_order'
        ));

        // 配送区域
        $shipping_area = & m('shipping_area');

        $relate_area = array();
        $config = array();
        $contained_area = array();
        foreach($deliveries as $key=>$delivery){

            $relate_area[$key] = $shipping_area->find(array(
                'conditions'    => 'dl_id = '.$delivery['dl_id'],
            ));
            foreach ($relate_area[$key] as $k => $v) {

                $config[$key] =  unserialize($v['configure']);

                $relate_area[$key][$k]['configure'] = array();
                foreach ($config[$key] as $k1 => $v1) {
                    $relate_area[$key][$k]['configure'][$v1['name']] = $v1['value'];
                }

                // $relate_area[$key][$k]['configure'] = unserialize($v['configure']);

                $relate_area[$key][$k]['contained_area'] = unserialize($v['contained_area']);
                
                // $contained_area[$key] = unserialize($v['contained_area']);
                
            }
                
        }

        // 用户区域与快递配送区域进行匹配
        $flag = array();

        foreach ($relate_area as $key => $value) {
            $flag[$key] = false;   // 初始化$flag标志
        }

        $target_shipping_area = array();
        $temp_by_number = array();     // 优先以重量来计算快递
        foreach (array_reverse($region) as $key => $value) {

            foreach ($relate_area as $k => $v) {
                if (!$flag[$k]){
                    foreach ($v as $k1 => $v1) {
                        foreach ($v1['contained_area'] as $k2 => $v2) {
                            if ($k2 == $value){
                                if($v1['configure']['fee_compute_mode']=='by_number'){
                                    $temp_by_number[$k] = $v1;
                                    break;
                                }
                                $target_shipping_area[$k] = $v1;
                                $flag[$k] = true;
                                break;
                            }
                        }
                        if ($flag[$k]) {
                            break;
                        }
                    }
                }                              
                
            }
           
        }

        foreach ($deliveries as $key => $value) {
            
            if(empty($target_shipping_area[$key])){
                if(!empty($temp_by_number[$key])){
                    $target_shipping_area[$key] = $temp_by_number[$key];
                }else{
                    $area = $shipping_area->get(array(
                        'conditions'    => 'dl_id = '.$value['dl_id'].' AND area_default = 1',
                    ));

                    if ($area){   // 判断有没有默认快递
                        $config =  unserialize($area['configure']);

                        $area['configure'] = array();

                        foreach ($config as $k => $v) {
                            $area['configure'][$v['name']] = $v['value'];
                        }

                        $area['contained_area'] = unserialize($area['contained_area']);

                        $target_shipping_area[$key] = $area;
                    }
                    
                }
            }
        }

        // print_r($target_shipping_area);exit;
        /*-- 取商品所属分类 --*/
        $array_gids = explode(',',$gids); 
        foreach ($array_gids as $key => $value) {
            if (!empty($value)){
                $array_gids[$key] = explode(':',$value); 
            }else{
                unset($array_gids[$key]);
            }
        }

        $cart_model = & m('cart');
        $goods_model =  &m('goods');
        $gcate_model = & m('gcategory');
        // $gcateb_model = new GcategoryBModel(array(), db());

        $goods= array();
        $gcate = array();
        $gcate = array();
        foreach ($array_gids as $key => $value) {

            // 取购物车的对应商品
            $goods[$key] = $cart_model->get(array(
                'conditions'    => 'rec_id = '. $value[1],
            ));

            $quantity[$key] = $goods[$key]['quantity'];

            // 找商品所属分类
            $goods_cate[$key] = $goods_model->get(array(
                'fields' => 'cate_id',
                'conditions'    => 'goods_id = '. $value[2],
            ));

            // 取分类
            $gcate[$key] = $gcate_model->get(array(
                'conditions'    => 'cate_id = '.$goods_cate[$key]['cate_id'],
            )); 
            // 取分类的所有祖先
             // $gcate[$value[2]] = $gcateb_model->get_ancestor($last_gcate[$key]['cate_id']);

        }
        // print_r($gcate);die;
        /*-- 快递费用计算 --*/
        
        //计算商品总重量和总量
        $total_weight = 0;
        $total_number = 0;
        foreach ($goods as $k => $v) {
            if($gcate[$k]['weight'] == 0){   //工作人员没有设置分类关联重量时，取一个大约值，然后直接写死
                $gcate[$k]['weight'] = 0.5;    //取0.5公斤
            }
            $total_weight += $v['quantity']*$gcate[$k]['weight'];
            $total_number += $v['quantity'];
        }



        $first_weight = 1;   // 首重暂时只按一公斤来算，暂时不考虑首重为别的公斤数的情况
        $step_weight = $total_weight - $first_weight;  
        if ($step_weight > 0){
            $step_number = ceil($step_weight/1);
        }else{
            $step_number = 0;
        }
        
        foreach ($deliveries as $key => $value) {

            if (!empty($target_shipping_area[$key])){
                if($target_shipping_area[$key]['configure']['fee_compute_mode'] == 'by_weight'){
                    $deliveries[$key]['dl_fee'] = $target_shipping_area[$key]['configure']['base_fee'] * $first_weight + $target_shipping_area[$key]['configure']['step_fee'] * $step_number;
                }else{
                    $deliveries[$key]['dl_fee'] = $target_shipping_area[$key]['configure']['item_fee'] + $target_shipping_area[$key]['configure']['item_step_fee'] * ($total_number-1);
                }
                
            }else{
                // 工作人员没有设置快递和分类的情况，取一个大约值，直接在程序写死, 首重：6元，续重4元。
                $deliveries[$key]['dl_fee'] = 6*$first_weight + 4*$step_number;
            }
                            
        }
         // print_r($target_shipping_area);exit;
        return $deliveries;
    }


    /**
     * zjh
     * 删除订单部分商品后，重新计算快递费用
     * 参数1：order_id（订单号）
     * 参数2：商品的 warehouse_ids（goods_warehouse的id数组）
     * 参数3：1，type 为 'keep' 时，$warehouse_ids传入的是要保留的商品数组；2，$type 为 'remove' 时，$warehouse_ids传入的是要删除的商品数组；默认为remove
     *
    **/
    function get_shipping_fee_after_order_cancel($order_id, $warehouse_ids = array(),$type='remove')
    {
        if (empty($order_id)){
            return false;
        }
        //通过order_id 获取user_id
        $order_model =& m('order');
        $order = $order_model->get(array(
            'conditions'    => 'order_id = '. $order_id,
        ));

        // 获取买家所在区域id
       // $address_model =& m('address');
        $order_extm_model = &m('orderextm');
        $address = $order_extm_model->get(array(
            'fields' => 'region_id',
            'conditions'    => 'order_id = '. $order_id,
        ));

        // 取买家所在区域
        $region_model = &m ('region');
        $region = $region_model->get_parents($address['region_id']);


        // 取对应的快递
        $deliveries = $this->getRelatedData('has_delivery',$order['bh_id'],array(
                'order'=>'sort_order'
        ));

        // 取到订单所用的快递
        $order_extm = $order_extm_model->get(array(
            'conditions'    => 'order_id = '. $order_id,
        ));

        foreach ($deliveries as $key => $value) {
            if($value['dl_id'] == $order_extm['dl_id']){
                $delivery = $value;
            }
        }

        // 配送区域
        $shipping_area = & m('shipping_area');

        $relate_area = array();
        $config = array();
        $contained_area = array();

        $relate_area = $shipping_area->find(array(
            'conditions'    => 'dl_id = '.$delivery['dl_id'],
        ));
        foreach ($relate_area as $k => $v) {

            $config =  unserialize($v['configure']);

            $relate_area[$k]['configure'] = array();
            foreach ($config as $k1 => $v1) {
                $relate_area[$k]['configure'][$v1['name']] = $v1['value'];
            }

            $relate_area[$k]['contained_area'] = unserialize($v['contained_area']);
            
        }
                

        // 用户区域与快递配送区域进行匹配
        $flag = false;

        $target_shipping_area = array();
        $temp_by_number = array();     // 优先以重量来计算快递
        foreach (array_reverse($region) as $key => $value) {

            foreach ($relate_area as $k1 => $v1) {
                foreach ($v1['contained_area'] as $k2 => $v2) {
                    if ($k2 == $value){
                        if($v1['configure']['fee_compute_mode']=='by_number'){
                            $temp_by_number = $v1;
                            break;
                        }
                        $target_shipping_area = $v1;
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    break;
                }
            }  
        }
  
        if(empty($target_shipping_area)){
            if(!empty($temp_by_number)){
                $target_shipping_area = $temp_by_number;
            }else{
                $area = $shipping_area->get(array(
                    'conditions'    => 'dl_id = '.$delivery['dl_id'].' AND area_default = 1',
                ));

                if ($area){   // 判断有没有默认快递
                    $config =  unserialize($area['configure']);

                    $area['configure'] = array();

                    foreach ($config as $k => $v) {
                        $area['configure'][$v['name']] = $v['value'];
                    }

                    $area['contained_area'] = unserialize($area['contained_area']);

                    $target_shipping_area = $area;
                }
                
            }
        }

        /*-- 取商品所属分类 --*/
        $goods_warehouse_model = &m('goodswarehouse');

        $goods_warehouse = $goods_warehouse_model->find(array(

            'conditions'    => 'order_id = '.$order_id.' AND  goods_status not '.db_create_in(array(BEHALF_GOODS_CANCEL,BEHALF_GOODS_ADJUST)),

        ));

        $remain_goods_warehouse = array();

        if($type == 'keep'){
            $flag = false;
        }else{
            $flag = true;
        }
         
        foreach ($goods_warehouse as $key => $value) {
            
            foreach ($warehouse_ids as $k => $v) {
                if ($value['id'] == $v){

                    if($type == 'keep'){
                        $flag = true;
                    }else{
                        $flag = false;
                    }

                }              
            }
            if ($flag){
                $remain_goods_warehouse[$key] = $value;
            }
            if($type == 'keep'){
                $flag = false;
            }else{
                $flag = true;
            }
        }

        // 如果得到要保留的数组为空 zjh 2017/8/7
        if(empty($remain_goods_warehouse)){

            return 0;  // 直接返回0
        }

        $goods_model =  &m('goods');
        $gcate_model = & m('gcategory');

        $goods_cate = array();
        $gcate = array();
        foreach ($remain_goods_warehouse as $key => $value) {

            // 找商品所属分类
            $goods_cate[$key] = $goods_model->get(array(
                'fields' => 'cate_id',
                'conditions'    => 'goods_id = '. $value['goods_id'],
            ));

            // 取分类
            $gcate[$key] = $gcate_model->get(array(
                'conditions'    => 'cate_id = '.$goods_cate[$key]['cate_id'],
            )); 

        }

        /*-- 快递费用计算 --*/
        
        //计算商品总重量和总量
        $total_weight = 0;
        $total_number = 0;
        foreach ($gcate as $k => $v) {
            if($gcate[$k]['weight'] == 0){   //工作人员没有设置分类关联重量时，取一个大约值，然后直接写死
                $gcate[$k]['weight'] = 0.5;    //取0.5公斤
            }
            $total_weight += $gcate[$k]['weight'];
            $total_number += 1;
        }
        Log::write("goods total_weight and total_number :".$total_weight."#".$total_number);
        

        $first_weight = 1;   // 首重暂时只按一公斤来算，暂时不考虑首重为别的公斤数的情况
        $step_weight = $total_weight - $first_weight;  
        if ($step_weight > 0){
            $step_number = ceil($step_weight/1);
        }else{
            $step_number = 0;
        }

        if (!empty($target_shipping_area)){
            if($target_shipping_area['configure']['fee_compute_mode'] == 'by_weight'){
                $delivery['dl_fee'] = $target_shipping_area['configure']['base_fee'] * $first_weight + $target_shipping_area['configure']['step_fee'] * $step_number;
            }else{
                $delivery['dl_fee'] = $target_shipping_area['configure']['item_fee'] + $target_shipping_area['configure']['item_step_fee'] * ($total_number-1);
            }
            
        }else{
            // 工作人员没有设置快递和分类的情况，取一个大约值，直接在程序写死, 首重：6元，续重4元。
            $delivery['dl_fee'] = 6*$first_weight + 4*$step_number;
        }
                            
        return $delivery['dl_fee'];
    }
    
    /**
     * 获取代发和代发可用的快递，(不用来计算快递费用)
     * $store_id = 0,获取所有市场的代发和本代发可发的快递
     * $store_id > 0,获取店铺所在市场 的代发和本代发可发的快递
     * @param number $store_id
     */
    function get_behalfs_deliverys($store_id=0)
    {
    	$mk_id = 0;//市场id
    	if($store_id > 0)
    	{
    		$mod_store = & m('store');
    		$store = $mod_store->get($store_id);
    		$store && $mk_id = $store['mk_id'];
    	}
    	
    	$mod_market = & m('market');
    	if($mk_id > 0)
    	{
    		//店铺所在市场的代发 及 相应可发的快递
    		$market = $mod_market->get($mk_id);
    		$market_layer = $mod_market->get_layer($mk_id);
    		if($market_layer == 3)
    		{
    			$mk_id = $market['parent_id'];
    		}
    		if($market_layer == 1)
    		{
    			$temp_array = array();
    			$mk_id = $mod_market->get_list($mk_id);
    			foreach($my_mk_id as $value)
    			{
    				$temp_array[] = $value['mk_id'];
    			}
    			$mk_id = $temp_array;
    		}
    		$behalfs = $mod_market->getRelatedData('belongs_to_behalf',$mk_id);
            if(!empty($behalfs))
            {
            	foreach ($behalfs as $key=>$behalf)
            	{
            		$behalfs[$key]['deliveries']=$this->getRelatedData('has_delivery',$behalf['bh_id'],array(
            				'order'=>'sort_order'
            		));
            	}
            }
    		//dump($behalfs);
    	}
    	else
    	{
    		//所有代发 及 相应可发的快递,加入  判别 代发是否 有拿货范围
    		$behalfs = $this->findAll(array(
    				'include'=>array(
    					'has_market'	
    		         )
    		));
    		
    		if(!empty($behalfs))
    		{
    			foreach ($behalfs as $key=>$behalf)
    			{
    				$behalfs[$key]['deliveries']=$this->getRelatedData('has_delivery',$behalf['bh_id'],array(
    						'order'=>'sort_order'
    				));
    				if(empty($behalf['market']))
    				{
    					unset($behalfs[$key]);
    				}
    			}
    		}
    		//dump($behalfs);
    	}
    	//
    	if(!empty($behalfs))
    	{
    	    foreach ($behalfs as $key=>$behalf)
    	    {
    	        if(!$this->usable_behalf_by_max_orders($behalf['bh_id']))
    	        {
    	            unset($behalfs[$key]);
    	        }
    	    }
    	    //随机排列代发
    	    shuffle($behalfs);
    	}
    	return $behalfs;
    }
    
    /**
     * 判断商品是否在代发的拿货范围
     * @param 代发id    $bh_id
     * @param 商品id数组    $goods_ids
     */
    function is_behalf_goods($bh_id=0,$goods_ids=array())
    {
    	if(empty($bh_id))
    	{
    		return false;
    	}
    	if(empty($goods_ids))
    	{
    		return false;
    	}
    	//获取代发的拿货范围
    	$markets = $this->getRelatedData('has_market', $bh_id);
    	if(empty($markets))
    	{
    		return false;
    	}
    	$model_market=& m('market');
    	
    	$mk_ids = array();
    	$mk_floor_ids=array();
    	foreach ($markets as $market)
    	{
    		$mk_ids[] = $market['mk_id'];
    	}
    	foreach ($mk_ids as $mk_id)
    	{
    		$floors = $model_market->get_list($mk_id);
    		if(!empty($floors))
    		{
    			foreach ($floors as $floor)
    			{
    				$mk_floor_ids[] = $floor['mk_id'];
    			}
    		}
    	}
    	$mk_ids = array_merge($mk_ids,$mk_floor_ids);
    	
    	$goods_mk_ids = array();
    	$model_goods=& m('goods');
    	foreach ($goods_ids as $goods_id)
    	{
    		$store = $model_goods->get(array(
    			'conditions'=>'goods_id='.$goods_id,
    			'fields'=>'s.*',
    			'join'=>'belongs_to_store',
    		));
    		if(!empty($store) && !in_array($store['mk_id'],$goods_mk_ids))
    		{
    			$goods_mk_ids[] = $store['mk_id'];
    		}
    	}
    	
    	foreach ($goods_mk_ids as $gmi)
    	{
    		if(!in_array($gmi, $mk_ids))
    		{
    			return false;
    		}
    	}
    	return true;
    }

   
}

?>