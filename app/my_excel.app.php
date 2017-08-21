<?php
require_once("includes/Alipay/corefunction.php");
require_once("includes/Alipay/md5function.php");
require_once("includes/Alipay/notify.php");
require_once("includes/Alipay/submit.php");

class My_excelApp extends MemberbaseApp {

    function My_excelApp() {
        parent::__construct();
        $this->my_money_mod = & m('my_money');
        $this->my_moneylog_mod = & m('my_moneylog');
        $this->my_mibao_mod = & m('my_mibao');
        $this->order_mod = & m('order');
        $this->my_card_mod = & m('my_card');
        $this->my_jifen_mod = & m('my_jifen');
        $this->my_paysetup_mod = & m('my_paysetup');
        $this->paylog_mod = & m('paylog');
    }

    function exits() {
        //执行关闭页面
        echo "<script language='javascript'>window.opener=null;window.close();</script>";
    }

    function index() {
        $user_id = $this->visitor->get('user_id');
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'), 'index.php?app=member', LANG::get('shangfutong'), 'index.php?app=my_money&act=index', LANG::get('jiaoyichaxun')
        );
        /* 当前用户中心菜单 */
        $this->_curitem('my_excel');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('shangfutong'));
        $my_money = $this->my_money_mod->getAll("select * from " . DB_PREFIX . "my_money where user_id=$user_id");
        $this->assign('my_excel', $my_money);
        $this->display('my_excel.index.html');
    }

    function excel_submit(){


        $xls = array(
            'xls_type'    => isset($_POST['xls_type']) ? intval($_POST['xls_type']) : 0,
            'upload'      => (isset($_FILES['xls_file']['error']) && $_FILES['xls_file']['error'] == 0) || (!isset($_FILES['xls_file']['error']) && isset($_FILES['xls_file']['tmp_name']) && $_FILES['xls_file']['tmp_name'] != 'none')
                ? $_FILES['xls_file'] : array()
        );

        $upload_size_limit = $GLOBALS['_CFG']['upload_size_limit'] == '-1' ? ini_get('upload_max_filesize') : $GLOBALS['_CFG']['upload_size_limit'];

        $last_char = strtolower($upload_size_limit{strlen($upload_size_limit)-1});

        switch ($last_char)
        {
            case 'm':
                $upload_size_limit *= 1024*1024;
                break;
            case 'k':
                $upload_size_limit *= 1024;
                break;
        }

        if ($xls['upload'])
        {
            $xls_name = $this->_upload_file($_FILES);
        }
        else
        {
            $this->show_warning('请提交订单表格');
        }

        $orderjson = array();

        if($xls['xls_type'] == 1){
            //蘑菇街

        }elseif($xls['xls_type'] == 2){
            //美丽说

        }elseif($xls['xls_type'] == 3){
            //本站表格
           $orders = $this->_parse_excel($xls_name);

        }

        $this->assign('orders',$orders);
        $this->assign('tbjson',json_encode($orders));
        $this->display('my_excel.submit.html');

    }

    private function _upload_file(){
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type('xls|xlsx'); //限制文件类型
        $uploader->addFile($_FILES['xls_file']);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/partner'))   //保存到指定目录，并以指定文件名$partner_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }


    private function  _parse_excel($filename){
        include_once(ROOT_PATH . "/includes/excel/reader.php");
        $xlsread = new Spreadsheet_Excel_Reader();
        $xlsread->setOutputEncoding('utf-8');
        $xlsread->read($filename);
        $orders = array();
        $fields = $xlsread->sheets[0]['cells'][1] ;
        $fields = array_flip($fields);
      //  $field_tmp = array('聚美订单号','姓名','地址','手机','型号','合作方条码','价格','购买件数');

        $field_f['ordersn'] = $fields['订单号'];
        $field_f['name'] = $fields['姓名'];
        $field_f['address'] = $fields['地址'];
        $field_f['mobile'] = $fields['手机'];
        $field_f['attr'] = $fields['型号'];
        $field_f['code'] = $fields['合作方条码'];
        $field_f['price'] = $fields['价格'];
        $field_f['num'] = $fields['购买件数'];


        for ($i=2; $i<=$xlsread->sheets[0]['numRows']; $i++) {
            $order['goods'] = array();
            $order['info']['ordersn'] = $xlsread->sheets[0]['cells'][$i][$field_f['ordersn']];
            $order['info']['name'] = $xlsread->sheets[0]['cells'][$i][$field_f['name']];
            $order['info']['address'] = $xlsread->sheets[0]['cells'][$i][$field_f['address']];
            $order['info']['mobile']  = $xlsread->sheets[0]['cells'][$i][$field_f['mobile']];
            $goods['attr'] = $xlsread->sheets[0]['cells'][$i][$field_f['attr']];
            $goods['code'] =  $xlsread->sheets[0]['cells'][$i][$field_f['code']];
            $goods['price'] =  $xlsread->sheets[0]['cells'][$i][$field_f['price']];
            $goods['num'] =  $xlsread->sheets[0]['cells'][$i][$field_f['num']];
            if(array_key_exists($xlsread->sheets[0]['cells'][$i][$field_f['ordersn']],$orders)){
                array_push($orders[$xlsread->sheets[0]['cells'][$i][$field_f['ordersn']]]['goods'] ,$goods);
                continue;
            }
            array_push($order['goods'],$goods);
            $order['tbjson'] = json_encode($order);

            $orders[$xlsread->sheets[0]['cells'][$i][$field_f['ordersn']]] = $order;
            unset($order);
        }
        return $orders;
    }


    private function _explode_addr($addstr){
        $l = 0;
        $i = 0;
        $p = 0;
        $find = false;
        while (!$find) {
            //判断是否超出lenarr数组的长度
            if (!isset($len_arr[$l])) {
                $arr_get[] = mb_substr($addstr, $p, null);
                $find = true;
                break;
            }

            //截取地址
            $ad = mb_substr($addstr, $p, $len_arr[$l][$i]);
            //匹配，匹配到就进入下一层级即$l++
            if (isset($address[$l][$ad])) {
                $arr_get[] = $ad; //存储值
                $p += $len_arr[$l][$i];
                $i = 0;
                $l++;
                continue;
            }
            $i++;

            //判断当前层级是否循环完毕
            //当前层级循环完毕仍未匹配到，则循环下一层级，一般是直辖市比如北京市海淀区这种情况，或者是信息不全
            if (isset($len_arr[$l]) && $i >= count($len_arr[$l])) {
                echo $ad . '<br/>'; //记录下来
                $i = 0;
                $l++;
                continue;
            }
        }
    }

    public function ajax_submit(){
        $default_goods = array(
            'goods_name' => '代发商品',
            'specification' => '',
            'attr_value' => '',
            'price' => 0,
            'quantity' => 1,
            'sku' => '',
            'befalf_fee' => 1,
            'goods_id'  => 0,
            'behalf_fee' => 0,  //代发服务费
            'delivery_id' => 0,
            'delivery_name' => '',
        );


        $goods_info = array(

            'quantity' => 1,
            'amount' => 0,
            'store_id' => 0,
            'store_name' => '代发商品',
            'type'=> 'material',
            'otype' => 'behalf',
            'allow_coupon' =>1,
            'rec_ids' => array(0),
            'behalf_fee' => 1,
            'store_im_qq' => '',
            'elementary_quality_check_fee'=>0,
            'secondary_quality_check_fee' => 0,
            'tags_change_fee' =>0,
            'packing_bag_change_fee' => 0,

        );


        $order_type =& ot('behalf');
        $model_behalf = & m('behalf');
        //$behalf_id = $model_behalf->getOne("SELECT bh_id FROM {$model_behalf->table} WHERE bh_allowed=1");
        $model_setting = &af('settings');
        $setting = $model_setting->getAll();
        $default_signed_behalfs_ids = $setting['default_signed_behalfs'];
        $behalf_id = reset($default_signed_behalfs_ids);
        $data = json_decode(stripslashes($_POST['ojson']),true);
        $goods_info['items'] = array();
        $amount = 0;
        $total_quantity = 0;

        foreach($data['goods'] as $v){

            $goods = array();
            $goods['specification'] = $v['attr'];
            $goods['price'] = $v['price'];
            $amount += $v['price'];
            $goods['attr_value'] = $v['code'];
            $goods['quantity'] = $v['num'];
            $goods['behalf_fee'] = $v['num'] * floatval(BEHALF_GOODS_SERVICE_FEE);
            $goods = array_merge($default_goods,$goods);
            $total_quantity +=  $goods['quantity'];
            array_push($goods_info['items'],$goods);
        }
        $goods_info['behalf_fee']  = floatval(BEHALF_GOODS_SERVICE_FEE) * $total_quantity ;
        $goods_info['elementary_quality_check_fee'] = BEHALF_GOODS_QUALITY_ELEMENTARY_CHECK_FEE * $total_quantity  ;
        $goods_info['quantity'] = $total_quantity;
        $goods_info['amount'] = $amount;
        $region = $this->_region($data['info']['address']);

        //默认选中通快递
        $delivery_model = & m('delivery');
        $delivery_id = $delivery_model->getOne("select dl_id from {$delivery_model->table} where dl_name='中通快递'");

        $post = array(
            'address_options' => 0,
            'consignee' => $data['info']['name'],
            'region_id' => $region['region_id'],
            'region_name' => $region['region_name'],
            'address' => $region['address'],
            'zipcode' => '000000',
            'phone_tel' => '000-00000000',
            'phone_mob' => $data['info']['mobile'],
            'shipping_choice' => 2 ,
            'behalf' => $behalf_id ,
            'delivery' => $delivery_id ,          //默认快递
            'quality_check' => 1 ,      //默认选择第一种普通质检
            'order_sn' => $data['info']['ordersn'],
		//'gids' => '33182:149237:1901511'
        );

        $order_id = $order_type->submit_order(array(
            'goods_info'    =>  $goods_info,      //商品信息（包括列表，总价，总量，所属店铺，类型）,可靠的!
            'post'          =>  $post,           //用户填写的订单信息
        ));

        if (!$order_id)
        {
            $this->show_warning($order_type->get_error());
            return;
        }

        echo json_encode(array('code'=>200,'order_id'=>$order_id));
    }

    private function _region($addr){

        $address = include_once(ROOT_PATH .'/data/ext/address.info.php');
        $len_arr = array();
        $len_arr[] = array_values(array_unique(array_map('mb_strlen', array_keys($address[0])))); //所有省的长度
        $len_arr[] = array_values(array_unique(array_map('mb_strlen', array_keys($address[1])))); //市的长度
        $len_arr[] = array_values(array_unique(array_map('mb_strlen', array_keys($address[2])))); //区的长度
        $l = 0;
        $i = 0;
        $p = 0;
        $find = false;
        $arr_get = array();
     //   $addr = "湖北省汉川市田二河镇开发区";

        while (!$find) {
            //判断是否超出lenarr数组的长度
            if (!isset($len_arr[$l])) {
                $arr_get[] = mb_substr($addr, $p, null);
                $find = true;
                break;
            }

            //截取地址
            $ad = mb_substr($addr, $p, $len_arr[$l][$i]);
            //匹配，匹配到就进入下一层级即$l++
            if (isset($address[$l][$ad])) {
                $arr_get[] = $ad; //存储值
                $p += $len_arr[$l][$i];
                $i = 0;
                $l++;
                continue;
            }
            $i++;

            //判断当前层级是否循环完毕
            //当前层级循环完毕仍未匹配到，则循环下一层级，一般是直辖市比如北京市海淀区这种情况，或者是信息不全
            if (isset($len_arr[$l]) && $i >= count($len_arr[$l])) {
            //    echo $ad . '<br/>'; //记录下来
                $i = 0;
                $l++;
                continue;
            }
        }
         $arr_get = array_filter($arr_get);
        $model_region = & m('region');
        $region_id = $model_region->getOne("SELECT region_id FROM {$model_region->table} WHERE region_name='".end($arr_get)."'");
        $address = substr($addr,strpos($addr ,end($arr_get))+ strlen(end($arr_get))) ;

       return array('region_id'=>$region_id ? $region_id : 0 ,'region_name' => implode(' ',$arr_get),'address'=>$address);
        //return array_unique($arr_get);
    }
}
?>
