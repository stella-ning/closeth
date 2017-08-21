<?php
/**
 * 
 * 通用的自定义函数库，今后不要写在global.lib.php ,这样便于升级维护
 * 作用同global.lib.php
 * 在global.lib.php  include_once();
 * 
 * author:tanaiquan
 * date:2015-11-23
 */


//PHP stdClass Object转array
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}


/**
 * 快递单号 是否存在
 * @param unknown $invoice_no
 */
function exist_invoiceno($invoice_no)
{
    //利用php文件锁解决并发问题
    $lock_file = ROOT_PATH."/data/exist_invoiceno.lock";
    if(!file_exists($lock_file))
    {
        file_put_contents($lock_file, 1);
    }
    
    //对文件加锁
    $fp = fopen($lock_file, 'a+');
    if(!$fp)
    {
        echo 'fail to open lock file!';
        return;
    }
    flock($fp, LOCK_EX);
    /* 主体程序 */
    $is_exist = false;
    $model_order = & m('order');
    $model_orderrefund = & m('orderrefund');
    
    $model_order->getOne("SELECT count(order_id) as c FROM ".$model_order->table." WHERE invoice_no='".trim($invoice_no)."'") > 0 && $is_exist = true;
    $model_orderrefund->getOne("SELECT count(order_id) as c FROM ".$model_orderrefund->table." WHERE invoice_no='".trim($invoice_no)."' AND status <> 2 ") > 0 && $is_exist = true;
    
    /*文件解锁*/
    flock($fp, LOCK_UN);
    fclose($fp);
    
    return $is_exist;
}
/**
 * 店铺是否属于代发区、实拍区和精品区
 * return false,order.app.php and taobao_order.app.php 会收取代发费
 * @param 店铺 $store_id
 */
function belong_behalfarea($store_id)
{
    /*
    $cache_server = & cache_server();
    $indexkey = 'store_belong_behalfarea_realityzone_brandarea_';
    $data = $cache_server->get($indexkey);
    if (!$data || empty($data)) {
        $mod_behalfarea = & m('storebehalfarea');
        $b_stores = $mod_behalfarea->getCol("SELECT store_id FROM ".$mod_behalfarea->table." WHERE state='1'");
        
        $mod_realityzone = & m('storerealityzone');
        $r_stores = $mod_realityzone->getCol("SELECT store_id FROM ".$mod_realityzone->table." WHERE state='1'");
        
        $mod_brandarea = & m('storebrandarea');
        $d_stores = $mod_brandarea->getCol("SELECT store_id FROM ".$mod_brandarea->table." WHERE state='1'");
        
        $stores = array_merge($b_stores,$r_stores,$d_stores);
        $stores = array_unique($stores);
        
        
        $cache_server->set($indexkey, $stores, 7200);
    }
    else 
    {
        $stores = $data;
    }
    
    
    if(empty($stores))
    {
        return false;
    }
    
    return in_array($store_id, $stores);
    */
    return false; //所有商品都收取代发费
}

/**每年开年都会面临档口调整，代发无法拿货的问题。
 * 故每年年初都会先整理部分档口，先将代发区档口全删除，
 * 整理一些后加入代发区，让客户只代发这个区的商品。
 * 等档口地址整理好后，可开放所有档口。
 * 允许 代发区 ，精品区，DH精选区，实拍区，全部，在后台配置
 */
function allow_behalf_open($store_id){
    $behalf_open = Conf::get('behalf_open');
    if(!is_array($behalf_open)) return false;
    if(in_array('all', $behalf_open)) return true;
    
    $cache_server = & cache_server();
    $indexkey = 'store_belong_open_';
    $data = $cache_server->get($indexkey);
    if (!$data || empty($data)) {
        $b_stores = $c_stores =$r_stores = $d_stores = array();
        
        if(in_array('bba', $behalf_open)){
            $mod_behalfarea = & m('storebehalfarea');
            $b_stores = $mod_behalfarea->getCol("SELECT store_id FROM ".$mod_behalfarea->table." WHERE state='1'");
        }
        if(in_array('brz', $behalf_open)){
            $mod_realityzone = & m('storerealityzone');
            $r_stores = $mod_realityzone->getCol("SELECT store_id FROM ".$mod_realityzone->table." WHERE state='1'");
        }
        if(in_array('bbr', $behalf_open)){
            $mod_brandarea = & m('storebrandarea');
            $d_stores = $mod_brandarea->getCol("SELECT store_id FROM ".$mod_brandarea->table." WHERE state='1'");
        }
        if(in_array('bbc', $behalf_open)){
             $mod_behalfchoice = & m('storebehalfchoice');
             $c_stores = $mod_behalfchoice->getCol("SELECT store_id FROM ".$mod_behalfchoice->table." WHERE state='1'");
        }
        $stores = array_merge($b_stores,$r_stores,$d_stores,$c_stores);
        $stores = array_unique($stores);
    
    
        $cache_server->set($indexkey, $stores, 7200);
    }
    else
    {
        $stores = $data;
    }
    
    
    if(empty($stores))
    {
        return false;
    }
    
    return in_array($store_id, $stores);
}

