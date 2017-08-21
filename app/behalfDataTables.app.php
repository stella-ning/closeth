<?php

/**
 *    为代发模块dataTables提供数据
 *
 *    @author    tanaiquan
 */
class BehalfDataTablesApp extends MallbaseApp {

    /**
     * 通过档口名称获取档口信息
     */
    function get_store_bystorename()
    {
        $store_name = $_GET['sname']?trim($_GET['sname']):'';
    
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
    
        $mod_store = & m('store');
        $stores = $mod_store->find(array(
            'conditions' => 'state = ' . STORE_OPEN ." AND store_name like '%".$store_name."%'",
            'limit' => "{$start},{$page_per}",
            'count'=>true,
            //'fields'  =>'store_name,user_name,sgrade,store_logo,recommended,praise_rate,credit_value,s.im_qq,im_ww,business_scope,region_name,serv_sendgoods,serv_refund,serv_exchgoods,serv_golden,dangkou_address,mk_name,shop_http,see_price',
        'order' => 'sort_order asc'
            ));
    
        $total_length = $mod_store->getCount();
    
        echo ecm_json_encode(array(
            "draw" => intval($_GET['draw']),
            "recordsTotal" => $total_length,
            "recordsFiltered" => $total_length,
            "data" => array_values($stores)
        ));
        //$this->json_result(1,$stores);
    }
    
    /**
     * 代发 档口黑名单
     */
    function get_store_blacklist()
    {
        $mod_behalf = & m('behalf');
        
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        
        $bh_id = $this->visitor->get('has_behalf');
        
        $black_list = $mod_behalf->getRelatedData("has_blacklist_stores",$bh_id,array(
            'limit' => "{$start},{$page_per}",
            'count'=>true
        ));
        
        $total_length = count($mod_behalf->getRelatedData("has_blacklist_stores",$bh_id,array(
            'field'=>'store_id'
        )));
        
        echo ecm_json_encode(array(
            "draw" => intval($_GET['draw']),
            "recordsTotal" => $total_length,
            "recordsFiltered" => $total_length,
            "data" => array_values($black_list)
        ));
    }
    /**
     * 代发 vip 列表
     */
    function get_vip_list()
    {
        $mod_membervip =& m('membervip');
        
            $start = intval($_GET['start']);
            $page_per = intval($_GET['length']);
            
            $bh_id = $this->visitor->get('has_behalf');
            
            $vip_list = $mod_membervip->find(array(
               'conditions'=>"level > 0 AND bh_id={$bh_id}", 
                'join'=>'belongs_to_user',
                'limit' => "{$start},{$page_per}",
                'order'=>'orders DESC',
                'count'=>true
            ));
            
            $total_length = count($mod_membervip->find(array(
               'conditions'=>"level > 0",
                'field'=>'user_id'
            )));
            
            echo ecm_json_encode(array(
                "draw" => intval($_GET['draw']),
                "recordsTotal" => $total_length,
                "recordsFiltered" => $total_length,
                "data" => array_values($vip_list)
            ));
    }
    
    
    /**
     * 商品仓库货物列表,dataTables,pipe-ajax
     */
    function get_pipe_goods_taker_inventory()
    {
        $goods_taker_inventory_mod =& m('goodstakerinventory');
        $goods_warehouse_mod =& m('goodswarehouse');
        $login_id = $this->visitor->get('user_id');
         
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
         
        //拼接排序sql
        $orderSql = "";
        /* if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                case 1:$orderSql = " goods_no ".$order_dir;break;
                case 3:$orderSql = " goods_name ".$order_dir;break;
                case 4:$orderSql = " goods_attr_value ".$order_dir;break;
                case 5:$orderSql = " goods_specification ".$order_dir;break;
                case 6:$orderSql = " goods_price ".$order_dir;break;
                case 7:$orderSql = " store_bargin ".$order_dir;break;
                default:$orderSql = ' taker_time DESC';
            }
        } */
         
        $recordsTotal = 0;
        $recordsFiltered = 0;
        //$goods_list = array();
        
        
        if($this->visitor->get('pass_behalf'))
        {
            $condition =" bh_id = {$login_id} ";
        }
        else
        {
            $condition =" taker_id = {$login_id} ";
        }
        
        $nhd_list = $goods_taker_inventory_mod->find(array(
            'conditions'=>' visible = 1 AND '.$condition,
            'count'=>true,
            'order'=>"createtime DESC",
            'limit'=>"{$start},{$page_per}"
        ));
        
        if($nhd_list)
        {
            foreach ($nhd_list as $key=>$nhd)
            {
                $nhd_list[$key]['createtime'] = local_date("Y-m-d H:i:s",$nhd['createtime']);
                $goods_ids = explode(',', $nhd['content']);
                $goods_list = $goods_warehouse_mod->find(array(
                    'conditions'=>db_create_in($goods_ids,'id')
                ));
                $goods_details = array(
                    'ready'=>array(
                        'count'=>0, //已备货数量
                        'amount'=>0, //已备货金额
                        'discount'=>0 //已备货档口优惠
                    ),
                    'lack'=>array(
                        'count'=>0,//缺货数量
                        'amount'=>0,
                        'discount'=>0
                    ),
                    'outhouse'=>array(
                        'count'=>0,//未入库数量
                        'amount'=>0,
                        'discount'=>0
                    ),
                    'reback'=>array(
                        'count'=>0,//已退货数量
                        'amount'=>0,
                        'discount'=>0
                    )
                );
                 
                if($goods_list)
                {
                    foreach ($goods_list as $gkey=>$goods)
                    {
                        if(in_array($goods['goods_status'],array(BEHALF_GOODS_DELIVERIES)))
                        {
                            $goods_details['outhouse']['count']++;
                            $goods_details['outhouse']['amount'] += floatval($goods['goods_price']);
                            $goods_details['outhouse']['discount'] += floatval($goods['store_bargin']);
                        }
                        elseif(in_array($goods['goods_status'],array(BEHALF_GOODS_REBACK)))
                        {
                            $goods_details['reback']['count']++;
                            $goods_details['reback']['amount'] += floatval($goods['goods_price']);
                            $goods_details['reback']['discount'] += floatval($goods['store_bargin']);
                        }
                        elseif(in_array($goods['goods_status'], array(BEHALF_GOODS_READY,BEHALF_GOODS_SEND)))
                        {
                            $goods_details['ready']['count']++;
                            $goods_details['ready']['amount'] += floatval($goods['goods_price']);
                            $goods_details['ready']['discount'] += floatval($goods['store_bargin']);
                        }
                        elseif(in_array($goods['goods_status'], array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE)))
                        {
                            $goods_details['lack']['count']++;
                            $goods_details['lack']['amount'] += floatval($goods['goods_price']);
                            $goods_details['lack']['discount'] += floatval($goods['store_bargin']);
                        }
                    }
                }
        
                $nhd_list[$key]['goods_details'] = $goods_details;
            }
        }
         
       
        $recordsTotal = $recordsFiltered = $goods_taker_inventory_mod->getCount();   
         
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($nhd_list)));
    }
    
    
}

?>
