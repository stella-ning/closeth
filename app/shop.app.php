<?php

class ShopApp extends StorebaseApp
{
    function index()
    {
        /* 店铺信息 */
        $_GET['act'] = 'index';
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        
        $search = array(
            'cid'   => isset($_GET['cid'])? $_GET['cid']: '',
            'price' => isset($_GET['price'])?$_GET['price']: '',
        );
        
        
        $this->set_store($id);
        $store = $this->get_store_data();
        
        //微信二维码
        if($store['im_wx'] && !preg_match('/[^\w]/', trim($store['im_wx'])))
        {
            if(file_exists(ROOT_PATH.'/data/qrcode/zwd51_s'.$store['store_id'].'.png'))
            {
                @unlink(ROOT_PATH.'/data/qrcode/zwd51_s'.$store['store_id'].'.png');
            }
           
            if(file_exists(ROOT_PATH.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png'))
            {
                $store['img_wx'] = SITE_URL.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png';
            }
            else
            {
                $success = generateQRfromQRCode($store['im_wx'], $store['store_id'].'_'.$store['im_wx']);
                $success && $store['img_wx'] = SITE_URL.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png';
            }
        }
        
        //主营
        if(!$store['business_scope'])
        {
            $mod_store =& m('store');
            $business_scope = $mod_store->getRelatedData('has_scategory',$id);
            if($business_scope)
            {
                $business_scope = array_values($business_scope);
                $store['business_scope'] = $business_scope[0]['cate_name'];
            }
        }
        
        $store['year'] = date('Y') - date('Y' ,$store['add_time']) + 1 ;
        
        //统计信息
        $goodsCountByCate = $this->_get_store_count_by_cate($this->_store_id, "g.closed = 0 AND g.if_show = 1 AND g.default_spec > 0");
        if (empty($store['goods_count'])){
            foreach ($goodsCountByCate as $v) {
                foreach ($v["cates"] as $total_info) {
                    $store['goods_count'] += $total_info ['t'];
                }
            }
        }
        
        if ($search ['cid']) {
            foreach ($goodsCountByCate as $v) {
                foreach ($v["cates"] as $total_info) {
                    if ($total_info ['cate_id'] == $search ['cid']) {
                        $cate_name = $total_info['cate_name'];
                        break;
                    }
                }
            }
            
            $this->assign('search_cate'  , array('cate_id'=> $search ['cid'] , 'cate_name'=>$cate_name));
        }

        $store ['goodsCountByCate'] = $goodsCountByCate;

        
        //dump($store);
        $this->assign('store', $store);
        // 店铺顶部两个广告
        $this->_get_store_advs();

        $this->assign('OEM',OEM);
        $this->assign("siteurl",SITE_URL);
        /* 取得友情链接 */
        $this->assign('partners', $this->_get_partners($id));
        /*取得n天内上新与下架商品数量*/
        //$this->assign("newclose_goods",$this->_get_newclose_goods_num($id,5));
        /* 取得推荐商品 */
        $recommended_goods =  $this->_get_recommended_goods($id,8);
        
        
        $this->assign('recommended_goods', $recommended_goods);
        //$this->assign('new_groupbuy', $this->_get_new_groupbuy($id));

        /* 取得最新商品   现为所有商品 */
        $new_goods = $this->_get_new_goods($id , $store['goods_count'] , $search);

        $this->assign('new_goods',$new_goods);
        
        
        /*收藏信息*/
        $this->assign('favorite',$this->_get_favorite($id));

        /* 取得热卖商品 */
        //$this->assign('hot_sale_goods', $this->_get_hot_sale_goods($id));

        $orders = $this->_get_orders();
        $this->assign('orders', $orders);

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store', $store['store_name']);

        $this->_config_seo('title', $store['mk_name'] . ' - '.$store['dangkou_address'] .'_'. $store['store_name']);
        /* 配置seo信息 */
        $this->_config_seo($this->_get_seo_info($store));
        /* 设置最后更新时间 */
        $mod_store =& m('store');
        $this->assign('last_update_date', $mod_store->last_update($store['store_id']));
        
        $this->display('store2017.index.html');
    }


    function search()
    {
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->set_store($id);
        $store = $this->get_store_data();
		if($store['im_wx'] && !preg_match('/[^\w]/', trim($store['im_wx'])))
        {
            if(file_exists(ROOT_PATH.'/data/qrcode/zwd51_s'.$store['store_id'].'.png'))
            {
                @unlink(ROOT_PATH.'/data/qrcode/zwd51_s'.$store['store_id'].'.png');
            }
           
            if(file_exists(ROOT_PATH.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png'))
            {
                $store['img_wx'] = SITE_URL.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png';
            }
            else
            {
                $success = generateQRfromQRCode($store['im_wx'], $store['store_id'].'_'.$store['im_wx']);
                $success && $store['img_wx'] = SITE_URL.'/data/qrcode/zwd51_'.$store['store_id'].'_'.$store['im_wx'].'.png';
            }
        }
		
		//主营
        if(!$store['business_scope'])
        {
            $mod_store =& m('store');
            $business_scope = $mod_store->getRelatedData('has_scategory',$id);
            if($business_scope)
            {
                $business_scope = array_values($business_scope);
                $store['business_scope'] = $business_scope[0]['cate_name'];
            }
        }
        
        $store['year'] = date('Y') - date('Y' ,$store['add_time']) + 1 ;
        
        //统计信息
        $goodsCountByCate = $this->_get_store_count_by_cate($this->_store_id, "g.closed = 0 AND g.if_show = 1 AND g.default_spec > 0");
        if (empty($store['goods_count'])){
            foreach ($goodsCountByCate as $v) {
                foreach ($v["cates"] as $total_info) {
                    $store['goods_count'] += $total_info ['t'];
                }
            }
        }
        
        if ($search ['cid']) {
            foreach ($goodsCountByCate as $v) {
                foreach ($v["cates"] as $total_info) {
                    if ($total_info ['cate_id'] == $search ['cid']) {
                        $cate_name = $total_info['cate_name'];
                        break;
                    }
                }
            }
            
            $this->assign('search_cate'  , array('cate_id'=> $search ['cid'] , 'cate_name'=>$cate_name));
        }

        $store ['goodsCountByCate'] = $goodsCountByCate;
		
        $this->assign('store', $store);
		
		// 店铺顶部两个广告
        $this->_get_store_advs();

        $this->assign('OEM',OEM);
        $this->assign("siteurl",SITE_URL);
        /* 取得友情链接 */
        $this->assign('partners', $this->_get_partners($id));
        /*取得n天内上新与下架商品数量*/
        //$this->assign("newclose_goods",$this->_get_newclose_goods_num($id,5));
        /* 取得推荐商品 */
        $recommended_goods =  $this->_get_recommended_goods($id,8);
        
        
        $this->assign('recommended_goods', $recommended_goods);
        //$this->assign('new_groupbuy', $this->_get_new_groupbuy($id));
        
        /*收藏信息*/
        $this->assign('favorite',$this->_get_favorite($id));

        $orders = $this->_get_orders();
        $this->assign('orders', $orders);
		
        /*如果是查询上新与下架*/
        if(isset($_GET['gids']) && !empty($_GET['gids']))
        {
            $gids=explode(',', trim($_GET['gids']));
            $this->_assign_newclose_goods($id,$gids);
            $this->assign("flag",trim($_GET['flag']));
        }else
        {
            /* 搜索到的商品 */
            $this->_assign_searched_goods($id);
        }
        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
                         $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
                         LANG::get('goods_list')
                         );

        $this->_config_seo('title', Lang::get('goods_list') . ' - ' . $store['store_name']);
        $this->display('store.search.html');
    }
    
    public function ajaxget(){
        $params = array(
            'store_id'  => $_GET ['store_id'],
            'order'     => $_GET ['order'],
            ''
        );
        
    }

    function groupbuy()
    {
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->set_store($id);
        $store = $this->get_store_data();
        $this->assign('store', $store);

        /* 搜索团购 */
        empty($_GET['state']) &&  $_GET['state'] = 'on';
        $conditions = '1=1';
        if ($_GET['state'] == 'on')
        {
            $conditions .= ' AND gb.state ='. GROUP_ON .' AND gb.end_time>' . gmtime();
            $search_name = array(
                array(
                    'text'  => Lang::get('group_on')
                      ),
                array(
                    'text'  => Lang::get('all_groupbuy'),
                    'url'  => url('app=store&act=groupbuy&state=all&id=' . $id)
                      ),
                                 );
        }
        else if ($_GET['state'] == 'all')
        {
            $conditions .= ' AND gb.state '. db_create_in(array(GROUP_ON,GROUP_END,GROUP_FINISHED));
            $search_name = array(
                array(
                    'text'  => Lang::get('all_groupbuy')
                      ),
                array(
                    'text'  => Lang::get('group_on'),
                    'url'  => url('app=store&act=groupbuy&state=on&id=' . $id)
                      ),
                                 );
        }

        $page = $this->_get_page(16);
        $groupbuy_mod = &m('groupbuy');
        $groupbuy_list = $groupbuy_mod->find(array(
            'fields'    => 'goods.default_image, gb.group_name, gb.group_id, gb.spec_price, gb.end_time, gb.state',
            'join'      => 'belong_goods',
            'conditions'=> $conditions . ' AND gb.store_id=' . $id ,
            'order'     => 'group_id DESC',
            'limit'     => $page['limit'],
            'count'     => true
                                                   ));
        $page['item_count'] = $groupbuy_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        if (empty($groupbuy_list))
        {
            $groupbuy_list = array();
        }
        foreach ($groupbuy_list as $key => $_g)
        {
            empty($groupbuy_list[$key]['default_image']) && $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $tmp = current(unserialize($_g['spec_price']));
            $groupbuy_list[$key]['price'] = $tmp['price'];
            if ($_g['end_time'] < gmtime())
            {
                $groupbuy_list[$key]['group_state'] = group_state($_g['state']);
            }
            else
            {
                $groupbuy_list[$key]['lefttime'] = lefttime($_g['end_time']);
            }
        }
        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
                         $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
                         LANG::get('groupbuy_list')
                         );

        $this->assign('groupbuy_list', $groupbuy_list);
        $this->assign('search_name', $search_name);
        $this->_config_seo('title', $search_name[0]['text'] . ' - ' . $store['store_name']);
        $this->display('store.groupbuy.html');
    }

    function article()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $article = $this->_get_article($id);
        if (!$article)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->assign('article', $article);

        /* 店铺信息 */
        $this->set_store($article['store_id']);
        $store = $this->get_store_data();
        $this->assign('store', $store);

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
                         $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
                         $article['title']
                         );

        $this->_config_seo('title', $article['title'] . ' - ' . $store['store_name']);
        $this->display('store.article.html');
    }