/**
 * 判断商品是否位于某个时间之后的拿货单中
 * @param $inp_time 10位整数
 */
function after_goods_taker_inventory($inp_time,$goods_warehouse_ids=array())
{
    $model_git = & m('goodstakerinventory');
    $gits = $model_git->find(array('conditions'=>"createtime >= {$inp_time}"));
    if(empty($gits)) return false;
    $query_goods_ids=array();
    foreach ($gits as $g)
    {
        $query_goods_ids = array_merge($query_goods_ids,explode(',', $g['content']));
    }
    if(empty($query_goods_ids)) return false;
    
    foreach ($goods_warehouse_ids as $gid)
    {
        if(in_array($gid, $query_goods_ids))
            return true;
    }
    return false;
}

/**
 * 获取商品退货率 缺货率
 * @param  $goods_id
 */
function get_goods_rates_in_common($goods_id)
{
    $bm_goods = & bm('goods');
    return $bm_goods->get_goods_rates($goods_id);
}

/**
 * 店铺是否在精品区
 * @param $store_id
 */
function exist_brandarea($store_id)
{
    $mod_sba =& m('storebrandarea');
    if($store_id <= 0 || !is_numeric($store_id)){ return false; }
    $store = $mod_sba->get(array('conditions'=>"store_id = '{$store_id}' AND state = 1"));
    if($store){ return true; }else{ return  false;}
    
}
/**
 * php文件处理并发
 * @param 文件名 $filename
 */
function zwd51_handle_concurrence_with_file_open($filename)
{
    $lock_file = ROOT_PATH."/data/".$filename.".lock";
    if(!file_exists($lock_file))
    {
        file_put_contents($lock_file, 1);
    }     
    //对文件加锁
    $fp = fopen($lock_file, 'a+');
    
    return $fp;   
}

function zwd51_handle_concurrence_with_file_close($fp)
{
    /*文件解锁*/
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 改变淘宝宝贝图片尺寸
 */
function change_taobao_imgsize($goods_image)
{
    if(preg_match('/\d{3}x\d{3}\.jpg$/', $goods_image))
    {
        //return str_replace('180x180.jpg', '240x240.jpg', $goods_image);
        return preg_replace('/\d{3}x\d{3}\.jpg$/','240x240.jpg', $goods_image);
    }
    if(!preg_match('/\d{3}x\d{3}\.jpg$/', $goods_image))
    {
        return $goods_image.'_240x240.jpg';
    }
    
    return $goods_image;
}
/**
 * 计算某天时间段值
 * 用于 数据库记录gmtime 与 本地时区
 * @param 天数 $days  默认1 = 昨天
 */
function cal_time_diff($days = 1)
{
    $now = time();
    $today_start = mktime(0,0,0,date('m',$now),date('d',$now),date('Y',$now));
    
    if($days <= 0)
    {
        //今天
        $start_time = $today_start - date('Z');
        $end_time = $now - date('Z');
    }
    else
    {
        $start_time = $today_start - $days*24*60*60 - date('Z');
        $end_time = $start_time + 24*60*60 -1;
    }
    
    return array(
        'start_time' => $start_time,
        'end_time'=> $end_time
    );
}

/**
 * 检查是否为一个合法的时间格式
 *
 * @param   string  $date
 * @return  void
 */
function is_date($date)
{
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}/';

    return preg_match($pattern, $date);
}
 
/**
 * 更新用户VIP等级及订单数
 * 还应考虑 确认收货后 再退货，应该再减去(未完成)
 */
function update_membervip_orders($order)
{
    //不是代发订单
    if(!$order['bh_id']) return ;
    
    $mod_membervip = & m('membervip');
    $mod_behalf = & m('behalf');
    
    $behalf_info = $mod_behalf->get($order['bh_id']);
    //代发没有开启vip优惠或开启了没有配置优惠
    if(!$behalf_info['vip_clients_discount'] || empty($behalf_info['vip_clients_conf']))  return;
        
    $vip1_orders = 0;
    $vip2_orders = 0;
    
    $confs = explode('|', $behalf_info['vip_clients_conf']);
    foreach ($confs as $conf)
    {
        $tmp_conf = explode(":", $conf);
        if($tmp_conf[0] == 'vip1') $vip1_orders = $tmp_conf[2];
        if($tmp_conf[0] == 'vip2') $vip2_orders = $tmp_conf[2];
    }
    
    $member_info = $mod_membervip->get($order['buyer_id']);
    if($member_info)
    {
        $level = 0;
        if($member_info['orders'] + 1 >= $vip1_orders && $member_info['orders'] + 1 < $vip2_orders)
            $level = 1;
        elseif($member_info['orders'] + 1 >= $vip2_orders)
            $level = 2;
        if($member_info['vip_reason'] == 'auto')
        {
            $mod_membervip->edit("user_id = {$order['buyer_id']} AND bh_id={$order['bh_id']}",array('level'=>$level,'orders'=>$member_info['orders'] + 1));
        }
        else 
        {
            $mod_membervip->edit("user_id = {$order['buyer_id']} AND bh_id={$order['bh_id']}",array('orders'=>$member_info['orders'] + 1));
        }
    }
    else
    {
        $mod_membervip->add(array(
            'user_id'=>$order['buyer_id'],
            'bh_id'=>$order['bh_id'],
            'orders'=>1
        ));
    }
}
/**
 * 计算用户是否vip并且快递费优惠多少
 * @param number $buyer_id 用户ID
 * @param number $bh_id 代发ID
 * @return number 优惠
 */
