<?php

/**
 * Tester
 * @author Administrator
 *
 */
class AtestApp extends MallbaseApp {

    function index() {
      echo "start...<br>";
      echo strtotime("2017-07-20"); //15004800003
      
      $mod_market = & m('market');
      $result =  $mod_market->get_layer(40);
        
      echo $result;
        
       /*  $ch = curl_init();
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=140.224.191.142';
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);
        //\u6f33\u5dde
        $res = ecm_json_decode($res);
        //dump($res);
        echo preg_match('/漳州/',$res->city); */
    }
    
    function update_wh_mk_id(){
        $mod_warehouse = & m('goodswarehouse');
        $goods_list = $mod_warehouse->find(array(
            'conditions'=>"order_add_time >= 1500480000"
        ));
        
        if($goods_list){
            $mod_market = & m('market');
            foreach ($goods_list as $goods){
                if($goods['floor_id'] == $goods['market_id'] ){
                    $result =  $mod_market->get("mk_id = {$goods['floor_id']}");
                    $mod_warehouse->edit("id={$goods['id']}",array('market_id'=>$result['parent_id']));
                }
            }
        }
        
        
    }

    function show_time() {
        $today_now = gmtime();
        $today_start = mktime(0, 0, 0, date('m', $today_now), date('d', $today_now), date('Y', $today_now));
        echo "gmtoday_start=" . $today_start . "=" . date('Y-m-d H:i:s', $today_start) . "<br>";
        echo "gmnow=" . $today_now . "=" . date('Y-m-d H:i:s', $today_now) . "<br><br>";
        
        $today_now = time();
        $today_start = mktime(0, 0, 0, date('m', $today_now), date('d', $today_now), date('Y', $today_now));
        echo "today_start_local=" . $today_start . "=" . date('Y-m-d H:i:s', $today_start) . "<br>";
        echo "now_local=" . $today_now . "=" . date('Y-m-d H:i:s', $today_now) . "<br>";
        
        echo server_timezone() . "<br>";
        echo date('Z') . "<br>" . "<br>";
        
        $result = cal_time_diff(1);
        echo date("Y-m-d H:i:s", $result['start_time']) . "<br>";
        echo date("Y-m-d H:i:s", $result['end_time']) . "<br>";
        // $today_start = gmstr2time(date('Y-m-d',$today_start));
        
        /*
         * $goods_image = 'http://img.alicdn.com/bao/uploaded/i3/791596228/TB2tM9SrVXXXXcMXXXXXXXXXXXX_!!791596228.jpg_180x180.jpg';
         *
         * $result = change_taobao_imgsize($goods_image);
         *
         * dump($result);
         */
    }

    function export_fxs() {
        $mod_gh = & m('goodswarehouse');
        $result = $mod_gh->find(array(
            'conditions' => "goods_name like '%牛仔%'",
            'fields' => 'order_id'
        ));
        
        if (empty($result)) {
            echo 'no goods_name';
            return false;
        }
        
        $order_ids = array();
        foreach ($result as $r)
            $order_ids[] = $r['order_id'];
            // print_r($order_ids);
        $mod_order = & m('order');
        $buyers = $mod_order->getCol("select distinct buyer_id from " . $mod_order->table . " where " . db_create_in($order_ids, 'order_id'));
        
        // print_r($buyers);
        $mod_member = & m('member');
        $qqs = $mod_member->getCol("select distinct im_qq from " . $mod_member->table . " where " . db_create_in($buyers, 'user_id'));
        
        echo join(';', $qqs);
        // dump($qqs);
    }
}

?>