    /* 信用评价 */
    function credit()
    {
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->set_store($id);
        $store = $this->get_store_data();
        $this->assign('store', $store);
        /* 取得评价过的商品 */
        if (!empty($_GET['eval']) && in_array($_GET['eval'], array(1,2,3)))
        {
            $conditions = "AND evaluation = '{$_GET['eval']}'";
        }
        else
        {
            $conditions = "";
            $_GET['eval'] = '';
        }
        $page = $this->_get_page(10);
        $order_goods_mod =& m('ordergoods');
        $goods_list = $order_goods_mod->find(array(
            'conditions' => "seller_id = '$id' AND evaluation_status = 1 AND is_valid = 1 " . $conditions,
            'join'       => 'belongs_to_order',
            'fields'     => 'buyer_id, buyer_name, anonymous, evaluation_time, goods_id, goods_name, specification, price, quantity, goods_image, evaluation, comment',
            'order'      => 'evaluation_time desc',
            'limit'      => $page['limit'],
            'count'      => true,
                                                   ));
        $this->assign('goods_list', $goods_list);

        $page['item_count'] = $order_goods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        /* 按时间统计 */
        $stats = array();
        for ($i = 0; $i <= 3; $i++)
        {
            $stats[$i]['in_a_week']        = 0;
            $stats[$i]['in_a_month']       = 0;
            $stats[$i]['in_six_month']     = 0;
            $stats[$i]['six_month_before'] = 0;
            $stats[$i]['total']            = 0;
        }

        $goods_list = $order_goods_mod->find(array(
            'conditions' => "seller_id = '$id' AND evaluation_status = 1 AND is_valid = 1 ",
            'join'       => 'belongs_to_order',
            'fields'     => 'evaluation_time, evaluation',
                                                   ));
        foreach ($goods_list as $goods)
        {
            $eval = $goods['evaluation'];
            $stats[$eval]['total']++;
            $stats[0]['total']++;

            $days = (gmtime() - $goods['evaluation_time']) / (24 * 3600);
            if ($days <= 7)
            {
                $stats[$eval]['in_a_week']++;
                $stats[0]['in_a_week']++;
            }
            if ($days <= 30)
            {
                $stats[$eval]['in_a_month']++;
                $stats[0]['in_a_month']++;
            }
            if ($days <= 180)
            {
                $stats[$eval]['in_six_month']++;
                $stats[0]['in_six_month']++;
            }
            if ($days > 180)
            {
                $stats[$eval]['six_month_before']++;
                $stats[0]['six_month_before']++;
            }
        }
        $this->assign('stats', $stats);

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
                         $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
                         LANG::get('credit_evaluation')
                         );