function caculate_vip_shipping_fee_bargin($buyer_id,$bh_id){
        $result =  0 ;//运费优惠每单
        // 检测代发是否开启优惠
        $mod_behalf = & m('behalf');
        $behalf_info = $mod_behalf->get($bh_id);
        if (empty($behalf_info['vip_clients_discount'])){
            return 0;
        }
        
        // 检测买家是否是vip level > 0
        $mod_membervip = & m('membervip');
        $membervip_info = $mod_membervip->get($buyer_id);
        if (empty($membervip_info['level'])){
            return 0;
        }
        
         // 没有设置优惠值
        if (empty($behalf_info['vip_clients_conf'])){
                return 0;
        }   
        
        // 计算出优惠
        $confs = explode('|', $behalf_info['vip_clients_conf']);
                
        //运费优惠
        $discount_arr = array();
                
        foreach ($confs as $conf) {
            $tmp_conf = explode(":", $conf);
            $discount_arr[$tmp_conf[0]] = $tmp_conf[1];
        }
         
        if(!empty($discount_arr['vip' . $membervip_info['level']])){
            $result = $discount_arr['vip' . $membervip_info['level']];
        }
                
       return $result;
}

/**
 * 	删除前后空格
 */
function trimallspace($str)
{
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    $str = str_replace($qian,$hou,$str);
    return strval(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($str)));
}
/**
 * 获取每月第一天和最后一天(2016-12-01 00:00:00 - 2016-12-31 23:59:59)
 * @param $date (Y-m-d)
 */
function getthemonth($date)
{    
    $firstday = date('Y-m-01', strtotime($date));
    $firsttime = strtotime($firstday);
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    $lasttime = strtotime($lastday)+24*60*60 -1;
    return array($firsttime,$lasttime);
}

/*根据淘宝价计算出现有价格*/
function make_price_by_taobaoprice($price, $seePrice, $title = null)
{
    $finalPrice = $rawPrice = floatval ( $price );
    if (strpos ( $seePrice, '减半' ) !== false)
    {
        $finalPrice = round($rawPrice / 2,2);
    }
    else if (strpos ( $seePrice, 'P' ) !== false || $seePrice == '减P' || $seePrice == '减p')
    {
        $regexP = '/[Pp](\d+)/';
        $regexF = '/[Ff](\d+)/';
        if (preg_match ( $regexP, $title, $matches ) == 1)
        {
            $finalPrice = floatval ( $matches [1] );
        }
        else if (preg_match ( $regexF, $title, $matches ) == 1)
        {
            $finalPrice = floatval ( $matches [1] );
        }
    }
    else if (strpos ( $seePrice, '减' ) === 0)
    {
        $finalPrice = $rawPrice - floatval ( mb_substr ( $seePrice, 1, mb_strlen ( $seePrice, 'utf-8' ) - 1, 'utf-8' ) );
    }
    else if (strpos ( $seePrice, '实价' ) !== false)
    {
        $finalPrice = $rawPrice;
    }
    else if (strpos ( $seePrice, '*' ) === 0)
    {
        $finalPrice = $rawPrice * floatval ( mb_substr ( $seePrice, 1, mb_strlen ( $seePrice, 'utf-8' ) - 1, 'utf-8' ) );
    }
    else if (strpos ( $seePrice, '打' ) === 0)
    {
        $finalPrice = $rawPrice * (floatval ( mb_substr ( $seePrice, 1, mb_strlen ( $seePrice, 'utf-8' ) - 1, 'utf-8' ) ) / 10);
    }
    else if (strpos ( $seePrice, '折' ) === mb_strlen ( $seePrice, 'utf-8' ) - 1)
    {
        $finalPrice = $rawPrice * (floatval ( mb_substr ( $seePrice, 0, mb_strlen ( $seePrice, 'utf-8' ) - 1, 'utf-8' ) ) / 10);
    }
    if (is_numeric ( $finalPrice ))
    {
        return $finalPrice;
    }
    else
    {
        return $price;
    }    
}

/**
 * ip地址是否广州
 * @param unknown $ip
 */
function is_guangzhou(){
    $ch = curl_init();
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . real_ip();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);
    $str =  ecm_json_decode($res);
    return preg_match('/广州/',$str->city);
}

?>
