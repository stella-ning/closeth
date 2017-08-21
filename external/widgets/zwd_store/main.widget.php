<?php

/**
 * 店铺图片广告挂件
 *
 * @param   string  $image_url  图片地址
 * @param   string  $link_url   链接地址
 */
class Zwd_storeWidget extends BaseWidget
{
    var $_name = 'zwd_store';

    function _get_data()
    {

        $cache_server = & cache_server();
        $isfirst = false;
        $indexkey = 'store_lists';
        $data = $cache_server->get($indexkey);
        if (!$data || empty($data)) {
            $isfirst = true;
        }
        if ($isfirst || count($_GET) > 2) {


            /* 取得该分类及子分类cate_id */
            $cate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
            $cate_ids = array();
            $condition_id = '';
            if ($cate_id > 0) {
                $scategory_mod = & m('scategory');
                $cate_ids = $scategory_mod->get_descendant($cate_id);
            }

            /* 店铺分类检索条件 */
            $condition_id = implode(',', $cate_ids);
            $condition_id && $condition_id = ' AND cate_id IN(' . $condition_id . ')';

            //mkid  如果有商场id,得出它的层级
            $market_param = array();
            //取得所有市场
            $market_mod = & m('market');
            $store_markets = $market_mod->get_list(1);
            $condition_market = '';
            $condition_service = '';
            $default_enter_store = false;
            if (isset($_GET['mkid']) && intval($_GET['mkid']) > 0) {
                $market_param['mkid'] = $mkid = intval($_GET['mkid']);
            } elseif (count($_GET) == 2) {
                //默认第一个市场
                //is_array($store_markets) && $market_param['mkid'] =$mkid = key($store_markets);
                $default_enter_store = true;
            }
            $market_param['mlayer'] = $market_mod->get_layer($mkid, true);
            //如果不是市场，则剔除
            if ($market_param['mlayer'] != 2) {
                unset($market_param['mkid']);
            }

            if (isset($_GET['fid']) && intval($_GET['fid']) > 0) {
                $market_param['fid'] = $fid = intval($_GET['fid']);
                $market_param['flayer'] = $market_mod->get_layer($fid, true);
                //如果不是楼层，则剔除
                if ($market_param['flayer'] != 3) {
                    unset($market_param['fid']);
                }
            }
            if (isset($_GET['service_detail']) && intval($_GET['service_detail']) == 1) {
                $market_param['service_detail'] = intval($_GET['service_detail']);
            }
            if (isset($_GET['service_send']) && intval($_GET['service_send']) == 1) {
                $market_param['service_send'] = intval($_GET['service_send']);
            }
            if (isset($_GET['service_cash']) && intval($_GET['service_cash']) == 1) {
                $market_param['service_cash'] = intval($_GET['service_cash']);
            }
            if (isset($_GET['service_pattern']) && intval($_GET['service_pattern']) == 1) {
                $market_param['service_pattern'] = intval($_GET['service_pattern']);
            }
            /* 查询市场，非楼层 */
            if (isset($market_param['mkid']) && (intval($market_param['mlayer']) == 2)) {
                $store_market_floors = $market_mod->get_list($market_param['mkid']);
            }
            if (OEM == 'nc') {
                $f_markets = $market_mod->get_sm_list(1);
                if (count($f_markets) != 1) {
                    $this->show_warning('no_single');
                }
                $f_markets = array_values($f_markets);
                $store_market_floors = $market_mod->get_list($f_markets[0]['mk_id']);
            }

            if (isset($market_param['service_send'])) {
                $condition_service .= " AND serv_sendgoods = 1"; //代发
            }
            if (isset($market_param['service_cash'])) {
                $condition_service .= " AND serv_refund = 1";  //退现
            }
            if (isset($market_param['service_pattern'])) {
                $condition_service .= " AND serv_exchgoods = 1"; //换款
            }

            if ($market_param['mkid'] > 1) {
                $mids = $market_mod->get_list($market_param['mkid']);
                $mids_arr = array($market_param['mkid']);
                foreach ($mids as $v) {
                    $mids_arr[] = $v['mk_id'];
                }
                $condition_market = " AND mk_id " . db_create_in($mids_arr);
            }
            if (($market_param['mkid'] > 1) && ($market_param['fid'] > 1)) {
                $condition_market = " AND mk_id = " . $market_param['fid'];
            }
            if (OEM == 'nc' && ($market_param['fid'] > 1)) {
                $condition_market = " AND mk_id = " . $market_param['fid'];
            }
			
            /* 其他检索条件 */
           /*  $conditions = $this->_get_query_conditions(array(

                array(//地区名称
                    'field' => 'region_name',
                    'equal' => 'LIKE',
                    'assoc' => 'AND',
                    'name' => 'region_name',
                    'type' => 'string',
                ),
                array(//地区id
                    'field' => 'region_id',
                    'equal' => '=',
                    'assoc' => 'AND',
                    'name' => 'region_id',
                    'type' => 'string',
                ),
                array(//店铺等级id
                    'field' => 'sgrade',
                    'equal' => '=',
                    'assoc' => 'AND',
                    'name' => 'sgrade',
                    'type' => 'string',
                ),
                array(//是否推荐
                    'field' => 'recommended',
                    'equal' => '=',
                    'assoc' => 'AND',
                    'name' => 'recommended',
                    'type' => 'string',
                ),
                array(//好评率
                    'field' => 'praise_rate',
                    'equal' => '>',
                    'assoc' => 'AND',
                    'name' => 'praise_rate',
                    'type' => 'string',
                ),
                array(//商家用户名
                    'field' => 'user_name',
                    'equal' => 'LIKE',
                    'assoc' => 'AND',
                    'name' => 'user_name',
                    'type' => 'string',
                ),
                    )); */
            
            if($_GET['keyword'])
            {
                $conditions .= " AND ( store_name like '%".trim($_GET['keyword'])."%' OR dangkou_address like '%".trim($_GET['keyword'])."%' OR address like '%".trim($_GET['keyword'])."%' )";
            }

            //  tyioocom  safe care
            $orders = array(
                'sales desc',
                'sales asc',
                'price desc',
                'price asc',
                'add_time desc',
                'add_time asc',
                'comments desc',
                'comments asc',
                'credit_value desc',
                'credit_value asc',
                'views desc',
                'views asc',
            );
            $step = intval(Conf::get('upgrade_required'));
            //echo "step:".$step;
            $step < 1 && $step = 5;
            $level_1 = $step * 5;
            $level_2 = $level_1 * 6;
            $level_3 = $level_2 * 6;
            if ($_GET['credit_value']) {
                switch (intval($_GET['credit_value'])) {
                    case 1;
                        $credit_condition = ' AND credit_value<' . $level_1 . ' ';
                        break;
                    case 2;
                        $credit_condition = ' AND credit_value<' . $level_2 . ' AND credit_value>=' . $level_1 . ' ';
                        break;
                    case 3;
                        $credit_condition = ' AND credit_value<' . $level_3 . ' AND credit_value>=' . $level_2 . ' ';
                        break;
                    case 4;
                        $credit_condition = ' AND credit_value>=' . $level_3 . ' ';
                        break;
                }
            }
            $model_store = & m('store');
           
            if (!$default_enter_store) {  //不是默认进入批发市场
                $stores = $model_store->find(array(
                    'conditions' => 'state = ' . STORE_OPEN . $credit_condition . $condition_id . $condition_service . $condition_market .$conditions,
                    //'limit'   =>$page['limit'],
                    //'fields'  =>'store_name,user_name,sgrade,store_logo,recommended,praise_rate,credit_value,s.im_qq,im_ww,business_scope,region_name,serv_sendgoods,serv_refund,serv_exchgoods,serv_golden,dangkou_address,mk_name,shop_http,see_price',
                    'fields' => 'store_name,dangkou_address,mk_id,mk_name,credit_value,recommended,serv_refund',
                    'order' => empty($_GET['order']) || !in_array($_GET['order'], $orders) ? 'dangkou_address' : $_GET['order'], //  tyioocom $orders
                    'join' => 'has_scategory', //belongs_to_user,
                        //'count'   => true   //允许统计
                        ));
            } else {   //默认进入批发市场，且没有点击市场
                $stores = $model_store->find(array(
                    'conditions' => 'state = ' . STORE_OPEN . $credit_condition . $condition_id . $condition_service . $condition_market . $conditions,
                    'limit' => "0,200",
                    //'fields'  =>'store_name,user_name,sgrade,store_logo,recommended,praise_rate,credit_value,s.im_qq,im_ww,business_scope,region_name,serv_sendgoods,serv_refund,serv_exchgoods,serv_golden,dangkou_address,mk_name,shop_http,see_price',
                    'fields' => 'store_name,dangkou_address,mk_id,mk_name,credit_value,recommended,serv_refund',
                    'order' => empty($_GET['order']) || !in_array($_GET['order'], $orders) ? 'credit_value DESC' : $_GET['order'], // 
                    //'join' => 'has_scategory', //belongs_to_user,
                        //'count'   => true   //允许统计
                        ));
            }
            //dump($stores);
            $model_goods = &m('goods');
            //$order_mod=&m('order');
            $sgrade_mod = &m('sgrade');

            foreach ($stores as $key => $store) {               
          
                $stores[$key]['credit_image'] = $this->_view->res_base . '/images/' . $model_store->compute_credit($store['credit_value'], $step);
				
            }

            /* 有实拍的店铺过滤 */
            if (isset($market_param['service_detail'])) {
                foreach ($stores as $key => $store) {
                    if (!isset($store['realpic'])) {
                        unset($stores[$key]);
                    }
                }
            }
            //展示给前台
            $storelist = array();
            //如果是有楼层
            $floorlist = array();
            if ($store_market_floors && $stores) {//进入了某个市场
                foreach ($store_market_floors as $mkey => $mvalue) {
                    $storelist[$mkey]['mkid'] = $mvalue['mk_id'];
                    $storelist[$mkey]['mk_name'] = $mvalue['mk_name'];
                    $floorlist[] = $mkey;
                }
                foreach ($stores as $skey => $svalue) {
                    if (in_array($svalue['mk_id'], $floorlist)) {
                        $storelist[$svalue['mk_id']]['stores'][] = $svalue;
                    }
                }
            } elseif (isset($market_param['fid'])) {
                $storelist = array();
                $queryfloor = $market_mod->get($market_param['fid']);
                $storelist[$queryfloor['mk_id']]['mk_id'] = $queryfloor['mk_id'];
               // $storelist[$queryfloor['mk_id']]['mk_name'] = $queryfloor['mk_name'];
                $storelist[$queryfloor['mk_id']]['stores'] = $stores;
            } else {
                $storelist[0]['mk_id'] = 1;
               // $storelist[0]['mk_name'] = $default_enter_store ? Lang::get('site51_200st_shops') : '';//龙虎榜
                $storelist[0]['stores'] = $stores;
            }
            
            if (count($_GET) == 2) {
                $cache_server->set($indexkey, $storelist, 7200);
                $cache_server->set($indexkey . "_floor", $store_market_floors, 7200);
                $cache_server->set($indexkey . "_market", $store_markets, 7200);
            }
        } else {
            $storelist = $data;
            $store_markets = $cache_server->get($indexkey . "_market");
            $store_market_floors = $cache_server->get($indexkey . "_floor");
        }
		$url = '?'.$_SERVER['QUERY_STRING'];
		
		$this->assign('tencent_maps', ecm_json_encode(Lang::get('tencent_maps')));
        $this->assign('markets', $store_markets);
        $this->assign('floors', $store_market_floors);
		$this->assign('url', $url);
        $this->assign('stores', $storelist);
       
        $this->assign('scategorys', $scategorys);
    }

    function parse_config($input)
    {

        return $input;
    }

    
  
}

?>