        $this->_config_seo('title', Lang::get('credit_evaluation') . ' - ' . $store['store_name']);
        $this->display('store.credit.html');
    }

    /* 取得友情链接 */
    function _get_partners($id)
    {
        $partner_mod =& m('partner');
        return $partner_mod->find(array(
            'conditions' => "store_id = '$id'",
            'order' => 'sort_order',
                                        ));
    }

    /* 取得推荐商品 */
    function _get_recommended_goods($num = 12)
    {
         //获取默认推荐
         $recom_mod =& m('recommend');
         $goods_list= $recom_mod->get_my_recommended_goods(49 , $num);
            
         if(!empty($goods_list))
         {
             foreach ($goods_list as $kk=>$rgoods)
             {
                 $goods_list[$kk]['default_image'] = change_taobao_imgsize($rgoods['default_image']);
             }
         }
  
        return $goods_list;
    }

    function _get_new_groupbuy($id, $num = 12)
    {
        $model_groupbuy =& m('groupbuy');
        $groupbuy_list = $model_groupbuy->find(array(
            'fields'    => 'goods.default_image, this.group_name, this.group_id, this.spec_price, this.end_time',
            'join'      => 'belong_goods',
            'conditions'=> $model_groupbuy->getRealFields('this.state=' . GROUP_ON . ' AND this.store_id=' . $id . ' AND end_time>'. gmtime()),
            'order'     => 'group_id DESC',
            'limit'     => $num
                                                     ));
        if (empty($groupbuy_list))
        {
            $groupbuy_list = array();
        }
        foreach ($groupbuy_list as $key => $_g)
        {
            empty($groupbuy_list[$key]['default_image']) && $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $tmp = current(unserialize($_g['spec_price']));
            $groupbuy_list[$key]['price'] = $tmp['price'];
            $groupbuy_list[$key]['lefttime'] = lefttime($_g['end_time']);
        }

        return $groupbuy_list;
    }

    /* 取得最新商品 */
    function _get_new_goods($id, $totalnum , $search)
    {
        /*加入分页功能 start*/
        $page = $this->_get_page(60);
        /*end*/
        
        $orders = $this->_get_orders();
        $goods_mod =& bm('goods', array('_store_id' => $id));
        
        
        $params = array(
                'conditions' => "g.closed = 0 AND g.if_show = 1 AND g.default_spec > 0",
                'fields'     => 'g.goods_name, g.default_image, gs.price,',
                'order'      => isset($_GET['order']) && isset($orders[trim(str_replace('asc','',str_replace('desc','',$_GET['order'])))]) ? $_GET['order'] : 'add_time desc',
                //'order'      => 'add_time desc',
                'limit'      => $page['limit'],
           );
        
        if ($search){
            if (isset($search ['cid']) && $search ['cid']) {
                $params['conditions'] .=  " AND g.cate_id=".$search ['cid'];
            }
            
            if (isset($search ['price']) && $search ['price']) {
                $tmp = explode('-', $search ['price']);
                $params['conditions'] .= " AND gs.price >= {$tmp[0]} and gs.price <= {$tmp[1]}";

            }
            
            
            $sql = "select count(*) as t from ".$goods_mod->table .' as ' . $goods_mod->alias .'
                    left join ecm_goods_spec gs ON g.default_spec = gs.spec_id 
                    where store_id='.$id.' and '. $params['conditions'] ;
            
            $totalnum = $goods_mod->getOne($sql);
        }
        
        $page['item_count'] = $totalnum;
        $this->_format_page($page);
        $this->assign('page_info', $page);
        
        $goods_list = $goods_mod->get_list($params);
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
            $goods_list[$key]['default_image'] = change_taobao_imgsize($goods_list[$key]['default_image']);
        }
        //dump(isset($_GET['order']) && isset($orders[trim(str_replace('asc','',str_replace('desc','',$_GET['order'])))]) ? $_GET['order'] : 'sales desc');
        return $goods_list;
    }
    /* 取得热卖商品 */
    function _get_hot_sale_goods($id, $num = 16)
    {
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $goods_list = $goods_mod->find(array(
            'conditions' => "closed = 0 AND if_show = 1",
            'join'           => 'has_goodsstatistics',
            'fields'     => 'goods_name, default_image, price,sales',
            'order'      => 'sales desc,add_time desc',
            'limit'      => $num,
                                             ));
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
        }
        return $goods_list;
    }

    /*上新与下架结果*/
    function _assign_newclose_goods($id,$gids)
    {
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $search_name = LANG::get('new_and_close_static_result');
        $goods_list = $goods_mod->get_list(array(
            'conditions' =>  db_create_in($gids,'g.goods_id'),
            //'order'      => isset($_GET['order']) && isset($orders[trim(str_replace('asc','',str_replace('desc','',$_GET['order'])))]) ? $_GET['order'] : 'sales desc',
            'order' => empty($_GET['order']) || !isset($orders[$_GET['order']]) ? 'add_time desc' : $_GET['order'],
                                                 ), array());
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
        }
        $this->assign('searched_goods', $goods_list);
        $this->assign('search_name', $search_name);
    }

    /* 搜索到的结果 */
    function _assign_searched_goods($id)
    {
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $search_name = LANG::get('all_goods');

        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'goods_name',
                'name'  => 'keyword',
                'equal' => 'like',
                  ),
                                                         ));
        
        if ($conditions)
        {
            $search_name = sprintf(LANG::get('goods_include'), $_GET['keyword']);
            $sgcate_id   = 0;
        }
        else
        {
            $sgcate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
        }

        if ($sgcate_id > 0)
        {
            $gcategory_mod =& bm('gcategory', array('_store_id' => $id));
            $sgcate = $gcategory_mod->get_info($sgcate_id);
            $search_name = $sgcate['cate_name'];

            $sgcate_ids = $gcategory_mod->get_descendant_ids($sgcate_id);
        }
        else
        {
            $sgcate_ids = array();
        }

        /* 排序方式 */
        /*  $orders = array(
            'add_time desc' => LANG::get('add_time_desc'),
            'price asc' => LANG::get('price_asc'),
            'price desc' => LANG::get('price_desc'),
            ); */
        $orders = $this->_get_orders();
        $this->assign('orders', $orders);

        $page = $this->_get_page(20);
        $goods_list = $goods_mod->get_list(array(
            'conditions' => 'closed = 0 AND if_show = 1 AND default_spec > 0 ' . $conditions,
            'count' => true,
            'order'      => isset($_GET['order']) && isset($orders[trim(str_replace('asc','',str_replace('desc','',$_GET['order'])))]) ? $_GET['order'] : 'add_time desc',
            //'order' => empty($_GET['order']) || !isset($orders[$_GET['order']]) ? 'add_time desc' : $_GET['order'],
            'limit' => $page['limit'],
                                                 ), $sgcate_ids);
        //当商品名称里没有货号时，搜索商品属性
        if(preg_match("/[a-z|A-Z|0-9]/",$_GET['keyword']))
        {
            $goods_ids = $goods_mod->find(array(
                'conditions'=>'closed = 0 AND if_show = 1 AND default_spec > 0  AND store_id = '.$id,
                'fields'=>'goods_id'
            ));
            
            $goods_ids = array_keys($goods_ids);
            
            if(!empty($goods_ids))
            {
                $goodsAttrModel = &m('goodsattr');
                $attrs = $goodsAttrModel->find(array(
                    'conditions' => db_create_in($goods_ids,'goods_id')
                ));
               
                $result_goods_ids = array();//存储包含货号的商品
                $pattern = "/".trim($_GET['keyword'])."/";
                
                if($attrs)
                {
                    foreach ($attrs as $attr)
                    {
                        if(preg_match($pattern, $attr['attr_value']))
                        {
                            $result_goods_ids[] = $attr['goods_id'];
                        }
                    }
                }
                
                if($result_goods_ids)
                {
                    $goods_list = $goods_mod->get_list(array(
                        'conditions' => 'closed = 0 AND if_show = 1 AND default_spec > 0 AND '.db_create_in($result_goods_ids,'g.goods_id') ,
                        'count' => true,
                        'order'      => isset($_GET['order']) && isset($orders[trim(str_replace('asc','',str_replace('desc','',$_GET['order'])))]) ? $_GET['order'] : 'add_time desc',
                        //'order' => empty($_GET['order']) || !isset($orders[$_GET['order']]) ? 'add_time desc' : $_GET['order'],
                        'limit' => $page['limit'],
                    ), $sgcate_ids);
                }
               
            }
            
           
        }

        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
            $goods_list[$key]['default_image'] = change_taobao_imgsize($goods['default_image']);
        }
        $this->assign('searched_goods', $goods_list);

        $page['item_count'] = $goods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('search_name', $search_name);
    }

    /**
     * 取得文章信息
     */
    function _get_article($id)
    {
        $article_mod =& m('article');
        return $article_mod->get_info($id);
    }

    function _get_seo_info($data)
    {
        $seo_info = $keywords = array();
        $seo_info['title'] = $data['mk_name'] . ' - '.$data['dangkou_address'] .'_'. $data['store_name'];
        $keywords = array(
            str_replace("\t", ' ', $data['region_name']),
            $data['store_name'],
                          );
        //$seo_info['keywords'] = implode(',', array_merge($keywords, $data['tags']));
        $seo_info['keywords'] = implode(',', $keywords);
        $seo_info['description'] = sub_str(strip_tags($data['description']), 10, true);
        return $seo_info;
    }

    /* 商品排序方式  edit  tyioocom  */
    function _get_orders()
    {
        return array(
            ''             => Lang::get('default_order'),
            'views'        => Lang::get('views'),
            'sales'        => Lang::get('sales_desc'),
            'price'        => Lang::get('price'),
            'add_time'     => Lang::get('add_time'),
            'comments'     => Lang::get('comment'),
            'credit_value' => Lang::get('credit_value'),
                     );
    }

    function permitTaobaoTmc() {
        if ($_GET['id'] != $this->visitor->get('user_id') && $_SESSION['taobao_nick'] != urldecode($_GET['im_ww'])) {
            $this->show_warning('亲，这不是您的店铺，请使用淘登录后再试');
        } else {
            $result = file_get_contents('http://yjsc.51zwd.com/taobao-upload-multi-store/index.php?g=Taobao&m=Api&a=permitTaobaoTmc&session_key='.$_SESSION['taobao_access_token']);
            if (strpos($result, 'true') !== false) {
                $store_mod =& m('store');
                $store_mod->edit($_GET['id'], array(
                    'auto_sync' => 1));
                file_get_contents('http://121.41.170.236:30005/stores/'.$_GET['id'].'?sync');
                $this->show_message('开启成功');
            } else {
                $resp = json_decode($result);
                $msg = '';
                if (isset($resp->sub_code)) {
                    $msg = $resp->sub_code;
                }
                $this->show_warning('开启失败'.$msg);
            }
        }
    }

    /**
     * 店铺顶部两个广告
     */
    function _get_store_advs()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); //载入系统设置数据

        $this->assign("store_advs",array(
                array(
                    'image'=>$setting['store_adv_image1'],
                    'img_url'=>$setting['store_adv_image1_href']
        ),
                array(
                    'image'=>$setting['store_adv_image2'],
                    'img_url'=>$setting['store_adv_image2_href']
        )
        ));
    }

    function _get_favorite($store_id)
    {
        $favorite = false;
        if($this->visitor->get('user_id'))
        {
            $mod_store = & m('store');
            $collect_stores = $mod_store->find(array(
                'conditions'=>'collect.user_id = ' . $this->visitor->get('user_id')." AND store_id=".$store_id,
                'join'=>'be_collect',
            ));
            $favorite = count($collect_stores) > 0 ?true:false;
        }

        return $favorite;
    }

    function update_status() {
        if (!IS_POST) {
            $this->display('update.status.html');
        } else {
            $mod_store =& m('store');
            echo ecm_json_encode($mod_store->update_status());
        }
    }
    
    
    /**
     *
     */
   function _get_store_count_by_cate($stor_id, $conditions = ''){
        $mod_goods =& m('goods');
        $gc = & m('gcategory');
       
        $sql = "select cate_id , cate_id_1 , count(goods_id) as t from ".$mod_goods->table .' as '.$mod_goods->alias;
        $sql .= ' WHERE store_id = '. $stor_id ;
        if ($conditions) {
            $sql .= ' AND '.$conditions;
        }
        
        $sql .= ' group by cate_id';
        
        $result = $mod_goods->getAll($sql);
        $ret = array();
        
        $cids = array();
        foreach ($result as $v){
            $ret [$v ['cate_id_1']] ['cates'][] =  $v;
            $cids[] =  $v['cate_id'];
        }
        
        $sql = array(
            'conditions'=> db_create_in(array_merge($cids , array_keys($ret)), 'cate_id'),
            'fields' => 'cate_name'
        );
        $cate_ids = $gc->find($sql);
        
        foreach ($ret as $key => &$row) {
            $row ['cate_name'] = $cate_ids [$key] ['cate_name'];
            
            foreach ($row['cates'] as &$cate) {
                $cate['cate_name'] = $cate_ids [$cate['cate_id']]['cate_name'];
            }
        }        
        return $ret;
    }
    
    
}

?>