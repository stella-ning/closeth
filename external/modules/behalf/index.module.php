<?php
include_once ROOT_PATH.'/external/modules/behalf/index.module.base.php';
include_once ROOT_PATH.'/external/modules/behalf/index.module.printer.php';
include_once ROOT_PATH.'/external/modules/behalf/index.module.client.php';
// zjh 菜单下功能点权限
include_once ROOT_PATH.'/external/modules/behalf/lib/menu_sub_priv.php';

class BehalfModule extends BehalfBaseModule
{
    var $_login_name;         //登录者：代发管理员、货物管理员(拿货员)
    var $_behalf_printer;    //打印类
    var $_behalf_client;
    
    function __construct()
    {
        $this->BehalfModule();
    }

    function BehalfModule()
    {

    	$this->_behalf_printer = new BehalfPrinterModule();
    	$this->_behalf_client = new BehalfClientModule();
    	// zjh 功能点权限(B类权限)
    	$this->_menu_sub_priv = new MenuSubPriv();

        parent::__construct();

        // 检测当前用户是否有权访问 zjh
        $this->_detect_priv();

        $this->_operate_behalf_setting(1);  // 先配置一下相关的配置信息，保证拥有配置信息

      // $this->_test_get_param();
    }
    
    function test()
    {
    	// zjh 检测功能点权限(B类权限)
    	$bool = $this->_menu_sub_priv->_detect_sub_priv('set_delivery_fee','remove_shipping_area'); var_dump($bool); exit;

    	/* $result = $this->_goods_warehouse_mod->find(array(
    			'conditions'=>'gwh.bh_id = 0',
    			//'fields'=>'gwh.*,gwh.bh_id as gwhbh_id,order_alias.*,order_alias.bh_id as orderbh_id',
    			'fields'=>'gwh.order_id as g_order,gwh.bh_id as gbh_id,order_alias.order_id as o_order,order_alias.bh_id as obh_id',
    			'join'=>'belongs_to_order'
    	)); */
    	$result = db( )->getAll( "SHOW COLUMNS FROM ".DB_PREFIX."member" );
    	dump($result);
    }
     
     function _test_get_param()
     {
     	$act= empty($_GET['act']) ? 'index' : trim($_GET['act']);
  		$str = 'index.php?module=behalf&act=see_behalf';
  		$str1 = 'index.php?module=behalf&act=gen_taker_list&act=manage_goodstaker';
  		$index = strrpos($str,'act=');
  		$menu_url_act = substr($str,$index+4);
  		// $a = parse_url($str1);
  		$t = explode('&', $a['query']);
  		print_r($c); 
     	// print_r($act);

     	// exit;
     	$emo = &m('employee_role');
     	$x=$emo->find(array(
     		'conditions'=>'er_r.role_id = r_p.role_id',
     		'fields'=>'er_r.*,r_p.*',
     		'join'=>'belongs_to_role'
     	));
     	print_r($x);
     	$mo = &m('role');
     	$y=$mo->find(array(
     		'conditions'=>'r_p.bh_id = behalf.bh_id',
     		'fields'=>'r_p.*,behalf.*',
     		'join'=>'belongs_to_behalf'
     	));
     	print_r($y);

     	$cmo = &m('employee_role');
     	$x=$cmo->find(array(
     		'conditions'=>'er_r.employee_id = member.user_id',
     		'fields'=>'er_r.*,member.*',
     		'join'=>'belongs_to_member'
     	));
     	print_r($x);
     	//获取域名或主机地址 
		echo $_SERVER['HTTP_HOST']."<br>"; #localhost

		//获取网页地址 
		echo $_SERVER['PHP_SELF']."<br>"; 

		//获取网址参数 
		echo $_SERVER["QUERY_STRING"]."<br>"; 

		//获取用户代理 
		echo $_SERVER['HTTP_REFERER']."<br>"; 

		//获取完整的url
		echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>";
		echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']."<br>";
		#http://localhost/blog/testurl.php?id=5

		//包含端口号的完整url
		echo 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]."<br>"; 
		#http://localhost:80/blog/testurl.php?id=5

		//只取路径
		$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]."<br>"; 
		echo dirname($url);

		die;
     }
    
    function updateShiptime()
    {
    	/* $result = $this->_goods_warehouse_mod->find(array(
    			'conditions'=>"bh_id='73499' AND goods_status > 0 "
    	));
    	
    	if(!empty($result))
    	{
    		foreach ($result as $r)
    		{
    			$this->_goods_warehouse_mod->edit($r['id'],array('taker_id'=>$r['bh_id'],'taker_time'=>(gmtime()-4800)));
    		}
    	}
    	
    	echo  count($result);     
    	dump($result); */
    	
    	/* $orderlogs = $this->_orderlog_mod->find(array(
    			'conditions'=>"order_status='待发货' AND changed_status='已发货' AND "
    			.db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),'order_alias.status')
    			." AND order_alias.ship_time is NULL "
    			." AND bh_id <> '10919'",
    			'join'=>'belongs_to_order',
    			'fields'=>'order_alias.order_id,order_alias.bh_id,order_alias.status,order_alias.ship_time,log_time',
    			'count'=>true
    	));//946656000 2000-01-01
    	 
    	echo count($orderlogs);73499
    	echo "<pre>";
    	print_r ($this->_behalf_mod->find());
    	echo "</pre>";
    	dump($orderlogs); */
    	
    	
    	/* $orderlogs = $this->_orderlog_mod->find(array(
    		'conditions'=>"order_status='待发货' AND changed_status='已发货' AND "
    			.db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),'order_alias.status')
    			." AND order_alias.ship_time > log_time "
    			." AND bh_id <> '10919'",
    		'join'=>'belongs_to_order',
    		'fields'=>'order_alias.order_id,order_alias.bh_id,order_alias.status,order_alias.ship_time,log_time',
    		'count'=>true	
    	));
    	
    	echo count($orderlogs);
    	if(!empty($orderlogs))
    	{
    		foreach ($orderlogs as $orderlog)
    		{
    			$this->_order_mod->edit($orderlog['order_id'],array('ship_time'=>$orderlog['log_time']));
    		}
    	} */
    }

    /**
     * 主体framesets
     * @see BaseApp::index()
     */
    function index()
    {
		$bh_id = $this->visitor->get('has_behalf');

    	if($bh_id)
    	{
	    	/* $goods_list = $this->_goods_warehouse_mod->find(array(
	    			'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in(array(BEHALF_GOODS_PREPARED,BEHALF_GOODS_TOMORROW,BEHALF_GOODS_UNFORMED),'goods_status')." AND order_alias.status=".ORDER_ACCEPTED,
	    			'fields'=>'gwh.id',
	    			'join'=>'belongs_to_order',
	    	));
	    	$start_time = gmstr2time('yesterday');
	    	$end_time = $start_time + 24*60*60;
	    	$order_list = $this->_order_mod->find(array(
	    			'conditions'=>"bh_id={$bh_id} AND ship_time >= {$start_time} AND ship_time < {$end_time} AND ".db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),'status'),
	    					));
	    	$order_list1 = $this->_order_mod->find(array(
	    			'conditions'=>"bh_id={$bh_id} AND status=".ORDER_ACCEPTED
	    					)); */
	    	
	    	$behalf_info = $this->_behalf_mod->get($bh_id);
	    	$behalf_info['region_name'] = $this->_remove_China($behalf_info['region_name']);
	    	
	    	$mail_counter = $this->_behalf_printer->getMailCounter(); 
	    	$mail_counter = object_array(json_decode($mail_counter));  
	    	if($mail_counter['result'] == false) {$mail_count = 0;}
	    	else{
	    	    $mail_count = empty($mail_counter['counter']['available']) ? 0 :$mail_counter['counter']['available'] ;
	    	}
    	}
	    
	    //zjh
	    $user_id = $this->visitor->get('user_id');	
	    $user_name = $this->visitor->get('user_name');	
	    if ($user_id != $bh_id){
	    	$employee = $this->_get_employee_detail($user_id);
	    }

    	//$this->assign('accepted_goods',count($goods_list));
    	//$this->assign('accepted_orders',count($order_list1));
    	//$this->assign('shipped_orders',count($order_list));
    	$this->assign('mail_counter',$mail_count);
    	$this->assign('user_id',$user_id);  //zjh
    	$this->assign('user_name',$user_name);  //zjh
    	$this->assign('employee',$employee);  //zjh
    	$this->assign('behalf',$behalf_info);
    	$this->_assign_leftmenu('dashboard');
    	//$this->_assign_curleftmenu('welcome_page');
        $this->display('index.whole.html');
    }
    
    /**
     * 订单列表
     */
    function order_list()
    {
    	$bh_id = $this->visitor->get('has_behalf');

    	/*获取市场列表*/
    	$this->_get_markets();
    	/* 获取订单列表 */
    	$this->_get_orders(true,'all_orders',true,true,true);
    	/*获取可用快递*/
    	$this->_get_related_delivery();
    	/* 当前用户中心菜单 */
    	$type = (isset($_GET['type']) && $_GET['type'] != '') ? trim($_GET['type']) : 'all_orders';

    	// 补收差价
    	$reapply = $this->_goods_reapply_status();
    	$this->assign('reapply',$reapply);
    	$this->_curmenu($type);
    	$this->_import_css_js('dt');
    	$this->_assign_leftmenu('order_manage');
    	$this->display('behalf.order.list_detail.html');
    }

	/**
	 * 代发订单列表管理
	 *
	 */
    function order_list_behalf(){
		/*获取市场列表*/
		//$this->_get_markets();
		/* 获取订单列表 */
		//$this->_get_orders(true,'all_orders',true,true,true);
		/*获取可用快递*/
		//$this->_get_related_delivery();
		/* 当前用户中心菜单 */
		$type = (isset($_GET['type']) && $_GET['type'] != '') ? trim($_GET['type']) : 'all_orders';
		$tomorrow = (isset($_GET['tomorrow']) && $_GET['tomorrow'] != '') ? trim($_GET['tomorrow']) : '0';

		$order_sn = (isset($_GET['order_sn_s']) && $_GET['order_sn_s'] != '') ? trim($_GET['order_sn_s']) : '';
		$invoice_no = (isset($_GET['invoice_no_s']) && $_GET['invoice_no_s'] != '') ? trim($_GET['invoice_no_s']) : '';
		$add_time_from = (isset($_GET['add_time_from_s']) && $_GET['add_time_from_s'] != '') ? trim($_GET['add_time_from_s']) : '';
		$add_time_to = (isset($_GET['add_time_to_s']) && $_GET['add_time_to_s'] != '') ? trim($_GET['add_time_to_s']) : '';
		$query = array(
			'order_sn' => $order_sn,
			'invoice_no' => $invoice_no ,
			'tomorrow' => $tomorrow,
			'type'	=> $type,
			'add_time_from' => $add_time_from,
			'add_time_to' => $add_time_to,
		);

		$this->_curmenu_2($type);
		$this->assign('type',$type);
		$this->assign('tomorrow',$tomorrow);
		$this->assign("query", $query);
		$this->_import_css_js('dtall');
		$this->_assign_leftmenu('stock_manage');
		$this->display('behalf.order.list.html');

	}

	function order_list_ajax(){


		echo json_encode(  $this->_get_orders_source(true,'all_orders',true,true,true));
		return ;
	}

	function sync_order_quantity()
	{
		$order_id = isset($_POST['order_id']) && $_POST['order_id'] ? trim($_POST['order_id']) : '';
		$model_goods_warehouse = & m('goodswarehouse');
		$model_order = & m('order');
		$goods_warehouse = $model_goods_warehouse->find(array(
			'conditions' => 'order_id='.$order_id.' AND NOT '.db_create_in(array( BEHALF_GOODS_CANCEL ,BEHALF_GOODS_ADJUST),'goods_status'),
		));

		$total_quantity = count($goods_warehouse);
		$flag = $model_order->edit('order_id='.$order_id , array('total_quantity' => $total_quantity));
		if(!$flag){
			echo ecm_json_encode(array('code'=>500,'msg'=>'更新失败'));
			return ;
		}
		echo ecm_json_encode(array('code'=>0,'msg'=>'更新成功'));
		return ;
	}
	/**
	 * 商品数据统计表
	 *
	 */
	function goods_statistics_list(){
		$this->_get_statistics_source();
		$this->_import_css_js('dtall');
		$this->_assign_leftmenu('stock_manage');
		$this->display('behalf.statistics.list.html');
	}

	function goods_statistics_ajax(){
		echo json_encode( $this->_get_statistics_source());
		return ;
	}

	/**
	 * 分拣报表
	 */
	function order_sort_list(){
		$tomorrow = (isset($_GET['tomorrow']) && $_GET['tomorrow'] != '') ? trim($_GET['tomorrow']) : '0';
		$query = array(
			'tomorrow' => $tomorrow,
		);
		$this->assign('query',$query);
		$this->_import_css_js('dtall');
		$this->_assign_leftmenu('stock_manage');
		$this->display('behalf.order.sort.html');
	}


	function order_sort_ajax(){


		echo json_encode(  $this->_get_orders_source(true,'accepted',true,true,true));
		return ;
	}

	/**
	 *  单号管理
	 */
    function invoice_list(){


		$this->_get_invoice();

		$this->_import_css_js('dt');
		$this->_assign_leftmenu('order_manage');

		$this->display('behalf.invoice.list.html');
	}

	/**
	 * 退货列表
	 */
	function back_list(){
		$bh_id = $this->visitor->get('has_behalf');
		//获取拿货市场，并按代发设置的顺序排序
		$bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);
		if($bh_markets)
		{
			$sort_arr = array();//用于多维排序
			foreach ($bh_markets as $k=>$v)
			{
				$sort_arr[] = $v['sort_ord'];
			}
			array_multisort($sort_arr,SORT_ASC,$bh_markets);
		}

		//获取关联快递
		$bh_deliverys = $this->_behalf_mod->getRelatedData('has_delivery',$bh_id);

		//代发信息
		$behalf_info =$this->_behalf_mod->get($bh_id);
		//获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
		$refund_order_ids = $this->get_refunds_orders();
		$conditions_refund = "";
		if(!empty($refund_order_ids))
		{
			$conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);
		}


		if(IS_POST)
		{

		}
		else //默认结果
		{	$model_goods_warehouse = & m('goodswarehouse');
			$model_order_refund = & m('orderrefund');
			$refund_goods = $model_order_refund->find('status=0 AND type=1 AND goods_ids_flag=1');
			if($refund_goods){
				$conditions_refund = $this->_get_refund_condition();
			}
			$refund_goods = $model_goods_warehouse->find(array(
				'conditions' => 'gwh.bh_id='.$bh_id.$conditions_refund,
				'fields'=>'gwh.*,order_alias.status,order_alias.pay_time',
				'join'=>'belongs_to_order',
				'count'=>true
			));



			if($refund_goods)
			{
				$count_ttt = $model_goods_warehouse->getCount();

				$total_count = count($refund_goods);//商品件数

				$total_amount = 0; //商品总金额
				$store_bargin = 0;//店家优惠
				$start_time = gmtime();
				$end_time = 0;//找出最小时间和最大时间
				foreach ($refund_goods as $gkey=>$goods)
				{
					$goods_ids[] = $goods['id'];
					$total_amount += floatval($goods['goods_price']);
					$store_bargin += floatval($goods['store_bargin']);
					$result[$gkey]['goods_attr_value'] = $this->_Attrvalue2Pinyin($goods['goods_attr_value']);
					$goods['pay_time'] > $end_time && $end_time = $goods['pay_time'];
					$goods['pay_time'] < $start_time && $start_time = $goods['pay_time'];
				}

				$rest_count = intval($count_ttt) - intval($total_count);

				$this->assign('rest_count',$rest_count);
				$this->assign('default_search',true);
				$this->assign('total_count',$total_count);
				$this->assign('total_amount',$total_amount);
				$this->assign('store_bargin',$store_bargin);
				$this->assign('last_amount',$total_amount-$store_bargin);
			}

			$this->assign('goods_list', $refund_goods);

			/*$model_goods_warehouse = & m('goodswarehouse');
			$result = $model_goods_warehouse->find(array(
				'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in(array(BEHALF_GOODS_PREPARED,BEHALF_GOODS_DELIVERIES,BEHALF_GOODS_TOMORROW),'goods_status').
					" AND order_alias.status=".ORDER_ACCEPTED.$conditions_refund.$lack_conditions,
				'fields'=>'gwh.*,order_alias.status,order_alias.pay_time',
				'join'=>'belongs_to_order',
				'order'=>'market_id ASC,floor_id ASC,store_address ASC',
				'limit'=>'50',
				'count'=>true
			));
			if($result)
			{
				$count_ttt = $model_goods_warehouse->getCount();

				$total_count = count($result);//商品件数

				$total_amount = 0; //商品总金额
				$store_bargin = 0;//店家优惠
				$start_time = gmtime();
				$end_time = 0;//找出最小时间和最大时间
				foreach ($result as $gkey=>$goods)
				{
					$goods_ids[] = $goods['id'];
					$total_amount += floatval($goods['goods_price']);
					$store_bargin += floatval($goods['store_bargin']);
					$result[$gkey]['goods_attr_value'] = $this->_Attrvalue2Pinyin($goods['goods_attr_value']);
					$goods['pay_time'] > $end_time && $end_time = $goods['pay_time'];
					$goods['pay_time'] < $start_time && $start_time = $goods['pay_time'];
				}

				$rest_count = intval($count_ttt) - intval($total_count);

				$this->assign('rest_count',$rest_count);
				$this->assign('default_search',true);
				$this->assign('total_count',$total_count);
				$this->assign('total_amount',$total_amount);
				$this->assign('store_bargin',$store_bargin);
				$this->assign('last_amount',$total_amount-$store_bargin);
			}

			$this->assign("end_time",local_date('Y-m-d H:i:s',$end_time));
			$this->assign('start_time',local_date('Y-m-d H:i:s',$start_time));
			$this->assign('goods_list',$result);*/
		}

		$this->assign('bh_name',$behalf_info['bh_name']);
		$this->assign('show_print',true);
		$this->_assign_leftmenu('order_manage');
		$this->_import_css_js('dtall');


		$this->display('behalf.goods.back.list.html');
	}

	private function _get_refund_condition(){
		$model_order_refund = & m('orderrefund');
		$refund_goods = $model_order_refund->find('status=0 AND type=1 AND goods_ids_flag=1 AND dl_status=1 ');
		$refund_ids = array();
		foreach($refund_goods as $v){
			$refund_ids[] = $v['goods_ids'];
		}
		$refund_ids = array_filter($refund_ids);
		$refund_ids = explode(',',implode( ',' , $refund_ids));
		$refund_conditions = ' AND '.db_create_in($refund_ids , 'id');
		return $refund_conditions;
	}

	function _get_refund_details($order_id){
        $model_order_refund = & m('orderrefund');
        $result = $model_order_refund->find(array(
            'conditions'=>'status=0 AND type=1 and order_id='.$order_id,
            'fields'=>'invoice_no,create_time,refund_reason',
            'count'=>true
        ));
        return $result;
	}

    /**
     * 出入库管理
     */
    function manage_goods_warehouse()
    {
    	if(IS_POST)
    	{
    		$goods_no = trim($_POST['goods_no']);
    		$goods_action =$this->_goods_status_translator(trim($_POST['goods_action']));
    		$goods_action_chinese = $this->_goods_chinese_translator(trim($_POST['goods_action']));
    		//商品编码是否存在
    		$goods_info = $this->_goods_warehouse_mod->get("goods_no='{$goods_no}'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL)));


    		if($goods_info)
    		{
    		    $order_id = $goods_info['order_id'];
    		    $spec_id = $goods_info['goods_spec_id'];
    		    $goods_id = $goods_info['goods_id'];
    		}
    		else
    		{
    		    $this->json_error('goods_no_not_exist');
    		    return;
    		}


    		//所在订单是否待发货或待付款
    		$order = $this->_order_mod->get($order_id);
			$financial_model = & m('financialstatistics');
    		if(!in_array($order['status'], array(ORDER_ACCEPTED,ORDER_PENDING)))
    		{
    		    $this->json_error('order_is_not_accepted');
    		    return;
    		}
    		//开启事务
    		$success = $this->_start_transaction();
    		
    		if(in_array($goods_action, array(BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE)))
    		{
    		    $order_message = $this->_ordermessaeg->get('order_id='.$order_id);
    		    if(empty($order_message['times']))
    		    {
    		        $buyer_info   = &ms()->user->_local_get($order['buyer_id']);
    		        //$this->sendSaleSms($buyer_info['phone_mob'], sprintf(Lang::get('order_message_lack'),$order['order_sn']));
					$this->sendSms($buyer_info['phone_mob'], sprintf(Lang::get('order_message_lack'),$order['order_sn']));

					$this->_ordermessaeg->add(array('order_id'=>$order_id,'times'=>1,'create_time'=>gmtime()));

    		    }
    		}

    		if(in_array($goods_action, array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE)))
    		{
    				$affect_rows = $this->_goods_warehouse_mod->edit("goods_no='{$goods_no}'",array('goods_status'=>$goods_action));
    				!$affect_rows && $success = false;
    				$affect_rows = $this->_ordergoods_mod->edit("order_id='{$order_id}' AND spec_id = '{$spec_id}' AND goods_id ='{$goods_id}' ",array('oos_value'=>'0','oos_reason'=>$goods_action_chinese));
    				$affect_rows === false && $success = false;  //可能已更新，返回0
    				//缺货统计
    				$goods_statistics = $this->_goods_statistics_mod->get("{$goods_id}");
    				if($goods_statistics)
    				{
    				    $affect_rows = $this->_goods_statistics_mod->edit("{$goods_id}",'oos=oos+1');
    				    $affect_rows === false && $success = false;
    				}else{
    				    $affect_rows = $this->_goods_statistics_mod->add(array('goods_id'=>"{$goods_id}",'oos'=>1));
    				    !$affect_rows && $success = false;
    				}

    		}
    		//同时下架
    		if($goods_action == BEHALF_GOODS_UNSALE)
    		{
    		    //$affect_rows = $this->_goods_mod->edit("goods_id ='{$goods_id}'",array('if_show'=>'0','closed'=>'1','close_reason'=>"behalf[{$this->visitor->get('user_name')}] close it."));
    		    //!$affect_rows && $success = false;
    		}
    		if($goods_action == BEHALF_GOODS_READY)
    		{	//需要确保当前商品的状态是 app已拿
    		    $result = $this->_goods_warehouse_mod->edit("goods_no='{$goods_no}' AND ".db_create_in(array(BEHALF_GOODS_READY_APP),'goods_status'),array(
    					'goods_status'=>$goods_action));

    			!$result && $success = false;
    			$result = $this->_ordergoods_mod->edit("order_id='{$order_id}' AND spec_id = '{$spec_id}' AND goods_id ='{$goods_id}' ",array('oos_value'=>'1'));
    			//!$result && $success = false;

				$financial_result = $financial_model->goods_success();
				!$financial_result && $success = false;


    		}
    		
    		$this->_end_transaction($success);
    		
    		if($success)
    		{
    			$this->json_result(1,'entern_goodswarehouse_success');
    			return;
    		}
    		else 
    		{
    			$this->json_error('entern_goodswarehouse_fail');
    			return;
    		}
	    	
    	}
    	
    	$this->_import_css_js ('dt');
    	if($this->visitor->get('has_behalf'))
    	{
    		$this->_assign_leftmenu('order_manage');
    	}else 
    	{
    		$this->_assign_leftmenu('dashboard');
    	}
    	
    	$this->display("behalf.goods.warehouse.manage.html");
    }
    
	
    function check_goods_warehouse(){
		if(IS_POST)
		{
			$imperfect_arr = array('wrong' , 'dirty' , 'broken' , 'open' , 'spot');
			if(!in_array( $_POST['goods_action'] ,$imperfect_arr)){
				return;
			}
			$goods_no = trim($_POST['goods_no']);
			$goods_action = $_POST['goods_action'];
			$goods_action_chinese = $this->_goods_chinese_translator(BEHALF_GOODS_IMPERFECT);

			//商品编码是否存在
			$goods_info = $this->_goods_warehouse_mod->get(" goods_no='{$goods_no}'");
			if($goods_info)
			{
				$order_id = $goods_info['order_id'];
				$spec_id = $goods_info['goods_spec_id'];
				$goods_id = $goods_info['goods_id'];
			}
			else
			{
				$this->json_error('goods_no_not_exist');
				return;
			}
			//所在订单是否待发货或待付款
			$order = $this->_order_mod->get($order_id);
			if(!in_array($order['status'], array(ORDER_ACCEPTED,ORDER_PENDING)))
			{
				$this->json_error('order_is_not_accepted');
				return;
			}
			//开启事务
			$success = $this->_start_transaction();

			//改变商品状态
			$affect_rows = $this->_goods_warehouse_mod->edit("goods_no='{$goods_no}'" , array('goods_status'=> BEHALF_GOODS_IMPERFECT));

			!$affect_rows && $success = false;

			$imperfect_log_model = & m('imperfectlog');
			$result = $imperfect_log_model->add(array('goods_id'=>$goods_info['id'] , 'type' => $goods_action , 'add_time' => time()));
			!$result && $success = false;

			//退货的商品插入一条记录
			$refund_reason_model = & m('refundreason');
			$refund_reason_result = $refund_reason_model->get("goods_id={$goods_info['id']}");
			$refund_reason_data = array('reason' => BEHALF_BACKREASON_IMPERFECT  ,'add_time' => time());
			if($refund_reason_result){
				$refund_reason_model->edit("goods_id={$goods_info['id']}" , $refund_reason_data );
			}else{
				$refund_reason_data['goods_id'] = $goods_info['id'];
				$refund_reason_model->add( $refund_reason_data );
			}

			$this->_end_transaction($success);

			if($success)
			{
				$this->json_result(1,'entern_goodswarehouse_success');
				return;
			}
			else
			{
				$this->json_error('entern_goodswarehouse_fail');
				return;
			}

		}

		$this->_import_css_js ('dt');
		if($this->visitor->get('has_behalf'))
		{
			$this->_assign_leftmenu('stock_manage');
		}else
		{
			$this->_assign_leftmenu('dashboard');
		}

		$this->display("behalf.goods.warehouse.check.html");
	}
    
    /**
     * 管理拿货员
     */
    function manage_goodstaker()
    {
    	$bh_id = $this->_get_bh_id();
    	$model_member =& m('member');
    	if (IS_POST)
    	{
    		$user_name = isset($_POST['user_name']) && $_POST['user_name']?trim($_POST['user_name']):'';
    		if(empty($user_name))
    		{
    			$this->json_error('user name empty!');
    			return ;
    		}
    		$infos = Lang::get('unvalid_user_name');
    		$member_info = ms()->user->_local_get(array('conditions'=>"user_name='{$user_name}'"));
    		if($member_info['user_id'] == $this->visitor->get('user_id'))
    		{
    			$infos = Lang::get('self_not_allow_to_taker');
    			$member_info = array();
    		}
    		$this->assign('show_member',true);
    		$this->assign('info_type',empty($member_info)?'warning':'info');
    		$this->assign('infos',$infos);
    		$this->assign('member_info',$member_info);
    	}
    	
    	$members = $model_member->find(array(
    		'conditions'=>'behalf_goods_taker='.$bh_id	
    	));

    	$this->_import_css_js();
    	$this->_assign_leftmenu('setting');
    	$this->assign('members',$members);
    	$this->display('behalf.goods.takers.manage.html');
    }
    
    /**
     * 管理拿货单
     */
    function manage_taker_list()
    {
        /*
    	$login_id = $this->visitor->get('user_id');
    	if($this->visitor->get('pass_behalf'))
    	{
    		$condition =" bh_id = {$login_id} ";
    	}
    	else
    	{
    		$condition =" taker_id = {$login_id} ";
    	}
    	$nhd_list = $this->_goods_taker_inventory_mod->find(array(
    		'conditions'=>' visible = 1 AND '.$condition,
    	    'order'=>'createtime DESC',
    	    'limit'=>'50'
    	));
    	if($nhd_list)
    	{
    	    foreach ($nhd_list as $key=>$nhd)
    	    {
    	        $goods_ids = explode(',', $nhd['content']);
    	        $goods_list = $this->_goods_warehouse_mod->find(array(
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
    	*/
    	$this->_import_css_js('dtall');
    	$this->_assign_leftmenu('order_manage');
    	//$this->assign('nhd_list',$nhd_list);
    	$this->display('behalf.goods.taker.list.manage.html');
    }
	/**
	 * 设置 拿货员
	 */
	function edit_goods_taker()
	{
		$bh_id = $this->_get_bh_id();
		$model_member =& m('member');
		
		if(isset($_GET['m']) && $_GET['m'])
    	{
    		$user_id = intval($_GET['id']);    		
    		//设为拿货员
    		if($_GET['m'] == 1)
    		{
    			$affect_rows = $model_member->edit($user_id,array('behalf_goods_taker'=>$bh_id));
    			if($affect_rows)
    			{
    				$this->json_result(1,'set_ok');
    			}
    			else 
    			{
    				$this->json_error('set_fail');
    			}
    		}
    		//解除拿货员
    		if($_GET['m'] == 2)
    		{
    			$affect_rows = $model_member->edit($user_id,array('behalf_goods_taker'=>'0'));
    			if($affect_rows)
    			{
    				$this->json_result(1,'set_ok');
    			}
    			else
    			{
    				$this->json_error('set_fail');
    			}
    		}
    	}
	}

    
    /**
     * 生成拿货单
     */
    function gen_taker_list()
    {
    	$bh_id = $this->visitor->get('has_behalf');
    	//获取拿货市场，并按代发设置的顺序排序
    	$bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);
    	if($bh_markets)
    	{
    	    $sort_arr = array();//用于多维排序
    	    foreach ($bh_markets as $k=>$v)
    	    {
    	        $sort_arr[] = $v['sort_ord'];
    	    }
    	    array_multisort($sort_arr,SORT_ASC,$bh_markets);
    	}   	
    	//获取关联快递
    	$bh_deliverys = $this->_behalf_mod->getRelatedData('has_delivery',$bh_id);
    	//代发信息
    	$behalf_info =$this->_behalf_mod->get($bh_id);
    	//获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
    	$refund_order_ids = $this->get_refunds_orders();
    	$conditions_refund = "";
    	if(!empty($refund_order_ids))
    	{
    	   $conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);    
    	}

    	//几天内报缺货的商品
    	//$lack_goods_ids = $this->_lack_goods_bystores();
    	$lack_conditions = "";
    	if($lack_goods_ids)
    	{
    	    //$lack_conditions = " AND goods_id NOT ".db_create_in($lack_goods_ids);
    	}
    	//dump($lack_goods_ids);
    	
    	if(IS_POST)
    	{
    		$goods_ids = array();//仓库商品ids
	    	$market_names = array();//市场名称，用于保存拿货单
	    	$start_time = $_POST['query_time'] ? gmstr2time($_POST['query_time']) : 0;
	    	$end_time = $_POST['query_endtime'] ? gmstr2time($_POST['query_endtime']) : 0;
	    	
	    	$mk_ids = $_POST['market'];//市场id，用于保存拿货单
	    	$dl_id = $_POST['delivery'] ? intval($_POST['delivery']) : 0;
	    	$condition_dl = $dl_id > 0 ?" AND delivery_id = '{$dl_id}' ":""; 
	    	
	    	foreach ($bh_markets as $mark)
	    	{
	    		foreach ($mk_ids as $mkid)
	    		{
	    			if($mkid == $mark['mk_id'])
	    			{
	    				$market_names[] = $mark['mk_name'];
	    			}
	    		}	
	    	}
	    	
	    	if(!empty($mk_ids) && $start_time)
	    	{
	    	    //拿货单商品
		    	$result = $this->_goods_warehouse_mod->find(array(
		    		'conditions'=>"gwh.bh_id = {$bh_id} AND order_alias.pay_time >= '{$start_time}' AND order_alias.pay_time <= '{$end_time}' {$condition_dl} AND ".
		    		db_create_in(array(BEHALF_GOODS_PREPARED,BEHALF_GOODS_DELIVERIES,BEHALF_GOODS_TOMORROW),'goods_status').
		    		" AND ".db_create_in($mk_ids,'market_id')." AND order_alias.status=".ORDER_ACCEPTED.$conditions_refund.$lack_conditions,
		    		'fields'=>'gwh.*,order_alias.status,order_alias.total_quantity,orderstock.stock_code',
		    		'join'=>'belongs_to_order,belongs_to_orderstock',
		    		'order'=>'market_id ASC,floor_id ASC,store_address ASC'	
		    	));
		    	if($result)
		    	{
		    		$total_count = count($result);//商品件数
		    		$total_amount = 0; //商品总金额
		    		$store_bargin = 0;//店家优惠
		    		foreach ($result as $gkey=>$goods)
		    		{
		    			$goods_ids[] = $goods['id'];
		    			$total_amount += floatval($goods['goods_price']);
		    			$store_bargin += floatval($goods['store_bargin']);
		    			$result[$gkey]['goods_attr_value'] = $this->_Attrvalue2Pinyin($goods['goods_attr_value']);
		    		}
		    		
		    		// $this->assign('takers',$this->_behalf_mod->getRelatedData('has_membertaker',$bh_id));//拿货员

		    		// 新的方式获取拿货员 zjh
        			$takers = $this->_get_spec_func_employees(BEHALF_TAKE_RETURN_GOODS);
        			$this->assign('takers',$takers);//拿货员


		    		$this->assign('content',implode(',',$goods_ids));//保存拿货单用
		    		$this->assign('mkids',implode(',',$mk_ids));//保存拿货单用
		    		$this->assign('mknames',implode(',',$market_names));//保存拿货单用
		    		$this->assign('bh_id',$bh_id);//保存拿货单用
		    		
		    		$this->assign('total_count',$total_count);
		    		$this->assign('total_amount',$total_amount);
		    		$this->assign('store_bargin',$store_bargin);
		    		$this->assign('last_amount',$total_amount-$store_bargin);
		    	}
		    	//dump($result);
		    	$this->assign("end_time",$_POST['query_endtime']);
		    	$this->assign('start_time',$_POST['query_time']);
		    	$this->assign('goods_list',$result);
	    	}
    	}
    	else //默认结果
    	{
    		$model_goods_warehouse = & m('goodswarehouse');
    		$result = $model_goods_warehouse->find(array(
    				'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in(array(BEHALF_GOODS_PREPARED,BEHALF_GOODS_DELIVERIES,BEHALF_GOODS_TOMORROW),'goods_status').
    				" AND order_alias.status=".ORDER_ACCEPTED.$conditions_refund.$lack_conditions,
    				'fields'=>'gwh.*,order_alias.status,order_alias.pay_time,order_alias.total_quantity,order_third.third_id,orderstock.stock_code',
    				'join'=>'belongs_to_order,belongs_to_orderthird,belongs_to_orderstock',
    				'order'=>'market_id ASC,floor_id ASC,store_address ASC',
    				'limit'=>'50',
    				'count'=>true
    		));

    		if($result)
    		{
    			$count_ttt = $model_goods_warehouse->getCount();
    			
    			$total_count = count($result);//商品件数
    			
    			$total_amount = 0; //商品总金额
    			$store_bargin = 0;//店家优惠
    			$start_time = gmtime();
    			$end_time = 0;//找出最小时间和最大时间
    			foreach ($result as $gkey=>$goods)
    			{
    				$goods_ids[] = $goods['id'];
    				$total_amount += floatval($goods['goods_price']);
    				$store_bargin += floatval($goods['store_bargin']);
    				$result[$gkey]['goods_attr_value'] = $this->_Attrvalue2Pinyin($goods['goods_attr_value']);
    				$goods['pay_time'] > $end_time && $end_time = $goods['pay_time'];
    				$goods['pay_time'] < $start_time && $start_time = $goods['pay_time'];
    			}
    			
    			$rest_count = intval($count_ttt) - intval($total_count);
    			
    			$this->assign('rest_count',$rest_count);
    			$this->assign('default_search',true);
    			$this->assign('total_count',$total_count);
    			$this->assign('total_amount',$total_amount);
    			$this->assign('store_bargin',$store_bargin);
    			$this->assign('last_amount',$total_amount-$store_bargin);
    		}
    		
    		$this->assign("end_time",local_date('Y-m-d H:i:s',$end_time));
    		$this->assign('start_time',local_date('Y-m-d H:i:s',$start_time));
    		$this->assign('goods_list',$result);
    	}

    	$this->assign('bh_name',$behalf_info['bh_name']);
    	$this->assign('show_print',true);
    	$this->_assign_leftmenu('order_manage');
    	$this->_import_css_js('dtall');    	
    	$this->assign('delivery',$_POST['delivery']);
    	$this->assign('market_choice',$mk_ids?$mk_ids:array());
    	$this->assign('markets',$bh_markets);
    	$this->assign("deliverys",$bh_deliverys);
    	$this->display('behalf.goods.taker.list.html');
    	
    }



    function goods_accepted_list(){
		//拿货单商品
		$bh_id = $this->visitor->get('has_behalf');

		$result = $this->_goods_warehouse_mod->find(array(
			'conditions'=>"gwh.bh_id = {$bh_id}  AND ".
				db_create_in(array( BEHALF_GOODS_READY ),'gwh.goods_status').
				" AND order_alias.status=".ORDER_ACCEPTED,
			'fields'=>'gwh.*,order_alias.status,order_alias.total_quantity,order_stock.stock_code',
			'join'=>'belongs_to_order,belongs_to_orderstock',
			'order'=>'market_id ASC,floor_id ASC,store_address ASC'
		));
		$total_count = count($result);

		$this->assign('total_count' , $total_count);
		$this->_assign_leftmenu('stock_manage');
		$this->_import_css_js('dtall');
		$this->assign('goods_list',$result);
		$this->display('behalf.goods.accepted.list.html');
	}
    /**
     * 获取几天之内报缺货的商品
     * return goods_ids = array()
     */
    private function _lack_goods_bystores($days=1)
    {
        $time_result = cal_time_diff($days);
        
        $result = $this->_goods_warehouse_mod->find(array(
		    		'conditions'=>"bh_id = {$this->visitor->get('has_behalf')} AND taker_time >= '{$time_result['start_time']}' AND taker_time <= '{$time_result['end_time']}' AND ".
		    		db_create_in(array(BEHALF_GOODS_UNSALE,BEHALF_GOODS_UNFORMED),'goods_status'),
		    		'fields'=>'goods_id'		    		
		    	));
        
        $data = array();
        if($result)
        {
            foreach ($result as $v)
            {
               !in_array($v['goods_id'], $data) &&  $data[] = $v['goods_id'];
            }
        }
        
       return $data;
    }
    
    
    /**
     * 获取拿货单商品详情
     */
    function get_nhd_goods()
    {
    	$id = isset($_POST['id']) && $_POST['id'] ? trim($_POST['id']) : '';
    	if(!$id)
    	{
    		$this->json_error('nothing');
    		return;
    	}
    	
    	$taker_invertory = $this->_goods_taker_inventory_mod->get($id);
    	if($taker_invertory)
    	{
    	    $ids = explode(',', $taker_invertory['content']);
        	$goods_list = $this->_goods_warehouse_mod->find(array(
        	    'conditions'=>db_create_in($ids,'id')." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))
        	));
    	}
    	
    	$this->assign('show_print',true);
    	$this->_assign_leftmenu('order_manage');
    	$this->_import_css_js('dtall');
    	$this->assign('goods_list',$goods_list);
    	$this->display('behalf.goods.taker.list.goods_detail.html');
    	
    }
    
    /**
     * 保存拿货单信息
     */
    function save_nhd()
    {
    	
    	$data = array(
    		'bh_id'=>trim($_POST['behalf']),	
    		'goods_count'=>intval(trim($_POST['goods_count'])),	
    		'goods_amount'=>floatval(trim($_POST['goods_amount'])),	
    		'store_bargin'=>floatval(trim($_POST['store_bargin'])),	
    		'content'=>trim($_POST['content']),	
    		'mk_ids'=>trim($_POST['market_id']),	
    		'mk_names'=>trim($_POST['market_name']),	
    		'taker_id'=>trim($_POST['nhd_taker']),	
    		'name'=>html_filter(trim($_POST['nhd_name'])),	
    		'deal_time'=>0,	
    		'createtime'=>gmtime(),
    	    'search_time'=>trim($_POST['search_time']),
    	    'search_delivery'=>trim($_POST['search_delivery'])
    	);
    	
    	if($data['bh_id'] != $this->visitor->get('has_behalf'))
    	{
    		$this->json_error('feifacaozuo');
    		return;	
    	}
    	if($data['taker_id'])
    	{
    		$member_info = ms()->user->_local_get($data['taker_id']);
    		$data['taker_name'] = $member_info['user_name']." | ".$member_info['real_name'];
    	}
    	if(empty($data['search_delivery']))
    	{
    	    $data['search_delivery'] = Lang::get('all_deliveries');
    	}
    	else 
    	{
    	    $delivery_result = $this->_delivery_mod->get($data['search_delivery']);
    	    $data['search_delivery'] = $delivery_result['dl_name'];
    	}
    	
    	$result = $this->_goods_taker_inventory_mod->add($data);

		//更新代发商品的采购人员以及采购状态
		if($result){
			//更新未派单的商品
			$result_goods = $this->_goods_warehouse_mod->edit("id in ({$data['content']}) AND goods_status  in (".BEHALF_GOODS_PREPARED.",".BEHALF_GOODS_TOMORROW.")",array('taker_id'=>$data['taker_id'],'taker_time'=>time(),'goods_status'=>BEHALF_GOODS_DELIVERIES));

		}


    	if($result_goods)
    	{
    	    $nhd_info = $this->_goods_taker_inventory_mod->get($result);
    		$this->json_result(local_date("Y-m-d H:i:s",$nhd_info['createtime']),'caozuo_success');
    	}
    	else
    	{
    		$this->json_error('caozuo_fail');
    	}
    }
    
    /**
     * 统计多个拿货单
     */
    function stat_nhd()
    {
        $ids = explode(',',$_GET['ids']);
        if(!$ids)
        {
            $this->json_error('caozuo_fail');
            return;
        }
        
        $nhd_list = $this->_goods_taker_inventory_mod->find(array(
            'conditions'=>"bh_id='{$this->visitor->get('has_behalf')}' AND ".' visible = 1 AND id '.db_create_in($ids)
        ));
        if($nhd_list)
        {
            $goods_ids = array();
            
            foreach ($nhd_list as $key=>$nhd)
            {
                $temp_ids =  explode(',', $nhd['content']);
                $goods_ids = array_merge($goods_ids,$temp_ids);
            }
            $goods_ids = array_unique($goods_ids);//filter repeat values
            $goods_ids = array_filter($goods_ids);//filter null
            
            
            //$goods_ids = explode(',', $nhd['content']);
            $goods_list = $this->_goods_warehouse_mod->find(array(
                'conditions'=>db_create_in($goods_ids,'id')." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))
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
                    'count'=>0,//退货数量
                    'amount'=>0,
                    'discount'=>0
                )
            );
                 
            if($goods_list)
            {
                foreach ($goods_list as $gkey=>$goods)
                {
                    if(in_array($goods['goods_status'],array(BEHALF_GOODS_PREPARED)))
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
        
           //$nhd_list[$key]['goods_details'] = $goods_details;
           $this->json_result(array('total'=>count($goods_ids),'details'=>$goods_details),'success');
           return;
        }
        else 
        {
            $this->json_error('caozuo_empty');
            return;
        }
        
        
    }
   
    
    /**
     * 发货统计
     */
    function stat_shipped_order()
    {
    	$bh_id = $this->visitor->get('has_behalf');
    	$start_time = $_POST['query_time'] ? gmstr2time($_POST['query_time']):0;
		$end_time = $_POST['query_time_end'] ? gmstr2time($_POST['query_time_end']):0;

    	
    	if($start_time)
    	{
    		$order_list = $this->_order_mod->find(array(
    			'conditions'=>"order_alias.bh_id={$bh_id} AND order_alias.ship_time >= {$start_time} AND order_alias.ship_time < {$end_time} AND ".db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),'order_alias.status'),	
    			'join'=>'has_orderextm,has_orderthird'
    		));

    		$order_count = 0;//订单总数
    		$order_goods_amount = 0;//商品总金额
    		$goods_count = 0;//商品总件数
    		$order_amount = 0;//订单总金额
    		$lack_goods_count = 0;//缺货件数
    		$lack_goods_amount = 0;//缺货总金额
    		$back_order_count = 0;//退货订单数
    		$back_order_amount =0;//退货总金额
    		$stat_delivery = array();//快递数据
    		$total_fr = 0;//总分润
    		$goods_fr = 0;//商品分润
    		$reback_fr = 0;//返回的分润
    		if($order_list)
    		{
    			$deliverys = $this->_delivery_mod->find(); 
    			
    			$order_count = count($order_list);
    			$order_ids_arr = array();
    			foreach ($order_list as $order)
    			{
    				$order_goods_amount += floatval($order['goods_amount']);
    				$order_amount += floatval($order['order_amount']);
    				$order_ids_arr[] = $order['order_id'];
    				$total_fr += floatval($order['behalf_discount']);//订单分润
    				if(!in_array($order['dl_id'],array_keys($stat_delivery)))
    				{
    					$stat_delivery[$order['dl_id']] = array(
    						'count'=>1,
    						'name'=>$deliverys[$order['dl_id']]['dl_name']	
    					);
    				}
    				else 
    				{
    					$stat_delivery[$order['dl_id']]['count'] += 1;
    				}


    			}
    			
    			$order_refunds = $this->_orderrefund_mod->find(array(
    					'conditions'=>"receiver_id = {$bh_id} AND ".db_create_in($order_ids_arr,'order_id')." AND status=1 AND closed=0"
    			));
    			if($order_refunds)
    			{
    				$order_refund_ids = array();
    				foreach ($order_refunds as $orefund)
    				{
    					$order_refund_ids[] = $orefund['order_id'];
    					$back_order_amount += floatval($orefund['refund_amount']);
    				}
    				$back_order_count = count(array_unique($order_refund_ids));
    			}
    			
    			$order_goods = $this->_goods_warehouse_mod->find(array(
    			    'conditions'=>db_create_in($order_ids_arr,'order_id')." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))
    			));
    			if($order_goods)
    			{
    			    $goods_count = count($order_goods);
    			    foreach ($order_goods as $ogoods)
    			    {
    			        if(in_array($ogoods['goods_status'], array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE,BEHALF_GOODS_PREPARED)))
    			        {
    			            $lack_goods_count ++;
    			            $lack_goods_amount += floatval($ogoods['goods_price']);
    			        }
    			        else
    			        {
    			            $goods_fr += floatval($ogoods['behalf_to51_discount']);
    			            $reback_fr += floatval($ogoods['zwd51_tobehalf_discount']);
    			        }
    			    }
    			}
    			/*
    			$order_goods = $this->_ordergoods_mod->find(array(
    			    'conditions'=>db_create_in($order_ids_arr,'order_id')
    			));
    			if($order_goods)
    			{
    			    foreach ($order_goods as $ogoods)
    			    {
    			        $goods_count  += intval($ogoods['quantity']);
    			        if(!$ogoods['oos_value'])
    			        {
    			            $lack_goods_count += intval($ogoods['quantity']);
    			            $lack_goods_amount += intval($ogoods['quantity']) * floatval($ogoods['price']);
    			        }
    			        else 
    			        {
    			            $goods_fr += floatval($ogoods['behalf_to51_discount']);
    			            $reback_fr += floatval($ogoods['zwd51_tobehalf_discount']);
    			        }
    			    }
    			}
    			*/
    			
    			$this->assign('order_list',$order_list);
    		}

    		$real_amount = $order_amount - $lack_goods_amount - $back_order_amount;

    		$this->assign('lack_goods_count',$lack_goods_count);
    		$this->assign('lack_goods_amount',$lack_goods_amount);
    		$this->assign('back_order_count',$back_order_count);
    		$this->assign('back_order_amount',$back_order_amount);
    		$this->assign('real_amount',$real_amount);
    		$this->assign('deliverys',$stat_delivery);
    		
    		$this->assign("order_count",$order_count);
    		$this->assign("goods_count",$goods_count);
    		$this->assign("order_goods_amount",$order_goods_amount);
    		$this->assign("order_amount",$order_amount);
    		$this->assign('kd_fr',$total_fr-$goods_fr);
    		$this->assign('goods_fr',$goods_fr-$reback_fr);
    	}
    	
    	$this->_assign_leftmenu('order_manage');
    	$this->_import_css_js('dtall');
    	$this->assign('start_time',$_POST['query_time']);
		$this->assign('end_time',$_POST['query_time_end']);
    	$this->display('behalf.stat.order.shipped.html');
    }
    
    /**
     * 入库统计
     */
    function stat_enter_warehouse()
    {  
   		$bh_id = $this->visitor->get('has_behalf');
    	$bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);
    	$bh_deliverys = $this->_behalf_mod->getRelatedData('has_delivery',$bh_id);
    	
    	$behalf_info =$this->_behalf_mod->get($bh_id);
    	if(IS_POST)
    	{
    		$start_time = $_POST['query_time'] ? gmstr2time($_POST['query_time']) : 0;
    		$end_time = $_POST['query_endtime'] ? gmstr2time($_POST['query_endtime']) : 0;
    	
    		$mk_ids = $_POST['market'];//市场id
    		$dl_id = $_POST['delivery'] ? intval($_POST['delivery']) : 0;
    		$condition_dl = $dl_id > 0 ?" AND delivery_id = '{$dl_id}' ":"";
    	
    		foreach ($bh_markets as $mark)
    		{
    			foreach ($mk_ids as $mkid)
    			{
    				if($mkid == $mark['mk_id'])
    				{
    					$market_names[] = $mark['mk_name'];
    				}
    			}
    		}
    	
    		if(!empty($mk_ids) && $start_time)
    		{
    			$goods_count = 0; //拿货件数
    			$goods_amount =0 ;//拿货金额
    			$store_bargin = 0;//档口优惠金额
    			$order_ids = array();//涉及订单数
    			$members = array();
    			$goods_list = $this->_goods_warehouse_mod->find(array(
    					'conditions'=>"bh_id = {$bh_id} AND taker_time >= '{$start_time}' AND taker_time < '{$end_time}' {$condition_dl} AND "
    							.db_create_in(array(BEHALF_GOODS_READY),'goods_status')." AND ".db_create_in($mk_ids,'market_id'),
    							'order'=>'taker_time DESC'
    							));
    			if($goods_list)
	    		{
	    			$goods_count = count($goods_list);
	    			foreach ($goods_list as $goods)
	    			{
	    				$goods_amount += floatval($goods['goods_price']);
	    				$store_bargin += floatval($goods['store_bargin']);
	    				if(!in_array($goods['order_id'], $order_ids))
	    				{
	    					$order_ids[] = $goods['order_id'];
	    				}
	    				if(!in_array($goods['taker_id'], $members))
	    				{
	    					$members[$goods['taker_id']] = array('user_id'=>$goods['taker_id']);
	    				}
	    			}
	    			
	    			foreach ($members as $mkey=>$m)
	    			{
	    				$member_info = ms()->user->_local_get($m['user_id']);
	    				$members[$mkey]['user_name'] = $member_info['user_name'];
	    			}
	    			
	    			foreach ($goods_list as $gkey=>$g)
	    			{
	    				$goods_list[$gkey]['taker_name'] = $members[$g['taker_id']]['user_name'];
	    			}
	    		}
	    		
	    		$this->assign("goods_count",$goods_count);
	    		$this->assign("goods_amount",$goods_amount);
	    		$this->assign("store_bargin",$store_bargin);
	    		$this->assign("last_amount",$goods_amount - $store_bargin);
	    		$this->assign("order_count",count($order_ids));
	    		$this->assign('goods_list',$goods_list);
    		}
    		
    		$this->assign("end_time",$_POST['query_endtime']);
    		$this->assign('start_time',$_POST['query_time']);
    	}
    	
    	$this->_import_css_js('dt');
   		if($this->visitor->get('has_behalf'))
    	{
    		$this->_assign_leftmenu('order_manage');
    	}else 
    	{
    		$this->_assign_leftmenu('dashboard');
    	}
    	
    	$this->assign('delivery',$_POST['delivery']);
    	$this->assign('market_choice',$mk_ids?$mk_ids:array());
    	$this->assign('markets',$bh_markets);
    	$this->assign("deliverys",$bh_deliverys);
    	$this->display('behalf.stat.warehouse.enter.html');
    }
    /**
     * 订单按月统计
     */
    function stat_order_bymonth()
    {        
        
        if(IS_POST)
        {            
            $stat_month = $_POST['stat_month'];
           
            $start_end_date = getthemonth($stat_month);
            //dump(date('Y-m-d H:i:s',$start_end_date[0])."#".date('Y-m-d H:i:s',$start_end_date[1]));
            
            $order_ids = array();
            
            $orders = $this->_order_mod->find(array(
               'conditions'=>"bh_id = ".$this->visitor->get('has_behalf')." and status = ".ORDER_FINISHED." and add_time >={$start_end_date[0]} and add_time <= {$start_end_date[1]}",
               'fields'=>'order_id,goods_amount,order_amount'
            ));            
           
            $ret_data = array(
              'order_amount_total'=>0,  //订单总金额
              'goods_amount_total'=>0,  //订单商品总金额
              'order_refund_amount'=>0,  //订单退款费
              'order_add_mail_fee'=>0,  //订单补邮费
              'compensation_fee'=>0,   //代发主动赔付款
              'counts'=>0,  //订单数
              'final_amount'=>0 //订单总金额 - 订单商品总金额 - 订单退款费 + 订单补邮费
            );
            
            if($orders)
            {
                
                $ret_data['counts'] = count($orders);
                
                foreach ($orders as $order)
                {
                    $order_ids[] = $order['order_id'];
                    $ret_data['order_amount_total'] += $order['order_amount'];
                    $ret_data['goods_amount_total'] += $order['goods_amount'];
                }
               
                $refunds = $this->_orderrefund_mod->find(array(
                   'conditions'=>db_create_in($order_ids,'order_id')." and receiver_id ={$this->visitor->get('has_behalf')} and type=1 and closed=0 and status=1",
                   'fields'=>'apply_amount'
                ));
                if($refunds)
                {
                    foreach ($refunds as $refund)
                    {
                        $ret_data['order_refund_amount'] += $refund['apply_amount'];
                    }
                }
                $mails = $this->_orderrefund_mod->find(array(
                   'conditions'=>db_create_in($order_ids,'order_id')." and sender_id ={$this->visitor->get('has_behalf')} and type=2 and closed=0 and status=1",
                   'fields'=>'apply_amount'
                ));
                if($mails)
                {
                    foreach ($mails as $mail)
                    {
                        $ret_data['order_add_mail_fee'] += $mail['apply_amount'];
                    }
                }
                $mod_ordercompensationbehalf = & m('ordercompensationbehalf');
                $compensations = $mod_ordercompensationbehalf->find(array(
                   'conditions'=>db_create_in($order_ids,'order_id')." and bh_id={$this->visitor->get('has_behalf')} ",
                   'fields'=>'pay_amount'
                ));
                if($compensations)
                {
                    foreach ($compensations as $comp)
                    {
                        $ret_data['compensation_fee'] += $comp['pay_amount'];
                    }
                }
                
            }
            
            $ret_data['final_amount'] = $ret_data['order_amount_total'] - $ret_data['goods_amount_total'] - $ret_data['order_refund_amount'] - $ret_data['compensation_fee'] + $ret_data['order_add_mail_fee'] ;
            
            $this->assign("rets",$ret_data);
            $this->assign('stat_month',$_POST['stat_month']);
        }
        
        
        
        $this->_import_css_js('dt');
        if($this->visitor->get('has_behalf'))
        {
            $this->_assign_leftmenu('order_manage');
        }else
        {
            $this->_assign_leftmenu('dashboard');
        }
        
        $this->display('behalf.stat.order.bymonth.html');
        
    }
    

    /******************退货管理********************/
    /*
     * 包裹清单
     * */

    function baoguo_list()
    {
        if(IS_POST)
        {

            $baoguo_no = trim($_POST['baoguo_no']);
            $storage_no = trim($_POST['kuwei_no']);
            //快递名称
			switch ($_POST['delivery_name']){
				case 0:
					$delivery_name='中通';
					break;
				case 1:
                    $delivery_name='圆通';
                    break;
                case 2:
                    $delivery_name='申通';
                    break;
                case 3:
                    $delivery_name='韵达';
                    break;
                case 4:
                    $delivery_name='百世汇通';
                    break;
                case 5:
                    $delivery_name='天天';
                    break;
                case 6:
                    $delivery_name='顺丰';
                    break;
                case 7:
                    $delivery_name='全峰';
                    break;
                default:
                    $delivery_name='其它';
                    break;
			}

			if(empty($baoguo_no) || empty($storage_no) ){
                $this->show_warning('库位号和包裹号不能为空值');
                return;
			}



			//是否已经申请退货
			$is_reply = 0;
            $is_apply_re = $this->is_applay_refunds($baoguo_no);
			if(!empty($is_apply_re)){
				$is_reply = 1;
			}
            $data['bg_num'] = $baoguo_no;
			$data['dev_name'] = $delivery_name;
			$data['storage_no'] =$storage_no;
			$data['is_apply_th'] = $is_reply;
			$data['add_time'] = time();
            $affect_id = $this->_tuihuobaoguo_mod->add($data);
            if($affect_id)
            {
                $this->show_message('添加成功',
                    'back_list', 'index.php?module=behalf&act=baoguo_list');
            }else{
                $this->show_warning('添加失败');
			}

        }else{

        $this->_import_css_js ('dtall');
        if($this->visitor->get('has_behalf'))
        {
            $this->_assign_leftmenu('order_manage');
        }else
        {
            $this->_assign_leftmenu('dashboard');
        }

        $this->display("behalf.tuihuo.baoguo.list.html");
        }
    }

    /*
     * 包裹列表
     * */
    function get_baoguo_list(){
       // $bh_id = $this->_get_bh_id();
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序

        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                //case 0:$orderSql = " tbg_id ".$order_dir;break;
                case 1:$orderSql = " bg_num ".$order_dir;break;
                case 2:$orderSql = " dev_name ".$order_dir;break;
                case 3:$orderSql = " add_time ".$order_dir;break;
                case 4:$orderSql = " storage_no ".$order_dir;break;
                case 5:$orderSql = " is_apply_th ".$order_dir;break;
                default:$orderSql = ' add_time DESC';
            }
        }

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $goods_list = array();
        if(strlen($search) == 0){
			$goods_list =$this->_tuihuobaoguo_mod->find(array(
				'conditions'=>"1='1'",
				'count'=>true,
				'order'=>$orderSql." ,add_time DESC",
				'limit'=>"{$start},{$page_per}"
			));
			$recordsTotal = $recordsFiltered = $this->_tuihuobaoguo_mod->getCount();
        }
        if(strlen($search) > 0)
        {

            $timestamp = strtotime($search);  // 将日期转换成时间戳
            $one_day = strtotime(date('Y-m-d',$timestamp));
            $one_second_before_next_day = strtotime(date('Y-m-d',$timestamp)) + 86399;
           /* if(trim($search)=='未申请' || trim($search)=='未'){
                $search1=0;
			}elseif(trim($search)=='已申请'){
                $search1=1;
			}
			$sql='';
			if(isset($search1)){
				$sql = " AND (dev_name like '%".$search."%' OR storage_no like '%".$search."%' OR  is_apply_th =".$search1." OR ( add_time between ".$one_day." AND ".$one_second_before_next_day."))";
			}else{*/
                $sql = " AND (dev_name like '%".$search."%' OR storage_no like '%".$search."%' OR   ( add_time between ".$one_day." AND ".$one_second_before_next_day."))";
			//}
            $goods_list =$this->_tuihuobaoguo_mod->find(array(
                'conditions'=>"1='1'".$sql,
                'count'=>true,
                'order'=>$orderSql." ,add_time DESC",
                'limit'=>"{$start},{$page_per}"

            ));

            $recordsFiltered = $this->_tuihuobaoguo_mod->getCount();
        }
        foreach ($goods_list as $k=>$value){
            $model_order_refund = &m('orderrefund');
            $refundstatus1 = $model_order_refund->get('invoice_no = "'.$value[bg_num].'" and warehouse_status=0');
            $refundstatus2 = $model_order_refund->get('invoice_no = "'.$value[bg_num].'" and warehouse_status>0');
            if(!empty($refundstatus1) && !empty($refundstatus2)){//一个包裹有多件衣服，没处理完
                $goods_list[$k]['is_deal']='处理中';
			}elseif(!empty($refundstatus2)){
                $goods_list[$k]['is_deal']='已处理';
			}else{
                $goods_list[$k]['is_deal']='未处理';
			}


            $goods_list[$k]['add_time'] = date('Y-m-d H:i:s',$value['add_time']);
            if($value['is_apply_th']==0){
                $goods_list[$k]['is_apply_th']='未申请';
            }else{
                $goods_list[$k]['is_apply_th']='已申请';
            }
        }
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($goods_list)));
    }




		//删除包裹列表内容
		function del_baoguo(){
            $tbg_id = $_REQUEST['tbg_id'];

            if($this->_tuihuobaoguo_mod -> drop("tbg_id=".$tbg_id)){
            	echo '1';
            	//$this->show_message('删除成功','back_list', 'index.php?module=behalf&act=baoguo_list');
			}else{
            	echo '0';
            	//$this->show_warning('删除失败','back_list', 'index.php?module=behalf&act=baoguo_list');
			}
            //$this->display("behalf.tuihuo.baoguo.list.html");
		}





	/*申请退货列表*/
    function th_apply_List()
    {
        $bh_id = $this->visitor->get('has_behalf');
        //获取拿货市场，并按代发设置的顺序排序
        $bh_markets = $this->_behalf_mod->getRelatedData('has_market', $bh_id);
        if ($bh_markets) {
            $sort_arr = array();//用于多维排序
            foreach ($bh_markets as $k => $v) {
                $sort_arr[] = $v['sort_ord'];
            }
            array_multisort($sort_arr, SORT_ASC, $bh_markets);
        }

        //获取关联快递
        $bh_deliverys = $this->_behalf_mod->getRelatedData('has_delivery', $bh_id);

        //代发信息
        $behalf_info = $this->_behalf_mod->get($bh_id);
        //获取代发退款申请的订单id（有退款申请的订单先处理后拿货）
        $refund_order_ids = $this->get_all_refunds_orders();
        $conditions_refund = "";
            $conditions_refund = " AND order_id  " . db_create_in($refund_order_ids);

        if (IS_POST) {

        } else //默认结果
        {
            $model_goods_warehouse = &m('goodswarehouse');
            $model_order_refund = &m('orderrefund');
            /* $refund_goods = $model_order_refund->find('status=0 AND type=1 AND goods_ids_flag=1');
             if($refund_goods){
                 $conditions_refund = $this->_get_refund_condition();
             }*/

            $refund_goods = $model_order_refund->find(array(
                'conditions' => '1=1 ' . $conditions_refund,
                'fields' => 'id,order_id,order_sn,refund_reason,create_time,operate_time,dl_name,invoice_no,warehouse_status,goods_ids',
                //'join'=>'belongs_to_order',
                'order' => 'create_time desc',
                'count' => true
            ));

            if ($refund_goods) {
                foreach ($refund_goods as $gkey => $goods) {
                    $refund_goods_details = $model_goods_warehouse->find(array(
                        'conditions' => 'gwh.order_id=' . $goods['order_id'] .' AND gwh.id in ('.$goods[goods_ids].')',
                        'fields' => 'gwh.*',
                    ));
                    if (!empty($refund_goods_details)) {

                        foreach ($refund_goods_details as $k => $details) {
                            $refund_goods_n[$gkey][$k]['order_id'] = $goods['order_id'];
                            $refund_goods_n[$gkey][$k]['order_sn'] = $goods['order_sn'];
                            $refund_goods_n[$gkey][$k]['refund_reason'] = $goods['refund_reason'];
                            $refund_goods_n[$gkey][$k]['create_time'] = date('Y-m-d H:i:s',$goods['create_time']+8*60*60);
                            $refund_goods_n[$gkey][$k]['operate_time'] = $goods['operate_time']==0?'--':date('Y-m-d H:i:s',$goods['operate_time']+8*60*60);
                            $refund_goods_n[$gkey][$k]['dl_name'] = $goods['dl_name'];
                            $refund_goods_n[$gkey][$k]['invoice_no'] = $goods['invoice_no'];
                            $refund_goods_n[$gkey][$k]['or_id'] = $goods['id'];
                           // $refund_goods_n[$gkey][$k]['warehouse_status'] = $goods['warehouse_status']==0?0:$goods['warehouse_status']==1?'已接受':'已拒绝';

							//判断该商品是否已经处理
                            $model_order_refund = &m('tuihuobatchgoods');
                            $status = $model_order_refund->get('th_goods_sn = "'.$details['goods_no'].'"');
                            if($status['warehouse_status']==0){
                                $refund_goods_n[$gkey][$k]['warehouse_status'] = 0;
							}elseif($status['warehouse_status']==1){
                                $refund_goods_n[$gkey][$k]['warehouse_status'] = '已接受';
							}else{
                                $refund_goods_n[$gkey][$k]['warehouse_status'] = '已拒绝';
							}



                            $refund_goods_n[$gkey][$k]['gw_id'] = $details['id'];
                            $refund_goods_n[$gkey][$k]['goods_specification'] = $details['goods_specification'];
                            //字符串中有颜色分类、主要颜色、尺码的给去掉这几个字

                            if(strpos($refund_goods_n[$gkey][$k]['goods_specification'],"分类")){
                           	 $refund_goods_n[$gkey][$k]['goods_specification']=str_replace('颜色分类:','',$refund_goods_n[$gkey][$k]['goods_specification']);
                            }
                            if(strpos($refund_goods_n[$gkey][$k]['goods_specification'],"要颜")){
                                $refund_goods_n[$gkey][$k]['goods_specification']=str_replace('主要颜色:','',$refund_goods_n[$gkey][$k]['goods_specification']);
                            }
                            if(strpos($refund_goods_n[$gkey][$k]['goods_specification'],"尺码:")){
                                $refund_goods_n[$gkey][$k]['goods_specification']=str_replace('尺码:','-',$refund_goods_n[$gkey][$k]['goods_specification']);
                            }

                            $refund_goods_n[$gkey][$k]['goods_price'] = $details['goods_price'];
                            $refund_goods_n[$gkey][$k]['goods_name'] = $details['goods_name'];
                            $refund_goods_n[$gkey][$k]['store_address'] = $details['store_address'];
                            $refund_goods_n[$gkey][$k]['market_name'] = $details['market_name'];
                            $refund_goods_n[$gkey][$k]['floor_name'] = $details['floor_name'];
                            $refund_goods_n[$gkey][$k]['goods_sku'] = $details['goods_sku'];
                            $refund_goods_n[$gkey][$k]['goods_no'] = $details['goods_no'];
                            $refund_goods_n[$gkey][$k]['store_bargin'] = $details['store_bargin'];
                            $refund_goods_n[$gkey][$k]['goods_image'] = $details['goods_image'];
                            $refund_goods_n[$gkey][$k]['goods_attr_value'] = $this->_Attrvalue2Pinyin($details['goods_attr_value']);
                            //拿货人
                            $refund_goods_n[$gkey][$k]['taker_name'] = $this->_taker_name($details['taker_id']);
                            $refund_goods_n[$gkey][$k]['taker_id'] = $details['taker_id'];
                            $refund_goods_n[$gkey][$k]['goods_id'] = $details['goods_id'];
                            $refund_goods_n[$gkey][$k]['goods_status'] = $details['goods_status'];
                        }
                    }
                   
                    $count_ttt = $model_goods_warehouse->getCount();
                    $total_count = count($refund_goods);//商品件数
                    $total_amount = 0; //商品总金额
                    $store_bargin = 0;//店家优惠
                    $start_time = gmtime();
                    $end_time = 0;//找出最小时间和最大时间

                    $rest_count = intval($count_ttt) - intval($total_count);
                    $this->assign('rest_count', $rest_count);
                    $this->assign('default_search', true);
                    $this->assign('total_count', $total_count);
                    $this->assign('total_amount', $total_amount);
                    $this->assign('store_bargin', $store_bargin);
                    $this->assign('last_amount', $total_amount - $store_bargin);
                }

            }
          /* echo "<pre/>";
print_r($refund_goods_n);exit;*/
            $this->assign('goods_list', $refund_goods_n);

            $this->assign('bh_name', $behalf_info['bh_name']);
            $this->assign('show_print', true);
            $this->_assign_leftmenu('order_manage');
            $this->_import_css_js('dtall');

            $this->display('behalf.goods.tuihuo.apply.list.html');

        }
    }
    protected  function get_th_tags_for_print(){
    	$order_id = $_REQUEST['or_id'];
    	$goods_wh_id = $_REQUEST['gw_id'];
        $model_goods_warehouse = &m('goodswarehouse');
        $model_order_refund = &m('orderrefund');

        $goods = $model_order_refund->get('id='.$order_id);
        $details = $model_goods_warehouse->get('id='.$goods_wh_id);

            $list['order_id'] = $goods['order_id'];
            $list['order_sn'] = $goods['order_sn'];
            $list['refund_reason'] = $goods['refund_reason'];
            $list['create_time'] = date('Y-m-d H:i:s', $goods['create_time'] + 8 * 60 * 60);
            $list['dl_name'] = $goods['dl_name'];
            $list['invoice_no'] = $goods['invoice_no'];
            $list['or_id'] = $goods['id'];

            $list['gw_id'] = $details['id'];
        	$list['goods_price'] = $details['goods_price'];
            $list['goods_specification'] = $details['goods_specification'];
            $list['goods_price'] = $details['goods_price'];
            $list['goods_name'] = $details['goods_name'];
            $list['store_address'] = $details['store_address'];
            $list['market_name'] = $details['market_name'];
            $list['floor_name'] = $details['floor_name'];
            $list['goods_sku'] = $details['goods_sku'];
            $list['goods_no'] = $details['goods_no'];
            $list['store_bargin'] = $details['store_bargin'];
            $list['goods_image'] = $details['goods_image'];
            $list['goods_attr_value'] = $this->_Attrvalue2Pinyin($details['goods_attr_value']);
            //拿货人
            $list['taker_name'] = $this->_taker_name($details['taker_id']);
            $list['taker_id'] = $details['taker_id'];
        echo ecm_json_encode($list);
	}

    /*处理是否接收退货*/
    protected function deal_refund_goods(){
				$data['warehouse_status'] = $_REQUEST['status'];//状态，2拒绝，1接受
				$reason = $_REQUEST['reason'];//原因
				$other_reason = $_REQUEST['other_reason'];//其它原因
				$goods_no = $_REQUEST['goods_no'];
				$data['warehouse_status']==2?$data['refuse_reason']=$reason:$data['accept_reason']=$reason;

				if($data['refuse_reason']=='其它' && !empty($other_reason)){
                    $data['refuse_reason'] = $other_reason;
				}

				if($data['accept_reason']=='其它' && !empty($other_reason)){
					$data['accept_reason'] = $other_reason;
				}

		$data['operate_time'] = gmtime();//操作时间
        $model_tuihuo_batch_goods = & m('tuihuobatchgoods');
        $status = '0';
        if($model_tuihuo_batch_goods->edit('th_goods_sn=' . $goods_no, $data)){
        	$status = 1;
		}else{
            $status = 2;
		}
        echo json_encode($status);
    }
    /*退货批次列表*/
    function th_batch_list(){
        if(isset($_REQUEST['batch_id']))//处理输入的批次商品
        {
            $batch_id = $_REQUEST['batch_id'];
            $goods_nos = $_REQUEST['good_nos'];

            if(empty($batch_id) || empty($goods_nos) ){
                $this->show_warning('批次号或者货号不能为空值');
                return;
            }
			$time = time();
			//插入表

            $market=array();
            foreach ($goods_nos as $k=>$goods_no){
            	if(!empty($goods_no)) {
            		//商品详情，根据goods_no在godos_warehouse获取市场
					$goods_info =  $this->_goods_warehouse_mod->get(array('conditions'=>"goods_no='{$goods_no}'"));
					//$market =  $goods_info['market_name'].",".$market;
                    $market[]=$goods_info['market_name'];
                    $data['th_batch_id'] = $batch_id;
                    $data['gwh_id'] = $goods_info['id'];
                    $data['th_goods_sn'] = $goods_no;
                    $data['th_in_time'] = $time;
                    $data['order_id'] = $goods_info['order_id'];
                   $th_id = $this->_tuihuobatchgoods_mod->add($data);
                }
			}
			$data=array();
            $data['batch_id'] = $batch_id;
            $data['num'] = count($market);
			//数组去重
			$market = array_unique($market);
            $data['markets']= implode(',',$market);
			//加入统计表
			if($data['num']){
                $data['th_user_id'] = 0;
				$new = & m('financialstatistics');
                $new->refund_increase( $data['num']);
            $insert_id = $this->_tuihuobatchgoodstotal_mod->add($data);
            if($insert_id)
            {
                $this->show_message('添加成功','back_list', 'index.php?module=behalf&act=th_batch_list');
            }else{
                $this->show_warning('添加失败');
            }
            }
        }else{

            $this->_assign_leftmenu('setting');
            $this->_import_css_js ('dt');
            if($this->visitor->get('has_behalf'))
            {
                $this->_assign_leftmenu('order_manage');
            }else
            {
                $this->_assign_leftmenu('dashboard');
            }

            $this->display("behalf.tuihuo.batch.list.html");
        }

    }

		//拿货员列表
		function goods_taker_list(){
			// $model_member =& m('member');
			// $members = $model_member->find(array(
			// 	'conditions'=>'behalf_goods_taker>0',
			// 	 'fields' => 'user_id,real_name'
			// ));

			// 新的方式获取拿货员 zjh

        	$members = $this->_get_spec_func_employees(BEHALF_TAKE_RETURN_GOODS);

			echo ecm_json_encode($members);
		}

		//分配退货员--修改退货批次统计表的th_user_id
		function  dist_taker_batch(){
			if(IS_POST){
				$data['batch_id'] = $_REQUEST['good_nos'];
                $data['th_user_id'] = $_REQUEST['th_user_id'];
                $this->_tuihuobatchgoods_mod->edit('th_batch_id ='. $data['batch_id'],array('th_user_id'=>$data['th_user_id']));
                $this->_tuihuobatchgoodstotal_mod->edit('batch_id ='. $data['batch_id'],array('th_user_id'=>$data['th_user_id']));
                if($this->_tuihuobatchgoodstotal_mod->has_error())
                {
                    $this->json_error('update failed!');
                    return;
                }else{
                	//更改这批商品状态，改为退货已派单th_status=5
                    $this->_tuihuobatchgoods_mod->edit('th_batch_id ='. $data['batch_id'],array('th_status'=>5));
                    $this->json_result(1,'分配成功');
				}

            }

		}


		function th_batch_list_info(){
		//退货商品批次统计

			$start = intval($_GET['start']);
			$page_per = intval($_GET['length']);
			//search
			$search = trim($_GET['search']['value']);
			//order
			$order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
			$order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序

			//拼接排序sql
			$orderSql = "";
			if(isset($order_column)){
				$i = intval($order_column);
				switch($i){
					//case 0:$orderSql = " tbg_id ".$order_dir;break;
					case 1:$orderSql = " s_id ".$order_dir;break;
					case 2:$orderSql = " batch_id ".$order_dir;break;
					case 3:$orderSql = " num ".$order_dir;break;
					case 4:$orderSql = " markets ".$order_dir;break;
					case 5:$orderSql = " th_user_name ".$order_dir;break;
					default:$orderSql = 's_id DESC';
				}
			}

			$recordsTotal = 0;
			$recordsFiltered = 0;
			$goods_list = array();

			$goods_list =$this->_tuihuobatchgoodstotal_mod->find(array(
				'conditions'=>"1='1'",
				'count'=>true,
				'order'=>$orderSql." ,s_id DESC",
				'limit'=>"{$start},{$page_per}"
			));
			$recordsTotal = $recordsFiltered = $this->_tuihuobatchgoodstotal_mod->getCount();
			if(strlen($search) > 0)
			{
				$goods_list =$this->_tuihuobatchgoodstotal_mod->find(array(
					'conditions'=>"1='1' AND (batch_id like '%".$search."%' or num like '%".$search."%' or markets like '%".$search."%') ",
					'count'=>true,
					'order'=>$orderSql." ,s_id DESC",
					'limit'=>"{$start},{$page_per}"

				));

				$recordsFiltered = $this->_tuihuobatchgoodstotal_mod->getCount();
			}
			foreach ($goods_list as $key=>$val){

				if($val['th_user_id']==0){
					$goods_list[$key]['real_name'] ='未分配';
				}else{
					$list = ms()->user->_local_get($val['th_user_id']);
					$goods_list[$key]['real_name'] = $list['real_name'];
				}

			}/*echo "<pre>";
			print_r($goods_list);exit;*/
			echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($goods_list)));
		}


		/*退货明细*/
    function th_detail_list(){
        if(IS_POST)
        {
        }else{

            $this->_assign_leftmenu('setting');
            $this->_import_css_js ('dtall');
            if($this->visitor->get('has_behalf'))
            {
                $this->_assign_leftmenu('order_manage');
            }else
            {
                $this->_assign_leftmenu('dashboard');
            }

            $this->display("behalf.tuihuo.detail.list.html");
        }

    }

  	/*退货明细详情*/
    function th_detail_list_info(){
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序

        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                //case 0:$orderSql = " tbg_id ".$order_dir;break;
                //case 0:$orderSql = " th_id ".$order_dir;break;
                case 1:$orderSql = " th_in_time ".$order_dir;break;
               // case 2:$orderSql = " order_sn ".$order_dir;break;
                case 3:$orderSql = " th_goods_sn ".$order_dir;break;
                case 4:$orderSql = " th_batch_id ".$order_dir;break;
                //case 5:$orderSql = " store_address ".$order_dir;break;
                //case 6:$orderSql = " goods_sku ".$order_dir;break;
                //case 7:$orderSql = " goods_specification ".$order_dir;break;
                case 8:$orderSql = " th_status ".$order_dir;break;
               // case 9:$orderSql = " goods_price ".$order_dir;break;
                case 10:$orderSql = " th_price ".$order_dir;break;
               // case 11:$orderSql = " user_name ".$order_dir;break;
                case 12:$orderSql = " remark ".$order_dir;break;
                default:$orderSql = 'th_in_time DESC';
            }
        }

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $goods_list = array();

        $goods_list =$this->_tuihuobatchgoods_mod->find(array(
            'conditions'=>"1='1'",
            'count'=>true,
            'order'=>$orderSql,
            'limit'=>"{$start},{$page_per}"
        ));
        $recordsTotal = $recordsFiltered = $this->_tuihuobatchgoods_mod->getCount();
        if(strlen($search) > 0)
        {

            $timestamp = strtotime($search);  // 将日期转换成时间戳
            $one_day = strtotime(date('Y-m-d',$timestamp));
            $one_second_before_next_day = strtotime(date('Y-m-d',$timestamp)) + 86399;

            $sql = " AND (dev_name like '%".$search."%' OR storage_no like '%".$search."%' OR   ( add_time between ".$one_day." AND ".$one_second_before_next_day."))";


            $goods_list =$this->_tuihuobatchgoods_mod->find(array(
                'conditions'=>"1='1' AND (th_batch_id like '%".$search."%' or th_goods_sn like '%".$search."%' or th_price like '%".$search."%' or th_id like '%".$search."%' OR   ( th_in_time between ".$one_day." AND ".$one_second_before_next_day.") or remark like '%".$search."%')",
                'count'=>true,
                'order'=>$orderSql." ,th_id DESC",
                'limit'=>"{$start},{$page_per}"

            ));

            $recordsFiltered = $this->_tuihuobatchgoods_mod->getCount();
        }
        foreach ($goods_list as $key=>$val){
            $goods_list[$key]['th_in_time'] = date('Y-m-d H:i:s',$val['th_in_time']);
           // $goods_list[$key]['th_time'] = date('Y-m-d H:i:s',$val['th_time']);
            switch ($val['th_status']){
				case 5:
                    $goods_list[$key]['th_status'] = '未处理';
                    break;
                case 10:
                    $goods_list[$key]['th_status'] = '退货完成';
                    break;
                case 20:
                    $goods_list[$key]['th_status'] = '未处理';
                    break;
                case 40:
                    $goods_list[$key]['th_status'] = '退货失败';
                    break;
				default:
                    $goods_list[$key]['th_status'] = '未处理';
                    break;
			}

            $goods_list[$key]['th_time'] = empty($val['th_time'])?'--':date('Y-m-d H:i:s',$val['th_time']);
            if($val['th_user_id']==0){
                $goods_list[$key]['user_name'] ='未分配';
            }else{
                $goods_list[$key]['user_name'] =$this->_taker_name($val['th_user_id']);
            }


            //根据th_goods_sn在warehouse表里获取商品信息
            $goods_info = $this->_goods_warehouse_mod -> get(array(
                'conditions'    => 'goods_no = ' . $val['th_goods_sn'],
            ));


            $goods_list[$key]['order_sn'] = $goods_info['order_sn'];

            $goods_list[$key]['store_address'] = $goods_info['store_address'];
            $goods_list[$key]['goods_sku'] = $goods_info['goods_sku'];
			$goods_list[$key]['goods_specification'] = $goods_info['goods_specification'];
            $goods_list[$key]['goods_price'] = $goods_info['goods_price'];
        }
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($goods_list)));
    }


    function refund_backed_list_ajax(){
		$start = intval($_GET['start']);
		$page_per = intval($_GET['length']);

	//	$tuihuobatchgoods = & m('tuihuobatchgoods');
		$tuihuo_result = $this->_tuihuobatchgoods_mod->find(array(
			'conditions' => db_create_in(array( BEHALF_REFUND_FINISHED ) , 'th_status'),
			'join'	=> 'belongs_to_warehouse,has_claimgoods',
			'order' => 'th_time desc',
			'limit' => "{$start} , {$page_per}"
		));


		foreach($tuihuo_result as $k=> & $value){
			$order_info = reset($this->_tuihuobatchgoods_mod->getRelatedData('belongs_to_order' , $value['gwh_id']));
			$claim_info = reset($this->_tuihuobatchgoods_mod->getRelatedData('belongs_to_claim_order' , $value['gwh_id']));
			$value['back_type'] = '无理由';
			$value['th_time'] = date('Y-m-d H:i:s' ,$value['th_time'] );
			$value['th_in_time'] = date('Y-m-d H:i:s' ,$value['th_in_time'] );
			$value['order_sn'] = $order_info['order_sn'];
			$value['invoice_no'] = $order_info['invoice_no'];
			$value['back_behalf_fee'] =   BEHALF_BACK_FEE ;
			$value['goods_amount'] = $value['goods_price']  ;
			$value['shipping_fee'] = 0;
			$value['back_shipping_fee'] = 0;
			//	$value['behalf_fee'] = !empty($claim_info) ? $order_info['behalf_fee'] / $order_info['total_quantity'] : 0;
			$value['shipping_fee'] = $claim_info['shipping_fee'] ? $claim_info['shipping_fee'] : 0;
			$value['back_shipping_fee'] = $claim_info['back_shipping_fee'] ? $claim_info['back_shipping_fee'] : 0;
			$value['total_price'] =  $value['goods_price'] - BEHALF_BACK_FEE;

			if($claim_info){
				$value['goods_amount'] = $value['goods_fee'];
				$value['back_type'] =  '有理由';
				$value['back_behalf_fee'] = 0;
				$value['shipping_fee'] = $claim_info['shipping_fee'] / $claim_info['total_quantity'];
				$value['back_shipping_fee'] = $claim_info['back_shipping_fee'] / $claim_info['total_quantity'];
				$value['total_price'] = $claim_info['total_price'];
			}



			$value['check_fee'] = 0;
			$value['goods_status'] = goods_status($value['goods_status']);
		}
		echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>count($tuihuo_result),'recordsFiltered'=>count($tuihuo_result),'data'=>array_values($tuihuo_result)));

	}



    /*退货执行统计表*/
    function th_execute_list(){
        if(IS_POST)
        {
        }else{

            $this->_assign_leftmenu('setting');
            $this->_import_css_js ('dt');
            if($this->visitor->get('has_behalf'))
            {
                $this->_assign_leftmenu('order_manage');
            }else
            {
                $this->_assign_leftmenu('dashboard');
            }

            $this->display("behalf.tuihuo.execute.list.html");
        }

    }

    /*退货明细详情*/
    function th_execute_list_info(){
        //退货商品批次统计

        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序

        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                //case 0:$orderSql = " tbg_id ".$order_dir;break;
                //case 0:$orderSql = " s_id ".$order_dir;break;
                case 1:$orderSql = " batch_id ".$order_dir;break;
                case 2:$orderSql = " num ".$order_dir;break;
                default:$orderSql = 'batch_id DESC';
            }
        }

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $goods_list = array();
        $goods_list =$this->_tuihuobatchgoodstotal_mod->find(array(
            'conditions'=>"1='1'",
            'count'=>true,
            'order'=>$orderSql." ,s_id DESC",
            'limit'=>"{$start},{$page_per}"
        ));
        $recordsTotal = $recordsFiltered = $this->_tuihuobatchgoodstotal_mod->getCount();
        if(strlen($search) > 0)
        {
            $goods_list =$this->_tuihuobatchgoodstotal_mod->find(array(
                'conditions'=>"1='1' AND (batch_id like '%".$search."%' or num like '%".$search."%') ",
                'count'=>true,
                'order'=>$orderSql." ,s_id DESC",
                'limit'=>"{$start},{$page_per}"

            ));

            $recordsFiltered = $this->_tuihuobatchgoodstotal_mod->getCount();
        }
        foreach ($goods_list as $key=>$val){
			//根据退货批次找出该批次已退件数，失败件数应退金额和实退金额

            $goods_list[$key]['has_back_num']  = $this->_tuihuobatchgoods_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status = 11");
            $goods_list[$key]['has_fail_num']  = $this->_tuihuobatchgoods_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status in (40,41,42,43,44,45,46)  ");
            //$goods_list[$key]['th_total_real']  = $this->_tuihuobatchgoods_mod->getOne("SELECT SUM (th_price) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status =  40");
            $goods_list[$key]['not_deal_num']  = $this->_tuihuobatchgoods_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status =  0");
            $goods_list[$key]['has_send_num']  = $this->_tuihuobatchgoods_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status =  5");
            $goods_list[$key]['tomorrow_deal_num']  = $this->_tuihuobatchgoods_mod->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status =  20");
           //批次已完成的实际退货总金额
            $goods_list[$key]['th_total_real']  = $this->_tuihuobatchgoods_mod->getOne("SELECT SUM(th_price) FROM " . DB_PREFIX . "th_batch_goods WHERE th_batch_id = '.$val[batch_id].'  AND th_status =  11");
            $goods_list[$key]['th_total_real'] = isset($goods_list[$key]['th_total_real'])?$goods_list[$key]['th_total_real']:0;
            //通过批次号拿到批次商品id
			$gwh_ids = $this->_get_batch_ids($val[batch_id]);
			//批次商品总的价格
            $goods_list[$key]['th_batch_total']  = $this->_goods_warehouse_mod->getOne("SELECT SUM(goods_price) FROM " . DB_PREFIX . "goods_warehouse WHERE id  ".db_create_in($gwh_ids));
            $goods_list[$key]['th_batch_total'] = isset($goods_list[$key]['th_batch_total'])?$goods_list[$key]['th_batch_total']:0;
            //批次已退商品id
            $gwh_hasback_ids = $this->_get_hasback_ids($val[batch_id],11);
            $goods_list[$key]['th_total_hasback']  = $this->_goods_warehouse_mod->getOne("SELECT SUM(goods_price) FROM " . DB_PREFIX . "goods_warehouse WHERE id ".db_create_in($gwh_hasback_ids));
            $goods_list[$key]['th_total_hasback'] = isset($goods_list[$key]['th_total_hasback'])?$goods_list[$key]['th_total_hasback']:0;

       //退货人_taker_name
            $goods_list[$key]['real_name'] = $this->_taker_name($val['th_user_id']);

        }
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($goods_list)));
    }


	/*退货失败商品入仓*/
    function th_fail_list()
    {
        if(IS_POST)
        {

            $goods_no = trim($_POST['goods_no']);

            if(empty($goods_no) ){
                $this->show_warning('标签号不能为空值');
                return;
            }

            $data['goods_no'] = $goods_no;

            $data['ftime'] = time();
            $affect_id = $this->_tuihuofailgoods_mod->add($data);
            if($affect_id)
            {
                $this->show_message('添加成功',
                    'back_list', 'index.php?module=behalf&act=th_fail_list');
            }else{
                $this->show_warning('添加失败');
            }

        }else{

            $this->_import_css_js ('dt');
            if($this->visitor->get('has_behalf'))
            {
                $this->_assign_leftmenu('order_manage');
            }else
            {
                $this->_assign_leftmenu('dashboard');
            }

            $this->display("behalf.tuihuo.fail.list.html");
        }
    }




	/**
	 * 已退商品列表
	 */
    function goods_backed_list(){
		if(IS_POST){

		}else{
			$this->_import_css_js ('dt');
			if($this->visitor->get('has_behalf'))
			{
				$this->_assign_leftmenu('order_manage');
			}else
			{
				$this->_assign_leftmenu('dashboard');
			}

			$this->display("behalf.backed.list.html");
		}
	}

    /*
     * 包裹列表
     * */
    function get_fail_list(){
        // $bh_id = $this->_get_bh_id();
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序


        $recordsTotal = 0;
        $recordsFiltered = 0;
        $goods_list = array();

        $refund_order_ids = $this->get_fail_orders();
        $conditions = "";
        $conditions = " AND goods_no  " . db_create_in($refund_order_ids);

        $goods_list =$this->_goods_warehouse_mod->find(array(
            'conditions'=>"1='1' ".$conditions,
            'count'=>true,
            'order'=>"id DESC",
            'limit'=>"{$start},{$page_per}"
        ));

        $recordsTotal = $recordsFiltered = $this->_goods_warehouse_mod->getCount();
        if(strlen($search) > 0)
        {

            $goods_list =$this->_goods_warehouse_mod->find(array(
                'conditions'=>"1='1'  AND (goods_attr_value like '%".$search."%' or goods_no like '%".$search."%' or goods_specification like '%".$search."%' or store_address like '%".$search."%') " .$conditions,
                'count'=>true,
                'order'=>"id DESC",
                'limit'=>"{$start},{$page_per}"

            ));

            $recordsFiltered = $this->_goods_warehouse_mod->getCount();
        }
        foreach ($goods_list as $k=>$details){
            $info = $this->_tuihuofailgoods_mod -> get(array(
                'conditions'    => 'goods_no = ' . $details['goods_no'],
            ));

            $refund_goods_n[$k]['ftime'] = date('Y-m-d H:i:s',$info['ftime']);
        	$refund_goods_n[$k]['gw_id'] = $details['id'];
            $refund_goods_n[$k]['goods_specification'] = $details['goods_specification'];
            //字符串中有颜色分类、主要颜色、尺码的给去掉这几个字

            if(strpos($refund_goods_n[$k]['goods_specification'],"分类")){
                $refund_goods_n[$k]['goods_specification']=str_replace('颜色分类:','',$refund_goods_n[$k]['goods_specification']);
            }
            if(strpos($refund_goods_n[$k]['goods_specification'],"要颜")){
                $refund_goods_n[$k]['goods_specification']=str_replace('主要颜色:','',$refund_goods_n[$k]['goods_specification']);
            }
            if(strpos($refund_goods_n[$k]['goods_specification'],"尺码:")){
                $refund_goods_n[$k]['goods_specification']=str_replace('尺码:','-',$refund_goods_n[$k]['goods_specification']);
            }

            $refund_goods_n[$k]['goods_price'] = $details['goods_price'];
            $refund_goods_n[$k]['goods_name'] = $details['goods_name'];
            $refund_goods_n[$k]['store_address'] = $details['store_address'];
            $refund_goods_n[$k]['market_name'] = $details['market_name'];
            $refund_goods_n[$k]['floor_name'] = $details['floor_name'];
            $refund_goods_n[$k]['goods_sku'] = $details['goods_sku'];
            $refund_goods_n[$k]['goods_no'] = $details['goods_no'];
            $refund_goods_n[$k]['store_bargin'] = $details['store_bargin'];
            $refund_goods_n[$k]['goods_image'] = $details['goods_image'];
            $refund_goods_n[$k]['goods_attr_value'] = $this->_Attrvalue2Pinyin($details['goods_attr_value']);
            //拿货人
            $refund_goods_n[$k]['taker_name'] = $this->_taker_name($details['taker_id']);
            $refund_goods_n[$k]['taker_id'] = $details['taker_id'];
            $refund_goods_n[$k]['goods_id'] = $details['goods_id'];
            $refund_goods_n[$k]['goods_status'] = $details['goods_status'];

            if($details['goods_status'] == BEHALF_GOODS_PREPARED){$refund_goods_n[$k]['goods_status']='备货中';}
            if($details['goods_status'] == BEHALF_GOODS_READY_APP){$refund_goods_n[$k]['goods_status']='APP已拿';}
            if($details['goods_status'] == BEHALF_GOODS_DELIVERIES){$refund_goods_n[$k]['goods_status']='已派单';}
            if($details['goods_status'] == BEHALF_GOODS_READY){$refund_goods_n[$k]['goods_status']='已备货';}
            if($details['goods_status'] == BEHALF_GOODS_TOMORROW){$refund_goods_n[$k]['goods_status']='明天有';}
            if($details['goods_status'] == BEHALF_GOODS_AFTERNOON){$refund_goods_n[$k]['goods_status']='下午有';}
            if($details['goods_status'] == BEHALF_GOODS_UNSURE){$refund_goods_n[$k]['goods_status']='不确定';}
            if($details['goods_status'] == BEHALF_GOODS_STOP_TAKING){$refund_goods_n[$k]['goods_status']='停止拿货';}
            if($details['goods_status'] == BEHALF_GOODS_UNFORMED){$refund_goods_n[$k]['goods_status']='未出货';}
            if($details['goods_status'] == BEHALF_GOODS_UNSALE){$refund_goods_n[$k]['goods_status']='已下架';}
            if($details['goods_status'] == BEHALF_GOODS_SEND){$refund_goods_n[$k]['goods_status']='已发货';}
            if($details['goods_status'] == BEHALF_GOODS_REBACK){$refund_goods_n[$k]['goods_status']='已退货';}
            if($details['goods_status'] == BEHALF_GOODS_ADJUST){$refund_goods_n[$k]['goods_status']='已换货';}
            if($details['goods_status'] == BEHALF_GOODS_CANCEL){$refund_goods_n[$k]['goods_status']='已取消';}
            if($details['goods_status'] == BEHALF_GOODS_IMPERFECT){$refund_goods_n[$k]['goods_status']='残次品';}
            if($details['goods_status'] == BEHALF_GOODS_AFTERNOON){$refund_goods_n[$k]['goods_status']='下午有';}
            if($details['goods_status'] == BEHALF_GOODS_PRICE_ERROR){$refund_goods_n[$k]['goods_status']='价格错误';}
            if($details['goods_status'] == BEHALF_GOODS_SKU_UNSALE){$refund_goods_n[$k]['goods_status']='SKU下架';}
            if($details['goods_status'] == BEHALF_GOODS_ERROR){$refund_goods_n[$k]['goods_status']='档口信息错误';}

        }
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($refund_goods_n)));
    }









    /**
     * 设置拿货市场
     */
    function set_markettaker()
    {    	
    	$bh_id = $this->visitor->get('has_behalf');
    	$bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);
    	if(!IS_POST)
    	{
    		$bh_markettakers = $this->_behalf_mod->getRelatedData('has_markettakers',$bh_id);
    		$this->_import_css_js();
    		$this->_assign_leftmenu('setting');
    		$this->assign('markets',$bh_markets);
    		$this->assign('markettakers',$bh_markettakers);
    		$this->display('behalf.goods.markettaker.set.html');
    	}
    	else 
    	{
    		$mt_name = trim($_POST['mt_name']);
    		$mk_ids = $_POST['market']?strval(implode(',', $_POST['market'])):'';
    		$mk_names = array();
    		foreach ($_POST['market'] as $mid)
    		{
    			foreach ($bh_markets as $bm)
    			{
    				if($mid == $bm['mk_id'])
    				{
    					$mk_names[] = $bm['mk_name'];
    				}
    			}
    		}
    		
    		$data = array(
    			'mt_name'=>$mt_name,
    			'mk_ids'=>$mk_ids,
    			'mk_names'=>implode(',', $mk_names),
    			'bh_id'=>$bh_id	
    		);
    		$model_markettaker =& m('markettaker');
    		$affect_id = $model_markettaker->add($data);
    		if($affect_id)
    		{
    			$this->json_result(1,'add_success');
    		}
    	}
    	
    	//dump($bh_markets);
    }
    
    function see_behalf()
    {
    	$user_id = $this->visitor->get('has_behalf');
    	$behalf = $this->_behalf_mod->get($user_id);
    	$this->_assign_leftmenu('setting');
    	$this->assign('behalf',$behalf);
    	$this->display('behalf.info.see.html');
    }
    
    
    /**
     * 设置代发
     */
    function set_behalf()
    {
    	$user_id = $this->visitor->get('has_behalf');
    	if (!IS_POST)
    	{
    		/* 当前位置 */    		
    		$behalf = $this->_behalf_mod->get($user_id);
    
    		$region_mod =& m('region');
    		$this->assign('regions', $region_mod->get_options(0));
    
    		$this->assign("behalf",$behalf);
    		$this->_assign_leftmenu('setting');
    		//$this->import_resource('jquery.plugins/jquery.validate.min.js,mlselection.js');
    		$this->display('behalf.info.set.html');
    	}
    	else
    	{
    		if(!$this->_allow_behalf_setting('set_behalf')) return;
    
    		$data = $_POST;
    		$data['max_orders'] = abs(intval($data['max_orders']));
    		foreach ($data as $key=>$value)
    		{
    			if(empty($value) && $data['max_orders'])
    				unset($data[$key]);
    		}
    		/* 检查名称是否已存在 */
    		if (!$this->_behalf_mod->unique(trim($data['bh_name']),$data['bh_id']))
    		{
    			$this->json_error('name_exist');
    			return;
    		}
    
    		$this->_behalf_mod->edit($data['bh_id'], $data);
    		if($this->_behalf_mod->has_error())
    		{
    			$this->json_error('update failed!');
    			return;
    		}
    		$this->json_result(1,'edit_delivery_successed');
    	}
    
    }
    /**
     * 设置代发关联快递
     */
    function set_delivery()
    {
    	$user_id = $this->visitor->get('has_behalf');
    	$behalf_deliveries = $this->_behalf_mod->getRelatedData('has_delivery',$user_id);
    	$exist_deliveries = array();
    	foreach ($behalf_deliveries as $value)
    	{
    	    $exist_deliveries[] = $value['dl_id'];
    	}
    	if (!IS_POST)
    	{
    		/* 当前位置 */
    		$behalf = $this->_behalf_mod->get($user_id);
    	
    		$deliveries = $this->_delivery_mod->find();
    		
    	
    		$this->assign("behalf",$behalf);
    		$this->assign("deliveries",$deliveries);
    		$this->assign("exist_deliveries",$exist_deliveries);
    		$this->_assign_leftmenu('setting');
    		//$this->import_resource('jquery.plugins/jquery.validate.min.js,mlselection.js');
    		$this->display('behalf.info.set_delivery.html');
    	}
    	else
    	{
    		if(!$this->_allow_behalf_setting('set_delivery')) return;
    	
    		$data = $_POST;
    		extract($data);
    		if(!empty($data))
    		{
    		    //dump($deliveries);
    		    $drop_ids = array_diff($exist_deliveries, $deliveries);
    		    $create_ids = array_diff($deliveries,$exist_deliveries);
    		    if(!empty($drop_ids))
    		    {
    		        $this->_behalf_mod->unlinkRelation('has_delivery',$user_id,$drop_ids);
    		    }
    		    if(!empty($create_ids))
    		    {
    		        $this->_behalf_mod->createRelation('has_delivery',$user_id,$create_ids);
    		    }    		    
    		    if($this->_behalf_mod->has_error())
    		    {
    		        $this->json_error('update failed!');
    		        return;
    		    }
    		   
    		}
    		$this->json_result(1,'edit_delivery_successed');
    	}
    }


 
    //  function set_delivery_fee1()
//  {
//      $user_id = $this->visitor->get('has_behalf');
//      if (!IS_POST)
//      {
// //           echo $shipping_html = $this->fetch('behalf.shipping_area.info.html');
// //           exit;
//          /* 当前位置 */
//          $behalf = $this->_behalf_mod->get($user_id);


//          $behalf_deliveries = $this->_behalf_mod->getRelatedData('has_delivery',$user_id);
//          $exist_deliveries = array();
//          foreach ($behalf_deliveries as $value)
//          {
//              $exist_deliveries[] = $value['dl_id'];
//              $exist_deliveries_name[$value['dl_id']] = $value['dl_name'];   //zjh 单独取出快递名
//          }

//          $this->assign("behalf",$behalf);
//          $this->assign('deliveries',$behalf_deliveries);

//          $this->_assign_leftmenu('setting');
//          //$this->import_resource('jquery.plugins/jquery.validate.min.js,mlselection.js');

//          //zjh 取配送地区信息
//          foreach ($exist_deliveries_name as $exist_delivery => $exist_delivery_name) {
//              $shipping_area[$exist_delivery_name] = $this->_shipping_area_mod -> find(array(
//                  'conditions'    => 'dl_id = ' . $exist_delivery,
//              ));
//          }
//          $this->assign('exist_deliveries',$exist_deliveries_name);   // zjh 本代发拥有的快递
//          $this->assign('shipping_area',$shipping_area);
//          //---

//          $this->display('behalf.info.set_delivery_fee.html');
//      }
//      else
//      {
//          //if(!$this->_allow_behalf_setting('set_delivery_fee')) return;

//          $data = $_POST;
//          extract($data);


//          // zjh

//          if(isset($delivery)){

//              if(isset($operate)&&$operate==='get'){

//                  $this->_show_delivery_fee_info($delivery);
//              }else if(isset($operate)&&$operate==='edit'){

//                  $this->_show_edit_shipping_area($delivery);
//              }else if(isset($operate)&&$operate==='remove'){

//                  $this->_remove_shipping_area($delivery);
//              }else{


//              }

// //               $this->json_result(array('status'=>1,'html'=>$shipping_html,'dl_id'=>$delivery),'更新成功');
//          }



//          //----

//          if(!empty($data))
//          {
//              /* $behalf_mod->unlinkRelation('has_delivery',$user_id);
//                  $behalf_mod->createRelation('has_delivery',$user_id,$deliveries); */
//              $deliveries_fees = array();
//              foreach ($dl_ids as $key=>$dl_id)
//              {
//                  $deliveries_fees[$key]['dl_id'] = intval($dl_id);
//                  $deliveries_fees[$key]['first_amount'] = abs(intval($dl1_quantity[$key])) > 1 ? abs(intval($dl1_quantity[$key])):1;
//                  $deliveries_fees[$key]['first_price'] = abs(floatval($dl1_fee[$key]));
//                  $deliveries_fees[$key]['step_amount'] = abs(intval($dl2_quantity[$key])) > 1 ? abs(intval($dl2_quantity[$key])):1;
//                  $deliveries_fees[$key]['step_price'] = abs(floatval($dl2_fee[$key]));
//              }

//              $this->_behalf_mod->unlinkRelation('has_delivery',$user_id);
//              $this->_behalf_mod->createRelation('has_delivery',$user_id,$deliveries_fees);
//              //$behalf_mod->updateRelation('has_delivery',$user_id,$deliveries_ids,$deliveries_fees);
//              if($this->_behalf_mod->has_error())
//              {
//                  $this->json_error('update failed!');
//                  return;
//              }
//          }
//          $this->json_result(1,'edit_delivery_successed');
//      }
//  }




    /**
     * 设置代发关联快递费用
     */
    function set_delivery_fee()
    {
    	$user_id = $this->visitor->get('has_behalf');

		$data = IS_POST ? $_POST : $_GET;
		extract($data);

		// zjh 用operate来判断是否在页内做操作
		if (isset($operate)) {

			if($operate==='get'){

				$this->_show_delivery_fee_info($delivery);
			}else if($operate==='edit'){

				$this->_show_edit_shipping_area($sa_id);
			}else if($operate==='remove'){

				$this->_remove_shipping_area($delivery,$sa_id);
			}else if($operate === 'add'){

                $this->_show_add_shipping_area($delivery);
			}else if($operate === 'changeDefault'){
                $this->_change_default($delivery,$sa_id);
            }

		}else if (!IS_POST) {

			/* 当前位置 */
			$behalf = $this->_behalf_mod->get($user_id);

			$behalf_deliveries = $this->_behalf_mod->getRelatedData('has_delivery',$user_id);
			$exist_deliveries = array();
			foreach ($behalf_deliveries as $value)
			{
				$exist_deliveries[] = $value['dl_id'];
				$exist_deliveries_name[$value['dl_id']] = $value['dl_name'];   //zjh 单独取出快递名
			}

			//zjh 取配送地区信息
			foreach ($exist_deliveries_name as $exist_delivery => $exist_delivery_name) {
				$shipping_area[$exist_delivery_name] = $this->_shipping_area_mod -> find(array(
					'conditions'    => 'dl_id = ' . $exist_delivery,
				));
			}

			$this->assign("behalf",$behalf);
			$this->assign('deliveries',$behalf_deliveries);
			$this->_assign_leftmenu('setting');

			$this->assign('exist_deliveries',$exist_deliveries_name);   // zjh 本代发拥有的快递
			$this->assign('shipping_area',$shipping_area);

			$this->display('behalf.info.set_delivery_fee.html');
		}
    }


	/**
	 * zjh test
	 */
	function zjh_test()
	{
		$one_shipping_area = $this->_shipping_area_mod -> get(array(
			'conditions'    => 'shipping_area_id = 4',
		));

//		$fields = unserialize($one_shipping_area['contained_area']);

		print_r($one_shipping_area);exit;

		$this->display('test.html');
	}

	/**
 	* zjh 展示快递费用的所有信息
 	*/
	function _show_delivery_fee_info($delivery)
	{
		$shipping_area = $this->_shipping_area_mod -> find(array(
			'conditions'    => 'dl_id = ' . $delivery,
            'order' => 'area_default desc'
		));

        foreach ($shipping_area as $key => $value) {

            $region_str = '';
            $temp_region = unserialize($value['contained_area']);
            foreach ($temp_region as $k => $v) {
                $region_str .= $v.',';
            }
            $region_str = rtrim($region_str, ",");
            $shipping_area[$key]['contained_area'] = $region_str;
            //$shipping_area[$key]['contained_area'] = "广州,北京,上海,深圳,广州,北京,上海,深圳,广州,北京,上海,深圳,广州,北京,上海,深圳,广州,北京,上海,深圳,广州,北京,上海,深圳";   //测试
            if ($shipping_area[$key]['area_default'])
            {
                $shipping_area[$key]['checked'] = 'checked';
            }else{
                $shipping_area[$key]['checked'] = '';
            }
            
        }
        
       
		$this->assign('shipping_area',$shipping_area);
        $this->assign('dl_id',$delivery);
		$shipping_html = $this->_view->fetch('behalf.shipping_area.info.html');

		$message = array('status'=>1,'msg'=>'成功','result'=>array('html'=>$shipping_html,'dl_id'=>$delivery));

		exit(json_encode($message));
	}

	/**
	 * zjh 显示编辑配送区域的相关信息
	 */
	function _show_edit_shipping_area($sa_id)
	{
		$one_shipping_area = $this->_shipping_area_mod -> get(array(
			'conditions'    => 'shipping_area_id = ' . $sa_id,
		));

		$fields = unserialize($one_shipping_area['configure']);

		$lang = Lang::get('behalf_shipping_area');    // 获取配送区域相关语言

		foreach ($fields AS $key => $val)
		{
			/* 替换更改的语言项 */
			if ($val['name'] == 'basic_fee')
			{
				$val['name'] = 'base_fee';
			}

			if ($val['name'] == 'item_fee')
			{
				$item_fee = 1;
			}
			if ($val['name'] == 'fee_compute_mode')
			{
				$this->assign('fee_compute_mode',$val['value']);
				unset($fields[$key]);
			}
			else
			{
				$fields[$key]['name'] = $val['name'];
				$fields[$key]['label']  = $lang[$val['name']];
			}
		}

		if(empty($item_fee))
		{
			$field = array('name'=>'item_fee', 'value'=>'0', 'label'=>empty($lang['item_fee']) ? '' : $lang['item_fee']);
			array_unshift($fields,$field);
		}

		/* 获得该区域下的所有地区 */
		$regions =  unserialize($one_shipping_area['contained_area']);

		// 取国家
		$countries = $this->_region_mod -> find(array(
			'conditions'    => 'parent_id = 0',
		));

		$this->assign('id',               $sa_id);
		$this->assign('fields',           $fields);
		$this->assign('shipping_area',    $one_shipping_area);
		$this->assign('regions',          $regions);
		$this->assign('form_action',      'update');
		$this->assign('countries',       $countries);
		$this->assign('default_country',  1);
		$this->assign('sa_lang',$lang);

		// $this->display('behalf.set.shipping_area.html');
        $x = $this->_view->fetch('behalf.set.shipping_area.html');
        echo $x;
	}

	/**
	 * zjh 显示增加配送区域页面
	 */
	function _show_add_shipping_area($delivery)
	{
        $one_shipping_area = array();

        $one_shipping_area['shipping_area_id'] = '';
        $one_shipping_area['shipping_area_id'] = '';
        $one_shipping_area['area_default'] = 0;
        $one_shipping_area['contained_area'] = '';
        $one_shipping_area['dl_id'] = $delivery;

        $one_shipping_area['configure'] = 'a:6:{i:0;a:2:{s:4:"name";s:8:"item_fee";s:5:"value";s:0:"";}i:1;a:2:{s:4:"name";s:8:"base_fee";s:5:"value";s:0:"";}i:2;a:2:{s:4:"name";s:13:"item_step_fee";s:5:"value";s:0:"";}i:3;a:2:{s:4:"name";s:8:"step_fee";s:5:"value";s:0:"";}i:4;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:1:"0";}i:5;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:9:"by_weight";}}';

        $fields = unserialize($one_shipping_area['configure']);

        $lang = Lang::get('behalf_shipping_area');    // 获取配送区域相关语言

        foreach ($fields AS $key => $val)
        {
            /* 替换更改的语言项 */
            if ($val['name'] == 'basic_fee')
            {
                $val['name'] = 'base_fee';
            }

            if ($val['name'] == 'item_fee')
            {
                $item_fee = 1;
            }
            if ($val['name'] == 'fee_compute_mode')
            {
                $this->assign('fee_compute_mode',$val['value']);
                unset($fields[$key]);
            }
            else
            {
                $fields[$key]['name'] = $val['name'];
                $fields[$key]['label']  = $lang[$val['name']];
            }
        }

        if(empty($item_fee))
        {
            $field = array('name'=>'item_fee', 'value'=>'0', 'label'=>empty($lang['item_fee']) ? '' : $lang['item_fee']);
            array_unshift($fields,$field);
        }

        /* 初始化为空区域 */
        $regions =  array();

        // 取国家
        $countries = $this->_region_mod -> find(array(
            'conditions'    => 'parent_id = 0',
        ));

      //  $this->assign('id',               $sa_id);
        $this->assign('fields',           $fields);
        $this->assign('shipping_area',    $one_shipping_area);
        $this->assign('regions',          $regions);
        $this->assign('form_action',      'update');
        $this->assign('countries',       $countries);
        $this->assign('default_country',  1);
        $this->assign('sa_lang',$lang);

        // $this->display('behalf.set.shipping_area.html');
        $x = $this->_view->fetch('behalf.set.shipping_area.html');
        echo $x;
	}

	/**
	 * zjh 移除单个配送区域
	 */
	function _remove_shipping_area($delivery,$sa_id)
	{
        // $message = array('status'=>-1,'msg'=>'删除失败！+'.$delivery.'+'.$sa_id);
        // exit(json_encode($message));
        $conditions = 'shipping_area_id = '.$sa_id;

        $flag = $this->_shipping_area_mod -> drop($conditions);

        if ($flag){
            
            $message = array('status'=>1,'msg'=>'删除成功！','result'=>$delivery);
            
        }else{
            $message = array('status'=>-1,'msg'=>'删除失败！');
        }
        exit(json_encode($message));
	}

    /**
     *
     * zjh 改变默认配送区域
     */

    function _change_default($delivery,$sa_id)
    {
        $edit_data = array(

            'area_default' => 0
        );
        
        $conditions = 'area_default = 1 AND dl_id = '.$delivery;
        

        $this->_shipping_area_mod ->edit($conditions, $edit_data);

        $edit_data = array(

            'area_default' => 1
        );
        
        $conditions = 'shipping_area_id = '.$sa_id.' AND dl_id = '.$delivery;

        $this->_shipping_area_mod ->edit($conditions, $edit_data);
        
        echo $delivery;

    }


	/**
	 * zjh 设置配送区域
	 */
	function set_shipping_area()
	{
		$data = IS_POST ? $_POST : $_GET;
		extract($data);

        if ($operate === 'checkAreaName'){

            $this->_check_shipping_area_name($data);

        }else if ($operate === 'update') {

			$this->_update_shipping_area($data);

		} else if($operate === 'sel_region'){
			if ($target === 'selProvinces' || $target === 'selCities' || $target === 'selDistricts') {

				$this->_sel_region($type, $target, $parent);
			}
		}
	}

	/**
	 * zjh 移除选定的配送区域
	 */
	function _sel_region($type,$target,$parent)
	{
		// 取省份
		$provinces = array();

		if (!empty($parent))
		{
			$provinces = $this->_region_mod->get_list($parent);
		}

		$regions['regions'] = $provinces;
		$regions['type']    = $type;
		$regions['target']  = !empty($target) ? stripslashes(trim($target)) : '';
		$regions['target']  = htmlspecialchars($regions['target']);

		$message = array('status'=>1,'msg'=>'成功','result'=>$regions);
		exit(json_encode($message));
	}

    /**
     * zjh 检查是否已经存在配送区域
     */
    function _check_shipping_area_name($data)
    {
        extract($data);

        $shipping_area = $this->_shipping_area_mod -> find(array(
            'fields' => 'shipping_area_id',
            'conditions'    => "shipping_area_name = '$shipping_area_name' AND dl_id = $dl_id",
        ));
        
        if (empty($shipping_area)){
            echo false;
        }else{
            echo true;
        }
    }

	/**
	 * zjh 更新配送区域
	 */
	function _update_shipping_area($data)
	{
		extract($data);

		// 组装config数据
		$config = array();
		$count = 0;
		$config[$count]['name']     = 'item_fee';
		$config[$count]['value']    = !is_numeric($item_fee) || empty($item_fee) ? '0' : $item_fee;
		$count++;
		$config[$count]['name']     = 'base_fee';
		$config[$count]['value']    = !is_numeric($base_fee) || empty($base_fee) ? '0' : $base_fee;
		$count++;
		$config[$count]['name']     = 'item_step_fee';
        $config[$count]['value']    = !is_numeric($item_step_fee) || empty($item_step_fee) ? '0' : $item_step_fee;
        $count++;
		$config[$count]['name']     = 'step_fee';
		$config[$count]['value']    = !is_numeric($step_fee) || empty($step_fee) ? '0' : $step_fee;
        $count++;
        $config[$count]['name']     = 'free_money';
        $config[$count]['value']    = !is_numeric($free_money) || empty($free_money) ? '0' : $free_money;
        $count++;
        $config[$count]['name']     = 'fee_compute_mode';
        $config[$count]['value']    = empty($fee_compute_mode) ? 'by_weight' : $fee_compute_mode;

		$config = serialize($config);

		/* 过滤掉重复的region */
		$selected_regions = array();
		if (isset($regions))
		{
			foreach ($regions AS $region_id)
			{
				$selected_regions[$region_id] = $region_id;
			}
		}

		$where = 'region_id = ';
		$i=0;
		foreach ($selected_regions as $region) {
			if ($i == 0){
				$where .= $region;
			}else{
				$where .= ' OR region_id = '.$region;
			}
			$i++;
		}

		// 获取所辖地区名
		$get_region_name = $this->_region_mod -> find(array(
			'fields' => 'region_id,region_name',
			'conditions'    => $where,
		));

		foreach($get_region_name as $name => $value) {
			$region_list[$value['region_id']] = $value['region_name'];
		}

		$contained_area = serialize($region_list);

		$edit_data = array(

			'shipping_area_name' => $shipping_area_name,
			'configure' => $config,
			'contained_area' => $contained_area,
            'dl_id' => $delivery_id
		);
		$conditions = 'shipping_area_id = '.$shipping_area_id;

        if (!empty($shipping_area_id)){
            // 修改数据
            $one_shipping_area = $this->_shipping_area_mod ->edit($conditions, $edit_data);
        }else{
             // 插入数据
            $one_shipping_area = $this->_shipping_area_mod ->add($edit_data);
        }
		   // header("Location:index.php?module=behalf&act=set_delivery_fee"); 
        // echo '<script type="text/javascript">js_success();js_success("操作成功！",1);getShippingInfo('.$delivery_id.')</script>';
         echo $delivery_id;

	}










	/**
     * 设置代发关联快递费用
     */
    function set_behalf_market()
    {
    	$user_id = $this->visitor->get('has_behalf');
    	if (!IS_POST)
    	{
    		/* 当前位置 */
    		$behalf = $this->_behalf_mod->get($user_id);
    	
    		$markets = $this->_market_mod->get_list(1);
            $behalf_markets = $this->_behalf_mod->getRelatedData('has_market',$user_id);
            //dump($behalf_markets);
            $exist_markets = array();
            foreach ($behalf_markets as $value)
            {
                $exist_markets[$value['mk_id']] = array('mk_id'=>$value['mk_id'],'sort_ord'=>$value['sort_ord']);
            }
            
            //behalf_markets sign to markets
            if($markets)
            {
                $sort_arr = array();//用于多维排序
                foreach ($markets as $k=>$v)
                {
                   $sort_arr[] = $markets[$k]['sort_order'] = $exist_markets[$k]['sort_ord'] ? $exist_markets[$k]['sort_ord'] : 255;
                     
                }
                array_multisort($sort_arr,SORT_ASC,$markets);
            }
            
            
            $this->assign("behalf",$behalf);
            $this->assign('markets',$markets);
            $this->assign('exist_markets',$exist_markets);
    		
    		$this->_assign_leftmenu('setting');
    		//$this->import_resource('jquery.plugins/jquery.validate.min.js,mlselection.js');
    		$this->display('behalf.info.set_behalf_market.html');
    	}
    	else
    	{
    		if(!$this->_allow_behalf_setting('set_behalf_market')) return;
    	      
    		$data = $_POST;
    		//dump($data);
            extract($data);
            if(!empty($data))
            {                
                $this->_behalf_mod->unlinkRelation('has_market',$user_id);
                $this->_behalf_mod->createRelation('has_market',$user_id,$markets);
                if($markets)
                {
                    foreach ($markets as $mark)
                    {
                        $this->_behalf_mod->updateRelation('has_market',$user_id,$mark,"sort_ord={$sorts[$mark]}");
                    }
                    
                }               
                if($this->_behalf_mod->has_error())
                {
                    $this->json_error('update failed!');
                    return;
                }
            }
            else 
            {
                $this->_behalf_mod->unlinkRelation('has_market',$user_id);
                if($this->_behalf_mod->has_error())
                {
                    $this->json_error('update failed!');
                    return;
                }
            }
    		$this->json_result(1,'edit_delivery_successed');
    	}
    }
    
    /**
     * 面单账号设置
     */
    function set_mbaccount()
    {
    	$user_id = $this->visitor->get('has_behalf');

    	if (IS_POST)
    	{
    		if(!$this->_allow_behalf_setting('set_behalf_account'))  return;
    		$data = array();
    		$data['behalf_modeb_account_'.$user_id] = array();
    		$yto_account = empty($_POST['yto_account'])?'':trim($_POST['yto_account']);
    		$yto_pass = empty($_POST['yto_pass'])?'':trim($_POST['yto_pass']);
    		$zto_account = empty($_POST['zto_account'])?'':trim($_POST['zto_account']);
    		$zto_pass = empty($_POST['zto_pass'])?'':trim($_POST['zto_pass']);
    		
    		if(!empty($yto_pass) && !empty($yto_account))
    		{
    			$data['behalf_modeb_account_'.$user_id]['yto_account'] = $yto_account;
    			$data['behalf_modeb_account_'.$user_id]['yto_pass']= $yto_pass;
    		}
    		if(!empty($zto_pass) && !empty($zto_account))
    		{
    			$data['behalf_modeb_account_'.$user_id]['zto_account'] = $zto_account;
    			$data['behalf_modeb_account_'.$user_id]['zto_pass']= $zto_pass;
    		}
    		
    		$model_setting = &af('settings');
    		   		
    		$model_setting->setAll($data);
    		$this->json_result(1,'edit_behalf_account_successed');
    	}
    	else 
    	{
	    	$this->assign('infos',Conf::get('behalf_modeb_account_'.$user_id));
	    	$this->_assign_leftmenu('setting');
	    	$this->display('behalf.info.account.set.html');
    	}
    }
    
    /**
     * 通过商品编码查找订单
     */
    function search_goods_no()
    {
    	$goods_no = isset($_GET['goods_no']) && $_GET['goods_no'] ? trim($_GET['goods_no']) : '';
    	if(!preg_match('/^\d{14,20}$/', $goods_no))
    	{
    		$this->json_error('find_fail');
    		return;
    	}
    	$goods_info = $this->_goods_warehouse_mod->get(array('conditions'=>"goods_no='{$goods_no}'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))));
    	if(!$goods_info)
    	{
    		$this->json_error('find_fail');
    		return;
    	}
    	$order_info = $this->_order_mod->findAll(array(
    		'conditions'=>"order_alias.order_id = {$goods_info['order_id']}",
    		'join'=>'has_orderextm',
    		'include'=>array('has_goodswarehouse')	
    	));
    	$order_info = current($order_info);
    	if($order_info)
    	{
    		$delivery = $this->_delivery_mod->get($order_info['dl_id']);
    		$order_info['dl_name'] = $delivery['dl_name'];
    		$behalf = $this->_behalf_mod->get($order_info['bh_id']);
    		$order_info['bh_name'] = $behalf['bh_name'];
    		$order_info['region_name'] = $this->_remove_China($order_info['region_name']);
    		
    		$orderrefunds = $this->_orderrefund_mod->find(array('conditions'=>"order_id={$order_info['order_id']}"));
    		if(!empty($orderrefunds))
    		{
    			foreach($orderrefunds as $refund)
    			{
    				if($refund['type'] == 1 && $refund['receiver_id'] == $order_info['bh_id'] && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($order_info['refunds']) && $order_info['refunds'] = $refund;
    				}
    				if($refund['type'] == 2 && $refund['sender_id'] == $order_info['bh_id'] && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($order_info['apply_fee']) && $order_info['apply_fee'] = $refund;
    				}
    			}
    		}
    	}
    	
    	//代发备忘录
    	$model_ordernote = & m('behalfordernote');
    	$note_info = $model_ordernote->get("{$order_info['order_id']}");
    	 
    	$this->assign('behalfordernote',$note_info);
    	
    	$this->assign('orderlogs',$this->_orderlog_mod->find(array('conditions'=>"order_id={$order_info['order_id']}"))); 
    	$this->assign('goods_info',$goods_info);
    	$this->assign('order_info',$order_info);
    	$this->_import_css_js();
    	$this->display('behalf.goods.search.order.html');
    }
    
    function show_order_detail()
    {
    	$order_id = isset($_GET['order_id']) && $_GET['order_id'] ? trim($_GET['order_id']) : '';
    	$bh_id = $this->visitor->get('has_behalf');
    	
    	if(!$order_id)
    	{
    		$this->json_error('find_fail');
    		return;
    	}
    	
    	$order_info = $this->_order_mod->findAll(array(
    			'conditions'=>"order_alias.order_id = {$order_id}",
    			'join'=>'has_orderextm',
    			'include'=>array('has_goodswarehouse' )
    	));
    	$order_info = current($order_info);
    	if($order_info)
    	{
    		$delivery = $this->_delivery_mod->get($order_info['dl_id']);
    		$order_info['dl_name'] = $delivery['dl_name'];
    		$behalf = $this->_behalf_mod->get($order_info['bh_id']);
    		$order_info['bh_name'] = $behalf['bh_name'];
    		$order_info['region_name'] = $this->_remove_China($order_info['region_name']);
    		
    		$orderrefunds = $this->_orderrefund_mod->find(array('conditions'=>"order_id={$order_id}"));
    		if(!empty($orderrefunds))
    		{
    		    $apply_refund_count = 0;//正在申请退款的订单数量
    		    
    			foreach($orderrefunds as $refund)
    			{
    				if($refund['type'] == 1 && $refund['receiver_id'] == $bh_id && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($order_info['refunds']) && $order_info['refunds'] = $refund;
    					$apply_refund_count++;
    				}
    				if($refund['type'] == 2 && $refund['sender_id'] == $bh_id && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($order_info['apply_fee']) && $order_info['apply_fee'] = $refund;
    				}
    			}
    			
    			if($apply_refund_count > 1)
    			{
    			    $this->assign("show_del_btn",true);
    			}
    			
    			$this->assign('refunds',$orderrefunds);
    		}
    	}

    	//代发备忘录
    	$model_ordernote = & m('behalfordernote');
    	$note_info = $model_ordernote->get("{$order_info['order_id']}");
    	
    	$this->assign('behalfordernote',$note_info);
    	
    	$this->assign('orderlogs',$this->_orderlog_mod->find(array('conditions'=>"order_id={$order_info['order_id']}")));
    	$this->assign('order_info',$order_info);
    	$this->_import_css_js();
    	$this->display('behalf.order.detail.show.html');
    }
    
    function check_name()
    {
    	$bh_name = empty($_GET['bh_name']) ? '' : trim($_GET['bh_name']);
    	$bh_id = empty($_GET['bh_id']) ? 0 : intval($_GET['bh_id']);
    
    	if($bh_id == 0){
    		echo ecm_json_encode(true);
    		return;
    	}
    
    	if (!$this->_behalf_mod->unique($bh_name, $bh_id))
    	{
    		echo ecm_json_encode(false);
    		return;
    	}
    	echo ecm_json_encode(true);
    }
    
    
    
    /**
     * 商品仓库货物列表,dataTables,pipe-ajax
     */
    function get_pipe_goods()
    {
    	$bh_id = $this->_get_bh_id();
    	
    	$start = intval($_GET['start']);
    	$page_per = intval($_GET['length']);
    	//search
    	$search = trim($_GET['search']['value']);
    	//order
    	$order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
    	$order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
    	if($_GET['status']){
    		$condition = " AND ".db_create_in(array(BEHALF_GOODS_IMPERFECT) , 'goods_status');
		}

    	//拼接排序sql
    	$orderSql = "";
    	if(isset($order_column)){
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
    	}
    	
    	$recordsTotal = 0;
    	$recordsFiltered = 0;
    	$goods_list = array();
    	
    	$goods_list =$this->_goods_warehouse_mod->find(array(
    	        'conditions'=>"bh_id='{$bh_id}'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL)).$condition,
    			'count'=>true,
    			'order'=>$orderSql." ,order_add_time DESC",
    			'limit'=>"{$start},{$page_per}"
    	));
    	$recordsTotal = $recordsFiltered = $this->_goods_warehouse_mod->getCount();
    	
    	if(strlen($search) > 0)
    	{
    		$goods_list =$this->_goods_warehouse_mod->find(array(
    		        'conditions'=>"bh_id='{$bh_id}' AND goods_no like '%".$search."%'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL)),
    				'count'=>true,
    				'order'=>$orderSql." ,order_add_time DESC",
    				'limit'=>"{$start},{$page_per}"
    				
    		));
    		$recordsFiltered = $this->_goods_warehouse_mod->getCount();
    	}
    	
    	echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>$recordsTotal,'recordsFiltered'=>$recordsFiltered,'data'=>array_values($goods_list))); 
    }
    
    /**
     * 在某行查看订单详情
     */
    function show_order_details()
    {
    	
    	$order_id = $_POST['id']?intval($_POST['id']):0;
    	$ajax = $_POST['ajax'] ? intval($_POST['ajax']):0;
    	$bh_id = $this->visitor->get('has_behalf');
    	
    	$model_goodsattr =& m('goodsattr');
    	$model_store=& m('store');
    	
    	$orders = $this->_order_mod->findAll(array(
    			'conditions'    => "order_alias.bh_id = ".$bh_id." AND order_alias.order_id=".$order_id,
    			'fields' => 'order_alias.*,orderextm.shipping_fee,orderextm.consignee,orderextm.region_name as consignee_region,orderextm.phone_mob,orderextm.address as consignee_address',
    			'join'          => 'has_orderextm',
    			'include'       =>  array(
    					'has_ordergoods',       //取出商品
    			)));
    	if(empty($orders))
    	{
    		$this->json_error('order is not exist!');
    		return;
    	}
    	// dump($orders);
    	foreach ( $orders as $key1 => $order )
    	{
    		if (! empty (  $order ['order_goods'] ))
    		{
    			$total_quantity = 0;
    			foreach ( $order ['order_goods'] as $key2 => $goods )
    			{
    				$total_quantity += intval($goods['quantity']);
    				empty ( $goods ['goods_image'] ) && $orders [$key1] ['order_goods'] [$key2] ['goods_image'] = Conf::get ( 'default_goods_image' );
    				// //商家编码
    				if (empty ( $goods ['attr_value'] ))
    				{
    					$result = $model_goodsattr->getOne ( "SELECT attr_value FROM {$model_goodsattr->table} WHERE goods_id={$goods['goods_id']} AND attr_id=1" );
    					$orders [$key1] ['order_goods'] [$key2] ['goods_seller_bm'] = $result;
    				}
    				else
    				{
    					$orders [$key1] ['order_goods'] [$key2] ['goods_seller_bm'] = $goods ['attr_value'];
    				}
    				/* $store = $model_store->get ( array (
    						'conditions' => 'store_id=' . $goods ['store_id'],
    						
    				) );
    				if (! empty ( $store ))
    				{
    					//$store = current ( $store );
    					$orders [$key1] ['order_goods'] [$key2] ['tel'] = $store ['tel'];
    					$orders [$key1] ['order_goods'] [$key2] ['im_qq'] = $store ['im_qq'];
    					$orders [$key1] ['order_goods'] [$key2] ['im_ww'] = $store ['im_ww'];
    				} */

    			}
				$orders[$key1]['total_quantity'] = $total_quantity;
    		}
    		
    		$orderrefunds = $this->_orderrefund_mod->find(array('conditions'=>"order_id={$order_id}"));
    		if(!empty($orderrefunds))
    		{
    			foreach($orderrefunds as $refund)
    			{
    				if($refund['type'] == 1 && $refund['receiver_id'] == $bh_id && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($orders [$key1]['refunds']) && $orders [$key1]['refunds'] = $refund;
    				}
    				if($refund['type'] == 2 && $refund['sender_id'] == $bh_id && $refund['status'] == 0 && $refund['closed'] == 0)
    				{
    					empty($orders [$key1]['apply_fee']) && $orders [$key1]['apply_fee'] = $refund;
    				}
    			}
    		}
    		
    	}
    	
    	foreach ( $orders as $key => $value )
    	{
    		$member_info = $this->_get_member_profile ( $value ['buyer_id'] );
    		$orders [$key] ['im_qq'] = $member_info ['im_qq'];
    		$orders [$key] ['im_aliww'] = $member_info ['im_aliww'];
    		$orders [$key] ['delivery_bm'] = $this->_order_mod->get_delivery_bm_bybehalf ( $value ['order_id'] );
    		$orders [$key] ['dl_name'] = $this->_order_mod->get_delivery_bybehalf ( $value ['order_id'], $value ['bh_id'] );
    	}
    	//dump($orders);
    	$order_info = current($orders);
    	$order_info['order_goods'] = array_values($order_info['order_goods']);
    	$order_info['add_time'] = $order_info['add_time'] ? local_date("Y-m-d H:i:s",$order_info['add_time']):'';
    	$order_info['pay_time'] = $order_info['pay_time'] ? local_date("Y-m-d H:i:s",$order_info['pay_time']):'';
    	$order_info['ship_time'] = $order_info['ship_time'] ? local_date("Y-m-d H:i:s",$order_info['ship_time']):'';
    	$order_info['finished_time'] = $order_info['finished_time'] ? local_date("Y-m-d H:i:s",$order_info['finished_time']):'';
    	
    	if($ajax)
    	{
    		$this->json_result($order_info,'success');
    	}
    	else 
    	{
    		$this->assign("order",$order_info);
    		$this->display('behalf.order.details.html');
    	}
    }
    
    /*三级菜单*/
    function _get_member_submenu()
    {
    	$array = array(
    			array(
    					'name' => 'all_orders',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=all_orders',
    			),
    			array(
    					'name' => 'pending',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=pending',
    			),
    			array(
    					'name' => 'accepted',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=accepted',
    			),
    			array(
    					'name' => 'shipped',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=shipped',
    			),
    			array(
    					'name' => 'finished',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=finished',
    			),
    			array(
    					'name' => 'canceled',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=canceled',
    			),
    			array(
    					'name' => 'refund',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=refund',
    			),
    			array(
    					'name' => 'applyfee',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=applyfee',
    			),
    			array(
    					'name' => 'refuse',
    					'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=refuse',
    			),
			array(
				'name' => 'lack',
				'url' => 'index.php?module=behalf&amp;act=order_list&amp;type=lack',
			),
    	);
    	return $array;
    }

	/*三级菜单  仓库定制*/
	function _get_member_submenu_2()
	{
		$array = array(
			array(
				'name' => 'all_orders',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=all_orders',
			),
			array(
				'name' => 'pending',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=pending',
			),
			array(
				'name' => 'accepted_1',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=accepted&tomorrow=1',
			),
			array(
				'name' => 'accepted_3',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=accepted&tomorrow=3',
			),
			array(
				'name' => 'accepted_2',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=accepted&tomorrow=2',
			),
			array(
				'name' => 'shipped',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=shipped',
			),
			array(
				'name' => 'finished',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=finished',
			),
			array(
				'name' => 'canceled',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=canceled',
			),
			array(
				'name' => 'refund',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=refund',
			),
			array(
				'name' => 'applyfee',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=applyfee',
			),
			array(
				'name' => 'refuse',
				'url' => 'index.php?module=behalf&amp;act=order_list_behalf&amp;type=refuse',
			),

		);
		return $array;
	}
    
    
    
    /**
     * 市场列表
     */
    function _get_markets()
    {
    	$markets = $this->_market_mod->get_list(1);
    	$this->assign("markets",$markets);
    }
    
    /**
     * 获取可用快递
     */
    function _get_related_delivery()
    {
    	$related_delivery=$this->_behalf_mod->getRelatedData('has_delivery',$this->visitor->get('has_behalf'));
    	$this->assign("related_delivery",$related_delivery);
    }
    
    /**
     * 允许代发设置与否    
     */
    function _allow_behalf_setting($fuc_name)
    {
    	$allowed = false;
    	$behalfs_menu = Conf::get('behalfs_menu');
    	if(!$behalfs_menu)
    	{
    		$this->json_error('not_allow_setting_behalf');
    		return false;
    	}
    	foreach ($behalfs_menu as $menu)
    	{
    		if($fuc_name == $menu)
    			$allowed = true;
    	}
    
    	if(!$allowed)
    	{
    		$this->json_error('not_allow_setting_behalf');
    		return false;
    	}
    
    	return $allowed;
    }
    
    function _curmenu($item)
    {
    	$_member_submenu = $this->_get_member_submenu();

    	foreach ($_member_submenu as $key => $value)
    	{
    		$_member_submenu[$key]['text'] = $value['text'] ? $value['text'] : Lang::get($value['name']);
    	}
    	$this->assign('_member_submenu', $_member_submenu);
    	$this->assign('_curmenu', $item);
    }

	function _curmenu_2($item)
	{
		$_member_submenu = $this->_get_member_submenu_2();

		foreach ($_member_submenu as $key => $value)
		{
			$_member_submenu[$key]['text'] = $value['text'] ? $value['text'] : Lang::get($value['name']);
		}
		if($_GET['tomorrow']){
            $item = $item.'_'.$_GET['tomorrow'];
        }
		$this->assign('_member_submenu', $_member_submenu);
		$this->assign('_curmenu', $item);
	}
    
    
    /**
     * 检测商品编码是否存在
     */
    function check_goodsno(){
    	$goods_no = trim($_POST['goods_no']);
    	$goods_info = $this->_goods_warehouse_mod->get(array(
    	    'conditions'=>"goods_no='{$goods_no}'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))
    	));
    	if(empty($goods_info))
    	{
    		echo ecm_json_encode(array('valid'=>false,'message'=>Lang::get('goods_unexisted')));
    		return;
    	}
    	$result = $this->_goods_warehouse_mod->get(array(
    			'conditions'=>"goods_no='{$goods_no}' AND ".db_create_in(array(BEHALF_GOODS_UNFORMED,BEHALF_GOODS_PREPARED,BEHALF_GOODS_TOMORROW,BEHALF_GOODS_READY ,BEHALF_GOODS_AFTERNOON,BEHALF_GOODS_UNSURE,BEHALF_GOODS_READY_APP,BEHALF_GOODS_DELIVERIES),'goods_status')

    	));
    	if($result)
    	{
    		$order_info = $this->_order_mod->get($result['order_id']);
    		if(in_array($order_info['status'],array(ORDER_ACCEPTED)))
    		{
    			echo ecm_json_encode(array('valid'=>true));
    		}
    		else 
    		{
    			echo ecm_json_encode(array('valid'=>false,'message'=>Lang::get('goods_order_not_accepted')));
    		}
    	}
    	else 
    	{
    		echo ecm_json_encode(array('valid'=>false,'message'=>Lang::get('goods_not_action')));
    	}
    	
    }
    
    /**
     * 用于检测 导航栏商品编码
     */
    function check_header_goodsno()
    {
    	$goods_no = isset($_GET['goods_no']) && $_GET['goods_no'] ? trim($_GET['goods_no']) : '';
    	if(!preg_match('/^\d{14,20}$/', $goods_no))
    	{
    		$this->json_error('find_fail');
    		return;
    	}
    	$goods_info = $this->_goods_warehouse_mod->get(array('conditions'=>"goods_no='{$goods_no}'"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))));
    	if(!$goods_info)
    	{
    		$this->json_error('find_fail');
    		return;
    	}
    	$this->json_result(1,'success');
    }
    
    /**
     * 左侧导航js
     */
 /*    function left_nav_js()
    {
    	header('Content-Encoding:' . CHARSET);
    	header("Content-Type: application/x-javascript\n");
    	header("Expires: " . date(DATE_RFC822, strtotime("+1 hour")) . "\n");
    	
    	if($this->visitor->get('pass_behalf'))	
    	{	// 导航栏配置文件
    		echo <<<EOT
    		var outlookbar=new outlook();
    		var t;
    		
    		t=outlookbar.addtitle('常用操作','管理首页',1);
    		outlookbar.additem('欢迎页面',t,'index.php?module=behalf&act=defaultmain');
    		outlookbar.additem('生成拿货单',t,'index.php?module=behalf&act=gen_taker_list');
    		outlookbar.additem('出入库管理',t,'index.php?module=behalf&act=manage_goods_warehouse');
    		outlookbar.additem('发货统计',t,'index.php?module=behalf&act=stat_shipped_order');
    		outlookbar.additem('面单打印',t,'index.php?module=behalf&act=mb_print');
    		
    		t=outlookbar.addtitle('基本设置','系统设置',1);
    		outlookbar.additem('查看个人资料',t,'index.php?module=behalf&act=defaultmain&m=look');
    		outlookbar.additem('修改个人资料',t,'index.php?module=behalf&act=set_behalf');
    		//outlookbar.additem('设置可发快递',t,'javascript:;');
    		//outlookbar.additem('设置快递费用',t,'javascript:;');
    		//outlookbar.additem('管理拿货市场',t,'javascript:;');
    		//outlookbar.additem('管理支付方式',t,'javascript:;');
    		
    		t=outlookbar.addtitle('账号管理','系统设置',1);
    		outlookbar.additem('设置面单账号',t,'index.php?module=behalf&act=set_mbaccount');
    		
    		t=outlookbar.addtitle('配货管理','系统设置',1);
    		//outlookbar.additem('设置配货市场',t,'index.php?module=behalf&act=set_markettaker');
    		outlookbar.additem('管理拿货人员',t,'index.php?module=behalf&act=manage_goodstaker');
    		
    		t=outlookbar.addtitle('订单管理','订单管理',1);
    		outlookbar.additem('订单列表',t,'index.php?module=behalf&act=order_list');
    		
    		t=outlookbar.addtitle('配货管理','订单管理',1);
    		outlookbar.additem('生成拿货单',t,'index.php?module=behalf&act=gen_taker_list');
    		outlookbar.additem('管理拿货单',t,'index.php?module=behalf&act=manage_taker_list');
    		outlookbar.additem('出入库管理',t,'index.php?module=behalf&act=manage_goods_warehouse');
    		
    		t=outlookbar.addtitle('订单统计','订单管理',1);
    		outlookbar.additem('发货统计',t,'index.php?module=behalf&act=stat_shipped_order');
    		outlookbar.additem('入库统计',t,'index.php?module=behalf&act=stat_enter_warehouse');
    		
    		t=outlookbar.addtitle('面单打印','打印管理',1);
    		outlookbar.additem('面单打印',t,'index.php?module=behalf&act=mb_print');
    		outlookbar.additem('面单模板',t,'index.php?module=behalf&act=mb_template');
    		
    		t=outlookbar.addtitle('普通打印','打印管理',1);
    		outlookbar.additem('普通打印',t,'index.php?module=behalf&act=common_print');
    		outlookbar.additem('普通模板',t,'index.php?module=behalf&act=common_template');
    		
    		t=outlookbar.addtitle('标签打印','打印管理',1);
    		outlookbar.additem('标签打印',t,'index.php?module=behalf&act=tag_print');
    		
    		t=outlookbar.addtitle('市场管理','其他管理',1);
    		outlookbar.additem('市场列表',t,'index.php?module=behalf&act=market_list');
EOT;
    	}
    	else 
    	{		
    		echo <<<EOT
    		var outlookbar=new outlook();
    		var t;
    				
    		t=outlookbar.addtitle('常用操作','管理首页',1);
    		outlookbar.additem('欢迎页面',t,'index.php?module=behalf&act=defaultmain');
    		outlookbar.additem('入库管理',t,'index.php?module=behalf&act=manage_goods_warehouse');
    		outlookbar.additem('入库统计',t,'index.php?module=behalf&act=stat_enter_warehouse');
EOT;
    	}   
    		
    } */

    /**
     *  重写
     *  要求登录才能访问
     */
    function _run_action()
    {        
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && in_array(MODULE, array('behalf')))
        {
        
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
        
                return;
            }
            else
            {
                $this->json_error('login_please','user_not_login');//user_not_login 在页面知道是用户没登录 by tiq 2015-04-26
                return;
            }
        }

        $member_info =ms()->user->_local_get($this->visitor->get('user_id'));
        //echo $this->visitor->get('pass_behalf')."#".$member_info['behalf_goods_taker'];
        /*只有已审核的代发能访问 和 拿货员*/
        if(!$this->visitor->get('pass_behalf') && !$member_info['behalf_goods_taker'])
        {
        	header('Location:index.php?app=member');
        	
        	return;
        }
        
        include_once MODULE_ABSPATH.'/lib/init.lib.php'; 
        
        parent::_run_action();
    }
    
    /**
     * 调整收货地址
     */
    function adjust_consignee()
    {
    	$bh_id = $this->visitor->get('has_behalf');
    	$thisdelivery = $this->_behalf_mod->getRelatedData('has_delivery',$bh_id);
    	
    	if (!IS_POST)
    	{
    		$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    		if (!$order_id)
    		{
    			echo Lang::get('no_such_order');
    			return;
    		}
    		header('Content-Type:text/html;charset=' . CHARSET);
    		$consignee  = $this->_orderextm_mod->get(array('conditions' => "order_id={$order_id} "));
    		$this->_import_css_js();
    		$this->assign('regions', $this->_region_mod->get_options(0));
    		$this->assign('consignee', $consignee);
    		$this->assign('deliverys', $thisdelivery);
    		$this->display('behalf.order.adjust_consignee.html');
    	}
    	else
    	{
    		$data = $_POST;
    		$dl_id = isset($_POST['dl_id']) && $_POST['dl_id'] ? intval($_POST['dl_id']) :0;
    		$data['dl_id'] = $dl_id;
    		$dl_name = '';
    		foreach ($data as $key=>$value)
    		{
    			if(empty($value))
    				unset($data[$key]);
    		}
    		foreach ($thisdelivery as $vdelivery)
    		{
    			if($dl_id == $vdelivery['dl_id'])
    				$dl_name = $vdelivery['dl_name'];
    		}
    		$this->_check_region($data);
    		
    		//start transaction
    		$trans = $this->_start_transaction();
    		
    		$affect_rows1 = $this->_orderextm_mod->edit($data['order_id'], $data);
    		$affect_rows1 === false && $trans = false;
    
    		$affect_rows2 = db()->query("UPDATE ".$this->_goods_warehouse_mod->table." SET delivery_id='{$dl_id}', delivery_name='{$dl_name}' WHERE order_id={$data['order_id']}"." AND goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL)));
    		$affect_rows2 === false && $trans = false;
    		
    		if(!$affect_rows1 && !$affect_rows2) {
    			$trans = false;
    		}
    		    
    		$order_info = $this->_order_mod->get($data['order_id']);
    		
    		
    		$affect_rows = $this->_orderlog_mod->add(array(
		    				'order_id'  => $data['order_id'],
		    				'operator'  => addslashes($this->visitor->get('user_name')),
		    				'order_status' => order_status($order_info['status']),
		    				'changed_status' => order_status($order_info['status']),
		    				'remark'    => Lang::get('adjust_consignee'),
		    				'log_time'  => gmtime(),
		    		));
    	   !$affect_rows && $trans = false;
    	   
    	   $success = $this->_end_transaction($trans);
    
    	   if($success)
    	   {
    	   		$this->pop_warning('ok','behalf_member_adjust_consignee');
    	   }
    	   else
    	   {
    	   		$this->pop_warning('caozuo_fail');
    	   } 
    	}
    
    }
    
    /**
     *    待发货的订单发货
     *
     *    @author    tiq
     *    @return    void
     */
    function shipped()
    {
    	list($order_id, $order_info)    = $this->_get_valid_order_info(array(ORDER_ACCEPTED, ORDER_SHIPPED));
    	if (!$order_id)
    	{
    		$this->pop_warning('caozuo_fail');
    		return;
    	}
    	$behalf_delivery = $this->_orderextm_mod->get($order_info['order_id']);
    
    	//分润
    	$fr_order = $this->_order_mod->findAll(array(
    			'conditions'=>'order_id='.$order_id.' AND status='.ORDER_ACCEPTED,
    			'include'=>array('has_ordergoods')
    	));
    	
    
    	if (!IS_POST)
    	{
	    	/* 显示发货表单 */
	    	header('Content-Type:text/html;charset=' . CHARSET);
    		$thisdelivery = $this->_behalf_mod->getRelatedData('has_delivery',$behalf_delivery['bh_id']);
    		$this->_import_css_js();
    		$this->assign('behalf_delivery',$behalf_delivery);
    		$this->assign("deliverys",$thisdelivery);
    		$this->assign('order', $order_info);
    		$this->display('behalf.order.shipped.html');
    	}
    	else
    	{
	    	if (empty($_POST['invoice_no']))
	    	{
	    		$this->pop_warning('invoice_no_empty');
	    		return;
	    	}
    
    		$edit_data = array('status' => ORDER_SHIPPED, 'invoice_no' => trim($_POST['invoice_no']));
    		$is_edit = true;
    		//开启事务
    		$trans = $this->_start_transaction();
    		$tran_reason = '';
    		
            if (empty($order_info['invoice_no']) || $edit_data['invoice_no'] == $order_info['invoice_no'])
            {
                /*商付通v2.2.1 更新商付通定单状态 开始*/
                if($order_info['payment_code']=='sft' || $order_info['payment_code']=='chinabank' || $order_info['payment_code']=='alipay' || $order_info['payment_code']=='tenpay' || $order_info['payment_code']=='tenpay2')
    			{
               		 $my_moneylog=& m('my_moneylog')->edit('order_id='.$order_id,array('caozuo'=>20));
               		 if(!$my_moneylog) {
               		     $trans = false;//不成功，则回滚
               		     $tran_reason = 'sft_update_fail';
               		 }
                }
                /*商付通v2.2.1  更新商付通定单状态 结束*/
                //不是修改发货单号
                $edit_data['ship_time'] = gmtime();
                $is_edit = false;
    		
    		    		
	            //分润
	            if(!empty($fr_order))
	            {
	                $behalf_discount = 0;
	                if(!empty($fr_order[$order_id]['order_goods']))
	                {
	                	foreach ($fr_order[$order_id]['order_goods'] as $goods)
	                	{//不能缺货
	                		if($goods['oos_value'])
	                		{
	                			$behalf_discount += floatval($goods['behalf_to51_discount']);
	               			}
	                	}
	                }
	                //快递费分润，8块分0.5
	                if($behalf_delivery['shipping_fee'] > 0)
	                {
	              		 $shipping_fee = intval($behalf_delivery['shipping_fee']);
	               		 $behalf_discount += (floor($shipping_fee/8))/2;
	                }
	    
	                if($behalf_discount > 0)
	                {
	                    $edit_data['behalf_discount'] = 0; // 关闭分润
	                    /*
	                	$edit_data['behalf_discount'] = $behalf_discount;//写入订单
	                	//转账
	                	include_once(ROOT_PATH.'/app/fakemoney.app.php');
	                	$fakemoneyapp = new FakeMoneyApp();
	                	$fr_reason = Lang::get('behalf_to_51_fr_reason').local_date('Y-m-d H:i:s',gmtime());
	    				//给用户转账
	    				$my_money_result=$fakemoneyapp->to_user_withdraw($this->visitor->get('user_id'),FR_USER,$behalf_discount, $fr_reason,$order_id,$fr_order[$order_id]['order_sn']);
	    				if($my_money_result !== true){
	    				    $trans = false;
	    				    $tran_reason = 'behalf_discount_pay_fail';
	    				}*/
	                }
	    
	             }
             
            }
    
             $affect_rows = $this->_order_mod->edit(intval($order_id), $edit_data);
             if(!$affect_rows){
                 $trans = false;
                 $tran_reason = 'order_update_fail';
             }
             
             //商品仓库更新
             $affect_rows = $this->_goods_warehouse_mod->edit("order_id = '{$order_id}' AND goods_status = '".BEHALF_GOODS_READY."'",array('goods_status'=>BEHALF_GOODS_SEND));
             //!$affect_rows && $trans = false;
           
             if(!empty($_POST['delivery']) && $behalf_delivery['dl_id'] != $_POST['delivery'])
             {
                //如果修改了快递
                $affect_rows = $this->_orderextm_mod->edit($order_id, array('dl_id' => intval($_POST['delivery'])));
                if(!$affect_rows){
                    $trans = false;
                    $tran_reason = 'order_extm_update_fail';
                }
             }
    
             #TODO 发邮件通知
             /*记录订单操作日志 */
             //$order_log =& m('orderlog');
             $affect_rows = $this->_orderlog_mod->add(array(
			                	'order_id'  => $order_id,
			                	'operator'  => addslashes($this->visitor->get('user_name')),
			                    'order_status' => order_status($order_info['status']),
			                    'changed_status' => order_status(ORDER_SHIPPED),
			                    'remark'    => $is_edit ? Lang::get('edit_invoice_no'). $_POST['remark']:Lang::get('shipped_order'),
			                    'log_time'  => gmtime(), 
			                ));
    		if(!$affect_rows){
    		    $trans = false;
    		    $tran_reason = 'order_log_add_fail';
    		}
    		
    		$this->_end_transaction($trans);

			if($trans){
				$noreply_info = $this->getNoreply();
				stockOrderPop($noreply_info['token'] , $order_id );
			}

    		/* 如果匹配到的话，修改第三方订单状态 */
    		if($trans)
    		{
    			$ordervendor_mod = &m('ordervendor');
    			$ordervendor_mod->edit("ecm_order_id={$order_id}", array(
    					'status' => VENDOR_ORDER_SHIPPED,
    			));
    			
    			/* 发送给买家订单已发货通知 */
    			$buyer_info   = ms()->user->_local_get($order_info['buyer_id']);
    			$order_info['invoice_no'] = $edit_data['invoice_no'];
    			$mail = get_mail('tobuyer_shipped_notify', array('order' => $order_info));
    			if($buyer_info['phone_mob'])
    			{
    				$com = $this->_order_mod->get_delivery_bybehalf($order_info['order_id'],$order_info['bh_id']);
    				$order_info['dl_name'] = $com;
    				$order_info['consignee'] = $behalf_delivery['consignee'];
    				$smail = get_mail('sms_order_notify', array('order' => $order_info));
    			//	$this->sendSaleSms($buyer_info['phone_mob'],  addslashes($smail['message']));
					$this->sendSms($buyer_info['phone_mob'],  addslashes($smail['message']));
    			}
    			
    			$this->pop_warning('ok','behalf_order_shipped');
    		}
    		else 
    		{
    			$this->pop_warning($tran_reason);
    		}
        }
    }
    
    /**
     * 调整订单费用
     */
    function adjust_fee()
    {
    	list($order_id, $order_info)    = $this->_get_valid_order_info(array(ORDER_SUBMITTED, ORDER_PENDING));
    	
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');    
    		return;
    	}
    	//$model_order    =&  m('order');
    	//$model_orderextm =& m('orderextm');
    	//$model_delivery = & m('delivery');
    	$shipping_info   = $this->_orderextm_mod->get($order_id);
    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);
    		$this->_import_css_js();
    		$this->assign('order', $order_info);
    		$this->assign('shipping', $shipping_info);
    		$this->display('behalf.order.adjust_fee.html');
    	}
    	else
    	{
    		/* 配送费用 */
    		$shipping_fee = isset($_POST['shipping_fee']) ? abs(floatval($_POST['shipping_fee'])) : 0;
    		/* 折扣金额 */
    		$goods_amount     = isset($_POST['goods_amount'])     ? abs(floatval($_POST['goods_amount'])) : 0;
    		/* 订单实际总金额 */
    		$order_amount = round($goods_amount + $shipping_fee, 2);
    		if ($order_amount <= 0)
    		{
    			/* 若商品总价＋配送费用扣队折扣小于等于0，则不是一个有效的数据 */
    			$this->pop_warning('invalid_fee');
    			return;
    		}
    		$data = array(
    				'goods_amount'  => $goods_amount,    //修改商品总价
    				'order_amount'  => $order_amount,     //修改订单实际总金额
    				'pay_alter' => 1    //支付变更
    		);
    
    		//开启事务
    		$trans = $this->_start_transaction();
    		
    		if ($shipping_fee != $shipping_info['shipping_fee'])
    		{
    			/* 若运费有变，则修改运费 */
    			$affect_row = $this->_orderextm_mod->edit($order_id, array('shipping_fee' => $shipping_fee));
    			!$affect_row && $trans = false;
    		}
    		$affect_row = $this->_order_mod->edit($order_id, $data);
    		!$affect_row && $trans = false;
    
    		/* if ($model_order->has_error())
    		{
    			$this->pop_warning($model_order->get_error());
    
    			return;
    		} */
    		/* 记录订单操作日志 */
    		//$order_log =& m('orderlog');
    		$affect_row = $this->_orderlog_mod->add(array(
    				'order_id'  => $order_id,
    				'operator'  => addslashes($this->visitor->get('user_name')),
    				'order_status' => order_status($order_info['status']),
    				'changed_status' => order_status($order_info['status']),
    				'remark'    => Lang::get('adjust_fee'),
    				'log_time'  => gmtime(),
    		));
    		!$affect_row && $trans = false;
    		
    		$this->_end_transaction($trans);
    		if($trans){
    			$this->pop_warning('ok','behalf_member_adjust_fee');
    		}
    		else{
    			$this->pop_warning('caozuo_fail');
    		}
    		
    	}
    }

	//更改商品
    function adjust_goods(){

		$order_id  = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$bh_id = $this->visitor->get('has_behalf');
		if(!$order_id){
			echo Lang::get('no_such_order');
			return;
		}
		//目前 明天有，未出货，下架 支持换款。
		$order_info = $this->_order_mod->findAll(array(
			'conditions' => "order_id={$order_id} AND bh_id={$bh_id} AND status ".db_create_in(array( ORDER_ACCEPTED)),
			'include' => array('has_goodswarehouse')

			));

		if (empty($order_info))
		{
			echo Lang::get('no_such_goods');
			return;
		}

		if(!IS_POST){
			header('Content-Type:text/html;charset=' . CHARSET);
			$this->_import_css_js();
			$order_info = reset($order_info);
			$ojson = json_encode($order_info['gwh']);
			//判断是已换货记录的，就不显示当前记录
			foreach($order_info['gwh'] as $k=>$goods){
				if(in_array($goods['goods_status'] , array( BEHALF_GOODS_ADJUST , BEHALF_GOODS_CANCEL))){
					unset($order_info['gwh'][$k]);
				}
			}
			$this->assign('order',  $order_info );
			$this->assign('ojson' , $ojson);
			$this->display('behalf.goods.adjust.html');
		}else{
			$goods_list = json_decode(stripslashes($_POST['ojson']),true);
			$goods_amount = 0;
			foreach($goods_list as $goods){
				$markets = explode('_',$goods['goods_market']);
				$market = $this->_market_mod->findAll(array(
						'conditions' => "mk_name='$markets[0]'",
						'include' => array(
							'has_market',
						)
					));
				$market = reset($market);
				if(!$market){ $this->show_warning('请确认你选择的市场是否存在');}
				$floor = array();

				foreach($market['market'] as $m){

					if(strtolower($m['mk_name']) == strtolower($markets[1]) ){
						$floor = $m;
						break;
					}
				}

				if(!$floor){ $this->show_warning('请确认你选择的楼层是否存在');}

				//直接更新商品信息
				/*$this->_goods_warehouse_mod->edit( $goods['goods_id'],array('goods_id'=>0,
					'goods_price'=>$goods['goods_price'],'goods_quantity'=>$goods['goods_num'],'goods_sku'=>$goods['goods_sku'],'goods_attr_value'=>$goods['goods_code'],'goods_status'=> BEHALF_GOODS_PREPARED,'goods_spec_id'=>0,'goods_specification'=>$goods['goods_attr'],'store_id'=>0,'store_address'=> $markets[2],'market_id'=>$market['mk_id'],'market_name'=>$market['mk_name'],'floor_id'=>$floor['mk_id'],'floor_name'=>$floor['mk_name'],'store_bargin'=>0));*/
				//取换款尾号
				$goods_old = $this->_goods_warehouse_mod->get($goods['goods_id']);

				$change_str = (int)substr($goods_old['goods_no'],14,2);

				$goods = array('goods_id'=>0,
							'goods_no' => substr($goods_old['goods_no'], 0 , 14).str_pad( $change_str + 1,2,'0' ,STR_PAD_LEFT),
							'goods_price'=>$goods['goods_price'],
							'goods_quantity'=>$goods['goods_num'],
							'goods_sku'=>$goods['goods_sku'],
							'goods_attr_value'=>$goods['goods_code'],
							'goods_status'=> BEHALF_GOODS_PREPARED,
							'goods_spec_id'=>0,
							'goods_specification'=>$goods['goods_attr'],
							'store_id'=>0,
							'store_address'=> $markets[2],
							'market_id'=>$market['mk_id'],
							'market_name'=>$market['mk_name'],
							'floor_id'=>$floor['mk_id'],
							'floor_name'=>$floor['mk_name'],
							'store_bargin'=>0,
							'update_time' => time(),
				);
				$goods = array_merge($goods_old ,$goods );

				//原商品信息进行取消，新增一条商品信息
				$this->_goods_warehouse_mod->edit($goods['id'] , array('goods_status' => BEHALF_GOODS_ADJUST ));
				unset($goods['id']);
				$this->_goods_warehouse_mod->add($goods);
			}

			//生成补款链接  退款  补款
			//重新订单该订单商品总价
			$order_info = $this->_order_mod->findAll(array(
				'conditions' => "order_alias.order_id={$order_id} ",
				'include' => array('has_goodswarehouse'),
			));

			$order_info = reset($order_info);
			$goods_amount = $order_info['goods_amount'];
			$g_price = array();
			foreach($order_info['gwh'] as $goods){
				if(!in_array($goods['goods_status'] , array( BEHALF_GOODS_ADJUST ))){
					array_push($g_price ,$goods['goods_price']);
				}
			}
			$goods_amount_new = array_sum($g_price);
			//更新订单最新信息


			if($goods_amount > $goods_amount_new){
				//原商品价格大于更改后的价格，要进行自动退款操作
				$offset_amount = $goods_amount - $goods_amount_new;
				if($offset_amount >= $goods_amount){
					echo Lang::get('pay_amount_much_correct');
					return;
				}

				$model_order = & m('order');
			//	$model_goods_warehouse = & m('goodswarehouse');
				//开启事务
				$success = $this->_start_transaction();

				//订单更新
				$data = array(
				//	'behalf_fee' => $order_info['behalf_fee'] - $goods['behalf_fee'],
					'order_amount' =>   $order_info['order_amount'] - $offset_amount ,
					'goods_amount' =>   $order_info['goods_amount'] - $offset_amount,
				);

				$affect_rows = $model_order->edit($order_id , $data);
				!$affect_rows && $success = false;//回滚


			//	$affect_rows = $model_goods_warehouse->edit($goods_id,array('goods_status' => BEHALF_GOODS_ADJUST ));
			//		!$affect_rows && $success = false;//回滚

				$id = $order_id;
				/*商付通v2.2.1  更新商付通定单状态 开始*/
				$my_money_mod =& m('my_money');
				$my_moneylog_mod =& m('my_moneylog');

				$money = $goods['goods_price'];//定单价格
				$buy_user_id=$order_info['buyer_id'];//买家ID
				$sell_user_id=$order_info['seller_id'];//卖家ID

				if($order_info['order_id']==$id) {
					$buy_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$buy_user_id'");
					$buy_money = $buy_money_row['money'];//买家的钱
					$buy_money_dj = $buy_money_row['money_dj'];//买家的钱

					$sell_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id'");
					$sell_money = $sell_money_row['money'];//卖家的冻结资金
					$sell_money_dj = $sell_money_row['money_dj'];//卖家的冻结资金

					$new_buy_money = $buy_money + $money;
					$new_sell_money = $sell_money_dj - $money;

					//更新数据
					$affect_rows = $my_money_mod->edit('user_id=' . $buy_user_id, array('money' => $new_buy_money));
					!$affect_rows && $success = false;//回滚

					$affect_rows = $my_money_mod->edit('user_id=' . $sell_user_id, array('money_dj' => $new_sell_money));
					!$affect_rows && $success = false;//回滚

					//更新商付通log为 定单已取消
					$change_buyer = array('caozuo' => 30, 'admin_time' => gmtime(), 'moneyleft' => $new_buy_money + $buy_money_dj);
					$change_seller = array('caozuo' => 30, 'admin_time' => gmtime(), 'moneyleft' => $sell_money + $new_sell_money);

					//                    $my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));
					Log::write('dualven:behalf:' . var_export($change_buyer, true));
					$affect_rows = $my_moneylog_mod->edit('order_id=' . $id . ' and user_id=' . $buy_user_id, $change_buyer);
					!$affect_rows && $success = false;//回滚
					$affect_rows = $my_moneylog_mod->edit('order_id=' . $id . ' and user_id=' . $sell_user_id, $change_seller);
					!$affect_rows && $success = false;//回滚

				}
				/* 加回订单商品库存 */
				$cancel_reason = '商品换款';
				$cancel_reason .= " ".Lang::get('goods_sn').":".$goods['goods_no'].";".Lang::get('reback_money_success');

				/* 记录订单操作日志 */
				$affect_rows = $this->_orderlog_mod->add(array(
					'order_id'  => $id,
					'operator'  => addslashes($this->visitor->get('user_name')),
					'order_status' => order_status($order_info['status']),
					'changed_status' => order_status($order_info['status']),
					'remark'    => $cancel_reason,
					'log_time'  => gmtime(),
				));

				!$affect_rows && $success = false;//回滚

				//提交或回滚
				$this->_end_transaction($success);

				if($affect_rows){
					$this->pop_warning('ok');
				}else{
					$this->pop_warning('fail');
				}





			}elseif($goods_amount < $goods_amount_new){

				//原商品价格小于更改后的价格，要进行补款信息生成操作
				$pay_amount = abs($goods_amount - $goods_amount_new);

				$data=array(
					'order_id'=>$order_info['order_id'],
					'order_sn'=>$order_info['order_sn'],
					'sender_id'=>$this->visitor->get('user_id'),
					'sender_name'=>$this->visitor->get('user_name'),
					'receiver_id'=>$order_info['buyer_id'],
					'receiver_name'=>$order_info['buyer_name'],
					'refund_reason'=>'更换商品',
					'refund_intro'=>'联系买家进行补款',
					'apply_amount'=>$pay_amount,
					'refund_amount'=>0,
					'create_time'=>gmtime(),
					'pay_time'=>0,
					'status'=>0,
					'closed'=>0,
					'type'=>2,//代发申请补邮
				);
				$model_orderrefund=& m('orderrefund');
				$model_orderrefund->add($data);

				$affect_rows = $this->_orderlog_mod->add(array(
					'order_id'  => $order_info['order_id'],
					'operator'  => addslashes($this->visitor->get('user_name')),
					'order_status' => order_status($order_info['status']),
					'changed_status' => order_status(ORDER_ACCEPTED),
					'remark'    => "商品换款",
					'log_time'  => gmtime(),
				));
				!$affect_rows && $success = false;//回滚
				if($model_orderrefund->has_error())
				{
					$this->pop_warning($model_orderrefund->get_error());
					return;
				}else{
					$this->pop_warning('ok');
				}
			}
			$this->pop_warning('ok');

		}

	}


	function adjust_goods_by_goodsid(){
		$order_id  = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$bh_id = $this->visitor->get('has_behalf');
		if(!$order_id){
			echo Lang::get('no_such_order');
			return;
		}
		//目前 明天有，未出货，下架 支持换款。
		$order_info = $this->_order_mod->findAll(array(
			'conditions' => "order_id={$order_id} AND bh_id={$bh_id} AND status ".db_create_in(array( ORDER_ACCEPTED)),
			'include' => array('has_goodswarehouse'=>array('conditions'=>" goods_status not ".db_create_in(array(BEHALF_GOODS_CANCEL))))

		));

		if (empty($order_info))
		{
			echo Lang::get('no_such_goods');
			return;
		}

		if(!IS_POST){
			header('Content-Type:text/html;charset=' . CHARSET);
			$this->_import_css_js();
			$order_info = reset($order_info);
			$ojson = json_encode($order_info['gwh']);
			//判断是已换货记录的，就不显示当前记录
			foreach($order_info['gwh'] as $k=>$goods){
				if(in_array($goods['goods_status'] , array( BEHALF_GOODS_ADJUST ,BEHALF_GOODS_CANCEL ))){
					unset($order_info['gwh'][$k]);
				}
			}

			$this->assign('order',  $order_info );
			$this->assign('ojson' , $ojson);
			$this->display('behalf.goods.adjust2.html');
		}else{
			//goods_list 包含 原来goods_warehouse  id 和 新的goods_id_new
			$goods_list = json_decode(stripslashes($_POST['ojson']),true);

			$goods_amount = 0;
			$model_market = & m('market');
			$model_store = & m('store');
			$model_goodsattr = & m('goodsattr');

			$markets = $model_market->get_list(1);
			foreach ($markets as $key => $m) {
				$markets[$key]['children'] = $model_market->get_list($m['mk_id']);
			}

			foreach($goods_list as $goods){
				$goods_new = $this->_goods_mod->get($goods['goods_id_new']);
				$spec = $goods['spec_new'];
				if(empty($goods_new)){
					$this->pop_warning('更换的商品不存在！');
					return;
				}

				if(!empty($goods_new['store_id'])){
					$store = $model_store->get($goods_new['store_id']);
					$floor_id = $store['mk_id'];
				}else{
					//解析商家编码
					$data_market = parse_code($goods_new['attr_value']);
					$floor_id = $model_market->getOne("select m2.mk_id from ".$model_market->table." m1 left join ".$model_market->table." m2 on m1.mk_id=m2.parent_id where m1.mk_name='{$data_market['market_name']}' and m2.mk_name='{$data_market['floor_name']}'");
				}

				$floor_name = '';
				$market_id = 0;
				$market_name = '';
				foreach ($markets as $mkey => $mm) {
					if (in_array($floor_id, array_keys($mm['children']))) {
						$market_id = $mm['mk_id'];
						$market_name = $mm['mk_name'];
						foreach ($mm['children'] as $floor) {
							if ($floor['mk_id'] == $floor_id) {
								$floor_name = $floor['mk_name'];
							}
						}
					}
				}
				//取换款尾号
				$goods_old = $this->_goods_warehouse_mod->get($goods['goods_id']);
//print_r($goods_new);exit;
				$change_str = (int)substr($goods_old['goods_no'],14,2);
				$goods_attr_value = $model_goodsattr->getOne("SELECT attr_value FROM {$model_goodsattr->table} WHERE goods_id={$goods_new['goods_id']} AND attr_id=1");
				$goods_attr = explode('_',$goods_attr_value);
				$goods = array(
				//	'id' => $goods_old['goods_id'],
					'goods_id' => $goods_new['goods_id'],
					'goods_no' => substr($goods_old['goods_no'], 0 , 14).str_pad( $change_str + 1,2,'0' ,STR_PAD_LEFT),
					'goods_image' => $goods_new['default_image'],
					'goods_name' => $goods_new['goods_name'],
					'goods_price'=>$goods_new['price'],
					'goods_quantity'=>$goods_old['goods_quantity'],
					'goods_sku'=>end($goods_attr),
					'goods_attr_value'=> $goods_attr_value,
					'goods_status'=> BEHALF_GOODS_PREPARED,
					'goods_spec_id'=>0,
					'goods_specification'=> $spec,
					'store_id'=> $goods_new['store_id'] ,
					'store_name' => $store['store_name'], // '店铺名称',
					'store_address' => $store['address'] ? $store['address'] : $data_market['store_address'], // '档口地址',
					'market_id'=>$market_id,
					'market_name'=>$market_name,
					'floor_id'=>$floor_id,
					'floor_name'=>$floor_name,

				);
			//	print_r($goods);exit;
				$goods = array_merge($goods_old ,$goods );

				//原商品信息进行取消，新增一条商品信息
				$this->_goods_warehouse_mod->edit($goods_old['id'] , array('goods_status' => BEHALF_GOODS_ADJUST ));
				unset($goods['id']);

				$this->_goods_warehouse_mod->add($goods);
			}

			//生成补款链接  退款  补款
			//重新订单该订单商品总价
			$order_info = $this->_order_mod->findAll(array(
				'conditions' => "order_alias.order_id={$order_id} ",
				'include' => array('has_goodswarehouse'),
			));

			$order_info = reset($order_info);
			$goods_amount = $order_info['goods_amount'];
			$g_price = array();
			foreach($order_info['gwh'] as $goods){
				if(!in_array($goods['goods_status'] , array( BEHALF_GOODS_ADJUST , BEHALF_GOODS_CANCEL ))){
					array_push($g_price ,$goods['goods_price']);
				}
			}
			$goods_amount_new = array_sum($g_price);
			//更新订单最新信息


			if($goods_amount > $goods_amount_new){
				//原商品价格大于更改后的价格，要进行自动退款操作
				$offset_amount = $goods_amount - $goods_amount_new;
				if($offset_amount >= $goods_amount){
					echo Lang::get('pay_amount_much_correct');
					return;
				}

				$model_order = & m('order');
				//	$model_goods_warehouse = & m('goodswarehouse');
				//开启事务
				$success = $this->_start_transaction();

				//订单更新
				$data = array(
				//	'behalf_fee' => $order_info['behalf_fee'] - $goods['behalf_fee'],
					'order_amount' =>   $order_info['order_amount'] - $offset_amount ,
					'goods_amount' =>   $order_info['goods_amount'] - $offset_amount,
				);

				$affect_rows = $model_order->edit($order_id , $data);
				!$affect_rows && $success = false;//回滚


				//	$affect_rows = $model_goods_warehouse->edit($goods_id,array('goods_status' => BEHALF_GOODS_ADJUST ));
				//		!$affect_rows && $success = false;//回滚

				$id = $order_id;
				/*商付通v2.2.1  更新商付通定单状态 开始*/
				$my_money_mod =& m('my_money');
				$my_moneylog_mod =& m('my_moneylog');

				$money = $goods['goods_price'];//定单价格
				$buy_user_id=$order_info['buyer_id'];//买家ID
				$sell_user_id=$order_info['bh_id'];//卖家ID

				if($order_info['order_id']==$id) {
					$buy_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$buy_user_id'");
					$buy_money = $buy_money_row['money'];//买家的钱
					$buy_money_dj = $buy_money_row['money_dj'];//买家的钱

					$sell_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id'");
					$sell_money = $sell_money_row['money'];//卖家的冻结资金
					$sell_money_dj = $sell_money_row['money_dj'];//卖家的冻结资金

					$new_buy_money = $buy_money + $money;
					$new_sell_money = $sell_money_dj - $money;

					//更新数据
					$add_mymoneylog = array(
						//	'user_id' => $user_id,
						//	'user_name' => $user_name,
						'buyer_id' => $order_info['buyer_id'],
						'buyer_name' => $order_info['buyer_name'],
						'seller_id' => $sell_user_id,
						'seller_name' => $order_info['seller_name'],
						'order_id' => $order_info['order_id'],
						'order_sn' => $order_info['order_sn'],
						'add_time' => gmtime(),
						'admin_time' => gmtime(),
						'leixing' => 30,
						'money_zs' => $money,
						'money' => $money,
						'log_text'=> '商品换款',
						'user_log_del' => 0,
						'caozuo' => 30,
						's_and_z' => 1,
					);

					//更新商付通log为 定单已取消
					$change_buyer = array_merge($add_mymoneylog , array('user_id' => $order_info['buyer_id'],'user_name'=> $order_info['buyer_name'],'moneyleft' => $new_buy_money));

					$change_seller = array_merge($add_mymoneylog , array('user_id' => $order_info['bh_id'],'user_name'=>  $order_info['bh_id'],'moneyleft' => $new_sell_money));

					//                    $my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));

					$affect_rows = $my_moneylog_mod->add( $change_buyer);
					!$affect_rows && $success = false;//回滚

					$affect_rows = $my_moneylog_mod->add( $change_seller);
					!$affect_rows && $success = false;//回滚


				}
				/* 加回订单商品库存 */
				$cancel_reason = '商品换款';
				$cancel_reason .= " ".Lang::get('goods_sn').":".$goods['goods_no'].";".Lang::get('reback_money_success');

				/* 记录订单操作日志 */
				$affect_rows = $this->_orderlog_mod->add(array(
					'order_id'  => $id,
					'operator'  => addslashes($this->visitor->get('user_name')),
					'order_status' => order_status($order_info['status']),
					'changed_status' => order_status($order_info['status']),
					'remark'    => $cancel_reason,
					'log_time'  => gmtime(),
				));

				!$affect_rows && $success = false;//回滚

				//提交或回滚
				$this->_end_transaction($success);

				if($affect_rows){
					$this->pop_warning('ok');
				}else{
					$this->pop_warning('fail');
				}
				return;



			}elseif($goods_amount < $goods_amount_new){
				//原商品价格小于更改后的价格，要进行补款信息生成操作
				$offset_amount = abs($goods_amount - $goods_amount_new);

				$data=array(
					'order_id'=>$order_info['order_id'],
					'order_sn'=>$order_info['order_sn'],
					'sender_id'=>$this->visitor->get('user_id'),
					'sender_name'=>$this->visitor->get('user_name'),
					'receiver_id'=>$order_info['buyer_id'],
					'receiver_name'=>$order_info['buyer_name'],
					'refund_reason'=>'更换商品',
					'refund_intro'=>'联系买家进行补款',
					'apply_amount'=>$offset_amount,
					'refund_amount'=>0,
					'create_time'=>gmtime(),
					'pay_time'=>0,
					'status'=>0,
					'closed'=>0,
					'type'=>2,//代发申请补邮
				);
				$model_orderrefund=& m('orderrefund');
				$model_orderrefund->add($data);




				$affect_rows = $this->_orderlog_mod->add(array(
					'order_id'  => $order_info['order_id'],
					'operator'  => addslashes($this->visitor->get('user_name')),
					'order_status' => order_status($order_info['status']),
					'changed_status' => order_status(ORDER_ACCEPTED),
					'remark'    => "商品换款",
					'log_time'  => gmtime(),
				));

				if($model_orderrefund->has_error())
				{
					$this->pop_warning($model_orderrefund->get_error());
					return;
				}else{
					$this->pop_warning('ok');
					return;
				}
			}
			$this->pop_warning('ok');
			return;
		}
	}

	/**
	 * 发起退货
	 */
	function back_goods(){
		$order_id  = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$bh_id = $this->visitor->get('has_behalf');
		if(!$order_id){
			echo Lang::get('no_such_order');
			return;
		}

		$order_info = $this->_order_mod->findAll(array(
			'conditions' => "order_id={$order_id} AND bh_id={$bh_id} AND status ".db_create_in(array( ORDER_SHIPPED , ORDER_FINISHED )),
			'include' => array('has_goodswarehouse')

		));
		$order_info = reset($order_info);
		if (empty($order_info))
		{
			echo Lang::get('no_such_goods');
			return;
		}

		if(! IS_POST){
			header('Content-Type:text/html;charset=' . CHARSET);
			$this->_import_css_js();

			$ojson = json_encode($order_info['gwh']);
			$this->assign('order',  $order_info );
			$this->assign('ojson' , $ojson);
			$this->display('behalf.goods.back.html');
		}else{

			$data = array(
				'goods_ids' => join(',',$_POST['id']),
				'dl_id' => $_POST['dl_id'],
				'invoice_no' => $_POST['invoice_no'],
				'order_id' => $_POST['order_id'],
				'order_sn' => $order_info['order_sn'],
				'sender_id' => $this->user->get('user_id'),
				'sender_name' => $this->visitor->get('user_name'),
				'receiver_id'=>$order_info['buyer_id'],
				'receiver_name'=>$order_info['buyer_name'],
				'apply_amount'=>$_POST['refund_amount'],
				'refund_amount'=>0,
				'create_time'=>gmtime(),
				'pay_time'=>0,
				'status'=>0,
				'closed'=>0,
				'type'=>1,//申请退货
				'refuse_reason' => '客服代为申请退货',
				'goods_ids_flag' => 1
			);

			$affect_rows = $this->_orderrefund_mod->add($data);
			if($affect_rows)
			{
				$refund_message = Lang::get('apply_fee_message').$order_info['order_sn'];
				/* 连接用户系统 */
				$ms =& ms();
				$msg_id = $ms->pm->send($bh_id, array($order_info['buyer_id']), '', $refund_message);

				/* 发送给买家订单补收差价通知 */
				$seller_info   = $ms->user->_local_get($order_info['buyer_id']);
				//	$this->sendSaleSms($seller_info['phone_mob'], $refund_message);
				$this->sendSms($seller_info['phone_mob'], $refund_message);
				$this->pop_warning('ok');
			}
			else
			{
				$this->pop_warning('caozuo_fail');
			}
		}

	}
    /**
     * 申请补邮
     */
    function apply_fee()
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    	// zjh  单个商品
    	$goods_ids = isset($_GET['goods_ids']) ? $_GET['goods_ids'] : 0;

    	$bh_id = $this->visitor->get('has_behalf');
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}    	
    	
    	/* 只有已付款,已发货,已完成的订单可以申请补邮 */
    	$order_info     = $this->_order_mod->get("order_id={$order_id} AND bh_id={$bh_id} AND status " . db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED)));
    
    	if (empty($order_info))
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);

    		$refund_info = $this->_orderrefund_mod->get("order_id='{$order_id}' AND sender_id='{$bh_id}' AND receiver_id='{$order_info['buyer_id']}' AND status='0' AND type='2' AND closed='0'");
    		
    		
     		if($refund_info)
     		{
     			echo Lang::get('exist_orderrefund');
     			return;
     		}
     		$this->_import_css_js();
    		$this->assign('order', $order_info);
    		
    		// zjh 2017/8/11
    		$goods_ids_array = explode(',', $goods_ids);
    		
    		$goods = $this->_goods_warehouse_mod->find(array('conditions'=>"bh_id='{$bh_id}' AND ".db_create_in($goods_ids_array,'id')));
    		$goods_info = array();
    		foreach ($goods as $key => $value) {
    			$goods_info[$key] = $value['goods_no'];
    		}
    		$this->assign('goods_info', $goods_info);
    		$this->assign('goods_ids_array', $goods_ids_array);
    		$this->assign('goods_ids', $goods_ids);
    		$this->display('behalf.order.apply_fee.html');
    	}
    	else
    	{
    		//status 0:申请，1：已同意，2：已拒绝  closed 0:未关闭 1：已关闭
    	
    		$refund_result=$this->_orderrefund_mod->get("order_id={$order_id} AND receiver_id={$order_info['buyer_id']} AND type=2 AND status=0 AND closed=0");
    	
    		
    		if(!empty($refund_result))
    		{
    			$this->pop_warning(Lang::get('exist_apply'));
    			return ;
    		} 

    		if(empty($_POST['apply_fee_reason']))
    		{
    			echo Lang::get('fill_apply_fee_reason');
    			return;
    		}
    		
    		if($_POST['goods_ids'] == 0){
    			$refund_amount = isset($_POST['refund_amount'])?floatval(trim($_POST['refund_amount'])):0;

    			if($refund_amount > 1000 || $refund_amount <= 0)
	    		{
	    			echo Lang::get('apply_fee_incorrect');
	    			return;
	    		}

	    		$data=array(
    				'order_id'=>$order_info['order_id'],
    				'order_sn'=>$order_info['order_sn'],
    				'sender_id'=>$this->visitor->get('user_id'),
    				'sender_name'=>$this->visitor->get('user_name'),
    				'receiver_id'=>$order_info['buyer_id'],
    				'receiver_name'=>$order_info['buyer_name'],
    				'refund_reason'=>html_filter($_POST['apply_fee_reason']),
    				'refund_intro'=>html_filter($_POST['refund_intro']),
    				'apply_amount'=>$refund_amount,
    				'refund_amount'=>0,
    				'create_time'=>gmtime(),
    				'pay_time'=>0,
    				'status'=>0,
    				'closed'=>0,
    				'type'=>2,//代发申请补邮
	    		);

    		}else{

    			$goods_ids_array = explode(',', $_POST['goods_ids']);

    			$goods_refund_amount = array();
    			$refund_amount = 0;
    			foreach ($goods_ids_array as $key => $value) {
    				$amount_flag = 'refund_amount_'.$value;
    				if(isset($_POST[$amount_flag]) && $_POST[$amount_flag] > 0){

    					$goods_refund_amount[$value] = isset($_POST[$amount_flag])?floatval(trim($_POST[$amount_flag])):0;
    					$refund_amount += $goods_refund_amount[$value];
    				}
    				
    			}


    			$str_goods_amount = serialize($goods_refund_amount);
    	
    			if($refund_amount > 1000 || $refund_amount <= 0)
	    		{
	    			echo Lang::get('apply_fee_incorrect');
	    			return;
	    		}

	    		$temp_goods_ids = array_keys($goods_refund_amount); 
    			$need_goods_ids = implode(',', $temp_goods_ids);

    			$data=array(
    				'order_id'=>$order_info['order_id'],
    				'order_sn'=>$order_info['order_sn'],
    				'sender_id'=>$this->visitor->get('user_id'),
    				'sender_name'=>$this->visitor->get('user_name'),
    				'receiver_id'=>$order_info['buyer_id'],
    				'receiver_name'=>$order_info['buyer_name'],
    				'refund_reason'=>html_filter($_POST['apply_fee_reason']),
    				'refund_intro'=>html_filter($_POST['refund_intro']),
    				'apply_amount'=>$refund_amount,
    				'refund_amount'=>0,
    				'create_time'=>gmtime(),
    				'pay_time'=>0,
    				'status'=>0,
    				'closed'=>0,
    				'type'=>2,//代发申请补邮
    				'goods_ids'=>$need_goods_ids,
    				'goods_ids_flag'=>1,
    				'goods_ids_amount'=>$str_goods_amount,
	    		);
    		}
	
    		if(!$data['refund_reason'] || !$data['refund_intro'])
    		{
    			echo Lang::get('refund_reason_intro_unexist');
    			return;
    		}
    		
    		$affect_rows = $this->_orderrefund_mod->add($data);
    		
    		
    		if($affect_rows)
    		{
    			$refund_message = Lang::get('apply_fee_message').$order_info['order_sn'];
    			/* 连接用户系统 */
    			$ms =& ms();
    			$msg_id = $ms->pm->send($bh_id, array($order_info['buyer_id']), '', $refund_message);
    			
    			/* 发送给买家订单补收差价通知 */
    			$seller_info   = $ms->user->_local_get($order_info['buyer_id']);
    		//	$this->sendSaleSms($seller_info['phone_mob'], $refund_message);
    			$this->sendSms($seller_info['phone_mob'], $refund_message);
    			$this->pop_warning('ok',9);
    		}
    		else 
    		{
    			$this->pop_warning('caozuo_fail');
    		}
    
    	}
    
    }
    
    /**
     * 查看补差
     */
    function apply_fee_look()
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	$bh_id = $this->visitor->get('has_behalf');
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	
    	header('Content-Type:text/html;charset=' . CHARSET);
    	$refund_info = $this->_orderrefund_mod->get("order_id='{$order_id}' AND sender_id='{$bh_id}'  AND status='0' AND type='2' AND closed='0'");
    	
    	$goods_ids_amount_array = unserialize($refund_info['goods_ids_amount']);

    	$goods_ids_array = array_keys($goods_ids_amount_array); 

    	$goods = $this->_goods_warehouse_mod->find(array('conditions'=>"bh_id='{$bh_id}' AND ".db_create_in($goods_ids_array,'id')));

		$goods_amount_array = array();
		foreach ($goods_ids_amount_array as $key => $value) {

			 $goods_amount_array[$goods[$key]['goods_no']] = $value;
		}

    	$this->_import_css_js();
    	$this->assign('goods_amount_array', $goods_amount_array);
    	$this->assign('refund', $refund_info);
    	$this->display('behalf.order.apply_fee.look.html');
    	
    }

    /**
     * 取消补差  zjh 017/8/10
     */
    function cancel_apply_fee()
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	$bh_id = $this->visitor->get('has_behalf');
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	  
    	$conditions = "order_id='{$order_id}' AND sender_id='{$bh_id}'  AND status='0' AND type='2' AND closed='0'";
    	$refund_info = $this->_orderrefund_mod->drop($conditions);	
    	if($refund_info){
			echo json_encode(array('code' =>0 ,'msg' => '操作成功!'));
		}else {
			echo json_encode(array('code' =>-1 ,'msg' => '操作失败!'));
		}  
    }
    
    /**
     * 卖家留言
     */
    function sell_message()
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	
    	$order_info = $this->_order_mod->get("order_id={$order_id} ");
    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);
    		$this->assign('order', $order_info);
    		$this->display('behalf.order.seller_message.html');
    	}
    	else
    	{
    		/* 卖家留言*/
    		$seller_message = isset($_POST['seller_message']) ? trim($_POST['seller_message']) : '';
    		/* 标志*/
    		if(!empty($seller_message)) $seller_message_flag=2;
    		else $seller_message_flag=0;
    
    		$data = array(
    				'seller_message'  => html_filter($seller_message),
    				'seller_message_flag'  => $seller_message_flag,
    		);
    
    		$this->_order_mod->edit($order_id, $data);
    
    		if ($this->_order_mod->has_error())
    		{
    			$this->json_error($this->_order_mod->get_error());
    
    			return;
    		}
    
    		$this->json_result('ok','behalf_member_seller_message');
    	}
    }
    
    /**
     * 显示代发备忘录
     */
    function show_ordernote()
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	$model_ordernote = & m('behalfordernote');
    	$order_info = $model_ordernote->get("order_id={$order_id} ");
    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);
    		$this->assign('ordernote', $order_info);
    		$this->assign('order_id',$order_id);
    		$this->display('behalf.order.ordernote.html');
    	}
    }
    
    /**
     *    取消订单
     *
     *    @author    tiq
     *    @return    void
     */
    function cancel_order()
    {
    	/* 取消的和完成的订单不能再取消 */
    	$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
    	$bh_id = $this->visitor->get('has_behalf');
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	
    	$exist_order_refunds = $this->_exist_order_refunds($bh_id,$order_id);
        if($exist_order_refunds)
        {
            echo Lang::get('exist_order_refunds');
            return;
        }
    	
    	$status = array(ORDER_SUBMITTED, ORDER_PENDING, ORDER_ACCEPTED, ORDER_SHIPPED);
    	
    	/* 只有已发货的货到付款订单可以收货 */
    	$order_info     = $this->_order_mod->get("order_id={$order_id} AND bh_id={$bh_id} AND status " . db_create_in($status));
    	
    	if (!$order_info)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);
    		$this->_import_css_js();
    		$this->assign('order', $order_info);
    		$this->display('behalf.order.cancel.html');
    	}
    	else
    	{
    		//开启事务
    		$success = $this->_start_transaction();
    		
    		$id = $order_info['order_id'];

			//取消订单如果已经分配了快递单号，，将快递单号进行入库
			$mod_order_modeb = & m('ordermodeb');

			if(!empty($order_info['invoice_no'])){
				$mod_order_modeb->edit("order_id='{$id}'" , array('status'=>0));
			}

    		
    		$affect_rows =	$this->_order_mod->edit($order_info['order_id'], array('status' => ORDER_CANCELED));
    		!$affect_rows && $success = false;//回滚
    
    			/*商付通v2.2.1  更新商付通定单状态 开始*/
    			$my_money_mod =& m('my_money');
    			$my_moneylog_mod =& m('my_moneylog');
				$order_row = $this->_order_mod->getrow("select * from ".DB_PREFIX. "order where order_id='$id'");

    		//	$my_moneylog_row=$my_moneylog_mod->getrow("select * from ".DB_PREFIX."my_moneylog where order_id='$id' and (caozuo='10' or caozuo='20') and s_and_z=1");
    			$money=$order_row['order_amount'];//定单价格
    			$buy_user_id=$order_row['buyer_id'];//买家ID
    			$sell_user_id=$order_row['bh_id'];//卖家ID
    			if($order_row['order_id']==$id)
    			{
    				$buy_money_row=$my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$buy_user_id'");
    				$buy_money=$buy_money_row['money'];//买家的钱
    				$buy_money_dj=$buy_money_row['money_dj'];//买家的钱
    
    				$sell_money_row=$my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$sell_user_id'");
    				$sell_money=$sell_money_row['money'];//卖家的冻结资金
    				$sell_money_dj=$sell_money_row['money_dj'];//卖家的冻结资金
    
    				$new_buy_money = $buy_money+$money;
    				$new_sell_money = $sell_money_dj-$money;
    				//更新数据

    				$affect_rows = $my_money_mod->edit('user_id='.$buy_user_id,array('money'=>$new_buy_money));

    				!$affect_rows && $success = false;//回滚

    				$affect_rows = $my_money_mod->edit('user_id='.$sell_user_id,array('money_dj'=>$new_sell_money));
    				!$affect_rows && $success = false;//回滚
    				//更新商付通log为 定单已取消


                    $add_mymoneylog = array(
                        //	'user_id' => $user_id,
                        //	'user_name' => $user_name,
                        'buyer_id' => $order_info['buyer_id'],
                        'buyer_name' => $order_info['buyer_name'],
                        'seller_id' => $sell_user_id,
                        'seller_name' => $order_info['seller_name'],
                        'order_id' => $order_info['order_id'],
                        'order_sn' => $order_info['order_sn'],
                        'add_time' => gmtime(),
                        'admin_time' => gmtime(),
                        'leixing' => 30,
                        'money_zs' => $money,
                        'money' => $money,
                        'log_text'=> '取消订单',
                        'user_log_del' => 0,
                        'caozuo' => 30,
                        's_and_z' => 1,
                    );



    				$change_buyer = array_merge($add_mymoneylog , array('user_id' => $order_info['buyer_id'],'user_name'=> $order_info['buyer_name'],'moneyleft' => $new_buy_money));

                    $change_seller = array_merge($add_mymoneylog , array('user_id' => $order_info['bh_id'],'user_name'=>  $order_info['bh_id'],'moneyleft' => $new_sell_money));

    				Log::write('dualven:behalf:'.var_export($change_buyer,true));
    				$affect_rows = $my_moneylog_mod->add($change_buyer);
    				!$affect_rows && $success = false;//回滚
    				$affect_rows = $my_moneylog_mod->add($change_seller);
    				!$affect_rows && $success = false;//回滚

    			}
    			/*商付通v2.2.1  更新商付通定单状态 结束*/
    			
    			//退还分润,必须是已发货或已完成，且退货有商品
    			if(in_array($order_info['status'], array(ORDER_SHIPPED,ORDER_FINISHED)))
    			{
    				$refund_results=$this->_orderrefund_mod->find(array(
    						'conditions'=>"order_id={$id} AND sender_id={$order_info['buyer_id']} AND receiver_id={$bh_id} AND status=0 AND closed=0 AND type=1"
    				));
    				if(count($refund_results) == 1)
    				{
    					$refund_result = current($refund_results);
    					if($refund_result['goods_ids'])
    					{
    						//计算返款
    						$rec_ids = explode(',', $refund_result['goods_ids']);
    						$rec_goods = $this->_ordergoods_mod->find(array(
    								'conditions'=> "order_id={$id} AND ".db_create_in($rec_ids,'rec_id')
    						));
    						$behalf_discount = 0;
    						if($rec_goods)
    						{
    							foreach ($rec_goods as $goods)
    							{
    								if($goods['oos_value'] && $goods['behalf_to51_discount'] > 0)
    								{
    									$behalf_discount += $goods['behalf_to51_discount'];
    									$affect_rows = $this->_ordergoods_mod->edit($goods['rec_id'],array('zwd51_tobehalf_discount'=>$goods['behalf_to51_discount']));
    									!$affect_rows && $success = false;//回滚
    								}
    							}
    						}
    
    						if($behalf_discount > 0)
    						{
    							include_once(ROOT_PATH.'/app/fakemoney.app.php');
    							$fakemoneyapp = new FakeMoneyApp();
    							$fr_reason = Lang::get('behalf_to_51_tk_reason').local_date('Y-m-d H:i:s',gmtime());
    							//给用户转账
    							$my_money_result=$fakemoneyapp->to_user_withdraw(FR_USER,$bh_id,$behalf_discount, $fr_reason,$order_info['order_id'],$order_info['order_sn']);
    							$my_money_result !== true && $success = false; //回滚
    						}
    					}
    				}
    			}
    
    			/* 加回订单商品库存 */
    			$cancel_reason = (!empty($_POST['remark'])) ? $_POST['remark'] : $_POST['cancel_reason'];
    			$cancel_reason .= " ".Lang::get('order_sn').":".$order_info['order_sn'].";".Lang::get('reback_money_success');
    			/* 记录订单操作日志 */
    			$affect_rows = $this->_orderlog_mod->add(array(
	    					'order_id'  => $id,
	    					'operator'  => addslashes($this->visitor->get('user_name')),
	    					'order_status' => order_status($order_info['status']),
	    					'changed_status' => order_status(ORDER_CANCELED),
	    					'remark'    => $cancel_reason,
	    					'log_time'  => gmtime(),
	    		));
    			!$affect_rows && $success = false;//回滚
				if($success){
					//订单解绑
					$noreply_info = $this->getNoreply();
					unbindOrder($noreply_info['token'] , $order_id);
				}
    			//提交或回滚
    			$this->_end_transaction($success);
    			
    			if($success)
    			{
	    			/* 连接用户系统 */
	    			//$ms =& ms();
	    			//$buyer_info   = $ms->user->_local_get($order_info['buyer_id']);
	    			//$msg_id = $ms->pm->send($this->visitor->get('user_id'), array($order_info[$id]['buyer_id']), '', Lang::get('order_cancel_notice').$cancel_reason);
	    			/*短信通知*/
	    			//$this->sendSaleSms($buyer_info['phone_mob'], Lang::get('order_cancel_notice').$cancel_reason);
	    
	    			/* 如果是关联到淘宝订单的话, 需要同时修改淘宝订单的状态 */
	    			$ordervendor_mod = &m('ordervendor');
	    			$ordervendor_mod->edit("ecm_order_id={$id}", array(
	    					'status' => VENDOR_ORDER_UNHANDLED,
	    					'ecm_order_id' => 0));
	    		
	    			$this->pop_warning('ok', 'behalf_member_cancel_order');
    			}
    			else 
    			{
    				$this->pop_warning('caozuo_fail');
    			}
    	} //end post else
    
    }
    /**
     * 订单是否存在退款申请
     */
    private function _exist_order_refunds($bh_id,$order_id)
    {
        $exist_refunds = $this->_orderrefund_mod->get("receiver_id='{$bh_id}' AND order_id='{$order_id}' AND type='1' AND closed='0' AND status='0'");
        return empty($exist_refunds)?false:true;
    }

    

    /**
     * 处理退货退款请求
     */
    function applied_refund()
    {
    	//2015-11-22 暂停关闭，存在并发性能问题
    	 $bh_id = $this->visitor->get('has_behalf');
    	//利用php文件锁解决并发问题
    	$lock_file = ROOT_PATH."/data/applied_refund.lock";
    	if(!file_exists($lock_file))
    	{
    		file_put_contents($lock_file, 1);
    	}
    
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	if (!$order_id)
    	{
    		echo Lang::get('no_such_order');
    		return;
    	}
    	//$model_order    =&  m('order');
    	//$model_ordergoods =& m('ordergoods');
    	//$model_orderrefund = & m('orderrefund');
    	//对文件加锁
    	$fp = fopen($lock_file, 'a+');
    	if(!$fp)
    	{
    		echo 'fail to open lock file!';
    		return;
    	}
    	flock($fp, LOCK_EX);
    
    	/* 只有已付款和已经发货、已完成的订单可以申请退货退款 */
    	$order_info     = $this->_order_mod->get("order_id={$order_id}  AND bh_id={$bh_id} AND status " . db_create_in(array(ORDER_ACCEPTED, ORDER_SHIPPED,ORDER_FINISHED)));
    	$order_info_status = $order_info['status'];//记录订单变化状态
    	if(!empty($order_info))
    	{
    		$refund_results=$this->_orderrefund_mod->find(array(
    				'conditions'=>'order_id='.$order_info['order_id'].' AND sender_id='.$order_info['buyer_id'].' AND receiver_id='.$order_info['bh_id']." AND status='0' AND closed='0' AND type='1'",
    		));
    	}
    	else
    	{
    		$refund_results = array();
    	}
    
    	/*文件解锁*/
    	flock($fp, LOCK_UN);
    	fclose($fp);
    
    
    	if(count($refund_results) != 1)
    	{
    		echo count($refund_results)>1?Lang::get('feifashenqi_1'):Lang::get('feifashenqi_0');
    		return ;
    	}
    	$refund_result = array();
    	foreach ($refund_results as $value)
    	{
    		if($value['status'] == 0 && $value['closed'] == 0)
    			$refund_result = $value;
    	}
    	//补丁，用于退货商品显示
    	$refund_result['refund_intro'] = str_replace('00;','00<br/>',$refund_result['refund_intro']);
    	if(!$refund_result || $refund_result['apply_amount'] <= 0)
    	{
    		echo Lang::get('feifashenqi_3');
    		return ;
    	}

    	$goods_ids = explode(',' , $refund_result['goods_ids']);

		$goods_refund = $this->_goods_warehouse_mod->find(array(
			'conditions' => db_create_in($goods_ids , 'id'),
		));
		$refund_tmp = reset($refund_results);
		$refund_status = array_combine(explode(',',$refund_tmp['goods_ids']) , explode(',',$refund_tmp['refund_project']));
		$reason_tmp = array();
		foreach($goods_refund as $k=> &$v){

			$v['project'] = $refund_status[$k];
			if($v['project'] != 1){
				array_push( $reason_tmp , $k);
			}
		}

    	if (!IS_POST)
    	{
    		header('Content-Type:text/html;charset=' . CHARSET);

    		$this->_import_css_js();

			$mod_behalf  = & m('behalf');
			$shipping_fee = $mod_behalf->get_shipping_fee_after_order_cancel($order_info['order_id'] , $reason_tmp ,'keep');
			$this->assign('shipping_fee', $shipping_fee);
    		$this->assign('order', $order_info);
    		$this->assign('refund',$refund_result);
            $this->assign('refund_goods', $goods_refund);
    		$this->assign("show_refund",count($refund_results) > 1 ?false:true);
    		$this->display('behalf.order.applied_refund.html');
    	}
    	else
    	{
    		$totalprice = isset($_POST['totalprice'])?intval(trim($_POST['totalprice'])):0;
			$goods_ids = isset($_POST['goods_ids']) ? $_POST['goods_ids']: array();
			$goods_price = isset($_POST['goods_price']) ? $_POST['goods_price']: array();
			$project_checks = isset($_POST['project_checks']) ? $_POST['project_checks']: array();

			$shipping_fee = isset($_POST['shipping_fee'])?intval(trim($_POST['shipping_fee'])):0;
			$back_shipping_fee = isset($_POST['back_shipping_fee'])?intval(trim($_POST['back_shipping_fee'])):0;


    		$refund_agree = isset($_POST['agree'])?intval(trim($_POST['agree'])):0;
    		$zf_pass = isset($_POST['zf_pass'])?trim($_POST['zf_pass']):'';
    		if(!in_array($refund_agree, array(1,2)))
    		{
    			//$this->pop_warning("feifacaozuo");
    			echo Lang::get("feifacaozuo");
    		    return;
    		}
    		if(empty($zf_pass) && $refund_agree==1)
    		{
    			//$this->pop_warning("passwd_again");
    			echo Lang::get("passwd_again");
    			return;
    		}
    		//$refund_result = $refund_result;
    
    		//开始数据库事务
    		$db_transaction_begin = db()->query("START TRANSACTION");
    		if($db_transaction_begin === false)
    		{
    			//$this->pop_warning('fail_caozuo');
    		    echo Lang::get("fail_caozuo");
    		    return;
    		}
    		$db_transaction_success = true;//默认事务执行成功，不用回滚
    		$db_transaction_reason = '';//回滚的原因
    		//同意退款转账
    		if($refund_agree == 1)
    		{
    		    /*for debug*/
    		    //echo 'start<br>';


				/****************添加退款金额为可调整start**************/
				$claim_order_model = & m('claimorder');
				$claim_goods_model = & m('claimgoods');
				$order_data = array(
					'order_id' => $order_info['order_id'] ,
					'shipping_fee' => $shipping_fee ,
					'back_shipping_fee' => $back_shipping_fee ,
					'goods_fee'	=> array_sum($goods_price),
					'total_price'	=> $totalprice ,
					'add_time'	=> time(),
					'total_quantity' => count($goods_ids),
				);

				$claim_result = $claim_order_model->add($order_data);
				$refund_result['apply_amount'] = $totalprice;
				!$claim_result && $db_transaction_success = false;
				foreach($goods_ids as $k=>$v){
					$goods_data = array(
						'goods_id'	=> $v,
						'order_id'	=> $order_info['order_id'] ,
						'goods_fee' => $goods_price[$k],
						'project'	=> $project_checks[$k],
					);
					$claim_goods_result = $claim_goods_model->add($goods_data);
					!$claim_goods_result && $db_transaction_success = false;
				}



				/****************添加退款金额为可调整end**************/


    		    
    			$data=array(
    					'order_id'=>$order_info['order_id'],
    					'order_sn'=>$order_info['order_sn'],
    					'refund_amount'=>$refund_result['apply_amount'] ,
    					'pay_time'=>gmtime(),
    					'status'=>$refund_agree,
    					'closed'=>0
    			);
    			$refund_message = Lang::get('refund_message').$order_info['order_sn'].','.$refund_result['apply_amount'];
    			 
    			$affect_rows = $this->_orderrefund_mod->edit($refund_result['id'],$data);
    			if(!$affect_rows){
    			    $db_transaction_success = false;
    			    $db_transaction_reason = 'update_refund_failed';
    			}
    			//退货统计
    			if($refund_result['goods_ids'] && $refund_result['goods_ids_flag'])
    			{
    			    $whgoods_ids = explode(',', $refund_result['goods_ids']);
    			    $whgoods_ids = array_unique($whgoods_ids);
    			    $whgoods_ids = array_filter($whgoods_ids);
    			    
    			    $reback_goods = $this->_goods_warehouse_mod->find(array('conditions'=>db_create_in($whgoods_ids,'id')));
    			    $reback_goods_ids = array();//允许重复goods_id
    			    if($reback_goods)
    			    {    foreach ($reback_goods as $rbgoods)
        			    {
        			        $reback_goods_ids[] = $rbgoods['goods_id'];
        			    }
    			    }
    			}

    			//退货则标记为退货状态,主要是考虑 待发货的 订单，不用返回 分润，因为发货时才分润
    			if($refund_result['goods_ids'] && $refund_result['goods_ids_flag'] && $order_info['status'] == ORDER_ACCEPTED)
    			{
    			    $affect_rows = $this->_goods_warehouse_mod->edit(db_create_in($whgoods_ids,'id'),array('goods_status'=>BEHALF_GOODS_REBACK));
    			    if(!$affect_rows){
    			        $db_transaction_success = false;
    			        $db_transaction_reason = 'update_goodswarecase_fail';
    			    }
    			    //统计退货
    			    if($reback_goods_ids)
    			    {
    			        foreach ($reback_goods_ids as $rbgoods_id)
    			        {
    			            $goods_statistics = $this->_goods_statistics_mod->get($rbgoods_id);
    			            if($goods_statistics)
    			            {
    			                $this->_goods_statistics_mod->edit("{$rbgoods_id}",'backs=backs+1');
    			            }
    			            else
    			            {
    			                $this->_goods_statistics_mod->add(array('goods_id'=>$rbgoods_id,'backs'=>1));
    			            }
    			        }
    			    }
    			}
    			
    			
    			    			
    			include_once(ROOT_PATH.'/app/fakemoney.app.php');
    			$fakemoneyapp = new FakeMoneyApp();

    			//退还分润,必须是已发货或已完成，且退货有商品
    			if(in_array($order_info['status'], array(ORDER_SHIPPED,ORDER_FINISHED)) && $refund_result['goods_ids'])
    			{
    				//计算返款
    				$rec_ids = explode(',', $refund_result['goods_ids']);  
    				$rec_ids = array_unique($rec_ids);
    				
    				$behalf_discount = 0;
    				
    				if(!$refund_result['goods_ids_flag'])//如果是goods_id
    				{
        				$rec_goods = $this->_ordergoods_mod->find(array(
        				    'conditions'=> 'order_id='.$order_id.' AND '.db_create_in($rec_ids,'rec_id'),
        				));
        				if($rec_goods)
        				{
        					foreach ($rec_goods as $goods)
        					{	// 当前流程只处理有理由退货

        						//商品仓库退货标记,如果只退 同一规格的 一件，如果同一订单有多件，则都会 标记！需要 修正
        						$affect_rows = $this->_goods_warehouse_mod->edit("order_id ='{$goods['order_id']}' AND goods_id='{$goods['goods_id']}' AND goods_spec_id='{$goods['spec_id']}' ",array('goods_status'=>BEHALF_GOODS_REBACK));
        						if(!$affect_rows){
        						    $db_transaction_success = false;
        						    $db_transaction_reason = 'update_goodswarecase_fail';
        						}
        						
        						if($goods['oos_value'] && $goods['behalf_to51_discount'] > 0)
        						{
        							$behalf_discount += $goods['behalf_to51_discount'];
        							$affect_rows = $this->_ordergoods_mod->edit($goods['rec_id'],array('zwd51_tobehalf_discount'=>$goods['behalf_to51_discount']));
        							if(!$affect_rows){
        							    $db_transaction_success = false;
        							    $db_transaction_reason = 'update_ordergoods_fail';
        							}
        							
        						}
        					}
        				}
    				}
    				else //如果是goods_warehouse的id 
    				{

    				    $warehouse_goods = $this->_goods_warehouse_mod->find(array(
    				        'conditions'=> 'order_id='.$order_id.' AND '.db_create_in($rec_ids,'id'),
    				    ));
    				    if($warehouse_goods)
    				    {
    				        foreach ($warehouse_goods as $whgoods)
    				        {

								if(!in_array($whgoods['id'] , $goods_ids)){
									continue;
								}
    				            //商品仓库退货标记,如果只退 同一规格的 一件，如果同一订单有多件，则都会 标记！需要 修正
    				            $affect_rows = $this->_goods_warehouse_mod->edit("id ='{$whgoods['id']}' ",array('goods_status'=>BEHALF_GOODS_REBACK,'refund_id'=>$refund_result['id']));
    				            if(!$affect_rows){
    				                $db_transaction_success = false;
    				                $db_transaction_reason = 'update_goodswarecase_fail';
    				            }
    				    
    				            if($whgoods['goods_status'] == BEHALF_GOODS_SEND && $whgoods['behalf_to51_discount'] > 0)
    				            {
    				                $behalf_discount += $whgoods['behalf_to51_discount'];
    				                $affect_rows = $this->_goods_warehouse_mod->edit($whgoods['id'],array('zwd51_tobehalf_discount'=>$whgoods['behalf_to51_discount']));
    				                if(!$affect_rows){
    				                    $db_transaction_success = false;
    				                    $db_transaction_reason = 'update_ordergoods_fail';
    				                }
    				                 
    				            }
    				        }
    				    }
    				    //统计退货
    				    if($reback_goods_ids)
    				    {
    				        foreach ($reback_goods_ids as $rbgoods_id)
    				        {
    				            $goods_statistics = $this->_goods_statistics_mod->get($rbgoods_id);
    				            if($goods_statistics)
    				            {
    				                $this->_goods_statistics_mod->edit("{$rbgoods_id}",'backs=backs+1');
    				            }
    				            else
    				            {
    				                $this->_goods_statistics_mod->add(array('goods_id'=>$rbgoods_id,'backs'=>1));
    				            }
    				        }
    				    }
    				    
    				}
    				
    				
    				if($behalf_discount > 0)
    				{
    					$fr_reason = Lang::get('behalf_to_51_tk_reason').local_date('Y-m-d H:i:s',gmtime());
    					//给用户转账
    					$my_money_result=$fakemoneyapp->to_user_withdraw(FR_USER,$this->visitor->get('user_id'),$behalf_discount, $fr_reason,$order_info['order_id'],$order_info['order_sn']);
    					if($my_money_result !== true)
    					{
    						//echo "fenrun reback! <br>";
    						$db_transaction_success = false;
    						$db_transaction_reason = 'fr_to_user_withdraw_fail';
    
    					}
    				}
    			}
    			/*for debug*/
                //echo 'fr_over#';
    
    			include_once(ROOT_PATH.'/app/my_money.app.php');
    			$my_moneyapp = new My_moneyApp();


    			//给用户转账
    			$my_money_result=$my_moneyapp->to_user_withdraw($order_info['buyer_name'],$refund_result['apply_amount'], $order_id,$order_info['order_sn'],$zf_pass);
				if($my_money_result !== true)
    			{
    				//echo "pay user.<br>";
    				$db_transaction_success = false;
    				$db_transaction_reason = $my_money_result;
    				
    			}

    			/*for debug*/
                //echo 'zz_over#';
    			//全额退款时，才解冻订单全部资金，自动关闭订单。未发货=订单总价，已发货=订单商品价格			ZH 增加前的可能,当标记为全额退款时
    			if($refund_result['apply_amount'] == $order_info['goods_amount'] || $refund_result['apply_amount'] == $order_info['order_amount'] || stripos($refund_result['refund_reason'] , '全额退款') !== FALSE)
    			{
    				if($order_info['status'] != ORDER_FINISHED)
    				{
    					
    					//这是相当于收货了，订单资金解冻
    					$my_money_result=$my_moneyapp->jd_behalf_refund($this->visitor->get('user_id'),$order_info['order_amount'], $order_info['order_sn']);
    					
    					if($my_money_result !== true)
    					{
    						//echo "jd money.<br>";
    						$db_transaction_success = false;
    						$db_transaction_reason = "jd_failed";
    					}
    				}
                   
    				$affect_rows = $this->_order_mod->edit($order_info['order_id'], array('status' => ORDER_CANCELED));

    				if (empty($affect_rows))
    				{
    					//echo "cancel order.<br>";
    					$db_transaction_success = false;
    					$db_transaction_reason = 'update_order_status_fail';
    				}
    
    				//商付通 更新状态
    				$my_moneylog_mod =& m('my_moneylog');
    				$affect_rows = $my_moneylog_mod->edit('order_id='.$order_info['order_id'],array('caozuo'=>80));
    				if(!$affect_rows){
    				    $db_transaction_success = false;
    				    $db_transaction_reason = 'update_moneylog_fail';
    				}
    				//商付通 结束
    				$order_info_status = ORDER_CANCELED;
    			}
    			/*for debug*/
                //echo 'tk_over#';
    			//这是已完成订单申请的退款，前面手动冻结，现在解冻
    			if($order_info['status'] == ORDER_FINISHED)
    			{
    				$affect_result	= $fakemoneyapp->manuRefro($order_info['bh_id'], $refund_result['apply_amount']);
    				if($affect_result === false)
    				{
    					//$db_transaction_success = false;
    					//$db_transaction_reason = 'jd_failed';
    				}
    			}
    
    
    		}
           
    		if($refund_agree == 2)
    		{
    			$data=array(
    					'order_id'=>$order_info['order_id'],
    					'order_sn'=>$order_info['order_sn'],
    					'refuse_reason'=>html_filter(trim($_POST['refuse_reason'])),
    					'status'=>$refund_agree,
    					'closed'=>isset($_POST['reapplay_refund'])&& !empty($_POST['reapplay_refund'])?1:0
    			);
    			$refund_message = Lang::get('refund_message_disagree').$order_info['order_sn'].','.$refund_result['apply_amount'];

    			$affect_rows = $this->_orderrefund_mod->edit($refund_result['id'],$data);
    			if(empty($affect_rows))
    			{
    				//echo "refuse requent.<br>";
    				$db_transaction_success = false;
    				$db_transaction_reason = 'refuse_update_refund_fail';
    				 
    				
    			}
    		}
    
    		
    	 $affect_rows = $this->_orderlog_mod->add(array(
    				'order_id'  => $order_info['order_id'],
    				'operator'  => addslashes($this->visitor->get('user_name')),
    				'order_status' => order_status($order_info['status']),
    				'changed_status' => order_status($order_info_status),
    				'remark'    => $refund_message,
    				'log_time'  => gmtime(),
    		));

    	 
    	 if(!$affect_rows){
    	     $db_transaction_success = false;
    	     $db_transaction_reason = 'add_orderlog_fail';
    	 }
    	 /*for debug*/
    	 //echo 'endlog';
    	 /*for debug*/
    	 //$db_transaction_success = false;
    	 
    	 if($db_transaction_success === false)
    	 {
    	 	db()->query("ROLLBACK");//回滚
    	 }
    	 else
    	 {
    	 	db()->query("COMMIT");//提交
    	 }
    	 
    	 if($db_transaction_success)
    	 {
    	 	/* 连接用户系统 */
    	 	$ms =& ms();
    	 	$msg_id = $ms->pm->send($this->visitor->get('user_id'), array($order_info['buyer_id']), '', $refund_message);
    	 	
    	 	/* 如果是关联到第三方订单的话, 需要同时修改淘宝订单的状态 */
    	 	$ordervendor_mod = &m('ordervendor');
    	 	$ordervendor_mod->edit("ecm_order_id=".$order_info['order_id'], array(
    	 			'status' => VENDOR_ORDER_UNHANDLED,
    	 			'ecm_order_id' => 0));
    	 	
    	 	echo Lang::get('refund_ok');
    	 	return;
    	 }
    	 else 
    	 {
    	 	echo Lang::get($db_transaction_reason) ;
    	 	return ;
    	 }
    	 
    	echo 'end';
    		
    	}
    }
    
    
    
    /**
     * @param data
     */
    private function _check_region($data)
    {
    	$regionArr = $this->_region_mod->get_layer($data['region_id']);
    	$region_name ='';
    
    	if(!$data['region_id'])
    	{
    		$this->pop_warning('region_illeage');
    		return;
    	}
    	if(!$this->_region_mod->isleaf($data['region_id']))
    	{
    		$this->pop_warning('region_illeage');
    		return;
    	}
    	foreach ($regionArr as $region)
    	{
    		if(strpos($data['region_name'],$region['region_name'])===false)
    		{
    			$this->pop_warning($region['region_name']);
    			return;
    		}
    		$region_name .= $region['region_name'].' ';
    	}
    	if(!preg_match('/^1[34578][0-9]{9}$/',$data['phone_mob']))
    	{
    		$this->pop_warning('phone_illeage');
    		return;
    	}
    	if(!empty($data['zipcode']))
    	{
    		if(!preg_match('/\d{6}/',$data['zipcode']))
    		{
    			$this->pop_warning('zipcode is error!');
    			return;
    		}
    	}
    
    
    	$data['region_name'] = $region_name;
    	return $data;
    }
    
    function _get_valid_order_info($status, $ext = '')
    {
    	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    	if (!$order_id)
    	{
    		return array();
    	}
    	
    	if (!is_array($status))
    	{
    		$status = array($status);
    	}
    
    	if ($ext)
    	{
    		$ext = ' AND ' . $ext;
    	}
    	
    	/* 只有已发货的货到付款订单可以收货 */
    	$order_info     = $this->_order_mod->get(array(
    			'conditions'    => "order_id={$order_id} " . " AND status " . db_create_in($status) . $ext,
    	));
    	if (empty($order_info))
    	{
    		return array();
    	}
    
    	return array($order_id, $order_info);
    }
    
    /**
     * 代发备忘录
     */
    function save_ordernote()
    {
    	$order_id = isset($_GET['order_id']) && $_GET['order_id'] ? trim($_GET['order_id']) :0;
    	$content = trim($_POST['content']);
    	if(!$order_id || empty($content))
    	{
    		$this->json_error('caozuo_fail');
    		return;
    	}
    	$model_ordernote = & m('behalfordernote');
    	$note_info = $model_ordernote->get($order_id);
    	if(empty($note_info))
    	{
    		$affect_rows = $model_ordernote->add(array('order_id'=>$order_id,'content'=>html_filter($content),'create_time'=>gmtime(),'login_id'=>$this->visitor->get('user_id')));
    		if($affect_rows)
    		{
    			$this->json_result(1,'caozuo_success');
    			return;
    		}
    		else 
    		{
    			$this->json_error('caozuo_fail');
    			return;
    		}
    	}
    	else 
    	{
    		if($this->visitor->get('user_id') != $note_info['login_id'])
    		{
    			$this->json_error('cannot_modify_others');
    			return;
    		}
    		$affect_rows = $model_ordernote->edit("order_id ='{$order_id}' ",array('content'=>html_filter($content)));
    		if($affect_rows)
    		{
    			$this->json_result(1,'caozuo_success');
    			return;
    		}
    		else
    		{
    			$this->json_error('caozuo_fail');
    			return;
    		}
    	}
    }
    
    public function mb_print()
    {
    	$this->_behalf_printer->mb_print();
    }

    public function scan_print(){
    	$this->_behalf_printer->scan_print();
	}
    
    public function common_print()
    {
    	$this->_behalf_printer->common_print();
    }
    
    public function get_invoice_no()
    {
    	$result = $this->_behalf_printer->get_invoice_no();
    	if($result === true)
    	{
    		$this->json_result(1,'modeb_success');
    	}
    	else 
    	{
    		$this->json_error($result);
    	}
    }

    public function get_invoice_no_ajax(){
    	$result = $this->_behalf_printer->get_invoice_no_ajax();
		$result = json_decode($result,true);

	if ($result['code'] == 0){
		$this->json_result(1,$result['msg']);
	}else{
		$this->json_error($result['msg']);
	}

	}
    /**
     * 显示模板
     */
    public function getMailCounter()
    {
        $this->_assign_leftmenu('setting');
        $this->display('behalf.system.getMailCounter.html');
    }
    /**
     * ajax
     */
    public function getMailCounter_ajax()
    {
        $result = $this->_behalf_printer->getMailCounter();
        if($result === false)
        {
           //$this->json_error(false); 
           echo ecm_json_encode(false);
        }
        else
        {
           //$this->json_result($result,'modeb_success');
           echo $result;
           
        }
    }
    
    public function async_shipped()
    {
    	$result = $this->_behalf_printer->async_shipped();
    	if($result === true)
    	{
    		$this->json_result(1,'success');
    	}
    	else
    	{
    		$this->json_error($result);
    	}
    }
    
    public function save_invoiceno()
    {
    	$result = $this->_behalf_printer->save_invoiceno();
    	if($result === false)
    	{
    		return ;
    	}
    	else
    	{
    		echo ecm_json_encode(array('invoice'=>$result,'code'=>0));
    	//	return $result;
    	}
    }
    
    public function check_invoiceno()
    {
    	$result = $this->_behalf_printer->check_invoiceno();
    	echo ecm_json_encode($result);
    }
    
    /**
     * 
     */
    public function member_list()
    {
    	$this->_behalf_client->member_list();
    	
    }
    public function store_black_list()
    {
    	$this->_behalf_client->store_black_list();
    	
    }
    public function new_clients_stats()
    {
        $this->_behalf_client->new_clients_stats();
    }
    public function vip_switch()
    {
        $this->_behalf_client->vip_switch();
    }
    public function vip_discount()
    {
        $this->_behalf_client->vip_discount();
    }
    public function vip_update()
    {
        $this->_behalf_client->vip_update();
    }
    public function vip_conf()
    {
        $this->_behalf_client->vip_conf();
    }
    public function vip_list()
    {
        $this->_behalf_client->vip_list();
    }
    public function vip_upgrade()
    {
        $this->_behalf_client->vip_grade();
    }
    /**
     * 统计所有下单数，及分润！
     */
    public function stat_all_fr()
    {
        $shipped_orders = 0;//正在发货的单数
        $finished_orders = 0;//已完成的单数
        
        $fr_deliverys = 0;//邮费分润
        $fr_store = 0; //商品优惠分润总金额
        $fr_storeback = 0; //退还分润
        
        $order_list = $this->_order_mod->findAll(array(
           'conditions'=>"order_alias.bh_id = {$this->visitor->get('has_behalf')} AND order_alias.status ".db_create_in(array(
               ORDER_SHIPPED,ORDER_FINISHED
           )), 
           'include'=>array('has_ordergoods')
        ));
        if($order_list)
        {
           foreach ($order_list as $order)
           {
               if($order['status'] == ORDER_SHIPPED)
               {
                   $shipped_orders ++ ;
               }
               elseif ($order['status'] == ORDER_FINISHED)
               {
                   $finished_orders ++ ;
               }
               $fr_deliverys += floatval($order['behalf_discount']) ;//快递 和 优惠和
               
               foreach ($order['order_goods'] as $goods)
               {
                   if($goods['oos_value'])
                   {
                       $fr_store += floatval($goods['behalf_to51_discount']);
                       $fr_storeback += floatval($goods['zwd51_tobehalf_discount']);
                   }
                   
               }
               
           }
        }
        
        $data = array(
          'total_orders'=>$shipped_orders + $finished_orders,
          'shipped_orders'=>$shipped_orders,
          'finished_orders'=>$finished_orders,
          'fr_deliverys'=>$fr_deliverys - $fr_store,
          'goods_fr'=>$fr_store,
          'reback_fr'=>$fr_storeback,
          'fr_result'=>$fr_store - $fr_storeback
        );
        $this->json_result($data,'success');
        //dump($data);
    }
    
    /**
     *   主动退还订单缺货款
     *   订单为 待发货， 已发货，已完成 情况下 
     */
    function refund_lackgoods()
    {
        $order_id = $_GET['order_id'] ? intval($_GET['order_id']):0;
        //查询款项信息
        $mod_ordercompensationbehalf = & m('ordercompensationbehalf');
        $compensation_info = $mod_ordercompensationbehalf->get("order_id='".$order_id."' AND type='lack'");
        if($compensation_info)
        {
            echo Lang::get('compensation_lack_exist');
            return;
        }
        //查询订单信息
        $order_info = $this->_order_mod->findAll(array(
           'conditions'=>"order_alias.order_id = {$order_id} AND order_alias.bh_id={$this->visitor->get('has_behalf')}"." AND ".db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED),"order_alias.status"),
           'include'=>array('has_goodswarehouse')
        ));
        if(empty($order_info))
        {
            echo 'empty order!';
            return;
        }
        //如果订单有申请退款，暂停主动退款
        $refund_info = $this->_orderrefund_mod->find(array('conditions'=>"order_id={$order_id} and receiver_id={$this->visitor->get('has_behalf')} and type=1 and closed=0"));
        if(!empty($refund_info) && $refund_info['status'] == 0 )
        {
            echo Lang::get('order_refunding');
            return;
        }
        
        
        //统计需退款项
        $total_amount = 0;//缺货总款项
        $total_count = 0;//缺货总件数
        $order_info = current($order_info);
        if($order_info['gwh'])
        {
            foreach ($order_info['gwh'] as $goods)
            {
                //备货中 和 已备货 在退款时 不属于 缺货了   zjh 2017/8/11 添加停止拿货
                if(in_array($goods['goods_status'],array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE,BEHALF_GOODS_STOP_TAKING)))
                {
                    $total_amount += floatval($goods['goods_price']);
                    $total_count++;
                }
            }
        }
        //代发信息
        $bh_info = &m('behalf')->get($order_info['bh_id']);
        //缺货款为0，退出
        if(empty($total_amount))
        {
            echo Lang::get('order_lack_amount_0');
            return;
        }
        
        $increment_deli = 2;//每多一件商品多收3元运费
        
        if(!IS_POST)
        {
            $this->assign('buyer_name',$order_info['buyer_name']);
            //这里 应该根据快递 查询 得出 每增加一件增加的运费 3或2
            $this->assign('total_amount',number_format($total_amount + $total_count*$increment_deli,2));
            $this->assign('order_id',$order_id);
            $this->display('behalf.order.compensation.lack.html');
        }
        else 
        {
            $pay_amount = $_POST['pay_amount'] ? floatval($_POST['pay_amount']) :0;
            //$pay_amount = $total_amount;
            if($refund_info['status'] == 1)
            {
               if($pay_amount + $refund_info['apply_amount'] >= $order_info['goods_amount'])
               {
                   echo Lang::get('amount_too_much_correct');
                   return;
               }
            }
            //限制转账金额
            if($pay_amount > ($total_amount + $total_count * $increment_deli) || $pay_amount > 1000)
            {
                echo sprintf(Lang::get('pay_amount_too_much'),$pay_amount);
                return;
            }
            
            $zf_pass = trim($_POST['zf_pass']);
            
            //开启事务
            $db_transaction_begin = db()->query("START TRANSACTION");
            if($db_transaction_begin === false)
            {
                //$this->pop_warning('fail_caozuo');
                echo 'fail_to_transaction';
                return;
            }
            $db_transaction_success = true;//默认事务执行成功，不用回滚
            
            include_once(ROOT_PATH.'/app/my_money.app.php');
    		$my_moneyapp = new My_moneyApp();
    		
    		$result_pay = $my_moneyapp->to_user_withdraw($order_info['buyer_name'],$pay_amount, $order_id,$order_info['order_sn'],$zf_pass);
    		$result_pay !== true && $db_transaction_success = false;

    		$affect_rows = $mod_ordercompensationbehalf->add(array(
    		   'order_id'=>$order_info['order_id'],
    		   'order_sn'=>$order_info['order_sn'],
    		    'bh_id'=>$this->visitor->get('has_behalf'),
    		    'create_time'=>gmtime(),
    		    'pay_amount'=>$pay_amount,
    		    'type'=>'lack'
    		));
    		
    		!$affect_rows && $db_transaction_success=false;
    		
    		if($db_transaction_success === false)
    		{
    		    db()->query("ROLLBACK");//回滚
    		    if($result_pay !== true ){echo $result_pay;}
    		    else{echo 'server is busy now,try again later!';}
    		    
    		    return ;
    		}
    		else
    		{
    		    db()->query("COMMIT");//提交
    		    //short message
    		   $message_ret = &ms()->pm->send($this->visitor->get('has_behalf'),$order_info['buyer_id'],Lang::get('active_refund_tip'),
    		       sprintf(Lang::get('active_refund_content'),$bh_info['bh_name'],$order_info['order_sn'],price_format($pay_amount),local_date("Y-m-d H:i",gmtime())));
    		    
    		    $this->pop_warning('ok');
    		}
    		
    		
        }
       
        
    }
    /**
     * 主动赔偿运费，因为发错货
     * 订单为 已发货 和 已完成 情况下
     */
    function compensate_fee()
    {
        $order_id = $_GET['order_id'] ? intval($_GET['order_id']):0;
        //查询款项信息
        $mod_ordercompensationbehalf = & m('ordercompensationbehalf');
        $compensation_info = $mod_ordercompensationbehalf->get("order_id='".$order_id."' AND type='deli'");
        if($compensation_info)
        {
            echo Lang::get('compensation_deli_exist');
            return;
        }
        //查询订单信息
        $order_info = $this->_order_mod->get(array(
           'conditions'=>"order_alias.order_id = {$order_id} AND order_alias.bh_id = {$this->visitor->get('has_behalf')}"." AND ".db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),"order_alias.status")
        ));
        if(empty($order_info))
        {
            echo 'empty order!';
            return;
        }
        //如果订单有申请退款，暂停主动退款
        $refund_info = $this->_orderrefund_mod->get(array('conditions'=>"order_id={$order_id} and receiver_id={$this->visitor->get('has_behalf')} and status=0 and type=1 and closed=0"));
        if( !empty($refund_info))
        {
            echo Lang::get('order_refunding');
            return;
        }
        
        //代发信息
        $bh_info = &m('behalf')->get($order_info['bh_id']);
        
        if(!IS_POST)
        {
            $this->assign('buyer_name',$order_info['buyer_name']);
            $this->assign('order_id',$order_id);
            $this->display('behalf.order.compensation.deli.html');
        }
        else 
        {
            $pay_amount = $_POST['pay_amount'] ? floatval($_POST['pay_amount']) :0;
            
            if($refund_info['status'] == 1)
            {
                if($pay_amount + $refund_info['apply_amount'] >= $order_info['goods_amount'])
                {
                    echo Lang::get('amount_too_much_correct');
                    return;
                }
            }
            
            if($pay_amount > 16)
            {
                echo sprintf(Lang::get('pay_amount_too_much'),$pay_amount);
                return;
            }
            
            $zf_pass = trim($_POST['zf_pass']);
            
            //开启事务
            $db_transaction_begin = db()->query("START TRANSACTION");
            if($db_transaction_begin === false)
            {
                //$this->pop_warning('fail_caozuo');
                echo 'fail_to_transaction';
                return;
            }
            $db_transaction_success = true;//默认事务执行成功，不用回滚
            
            include_once(ROOT_PATH.'/app/my_money.app.php');
    		$my_moneyapp = new My_moneyApp();
    		
    		$result_pay = $my_moneyapp->to_user_withdraw($order_info['buyer_name'],$pay_amount, $order_id,$order_info['order_sn'],$zf_pass);
    		$result_pay !== true && $db_transaction_success = false;
    		
    		$affect_rows = $mod_ordercompensationbehalf->add(array(
    		   'order_id'=>$order_info['order_id'],
    		   'order_sn'=>$order_info['order_sn'],
    		    'bh_id'=>$this->visitor->get('has_behalf'),
    		    'create_time'=>gmtime(),
    		    'pay_amount'=>$pay_amount,
    		    'type'=>'deli'
    		));
    		
    		!$affect_rows && $db_transaction_success=false;
    		
    		if($db_transaction_success === false)
    		{
    		    db()->query("ROLLBACK");//回滚
    		    if($result_pay !== true ){echo $result_pay;}
    		    else{echo 'server is busy now,try again later!';}
    		    
    		    return ;
    		}
    		else
    		{
    		    db()->query("COMMIT");//提交
    		    //send message

    		    $message_ret = &ms()->pm->send($this->visitor->get('has_behalf'),$order_info['buyer_id'],Lang::get('active_refund_tip'),
    		        sprintf(Lang::get('active_refund_content1'),$bh_info['bh_name'],price_format($pay_amount),$order_info['order_sn'],local_date("Y-m-d H:i",gmtime())));
    		    
    		    $this->pop_warning('ok');
    		}
    		
    		
        }
    }


	/**
	 * 主动赔偿质检补偿费(不需要退货)
	 * 订单为 已发货 和 已完成 情况下
	 */
	function compensate_claim_fee()
	{
		$order_id = $_GET['order_id'] ? intval($_GET['order_id']):0;
		//查询款项信息
		$mod_ordercompensationbehalf = & m('ordercompensationbehalf');
		$compensation_info = $mod_ordercompensationbehalf->get("order_id='".$order_id."' AND type='claim'");
		if($compensation_info)
		{
			echo Lang::get('compensation_claim_exist');
			return;
		}
		//查询订单信息
		$order_info = $this->_order_mod->get(array(
			'conditions'=>"order_alias.order_id = {$order_id} AND order_alias.bh_id = {$this->visitor->get('has_behalf')}"." AND ".db_create_in(array(ORDER_SHIPPED,ORDER_FINISHED),"order_alias.status")
		));
		if(empty($order_info))
		{
			echo 'empty order!';
			return;
		}
		//如果订单有申请退款，暂停主动退款
		$refund_info = $this->_orderrefund_mod->get(array('conditions'=>"order_id={$order_id} and receiver_id={$this->visitor->get('has_behalf')} and status=0 and type=1 and closed=0"));
		if( !empty($refund_info))
		{
			echo Lang::get('order_refunding');
			return;
		}

		//代发信息
		$bh_info = &m('behalf')->get($order_info['bh_id']);

		if(!IS_POST)
		{
			$this->assign('buyer_name',$order_info['buyer_name']);
			$this->assign('order_id',$order_id);
			$this->display('behalf.order.compensation.deli.html');
		}
		else
		{
			$pay_amount = $_POST['pay_amount'] ? floatval($_POST['pay_amount']) :0;

			if($refund_info['status'] == 1)
			{
				if($pay_amount + $refund_info['apply_amount'] >= $order_info['goods_amount'])
				{
					echo Lang::get('amount_too_much_correct');
					return;
				}
			}

			if($pay_amount > 16)
			{
				echo sprintf(Lang::get('pay_amount_too_much'),$pay_amount);
				return;
			}

			$zf_pass = trim($_POST['zf_pass']);

			//开启事务
			$db_transaction_begin = db()->query("START TRANSACTION");
			if($db_transaction_begin === false)
			{
				//$this->pop_warning('fail_caozuo');
				echo 'fail_to_transaction';
				return;
			}
			$db_transaction_success = true;//默认事务执行成功，不用回滚

			include_once(ROOT_PATH.'/app/my_money.app.php');
			$my_moneyapp = new My_moneyApp();

			$result_pay = $my_moneyapp->to_user_withdraw($order_info['buyer_name'],$pay_amount, $order_id,$order_info['order_sn'],$zf_pass);
			$result_pay !== true && $db_transaction_success = false;

			$affect_rows = $mod_ordercompensationbehalf->add(array(
				'order_id'=>$order_info['order_id'],
				'order_sn'=>$order_info['order_sn'],
				'bh_id'=>$this->visitor->get('has_behalf'),
				'create_time'=>gmtime(),
				'pay_amount'=>$pay_amount,
				'type'=>'claim'
			));

			!$affect_rows && $db_transaction_success=false;

			if($db_transaction_success === false)
			{
				db()->query("ROLLBACK");//回滚
				if($result_pay !== true ){echo $result_pay;}
				else{echo 'server is busy now,try again later!';}

				return ;
			}
			else
			{
				db()->query("COMMIT");//提交
				//send message

				$message_ret = &ms()->pm->send($this->visitor->get('has_behalf'),$order_info['buyer_id'],Lang::get('active_refund_tip'),
					sprintf(Lang::get('active_refund_content1'),$bh_info['bh_name'],price_format($pay_amount),$order_info['order_sn'],local_date("Y-m-d H:i",gmtime())));

				$this->pop_warning('ok');
			}


		}
	}
    
    function remove_double_refund()
    {
        $id = $_GET['id']?intval($_GET['id']):0;
        $this->_orderrefund_mod->edit("id=$id",array("closed"=>1,"refuse_reason"=>$this->visitor->get('user_name')." close it at ".date('Y-m-d H:i:s')));
        echo 'ok!';
        return;
    }

    function arrived(){
    	$refund_id = $_POST['refund_id'];
		$mod_order_refund = & m('orderrefund');
		$flag = $mod_order_refund->edit($refund_id ,array('dl_status'=>1));
		echo ecm_json_encode(array('code'=>0 ));
		exit;
	}

	function goods_cancel_ajax(){
		$goods_id = $_POST['goods_id'];
		$bh_id =  $this->visitor->get('has_behalf');
		$model_order = & m('order');

		$model_goods_warehouse = & m('goodswarehouse');
		$model_behalf_delivery = & m('behalfdelivery');
		$goods = $model_goods_warehouse->get($goods_id);
		$order_id = $goods['order_id'];
		$status = array(ORDER_ACCEPTED , ORDER_SHIPPED);
		$order_info = $model_order->get(array(
			'conditions' => "order_alias.order_id={$order_id} AND order_alias.bh_id={$bh_id} AND status " . db_create_in($status) ,
			'join' => 'has_orderextm',
		));

		if($order_info['total_quantity'] < 2){
			echo ecm_json_encode(array('code' =>500 ,'msg' => '当前订单只有一件商品,请直接取消订单'));
			return;
		}

		$behalf_deliveries = $this->_behalf_mod->getRelatedData('has_delivery',$bh_id);
		$deliveries = $behalf_deliveries[$bh_id.'_'.$order_info['dl_id']];

		//质检费
		$check_fee = $order_info['quality_check_fee'] / $order_info['total_quantity'];
		//快递差额   与超重有关
		$new_shipping_fee = $this->_behalf_mod->get_shipping_fee_after_order_cancel($order_id, array($goods_id));

		$offset_shipping_fee = abs($order_info['shipping_fee'] - $new_shipping_fee);

		//开启事务
		$success = $this->_start_transaction();
		//订单更新
		$data = array(
			'behalf_fee' => $order_info['behalf_fee'] - $goods['behalf_fee'],
			// 'total_quantity' => $order_info['total_quantity'] - $goods['order_goods_quantity'],
			'total_quantity' => $order_info['total_quantity'] - 1,
			'order_amount' =>   $order_info['order_amount'] - $goods['behalf_fee'] - $goods['goods_price'] - $check_fee - $offset_shipping_fee,
			'goods_amount' =>   $order_info['goods_amount'] - $goods['goods_price'],
			'quality_check_fee' => $order_info['quality_check_fee'] - $check_fee,

		);

		//运费更新
		$this->_orderextm_mod->edit($order_id , array('shipping_fee' => $new_shipping_fee,));

		$affect_rows = $model_order->edit($order_id , $data);
		!$affect_rows && $success = false;//回滚


		$affect_rows = $model_goods_warehouse->edit($goods_id,array('goods_status' => BEHALF_GOODS_CANCEL ));
		!$affect_rows && $success = false;//回滚



        //针对单个取消的商品 更新财务统计
        $financial_model = & m('financialstatistics');
        $financial_model->order_cut(1);

		$id = $order_id;
		/*商付通v2.2.1  更新商付通定单状态 开始*/
		$my_money_mod =& m('my_money');
		$my_moneylog_mod =& m('my_moneylog');
		//取消商品包含   货品价格 代发服务费 质检费 超重费   只有一件商品的取消不在此操作
		$money =  $goods['goods_price'] + $goods['behalf_fee'] + $check_fee + $offset_shipping_fee ;//定单价格

		$buy_user_id=$order_info['buyer_id'];//买家ID
		//当前资金对接用户为代发用户
		$sell_user_id=$order_info['bh_id'];//卖家ID

		if($order_info['order_id']==$id) {
			$buy_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$buy_user_id'");
			$buy_money = $buy_money_row['money'];//买家的钱
			$buy_money_dj = $buy_money_row['money_dj'];//买家的钱

			$sell_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id'");
			$sell_money = $sell_money_row['money'];//卖家的冻结资金
			$sell_money_dj = $sell_money_row['money_dj'];//卖家的冻结资金

			$new_buy_money = $buy_money + $money;
			$new_sell_money = $sell_money_dj - $money;

			//更新数据
			$affect_rows = $my_money_mod->edit('user_id=' . $buy_user_id, array('money' => $new_buy_money));
			!$affect_rows && $success = false;//回滚

			$affect_rows = $my_money_mod->edit('user_id=' . $sell_user_id, array('money_dj' => $new_sell_money));
			!$affect_rows && $success = false;//回滚


			$add_mymoneylog = array(
			//	'user_id' => $user_id,
			//	'user_name' => $user_name,
				'buyer_id' => $order_info['buyer_id'],
				'buyer_name' => $order_info['buyer_name'],
				'seller_id' => $sell_user_id,
				'seller_name' => $order_info['seller_name'],
				'order_id' => $order_info['order_id'],
				'order_sn' => $order_info['order_sn'],
				'add_time' => gmtime(),
				'admin_time' => gmtime(),
				'leixing' => 30,
				'money_zs' => $money,
				'money' => $money,
				'log_text'=> '取消商品 退款金额'.$money,
				'user_log_del' => 0,
				'caozuo' => 30,
				's_and_z' => 1,
			);

			//更新商付通log为 定单已取消
			$change_buyer = array_merge($add_mymoneylog , array('user_id' => $order_info['buyer_id'],'user_name'=> $order_info['buyer_name'],'moneyleft' => $new_buy_money));

			$change_seller = array_merge($add_mymoneylog , array('user_id' => $order_info['bh_id'],'user_name'=>  $order_info['bh_id'],'moneyleft' => $new_sell_money));

			//                    $my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));
			Log::write('dualven:behalf:' . var_export($change_buyer, true));
			$affect_rows = $my_moneylog_mod->add( $change_buyer);
			!$affect_rows && $success = false;//回滚

			$affect_rows = $my_moneylog_mod->add( $change_seller);
			!$affect_rows && $success = false;//回滚

		}

		/* 加回订单商品库存 */
		$cancel_reason = '商品取消';
		$cancel_reason .= " ".Lang::get('goods_sn').":".$goods['goods_no'].";".Lang::get('reback_money_success').',退回'.price_format($money).',当前件数'.$data['total_quantity'];

		/* 记录订单操作日志 */
		$affect_rows = $this->_orderlog_mod->add(array(
			'order_id'  => $id,
			'operator'  => addslashes($this->visitor->get('user_name')),
			'order_status' => order_status($order_info['status']),
			'changed_status' => order_status($order_info['status']),
			'remark'    => $cancel_reason,
			'log_time'  => gmtime(),
		));

		!$affect_rows && $success = false;//回滚

		//提交或回滚
		$this->_end_transaction($success);

		if($success){
			echo ecm_json_encode(array('code' =>0 ,'msg' => '操作成功'));
		}else{
			echo ecm_json_encode(array('code' =>500 ,'msg' => '操作失败'));
		}
	}

	/**
     * @name 取消选中的商品
     * @author zjh 2017-08-08
     */
	function select_goods_cancel_ajax()
	{		
		//zjh

		$goods_ids_array = explode(',', $_POST['goods_ids']);
	
		$goods = $this->_get_goods_cancel_info($goods_ids_array);

		$cancel_goods_price = 0;   // 需要减去的商品金额

		$cancel_behalf_fee = 0;  // 需要减去的代发服务费

		$cancel_check_fee = 0;   // 需要减去的质检费用

		$str_goods_no = '';    // 记录商品编码

		foreach ($goods as $key => $value) {

			$cancel_goods_price += $value['goods_price'];

			$cancel_behalf_fee += $value['behalf_fee'];

			$cancel_check_fee += $value['quality_check_fee'] / $value['total_quantity'];

			$order_id = $value['order_id'];

			$str_goods_no .= $value['goods_no'].'，';
		}

		$str_goods_no = rtrim($str_goods_no,'，');

		// 获取订单中的快递费
		$order_info = $this->_get_order_info($order_id);

		if($order_info['total_quantity'] < 2){
			echo ecm_json_encode(array('code' =>500 ,'msg' => '当前订单只有一件商品,请直接取消订单'));
			return;
		}

		//快递差额   与超重有关
		$new_shipping_fee = $this->_behalf_mod->get_shipping_fee_after_order_cancel($order_id, $goods_ids_array);

		$offset_shipping_fee = abs($order_info['shipping_fee'] - $new_shipping_fee);


		//开启事务
		$success = $this->_start_transaction();

		//订单更新
		$data = array(
			'behalf_fee' => $order_info['behalf_fee'] - $cancel_behalf_fee,
			'total_quantity' => $order_info['total_quantity'] - count($goods_ids_array),
			'order_amount' =>   $order_info['order_amount'] - $cancel_behalf_fee - $cancel_goods_price - $cancel_check_fee - $offset_shipping_fee,
			'goods_amount' =>   $order_info['goods_amount'] - $cancel_goods_price,
			'quality_check_fee' => $order_info['quality_check_fee'] - $cancel_check_fee,

		);

		//运费更新
		$this->_orderextm_mod->edit($order_id , array('shipping_fee' => $new_shipping_fee,));

		$affect_rows = $this->_order_mod->edit($order_id , $data);
		!$affect_rows && $success = false;//回滚

		// 修改warehouse的商品状态等信息
		$conditions = db_create_in($goods_ids_array,'id');
		$affect_rows = $this->_goods_warehouse_mod->edit($conditions,array('goods_status' => BEHALF_GOODS_CANCEL));
		!$affect_rows && $success = false;//回滚

        //针对单个取消的商品 更新财务统计
        $financial_model = & m('financialstatistics');
        $financial_model->order_cut(count($goods_ids_array));  // zjh 2017/8/8/

		$id = $order_id;
		/*商付通v2.2.1  更新商付通定单状态 开始*/
		$my_money_mod =& m('my_money');
		$my_moneylog_mod =& m('my_moneylog');
		//取消商品包含   货品价格 代发服务费 质检费 超重费   只有一件商品的取消不在此操作
		// $money =  $goods['goods_price'] + $goods['behalf_fee'] + $check_fee + $offset_shipping_fee ;//定单价格

		//zjh 客服是否修改了退货费用
		if(($_POST['origin_total_fee'] != $_POST['change_total_fee']) && $_POST['origin_total_fee'] > 0 && $_POST['change_total_fee'] > 0){ // 修改了

			$money = $_POST['change_total_fee'];

		}else{ 

			$money = $cancel_goods_price + $cancel_behalf_fee + $cancel_check_fee + $offset_shipping_fee;
			
		}
		

		$buy_user_id=$order_info['buyer_id'];//买家ID
		//当前资金对接用户为代发用户
		$sell_user_id=$order_info['bh_id'];//卖家ID

		if($order_info['order_id']==$id) {
			$buy_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$buy_user_id'");
			$buy_money = $buy_money_row['money'];//买家的钱
			$buy_money_dj = $buy_money_row['money_dj'];//买家的钱

			$sell_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id'");
			$sell_money = $sell_money_row['money'];//卖家的资金
			$sell_money_dj = $sell_money_row['money_dj'];//卖家的冻结资金

			$new_buy_money = $buy_money + $money;
			$new_sell_money = $sell_money_dj - $money;

			//更新数据
			$affect_rows = $my_money_mod->edit('user_id=' . $buy_user_id, array('money' => $new_buy_money));
			!$affect_rows && $success = false;//回滚

			$affect_rows = $my_money_mod->edit('user_id=' . $sell_user_id, array('money_dj' => $new_sell_money));
			!$affect_rows && $success = false;//回滚


			$add_mymoneylog = array(
			//	'user_id' => $user_id,
			//	'user_name' => $user_name,
				'buyer_id' => $order_info['buyer_id'],
				'buyer_name' => $order_info['buyer_name'],
				'seller_id' => $sell_user_id,
				'seller_name' => $order_info['seller_name'],
				'order_id' => $order_info['order_id'],
				'order_sn' => $order_info['order_sn'],
				'add_time' => gmtime(),
				'admin_time' => gmtime(),
				'leixing' => 30,
				'money_zs' => $money,
				'money' => $money,
				'log_text'=> '取消商品 退款金额'.$money,
				'user_log_del' => 0,
				'caozuo' => 30,
				's_and_z' => 1,
			);

			//更新商付通log为 定单已取消
			$change_buyer = array_merge($add_mymoneylog , array('user_id' => $order_info['buyer_id'],'user_name'=> $order_info['buyer_name'],'moneyleft' => $new_buy_money));

			$change_seller = array_merge($add_mymoneylog , array('user_id' => $order_info['bh_id'],'user_name'=>  $order_info['bh_id'],'moneyleft' => $new_sell_money));

			//                    $my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));
			Log::write('dualven:behalf:' . var_export($change_buyer, true));
			$affect_rows = $my_moneylog_mod->add( $change_buyer);
			!$affect_rows && $success = false;//回滚

			$affect_rows = $my_moneylog_mod->add( $change_seller);
			!$affect_rows && $success = false;//回滚

		}

		/* 加回订单商品库存 */
		$cancel_reason = '商品取消';
		$cancel_reason .= " ".Lang::get('goods_sn').":".$str_goods_no.";".Lang::get('reback_money_success').',退回'.price_format($money).',当前件数'.$data['total_quantity'];

		/* 记录订单操作日志 */
		$affect_rows = $this->_orderlog_mod->add(array(
			'order_id'  => $id,
			'operator'  => addslashes($this->visitor->get('user_name')),
			'order_status' => order_status($order_info['status']),
			'changed_status' => order_status($order_info['status']),
			'remark'    => $cancel_reason,
			'log_time'  => gmtime(),
		));

		!$affect_rows && $success = false;//回滚

		//提交或回滚
		$this->_end_transaction($success);

		if($success){
			echo ecm_json_encode(array('code' =>0 ,'msg' => '操作成功','data'=>$goods_ids_array));
		}else{
			echo ecm_json_encode(array('code' =>500 ,'msg' => '操作失败'));
		}
	}


	/**
     * @name  获取要取消的商品的各种费用
     * @author zjh 2017-08-07
     */
	function get_goods_cancel_fee()
	{
		$goods_ids_array = $_GET['goods_ids_array'];

		$goods = $this->_get_goods_cancel_info($goods_ids_array);

		$goods_fee = array();   // 记录商品的各种费用

		foreach ($goods as $key => $value) {
			$goods_fee[$key]['goods_no'] = $value['goods_no'];     // 商品编码

			$goods_fee[$key]['goods_price'] = $value['goods_price'];   // 商品单价

			$goods_fee[$key]['behalf_fee'] = $value['behalf_fee'];     // 代发服务费

			$goods_fee[$key]['quality_check_fee'] = number_format($value['quality_check_fee'] / $value['total_quantity'], 2, '.', '');  // 质检费用

			$order_id = $value['order_id'];
		}

		//快递差额   与超重有关
		$new_shipping_fee = $this->_behalf_mod->get_shipping_fee_after_order_cancel($order_id, $goods_ids_array);

		// 获取订单中的快递费
		$order_info = $this->_get_order_info($order_id);

		$offset_shipping_fee = abs($order_info['shipping_fee'] - $new_shipping_fee);

		$offset_shipping_fee = number_format($offset_shipping_fee, 2, '.', '');

		echo json_encode(array('code' =>0 ,'msg' => '操作成功','goods_fee'=>$goods_fee,'shipping_fee'=>$offset_shipping_fee,'goods_ids'=>$goods_ids_array));
	}

	/**
     * @name  获取要取消的商品的信息
     * @param $goods_ids_array  warehouse的id组
     * @author zjh 2017-08-07
     */
	function _get_goods_cancel_info($goods_ids_array)
	{
		$bh_id =  $this->visitor->get('has_behalf');

		$status = array(ORDER_ACCEPTED , ORDER_SHIPPED);
		$goods = $this->_goods_warehouse_mod->find(array(
            'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($goods_ids_array,'gwh.id')." AND order_alias.status ".db_create_in($status),
            'fields'=>'gwh.*,order_alias.*,gwh.behalf_fee',
            'join'=>'belongs_to_order'
        ));

        return $goods;
	}

	/**
     * @name  获取关联到快递费的订单信息
     * @param $order_id  订单id
     * @author zjh 2017-08-07
     */
	function _get_order_info($order_id)
	{	
		$bh_id =  $this->visitor->get('has_behalf');

		$status = array(ORDER_ACCEPTED , ORDER_SHIPPED);

		$order_info = $this->_order_mod->get(array(
			'conditions' => "order_alias.order_id={$order_id} AND order_alias.bh_id={$bh_id} AND order_alias.status " . db_create_in($status) ,
			'join' => 'has_orderextm',
		));

		return $order_info;
	}

    /**
     * @name  退货商品列表
     * @author zjh 2017-07-26
     */
    function goods_back_list()
    {
        $bh_id = $this->visitor->get('has_behalf');

		$behalf_info = $this->_behalf_mod->get($bh_id);
		$behalf_info['region_name'] = $this->_remove_China($behalf_info['region_name']);

       // $this->_import_css_js('dt');
    	$this->assign('show_print',true);
    	$this->assign('behalf', $behalf_info);
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.tuihuo.html');
    }

    /**
     * @name  获取退货商品的信息，用于打印
     * @author zjh 2017-08-01
     */
    function get_print_goods_back()
    {
        $bh_id = $this->visitor->get('has_behalf');

		if($_POST['print_style'] == 'order'){

			$return_pack = $this->_orderrefund_mod->get(array(

	            'conditions' => "receiver_id = {$bh_id} AND status='0' AND closed='0' AND type='1' AND goods_ids_flag = '1' AND order_sn = ".$_POST['order_sn'],
	            'fields'=>"create_time,order_id,order_sn,goods_ids",
	            'count'=>true,
	        ));

	        // 处理return_pack的一些数据

	        $get_goods = explode(',', $return_pack['goods_ids']);

	         // 获取订单下的商品
	        $goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"bh_id = {$bh_id} AND ".db_create_in($get_goods,'id'),
                'fields'=>'goods_id,goods_no,goods_sku,goods_spec_id,order_sn,goods_attr_value,goods_specification',
                'count'=>true,
	        ));

	        // 装载快递单需要的数据
	        $orders = array();

	        // 获取商品数量
	        $order_goods_count = $this->_goods_warehouse_mod->getCount(); 

	        $order_goods_str = '';
	        foreach ($goods as $key => $value) {
	        	$order_goods_str .= $this->_Attrvalue2Pinyin($value['goods_attr_value']) . "(" . $value['goods_specification'] . ")";
	        }

	        // 订单信息
	        $orders['goods_info'] = Lang::get('order_goods_quantity1').$order_goods_count.Lang::get('order_goods_quantity2').$order_goods_str;

	        // 获取客户信息
	        $customer_info = $this->_th_customer_info_mod ->get(array(

	        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
	        ));

	        // 收件人
	        $orders['consignee'] = $customer_info['customer_name'];
	        // 收件人手机号
	        $orders['phone_mob'] = $customer_info['tel'];
	        // 订单编号
	        $orders['order_sn'] = $_POST['order_sn'];
	        // 快递单号
	        $orders['invoice_no'] = $customer_info['dl_no'];
	        // 收件人省市区
	        $orders['consignee_region'] = $customer_info['region'];
	        // 收件人详细地址
	        $orders['consignee_address'] = $customer_info['address'];
	        // 时间编码
	        $orders['time'] = date("m-d-H-i",time());
	        // 客户是否同意寄回
	        $orders['agree'] = $customer_info['agree'];

	        echo json_encode(array('code' =>0 ,'msg' => '操作成功','data'=>$orders));

		}else if($_POST['print_style'] == 'goods'){

			 // 获取订单下的商品
	        $goods = $this->_goods_warehouse_mod->get(array(
                'conditions'=>"bh_id = {$bh_id} AND id = ".$_POST['goods_id'],
                'fields'=>'goods_id,goods_no,goods_sku,goods_spec_id,order_sn,goods_attr_value,goods_specification',
                'count'=>true,
	        ));

	        // 装载快递单需要的数据
	        $orders = array();

	        // 获取商品数量
	        $order_goods_count = $this->_goods_warehouse_mod->getCount(); 

	        $order_goods_str = $this->_Attrvalue2Pinyin($goods['goods_attr_value']) . "(" . $goods['goods_specification'] . ")";

	        // 订单信息
	        $orders['goods_info'] = Lang::get('order_goods_quantity1').$order_goods_count.Lang::get('order_goods_quantity2').$order_goods_str;

	        // 获取客户信息
	        $customer_info = $this->_th_customer_info_mod ->get(array(

	        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
	        ));

	        // 收件人
	        $orders['consignee'] = $customer_info['customer_name'];
	        // 收件人手机号
	        $orders['phone_mob'] = $customer_info['tel'];
	        // 订单编号
	        $orders['order_sn'] = $_POST['order_sn'];
	        // 快递单号
	        $orders['invoice_no'] = $customer_info['dl_no'];
	        // 收件人省市区
	        $orders['consignee_region'] = $customer_info['region'];
	        // 收件人详细地址
	        $orders['consignee_address'] = $customer_info['address'];
	        // 时间编码
	        $orders['time'] = date("m-d-H-i",time());
	        // 客户是否同意寄回
	        $orders['agree'] = $customer_info['agree'];

	        echo json_encode(array('code' =>0 ,'msg' => '操作成功','data'=>$orders));
		}
    	
    }

    /**
     * @name  异步获取退货商品
     * @author zjh 2017-07-28
     */
    function get_pipe_goods_back()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
        
        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                case 1:$orderSql = " order_sn ".$order_dir;break;
                case 2:$orderSql = " goods_no ".$order_dir;break;
                default:$orderSql = ' create_time DESC';
            }
        }
        
        $recordsTotal = 0;
        $recordsFiltered = 0;

        if(strlen($search) == 0){

	        $return_pack = $this->_orderrefund_mod->find(array(

	            'conditions' => "receiver_id = {$bh_id} AND status='0' AND closed='0' AND type='1' AND goods_ids_flag = '1' ",
	            'fields'=>"create_time,order_id,order_sn,goods_ids",
	            'count'=>true,
	            'order'=>$orderSql." ,create_time DESC",
	            

	        ));
        }
        
        if(strlen($search) > 0){

	        $return_pack = $this->_orderrefund_mod->find(array(

	            'conditions' => "receiver_id = {$bh_id} AND status='0' AND closed='0' AND type='1' AND goods_ids_flag = '1' AND order_sn = {$search}",
	            'fields'=>"create_time,order_id,order_sn,goods_ids",
	            'count'=>true,
	            'order'=>$orderSql." ,create_time DESC",
	            

	        ));
        }
        

        // 存储商品数据
        $refund_data = array();
        $tmp_refund_data  = array();

        // 处理return_pack的一些数据
        $get_goods = array();
        foreach ($return_pack as $key => $value) {
            
            $temp = explode(',', $value['goods_ids']);
            $get_goods = array_merge($get_goods,$temp);

            foreach ($temp as $k => $v) {
            	// 记录退货申请时间
            	$tmp_refund_data[$v]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
            }
            
        }

        // 获取订单下的商品
        $goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"bh_id = {$bh_id} AND ".db_create_in($get_goods,'id'),
                'fields'=>'goods_id,goods_no,goods_sku,goods_spec_id,order_sn',
                'count'=>true,
                'limit'=>"{$start},{$page_per}"
        ));
        
    	$recordsTotal = $recordsFiltered = $this->_goods_warehouse_mod->getCount(); 

        // 获取商品规格
        $goods_spec_id = array();
        $get_goods_2 = array();
        foreach ($goods as $key => $value) {
        	$goods_spec_id[] = $value['goods_spec_id'];
        	$get_goods_2[] = $value['id'];
        }

        $goods_spec = $this->_goods_spec_mod ->find(array(

            'conditions' => db_create_in($goods_spec_id,'spec_id'),
            'fields' => "spec_id,spec_1,spec_2"
        ));

        // 获取客户信息
        $customer_info = $this->_th_customer_info_mod ->find(array(

        	'conditions'=>"bh_id = {$bh_id} AND ".db_create_in($get_goods_2,'goods_id'),
        ));

        // 记录用户信息
        $customers =  array();
        foreach ($customer_info as $key => $value) {
        	$customers[$value['goods_id']]['customer_name'] = $value['customer_name'];   // 姓名
        	$customers[$value['goods_id']]['address'] = $value['address'];   //地址
        	$customers[$value['goods_id']]['tel'] = $value['tel'];   // 电话

        	if(!empty($value['customer_name']) || !empty($value['address']) || !empty($value['tel'])){
        		$arr = array();
        		if(!empty($value['customer_name'])){
        			$tmp = "<b>姓名:</b> ".$value['customer_name'];
        			array_push($arr,$tmp);
        		}
        		if(!empty($value['address'])){
        			$tmp = "<b>地址: </b>".$value['address'];
        			array_push($arr,$tmp);
        		}
        		if(!empty($value['tel'])){
        			$tmp = "<b>电话: </b>".$value['tel'];
        			array_push($arr,$tmp);
        		}

        		$customers[$value['goods_id']]['info'] = implode('，', $arr);   // 组装上面的信息
        	}

        	$customers[$value['goods_id']]['dl_no'] = $value['dl_no'];   // 寄回快递号
        	$customers[$value['goods_id']]['agree'] = $value['agree'];   // 是否同意
        	$customers[$value['goods_id']]['reason'] = $value['reason'];   // 理由
        }

        foreach ($goods as $key => $value) {

        	// 记录添加时间
        	$refund_data[$key]['create_time'] = $tmp_refund_data[$key]['create_time'];

        	// 记录goods_warehouse 的id
        	$refund_data[$key]['goods_id'] = $value['id'];

        	// 记录订单号
        	$refund_data[$key]['order_sn'] = $value['order_sn'];

        	//记录商品标签号
        	$refund_data[$key]['goods_no'] = $value['goods_no'];

        	// 记录颜色和尺码
        	$refund_data[$key]['color'] = $goods_spec[$value['goods_spec_id']]['spec_1'];
            $refund_data[$key]['size'] = $goods_spec[$value['goods_spec_id']]['spec_2'];

            // 记录货号
            $refund_data[$key]['goods_sku'] = rtrim($goods[$key]['goods_sku'],'#');       

            // 记录库位号
            $refund_data[$key]['stock_no'] = '';   

            // 记录是否同意（客户信息） 和状态（快递号）
            if(empty($customers[$key]['agree'])){

            	$refund_data[$key]['customer_info'] = '';
            	$refund_data[$key]['dl_no'] = empty($customers[$key]['dl_no']) ? '':'<b>快递号: </b>'.$customers[$key]['dl_no'];

            }else if($customers[$key]['agree'] == 1){  //同意

            	$refund_data[$key]['customer_info'] = empty($customers[$key]['info']) ? '':$customers[$key]['info'];
            	$refund_data[$key]['dl_no'] = empty($customers[$key]['dl_no']) ? '':'<b>快递号:</b> '.$customers[$key]['dl_no'];

            }else if($customers[$key]['agree'] == 2){  //不同意

            	$refund_data[$key]['customer_info'] = empty($customers[$key]['reason']) ? '':'<b>不同意:</b> '.$customers[$key]['reason'];
            	$refund_data[$key]['dl_no'] = 'hidden';  //不同意时，隐藏快递号的处理按钮
            }
            
        }

         echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>intval($recordsTotal),'recordsFiltered'=>intval($recordsFiltered),'data'=>array_values($refund_data))); 
    }

     /**
     * @name  处理退货客户信息
     * @author zjh 2017-07-29
     */
    function del_customer_info()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	if($_POST['agree'] == 1 || $_POST['agree'] == 2){   // 同意或不同意

    		if($_POST['operate'] == 'get'){  //获取

    			// 获取客户信息
		        $customer_info = $this->_th_customer_info_mod ->get(array(

		        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
		        ));

		        if($customer_info){
		        	echo json_encode(array('code' =>0 ,'msg' => '操作成功','data'=>$customer_info));
		        }else{
		        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
		        }

    		}else if($_POST['operate'] == 'add'){  // 增加

    			$check = $this->_th_customer_info_mod ->get(array(

		        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
		        ));

		        if($check){   // 已经存在了，改为编辑

		        	if($_POST['agree'] == 1){  //同意

	    				$edit_data = array(

							'customer_name' => $_POST['customer_name'],
							'address' => $_POST['address'],
				            'tel' => $_POST['tel'],
				            'region' => $_POST['region'],
				            'agree'=>$_POST['agree']
						);
		 
	    			}else if($_POST['agree'] == 2){  // 不同意

	    				$edit_data = array(

	    					'reason' => $_POST['reason'],
	    					'agree'=>$_POST['agree']

						);
	    			}
	    			
					
					$conditions = 'goods_id = '.$_POST['goods_id'];

		    		// 编辑客户信息
			        $customer_info = $this->_th_customer_info_mod ->edit($conditions, $edit_data);

			        if($customer_info){
			        	echo json_encode(array('code' =>0 ,'msg' => '操作成功'));
			        }else{
			        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
			        }
		        	exit;
		        }

    			$edit_data = array(

					'goods_id' => $_POST['goods_id'],
					'customer_name' => $_POST['customer_name'],
					'address' => $_POST['address'],
					'region' => $_POST['region'],
		            'tel' => $_POST['tel'],
		            'del_time' => time(),
		            'agree'=>$_POST['agree'],
		            'reason'=>$_POST['reason'],
		            'bh_id'=>$bh_id
				);

	    		// 添加客户信息
		        $customer_info = $this->_th_customer_info_mod ->add($edit_data);

		        if($customer_info){
		        	echo json_encode(array('code' =>0 ,'msg' => '操作成功'));
		        }else{
		        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
		        }

    		}else if($_POST['operate'] == 'edit'){ //修改

    			if($_POST['agree'] == 1){  //同意

    				$edit_data = array(

						'customer_name' => $_POST['customer_name'],
						'address' => $_POST['address'],
			            'tel' => $_POST['tel'],
			            'region' => $_POST['region'],
			            'agree'=>$_POST['agree']
					);
	 
    			}else if($_POST['agree'] == 2){  // 不同意

    				$edit_data = array(

    					'reason' => $_POST['reason'],
    					'agree'=>$_POST['agree']

					);
    			}
    			
				
				$conditions = 'goods_id = '.$_POST['goods_id'];

	    		// 编辑客户信息
		        $customer_info = $this->_th_customer_info_mod ->edit($conditions, $edit_data);

		        if($customer_info){
		        	echo json_encode(array('code' =>0 ,'msg' => '操作成功'));
		        }else{
		        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
		        }

    		}
    		

    	}else if(isset($_POST['dl_no'])){  //输入快递号

    		if($_POST['operate'] == 'get'){  //获取

    			// 获取客户信息
		        $customer_info = $this->_th_customer_info_mod ->get(array(

		        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
		        ));

		        if($customer_info){
		        	echo json_encode(array('code' =>0 ,'msg' => '操作成功','data'=>$customer_info));
		        }else{
		        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
		        }

    		}else if($_POST['operate'] == 'edit'){ //修改

    			$customer_info = $this->_th_customer_info_mod ->get(array(

		        	'conditions'=>"bh_id = {$bh_id} AND goods_id = ".$_POST['goods_id'],
		        ));

		        if(!$customer_info){  // 在输入快递号前没有填写客户信息，则返回错误
		        	echo json_encode(array('code' =>-1 ,'msg' => '请先填写客户信息'));
		        	exit;
		        }

				$edit_data = array(

					'dl_no' => $_POST['dl_no'],
					'send_back_time' => time(),

				);
	 				
				$conditions = 'goods_id = '.$_POST['goods_id'];

	    		// 编辑客户信息
		        $customer_info = $this->_th_customer_info_mod ->edit($conditions, $edit_data);

		        if($customer_info){
		        	echo json_encode(array('code' =>0 ,'msg' => '操作成功'));
		        }else{
		        	echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
		        }
    		}
    	}

    }

    /**
     * @name  读取配置信息
     * @param $setting 相关的配置信息
     *        ++ 数组array('batch_has_num'=>'x','afternoon_sign_time'=>'y','tags_reset_date'=>'z',...)
     * @param $type (0,1,2) , 0:读取，1:增加，2:修改
     * @author zjh 2017-07-19
     */
    function _operate_behalf_setting($type=0,$setting=array())
    {
        $bh_id =  $this->visitor->get('has_behalf');

        if(!$bh_id){
            return;
        }
        $behalf_setting = array();
        switch ($type) {
            case 0:
                //zjh 读取behalf的相关配置信息
                $behalf_setting= $this->_behalf_setting_mod->get(array(
                    'conditions'=>'bh_id = '.$bh_id,
                    'order'=>'id ASC',
                    'limit' => 1                   
                ));

                break;

            case 1:
                //zjh 插入behalf的相关配置信息,先读取
                $behalf_setting= $this->_behalf_setting_mod->get(array(
                    'conditions'=>'bh_id = '.$bh_id,
                    'order'=>'id ASC',
                    'limit' => 1
                ));

                if (!$behalf_setting){  // 配置信息永远只有一条
                    $this->_behalf_setting_mod->add(array(
                        'bh_id'=>$bh_id,
                        'batch_has_num'=>50,
                        'afternoon_sign_time'=>13
                    ));
                }

                break;

            case 2:

                //zjh 先取出要修改的id
                $behalf_setting = $this->_behalf_setting_mod->get(array(
                    'conditions'=>'bh_id = '.$bh_id,
                    'order'=>'id ASC',
                    'limit' => 1
                ));

                //zjh 修改配置信息
                if(!empty($setting)){
                    $conditions = 'id = '.$behalf_setting['id'];
                    $this->_behalf_setting_mod->edit($conditions,$setting);
                }    
                
                break;

            default:
                //zjh 默认时读取
                $behalf_setting= $this->_behalf_setting_mod->get(array(
                    'conditions'=>'bh_id = '.$bh_id,
                    'order'=>'id ASC',
                    'limit' => 1
                ));
                break;
        }

        return $behalf_setting;
        
    }

    /**
     * @name  编辑拿货员的信息
     * @author zjh 2017-07-28
     */
    function edit_taker_info()
    {
        if($_GET['taker_id']){

            $bh_id = $this->visitor->get('has_behalf');

            $member_mod = &m('member');

            $edit_data = array(

                'real_name' => $_GET['real_name'] ,
                'phone_mob' => $_GET['tel'],
            );
        
            $conditions = 'user_id = '.$_GET['taker_id'];

            $taker = $member_mod->edit($conditions, $edit_data);

        }
    }



    /**
     * @name  取消分配
     * @author zjh 2017-07-28
     */
    function cancel_assign()
    {

        $bh_id =  $this->visitor->get('has_behalf');

        if($_POST['batch_id']){

            $batch = $this->_goods_taker_batch_mod->get(array(
                'conditions'=>"bh_id = {$bh_id} AND batch_id = ".$_POST['batch_id'],
                'fields'=>'content',
            ));

            // 改变商品的状态
            $warehouse_id = explode(',', $batch['content']);

            $edit_data = array(

                'goods_status' => BEHALF_GOODS_PREPARED,
                'batch_id'=>0,
                'taker_time'=>0
            );
        
            $conditions = db_create_in($warehouse_id,'id');
 
            $goods = $this->_goods_warehouse_mod->edit($conditions, $edit_data);

            // 删除对应批次
            $conditions = 'batch_id = '.$_POST['batch_id'];
            $flag = $this->_goods_taker_batch_mod -> drop($conditions);

            $result = "批次：".$_POST['batch_id']." 下的商品已被重置，可再次分配！";

            if($flag){
                echo ecm_json_encode($result);
            }
            
        }
    }

    /**
     * @name  获取标签所属批次
     * @author zjh 2017-07-26
     */
    function get_batch()
    {
        $bh_id =  $this->visitor->get('has_behalf');

        if($_POST['goods_no']){

            // 根据标签号
            $batch_id = $this->_goods_warehouse_mod->get(array(
                'conditions'=>"bh_id = {$bh_id} AND  goods_no = ".$_POST['goods_no'],
                'fields'=>'id,taker_time',
            ));

            // 三次拿货缺货后将不再分配标签，也就是同一个标签最多只会被分配3次，标签状态为不确定时，再分配间隔为48小时
            // 所以，理想的情况是，第一次分配到第三次分配的间隔时间为4天，但是从解冻标签到再次分配标签，中间也会有一段时间。另外也不能排除隔天甚至隔几天再分配的情况
            // 所以，为了降低数据库的搜索时间，暂时取7天范围内的批次(获取到的taker_time 往前推7天)
            $taker_day = strtotime(date("Y-m-d",$batch_id['taker_time']));
            $seven_day_before = $taker_day - 86400 * 7;
            $batchs = $this->_goods_taker_batch_mod->find(array(
                    'conditions'=> "assign_time between {$seven_day_before} and {$batch_id['taker_time']}",
                    'fields'=>'batch_id,content',
            ));

            $collect_batch_id = array();
            foreach ($batchs as $key => $value) {
                $id_array = explode(',', $value['content']);
                if(in_array($batch_id['id'], $id_array)){
                    $collect_batch_id[] = $value['batch_id'];
                }
            }

            $str_batch_id = implode(',', $collect_batch_id);
            $result = "标签:".$_POST['goods_no'].",曾经所在批次(包括现在)：".$str_batch_id;
            echo ecm_json_encode($result);
        }

    }


     /**
     * @name  重置标签状态
     * @author zjh 2017-07-19
     */
    function _reset_tags()
    {
        $bh_id =  $this->visitor->get('has_behalf');
    
        // 读取配置
        $behalf_setting = $this->_operate_behalf_setting(0);

        if ($behalf_setting['tags_reset_date']){

            $timestamp = $behalf_setting['tags_reset_date'];  

            $one_day = strtotime(date('Y-m-d',$timestamp)) - 86400;    // 考虑到 不确定的需要48小时，所以查找缺货状态的商品要再往前推一天
            $today = strtotime(date('Y-m-d',time()));

            $tmp_today_afternoon = date('Y-m-d',time()).' '.$behalf_setting['afternoon_sign_time'].':00:00';
            $today_afternoon = strtotime($tmp_today_afternoon);

            $one_second_before_tomorrow = strtotime(date('Y-m-d',time())) + 86399;

            $this_moment = time();   //这一刻

            // 早上更新过一遍后，不再更新。 或者到了下午更新过一遍后，不再更新
            if ($timestamp >= $today && $timestamp < $today_afternoon){  //上午，已经更新过一次

                if ($this_moment >= $today && $this_moment < $today_afternoon){  //当前时间为上午，则不再更新
                    
                    return; 
                }
                    
            }else if ($timestamp >= $today_afternoon && $timestamp < $one_second_before_tomorrow){  // 下午，已经更新过一次，不再更新

                return;
            }

            // 获取部分缺货状态的商品(状态标志为：下午有、明天有、不确定的商品)
            $part_lack_goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"gwh.bh_id = {$bh_id} AND ( gwh.taker_time between $one_day and $this_moment ) AND ".db_create_in(array(BEHALF_GOODS_UNSURE,BEHALF_GOODS_AFTERNOON,BEHALF_GOODS_TOMORROW),'goods_status'),
                'fields'=>'gwh.id,gwh.goods_status,gwh.taker_time,gwh.batch_id',
            ));

        }else{

            // 获取部分缺货状态的商品(状态标志为：下午有、明天有、不确定的商品)
            $part_lack_goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in(array(BEHALF_GOODS_UNSURE,BEHALF_GOODS_AFTERNOON,BEHALF_GOODS_TOMORROW),'goods_status'),
                'fields'=>'gwh.id,gwh.goods_status,gwh.taker_time,gwh.batch_id',
            ));
        }

        
        // 获取缺货警示的相关信息
        $keys_part_lack_goods = array_keys($part_lack_goods);  // 获取键名

        $goods_warn = $this->_goods_warn->find(array(
                'conditions'=>db_create_in($keys_part_lack_goods,'goods_id'),
        ));

        $stop_taking = array();   //用于记录需要停止拿货的商品
        $reset_status = array();   // 用于记录需要重置状态的商品

        $shortage_record_data = array();  // 用于记录缺货的信息

        $tmp_time = date('Y-m-d',time());
        $time_16 = strtotime($tmp_time.' 16:00:00');

        foreach ($part_lack_goods as $key => $value) {

            // 在警示表中找出对应的数据
            $temp_warn =  array();
            foreach ($goods_warn as $k => $v) {
                if ($v['goods_id'] == $value['id']){
                    $temp_warn[] = $v;
                }
            }

            // 同一商品出现3次，记录这个商品的warehouse的id
            $warn_times = 0;
            if(count($temp_warn) >= 3){
                foreach ($temp_warn as $k => $v) {
                    // 计算明天有或不确定警示的次数（大于等于3次后，后面的处理就冻结这个货品，使不再拿货）
                    if($v['status'] == BEHALF_GOODS_TOMORROW || $v['status'] == BEHALF_GOODS_UNSURE){
                        $warn_times++;
                    }
                }
            }
            
            if($warn_times >= 3){
                $stop_taking[] = $value['id'];

                $shortage_record_data[$key]['batch_id'] = $value['batch_id'];
                $shortage_record_data[$key]['goods_id'] = $value['id'];
                $shortage_record_data[$key]['shortage_status'] = $value['goods_status'];
                $shortage_record_data[$key]['real_price'] = 0;

                continue;    // 跳过下面的操作，继续往下一个循环
            }


            if ($value['goods_status'] == BEHALF_GOODS_TOMORROW){ // 明天有的情况

                // 判断当前时间与商品被派发时是否还在同一天
                if(strtotime(date("Y-m-d",$value['taker_time'])) !== strtotime(date("Y-m-d",time()))){
                    // 不在同一天，即已经到第二天了，则解冻商品标签，使其状态可以被重置
                    $reset_status[] = $value['id'];

                    $shortage_record_data[$key]['batch_id'] = $value['batch_id'];
                    $shortage_record_data[$key]['goods_id'] = $value['id'];
                    $shortage_record_data[$key]['shortage_status'] = $value['goods_status'];
                    $shortage_record_data[$key]['real_price'] = 0;
                }

            }else if ($value['goods_status'] == BEHALF_GOODS_AFTERNOON){ // 下午有的情况

                // 判断当前时间与商品被派发时是否还在同一天 或者 当前时间是否已经是下午  整点(0~23)
                if((strtotime(date("Y-m-d",$value['taker_time'])) !== strtotime(date("Y-m-d",time()))) || (date("H",time())-$behalf_setting['afternoon_sign_time'] >= 0)){
                    // 到了下午，则解冻商品标签，使其状态可以被重置
                    $reset_status[] = $value['id'];

                    $shortage_record_data[$key]['batch_id'] = $value['batch_id'];
                    $shortage_record_data[$key]['goods_id'] = $value['id'];
                    $shortage_record_data[$key]['shortage_status'] = $value['goods_status'];
                    $shortage_record_data[$key]['real_price'] = 0;
                }

            }else if ($value['goods_status'] == BEHALF_GOODS_UNSURE){   // 不确定的情况（48小时后再分配）
                if (floor(($time_16-$value['taker_time'])/86400) >= 2){  // 当天16点距离商品被分配时间是否超过48小时，即2天
                    $reset_status[] = $value['id'];

                    $shortage_record_data[$key]['batch_id'] = $value['batch_id'];
                    $shortage_record_data[$key]['goods_id'] = $value['id'];
                    $shortage_record_data[$key]['shortage_status'] = $value['goods_status'];
                    $shortage_record_data[$key]['real_price'] = 0;
                }
            }

        }

        // 进入数据表重置已标志解冻的标签(重置为备货中)
        $edit_data = array(
            'goods_status' => BEHALF_GOODS_PREPARED,
        );
        $conditions = "bh_id = {$bh_id} AND ".db_create_in($reset_status,'id');
        $this->_goods_warehouse_mod->edit($conditions, $edit_data);


        // 进入数据表作废 明天或不确定的拿货3次缺货的商品标签(标注停止拿货)
        $edit_data = array(
            'goods_status' => BEHALF_GOODS_STOP_TAKING,
        );

        $conditions = "bh_id = {$bh_id} AND ".db_create_in($stop_taking,'id');
        $this->_goods_warehouse_mod->edit($conditions, $edit_data);

        // 写缺货表       
        $this->_add_shortage_record($shortage_record_data);

        // 记录当前重置的时间
        $this->_operate_behalf_setting(2,array('tags_reset_date'=>time()));
    }

    /**
     * @name  给缺货记录表增加记录（添加多条记录）
     * @param $data 要添加的信息
     * @author zjh 2017-08-09
     */
    function _add_shortage_record($data)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	// $edit_data = array(

     //        'batch_id' => $data['batch_id'],
     //        'goods_id' => $data['goods_id'],
     //        'shortage_status'=>$data['shortage_status'],
     //        'add_time'=>time(),
     //        'bh_id'=>$bh_id,
     //    );
    	// $data = array(array('batch_id'=>$x,'goods_id'=>$y,'shortage_status'=>$z),array()); 这种格式
		// $data = array(array('batch_id'=>1,'goods_id'=>2,'shortage_status'=>3),array('batch_id'=>1,'goods_id'=>2,'shortage_status'=>3));
    	$time = time();
    	$str_data = '';
    	foreach ($data as $key => $value) {
    		$str_data .= '( ';
    		$edit_data = array($value['batch_id'],$value['goods_id'],$value['shortage_status'],$value['real_price'],$time,$bh_id);
    		$str_data .= implode(',', $edit_data);
    		$str_data .= ' ),';
    	}

    	$str_data = rtrim($str_data,',');

    	$this->_shortage_record_mod->db->query("insert into ecm_goods_shortage_record (batch_id, goods_id ,shortage_status,real_price ,add_time,bh_id) values 
    		$str_data 
    	"); 
    }

    /**
     * @name  添加缺货原因（主要是残次品）
     * @param $data 要添加的信息
     * @author zjh 2017-08-11
     */
    function _add_shortage_reason($data)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$time = time();
    	$str_data = '';
    	foreach ($data as $key => $value) {
    		$str_data .= '( ';
    		$edit_data = array($value['goods_id'],$value['reason'],$time);
    		$str_data .= implode(',', $edit_data);
    		$str_data .= ' ),';
    	}

    	$str_data = rtrim($str_data,',');

    	$this->_refund_reason_mod->db->query("insert into ecm_refund_reason ( goods_id ,reason,add_time ) values 
    		$str_data 
    	"); 
    }

    /**
     * @name  获取打印需要的标签信息
     * @author zjh 2017-07-20
     */

    function get_tags_info_for_print()
    {
        $bh_id =  $this->visitor->get('has_behalf');

        if(IS_POST && isset($_POST['print_num'])&&isset($_POST['batch_id_array'])){

            // 记录打印次数

            $batch_id_array = array();
            // 过滤重复值
            foreach ($_POST['batch_id_array'] as $key => $value) {
                $batch_id_array[$value] = $value;
            }

            $batch_id_array = array_values($batch_id_array);

            $batch = $this->_goods_taker_batch_mod->find(array(
                    'conditions'=>db_create_in($batch_id_array,'batch_id'),
                    'fields'=>'batch_id,print_num',
            ));

            $print_num = array();
            foreach ($batch as $key => $value) {
                
                $print_num[$key] = $value['print_num'];
                $print_num[$key]++;   //加1

                $edit_data = array(

                    'print_num' => $print_num[$key],

                );
        
                $conditions = "batch_id = ".$value['batch_id'];

                $this->_goods_taker_batch_mod ->edit($conditions, $edit_data);
            }
            
            echo ecm_json_encode($print_num);   // 返回打印次数

        }else if(IS_POST){

            if (isset($_POST['taker'])){

                if(empty($_POST['taker'])){
                    echo ecm_json_encode(0);  // 如果拿货员是空的
                }

                // 打印某个时间段内某个拿货员所有的批次
                $taker_name = $_POST['taker'];

                $from = strtotime($_POST['query_time']);
                $to = strtotime($_POST['query_endtime']);

                if($_POST['query_time'] == $_POST['query_endtime']){

                    $from = strtotime(date('Y-m-d',strtotime($_POST['query_time'])));  // 取当天的
                }

                $batch = $this->_goods_taker_batch_mod->find(array(
                        'conditions'=>"taker_name = '{$taker_name}' AND (assign_time between $from and $to)",
                        'fields'=>'batch_id,content',
                ));

                $str_content = '';
                $temp_batch_id = array(); //记录batch_id
                foreach ($batch as $key => $value) {

                   foreach (explode(',', $value['content']) as $k => $v) {
                       $temp_batch_id[$v] = $value['batch_id'];
                   }
                   
                   $str_content .= $value['content'].',';  
                }
                
                $str_content = rtrim($str_content, ",");

            }else if(isset($_POST['batch_id'])){
                
                $batch = $this->_goods_taker_batch_mod->get(array(
                    'conditions'=>"batch_id = ".$_POST['batch_id'],
                    'fields'=>'batch_id,content',
                ));

                $temp_batch_id = array(); //记录batch_id
                foreach (explode(',', $batch['content']) as $k => $v) {
                       $temp_batch_id[$v] = $batch['batch_id'];
                }

                $str_content = $batch['content'];
            }

            if($batch){

                $array_content = explode(',', $str_content);
                
                $tags_info = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($array_content,'gwh.id'),
                    'fields'=>'gwh.goods_attr_value,gwh.goods_specification,gwh.store_bargin,gwh.delivery_name,order_alias.total_quantity,gwh.order_sn,gwh.goods_no,gwh.goods_price,orderstock.stock_code',
                    'order'=>'gwh.market_id ASC,gwh.store_id ASC',
                    'join'=>'belongs_to_order,belongs_to_orderstock'

                ));

                $tags_info = array_values($tags_info);
                foreach ($tags_info as $key => $value) {

                    $tags_info[$key]['goods_attr_value'] = $this->_Attrvalue2Pinyin($value['goods_attr_value']);
                    if($value['store_bargin'] == 0){
                        $tags_info[$key]['store_bargin'] = '';
                    }

                    $tags_info[$key]['batch_id'] = $temp_batch_id[$value['id']];   //记录batch_id，用于接下来的打印次数统计
                }
                echo ecm_json_encode($tags_info); 
            }else{
                echo ecm_json_encode(0); 
            }
        }
    }


    /**
     * @name  分配拿货标签（拿货单）
     * @author zjh 2017-07-13
     */

    function assign_tags()
    {
        $bh_id =  $this->visitor->get('has_behalf');
        // 读取配置
        $behalf_setting = $this->_operate_behalf_setting(0);

        //获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
        $refund_order_ids = $this->get_refunds_orders();
        $conditions_refund = "";
        if(!empty($refund_order_ids))
        {
           $conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);    
        }

        // 判断表单是否重复提交，导致重复分配
        $repeat_assign = $_POST['assign_token'] === $_SESSION['assign_token'] ? false : true;
   

        if(IS_POST && isset($_POST['is_set'])){
          
            $batch_num = abs($_POST['batch_num']);

            $config = array();
            if (is_numeric($batch_num) && is_int($batch_num)){  // 判断数据是否合法，不合法则不写入
                
                $config['batch_has_num'] = $batch_num;
            }

            $config['afternoon_sign_time'] = intval($_POST['noon']);
            $this->_operate_behalf_setting(2,$config);   // 修改相关配置信息

            //从新再读取一遍配置
            $behalf_setting = $this->_operate_behalf_setting(0);

        }else if(IS_POST && $repeat_assign == false){

            if($_POST['market_id']){

                extract($_POST);
                // $taker = 'market_'.$market_id.'_takers';
                if (empty($_POST['takers'])){
                    $takers = array();
                }else{
                    $takers = explode(',', $_POST['takers']);
                }
                
      
               // 残次品状态的也要派发
                $this_market_need_assign = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND gwh.market_id = {$market_id} AND ".db_create_in(array(BEHALF_GOODS_IMPERFECT,BEHALF_GOODS_PREPARED),'gwh.goods_status')." AND order_alias.status=".ORDER_ACCEPTED.$conditions_refund,
                    'fields'=>'gwh.id,gwh.goods_price,gwh.market_id,gwh.market_name,gwh.floor_id,gwh.goods_status,gwh.store_id,gwh.taker_id,gwh.batch_id,gwh.real_price,order_alias.status',
                    'join'=>'belongs_to_order,belongs_to_orderthird',
                    'order'=>'floor_id ASC,store_address ASC',
                    'count'=>true
                ));

            }else{

                // 市场合并分配
                $takers[0] = $_POST['taker'];

                $markets_id_array = $_POST['markets'];
                // 残次品状态的也要派发
                $this_market_need_assign = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($markets_id_array ,'gwh.market_id')." AND ".db_create_in(array(BEHALF_GOODS_IMPERFECT,BEHALF_GOODS_PREPARED),'gwh.goods_status')." AND order_alias.status=".ORDER_ACCEPTED.$conditions_refund,
                    'fields'=>'gwh.id,gwh.goods_price,gwh.market_id,gwh.market_name,gwh.floor_id,gwh.goods_status,gwh.store_id,gwh.taker_id,gwh.batch_id,gwh.real_price,order_alias.status',
                    'join'=>'belongs_to_order,belongs_to_orderthird',
                    'order'=>'floor_id ASC,store_address ASC',
                    'count'=>true
                ));

            }

            // 取拿货员名字
            $temp_taker_id = array_values($takers);
            $member_mod = &m('member');
            $temp_user_name = $member_mod->find(array(
                'conditions'=> db_create_in($temp_taker_id , 'user_id'),
                'fields' => 'user_id,user_name,real_name',
            ));

            $user_name = array();
            $real_name = array();
            foreach ($temp_user_name as $key => $value) {
                $user_name[$value['user_id']] = $value['user_name'];
                $real_name[$value['user_id']] = $value['real_name'];
            }
 
            // 按档口分组
            $store_group = array();
            $floor = array();
            $goods_prices = array();    // 记录所有商品的价格
            $markets = array();       // 记录市场
            foreach ($this_market_need_assign as $key => $value) {

                $store_group[$value['market_id']][$value['store_id']][$key] = $value;   // zjh 2017/7/24 多加一个商场的键名
                $floor[] = $value['floor_id'];
                $goods_prices[$value['id']] = $value['goods_price'];   // 记录商品价格
                $markets[$value['market_id']] = $value['market_name'];   //记录市场
            }

            // 取所有楼层id的排序sort_order
            $sort_floor = $this->_market_mod->find(array(
                'conditions'=>db_create_in($floor,'mk_id'),
                'fields'=>'sort_order',
            ));

            $sort_temp = array();
            foreach ($sort_floor as $key => $value) {
                $sort_temp[$value['mk_id']] = $value['sort_order'];
            }

            foreach ($store_group as $key => $value) {
                foreach ($value as $k => $v) {
                    //记录每一个商店的已支付的订单的商品数量
                    $store_group[$key][$k]['number'] = count($v);
                    $temp = current($v);
                    //给每一个商店添加按楼层排序的标志
                    $store_group[$key][$k]['sort_order'] = $sort_temp[$temp['floor_id']];
                }
            }
     
            // 将商品数量大于批次设定数量的和小于批次设定数量的分离开
            $gt_batch_num = array();
            $lt_batch_num = array();
            $sort_arr = array();
            $gt_market_sign = array();
            foreach ($store_group as $key => $value){
                foreach ($value as $k => $v) {
                    if ($v['number'] >= $behalf_setting['batch_has_num']){
                        foreach ($v as $k1 => $v1) {
                            if(is_array($v1)){
                                $gt_batch_num[$k][] = $k1;     // 大于批次数量的，不需要按市场来分类
                            }
                        }
                        $gt_market_sign[$k] = $key;
                    }else{
                        foreach ($v as $k1 => $v1) {
                            if(is_array($v1)){
                                $lt_batch_num[$key][$k][] = $k1;
                            }
                        }
                        $sort_arr[$key][$k] = $v['sort_order'];
                    }
                }               
            }

            // 对商品数量少于批次设定数量的商店按楼层进行排序（从低到高）
            foreach ($lt_batch_num as $key => $value) {
                array_multisort($sort_arr[$key],SORT_ASC,$lt_batch_num[$key]);
            }

            // 对商品数量少于批次设定数量的商店按市场顺序合并一下
            $merge_lt_batch_num = array();
            $i=0;
            $lt_market_sign = array();
            foreach ($lt_batch_num as $key => $value) {

                foreach ($value as $k => $v) {

                    $lt_market_sign[$i]=$key;  

                    foreach ($v as $k1 => $v1) {

                        $merge_lt_batch_num[$i][] = $v1;     
                    }
                    $i++;
                }
            }

            // 给拿货员分配商品标签
            $edit_data = array();
            $conditions = '';
            $need_assign_id = array(); 

            $get_imperfect = array();   // 获取残次品 zjh 2017/8/9

            $gt_num = count($gt_batch_num);
            $lt_num = count($merge_lt_batch_num);

            foreach ($takers as $k => $tk) {

                $recode_mk = array();   //记录市场id

                if ($gt_num != 0){  // 首先分配大于批次设定数量的标签
                    $need_assign_id = current($gt_batch_num);
                    $tmp_store_id = array_search(current($gt_batch_num), $gt_batch_num);
                    $recode_mk[] = $gt_market_sign[$tmp_store_id];
                    next($gt_batch_num);
                    $gt_num--;

                }else if($lt_num != 0){ // 分配小于批次设定数量的标签

                    $recode_mk[] = $lt_market_sign[count($merge_lt_batch_num)-$lt_num];

                    $need_assign_id = current($merge_lt_batch_num);
                    $lt_num--;              
                    for (; $lt_num > 0 ; $lt_num--) {

                        if(count($need_assign_id) < $behalf_setting['batch_has_num']){ 
                            
                            next($merge_lt_batch_num); 
                            // 不断合并两两商店的标签，直到标签数目大于等于批次设定数目，才给拿货员分配
                            $need_assign_id = array_merge($need_assign_id,current($merge_lt_batch_num)); 

                            $recode_mk[] = $lt_market_sign[count($merge_lt_batch_num)-$lt_num];
                        }else{
                            next($merge_lt_batch_num);
                            break;
                        }
                    }                            

                }else{ // 该市场下所有商品标签已经分配完，则跳出循环，不再给下一个拿货员分配标签

                    break;
                }
                
                // 计算所取到的商品的总价
                $sum_goods_price = 0;
                foreach ($need_assign_id as $key => $value) {
                    $sum_goods_price += $goods_prices[$value];
                }

                // 将数组 $need_assign_id 转化为字符串
                $str_id = implode(",", $need_assign_id);

                // 将数组 $recode_mk 转化为字符串
                $recode_mk=array_unique($recode_mk);   // 去掉重复值
                $str_market_id = implode(",", $recode_mk);

                // 处理市场名
                $str_market_name = '';
                foreach ($recode_mk as $key => $value) {
                    $str_market_name .= $markets[$value].',';
                }
                $str_market_name = rtrim($str_market_name,',');

                // 添加作为残次品的缺货记录 zjh 2017/8/9
                $shortage_record_data = array();
                $shortage_reason = array();
                foreach ($need_assign_id as $key => $value) {  // 记录残次品
                	 
	            	 if($this_market_need_assign[$value]['goods_status'] == BEHALF_GOODS_IMPERFECT){

	            	 	$shortage_record_data[$value]['batch_id'] = $this_market_need_assign[$value]['batch_id'];
	                    $shortage_record_data[$value]['goods_id'] = $this_market_need_assign[$value]['id'];
	                    $shortage_record_data[$value]['shortage_status'] = $this_market_need_assign[$value]['goods_status'];
	                    $shortage_record_data[$value]['real_price'] = $this_market_need_assign[$value]['real_price'];

	                    // 记录缺货原因相关的
	                    $shortage_reason[$value]['goods_id'] = $this_market_need_assign[$value]['id'];
	                    $shortage_reason[$value]['reason'] = BEHALF_BACKREASON_IMPERFECT;
	            	 }
                }

                // 写缺货表       
        		$this->_add_shortage_record($shortage_record_data);

        		// 填写缺货原因
        		$this->_add_shortage_reason($shortage_reason);

                $time = time();
                // 给批次表添加对应的记录
                $add_data = array(
                    'assign_time' => $time,
                    'mk_id' => $str_market_id,
                    'mk_name'=>$str_market_name,
                    'goods_count'=> count($need_assign_id),
                    'batch_amount'=>$sum_goods_price,
                    'taker_id' => $tk,
                    'taker_name'=>$user_name[$tk],
                    'real_name'=>$real_name[$tk],
                    'content'=>$str_id,
                    'bh_id'=>$bh_id
                );

                $ret_batch_id = $this->_goods_taker_batch_mod ->add($add_data);
                
                // 给表goods_warehouse添加对应的拿货员id 和 更改商品状态（改为已派发）
                $edit_data = array(
                    'goods_status' => BEHALF_GOODS_DELIVERIES,
                    'taker_id' => $tk,
                    'taker_time'=> $time,
                    'batch_id'=> $ret_batch_id,
                    'real_price'=>0.00, 
                );

                $conditions = db_create_in($need_assign_id,'id');
                $this->_goods_warehouse_mod->edit($conditions, $edit_data);

            }        
           
        }

        // 重置标签
        $this->_reset_tags();

        //获取每个市场的可分配标签的对应数量
        $need_assign = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in(array(BEHALF_GOODS_IMPERFECT,BEHALF_GOODS_PREPARED),'gwh.goods_status')." AND order_alias.status= ".ORDER_ACCEPTED.$conditions_refund,
                    'fields'=>'gwh.market_id',
                    'join'=>'belongs_to_order,belongs_to_orderthird',
        ));


        // 根据市场划分数量
        $get_market_id = array();
        $market_goods_num = array();
        foreach ($need_assign as $key => $value) {
            $get_market_id[] = $value['market_id'];
        }

        $market_goods_num = array_count_values ($get_market_id);

        // 查看批次表，获取当天曾经被分配的市场
        $today = strtotime(date('Y-m-d',time()));
        $one_second_before_next_day = strtotime(date('Y-m-d',strtotime('+1 day')))-1;

        $today_assigned_markets = $this->_goods_taker_batch_mod->find(array(
                    'conditions'=>"bh_id = {$bh_id} AND assign_time between $today and $one_second_before_next_day",
                    'fields'=>'mk_id',
        ));

        // 合并到商品还没有被分配完的市场
        $mk_id_array = array();
        foreach ($today_assigned_markets as $key => $value) {

            $mk_id_array = explode(',', $value['mk_id']);

            foreach ($mk_id_array as $k => $v) {
                if(!array_key_exists($v,$market_goods_num)){
                    $market_goods_num[$v] = 0;
                }
            }  
        }

        // 获取代发所拥有的市场
        $bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);  
        $need_assign_markets = array();   
        if($bh_markets)
        {
            foreach ($bh_markets as $key => $value) {
                
                foreach ($market_goods_num as $k => $v) {
                    if($k == $value['mk_id']){
                        $need_assign_markets[$key] = $value;
                        $need_assign_markets[$key]['goods_num'] = $v;
                    }
                }
            }

            $sort_arr = array();//用于多维排序,商品数量多的市场排在前面
            foreach ($need_assign_markets as $k=>$v)
            {
                $sort_arr[] = $v['goods_num'];
            }
            array_multisort($sort_arr,SORT_DESC,$need_assign_markets);
        }  

        //生成一个token
		$_SESSION['assign_token'] = md5(microtime(true));



        // 列出拿货员
        // $model_member =& m('member');
        // $takers = $model_member->find(array(
        //     'conditions'=>'behalf_goods_taker= '.$bh_id,
        //     'fields' => 'user_id,user_name,real_name' 
        // ));

        // 新的方式获取拿货员 zjh
        $takers = $this->_get_spec_func_employees(BEHALF_TAKE_RETURN_GOODS);

        $this->assign('assign_token',$_SESSION['assign_token']);  //token,防止表单重复提交

        $this->assign('noon',$behalf_setting['afternoon_sign_time']); //中午时间点
        $this->assign('date',date("Y-m-d",time()));   //当天时间
        $this->assign('batch_has_num',$behalf_setting['batch_has_num']);  // 每一批次的件数
        $this->assign('takers',$takers);  //拿货员
        $this->assign('need_assign_markets',$need_assign_markets);   // 各个市场的信息，包含商品量
        $this->assign('batch_has_num',$behalf_setting['batch_has_num']);    // 每一批的件数
       // $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.assign_tags.html');
    }

    /**
     * @name  拿货批次管理
     * @author zjh 2017-07-13
     */

    function goods_batch_manage()
    {   
        $bh_id =  $this->visitor->get('has_behalf');
        if (IS_POST){
            if(isset($_POST['query_time']) && isset($_POST['query_endtime'])){

                $start_time = $_POST['query_time'];
                $end_time = $_POST['query_endtime'];

            }else if(isset($_POST['goods_no'])){

                // 单张标签打印
                $tags_info = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND gwh.goods_no = ".$_POST['goods_no'],
                    'fields'=>'gwh.goods_attr_value,gwh.goods_specification,gwh.store_bargin,gwh.delivery_name,order_alias.total_quantity,gwh.order_sn,gwh.goods_no,gwh.goods_price,orderstock.stock_code',
                    'join'=>'belongs_to_order,belongs_to_orderstock'
                ));

                $tags_info = array_values($tags_info);
                foreach ($tags_info as $key => $value) {

                    $tags_info[$key]['goods_attr_value'] = $this->_Attrvalue2Pinyin($value['goods_attr_value']);
                    if($value['store_bargin'] == 0){
                        $tags_info[$key]['store_bargin'] = '';
                    }
                }
                echo ecm_json_encode($tags_info); 
                exit;
            }
        }

        if(!isset($start_time) || !isset($end_time)){
            // $start_time = date("Y-m-d H:i:s",time());
            // $end_time = date("Y-m-d H:i:s",time());

            $day = date("Y-m-d",time());
        	$day_stamp = strtotime($day);
            $start_time = date("Y-m-d H:i:s",$day_stamp);   // 选一天内
            $end_time = date("Y-m-d H:i:s",$day_stamp+86399);
        }

        $this->assign('show_print',true);   //显示打印的js
        // $this->assign('batch',$batch);   //批次的信息
        $this->assign('start_time',$start_time);  // 开始时间
        $this->assign('end_time',$end_time);  // 结束时间
        //$this->_import_css_js('dt');
         $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.goods_batch_manage.html');

    }

    /**
     * @name  异步获取拿货批次
     * @author zjh 2017-07-17
     */
    function get_pipe_batch()
    {

        $bh_id =  $this->visitor->get('has_behalf');

        //建立缺货相关的状态数组(明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货,残次品)
        $shortage_array = array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_AFTERNOON,BEHALF_GOODS_UNSURE,BEHALF_GOODS_STOP_TAKING,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE,BEHALF_GOODS_SKU_UNSALE,BEHALF_GOODS_ERROR,BEHALF_GOODS_PRICE_ERROR,BEHALF_GOODS_ERROR2,BEHALF_GOODS_IMPERFECT,BEHALF_GOODS_CANCEL,BEHALF_GOODS_ADJUST);
        
        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
        
        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                case 4:$orderSql = " goods_count ".$order_dir;break;
                case 8:$orderSql = " batch_amount ".$order_dir;break;
                default:$orderSql = ' assign_time DESC';
            }
        }
        
        $recordsTotal = 0;
        $recordsFiltered = 0;
   
        $from = strtotime($_GET['query_time']);
        $to = strtotime($_GET['query_endtime']);

        if(strlen($search) == 0){

            if($_GET['query_time'] == $_GET['query_endtime']){

                $from = 0;
            }

            $batch = $this->_goods_taker_batch_mod->find(array(
                'conditions'=>"bh_id = {$bh_id} AND assign_time between $from and $to",
                'count'=>true,
                'order'=>$orderSql." ,assign_time DESC",
                'limit'=>"{$start},{$page_per}"
            ));

            $recordsTotal = $recordsFiltered = $this->_goods_taker_batch_mod->getCount();
        }
        
        if(strlen($search) > 0)
        { 
            if($_GET['query_time'] == $_GET['query_endtime']){

                $from = strtotime(date('Y-m-d',strtotime($_GET['query_time'])));  // 取当天的
            }

            $batch = $this->_goods_taker_batch_mod->find(array(
                'conditions'=>"bh_id = {$bh_id} AND real_name like '%{$search}%' AND (assign_time between $from and $to)",
                'count'=>true,
                'order'=>$orderSql." ,assign_time DESC",
                'limit'=>"{$start},{$page_per}"
            ));

            $recordsTotal = $recordsFiltered = $this->_goods_taker_batch_mod->getCount();
        }

        $temp_mk_id = array();
        $temp_taker_id = array();
        $warehouse_id = array();
        $batch_id = array();   // 记录批次
        foreach ($batch as $key => $value) {

        	// 记录批次id
        	$batch_id[] = $value['batch_id'];

            //将字段content内容转化为数组
            $warehouse_id[$key] = explode(',', $value['content']);
            //格式化分配日期
            $batch[$key]['assign_time'] = date('Y-m-d H:i:s',$value['assign_time']);
            // 计算拿货用时
            $taken_time = '';
            $batch[$key]['taken_time'] = '';

            if (($value['end_time'] != 0) && ($value['end_time'] > $value['assign_time'])){

                $diff = $value['end_time']-$value['assign_time'];

                // $diff = time() - 1501053246;

                $date=floor(($diff)/86400);
                // echo "相差天数：".$date."天<br/><br/>";
                $taken_time .= $date ? $date.'天 ':'';

                $hour=floor(($diff)%86400/3600);
                // echo "相差小时数：".$hour."小时<br/><br/>";
                $taken_time .= ($date || $hour) ? $hour.'时 ':'';
                 
                $minute=floor(($diff)%86400%3600/60);
                // echo "相差分钟数：".$minute."分钟<br/><br/>";
                $taken_time .= ($date || $hour || $minute) ? $minute.'分 ':'';
                 
                $second=floor(($diff)%86400%3600%60);
                // echo "相差秒数：".$second."秒";
                $taken_time .= ($date || $hour || $minute || $second) ? $second.'秒 ':'';

                $batch[$key]['taken_time'] = $taken_time;

            }
        }


        // 获取缺货记录
        // $shortage_record = $this->_get_shortage_record($batch_id);
        // $real_price = $shortage_record[$batch_id][$value['id']]['real_price'];

        // 计算已拿件数、缺货件数、入仓件数、应使用金额、实际使用金额
        
        foreach ($batch as $key => $value) {

            $taken_num = 0;
            $warehouse_num = 0;
            $shortage_num = 0;
            $need_amount = 0;
            $actual_amount = 0;

            $goods = $this->_goods_warehouse_mod->find(array(

                'conditions'=> db_create_in($warehouse_id[$key] , 'gwh.id'),
                'fields' => 'gwh.goods_price,gwh.real_price,gwh.goods_status,order_alias.status,gwh.batch_id',
                'join'=>'belongs_to_order,belongs_to_orderthird',
            ));

            foreach ($goods as $k => $v) {

            	if($v['batch_id'] == $value['batch_id']){  // 是否为最后一次分配的批次

            		if($v['goods_status'] == BEHALF_GOODS_READY_APP){ // app 已经拿到
	                    $taken_num += 1;
	                    // 累加应使用金额和实际使用金额
	                    $need_amount += $v['goods_price'];
	                    if($v['real_price'] == 0){
	                    	$actual_amount += $v['goods_price'];
	                    }else{
	                    	$actual_amount += $v['real_price'];
	                    }
	                    

	                }else if ($v['goods_status'] == BEHALF_GOODS_READY || $v['goods_status'] == BEHALF_GOODS_SEND || $v['goods_status'] == BEHALF_GOODS_REBACK|| $v['goods_status'] == BEHALF_GOODS_BACKING || $v['goods_status'] == BEHALF_GOODS_REBACK_FAIL){  //已备货，代表已入仓; 已发货，退货
	                    $taken_num += 1;
	                    $warehouse_num += 1;
	                    // 累加应使用金额和实际使用金额
	                    $need_amount += $v['goods_price'];
	                    // $actual_amount += $v['real_price'];
	                    if($v['real_price'] == 0){
	                    	$actual_amount += $v['goods_price'];
	                    }else{
	                    	$actual_amount += $v['real_price'];
	                    }
	                    //明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货
	                }else if(in_array($v['goods_status'], $shortage_array) || $v['status'] == ORDER_CANCELED){
	                    $shortage_num += 1;
	                }

            	}else{

            		$shortage_num += 1;
            	}

                

            }

            $batch[$key]['taken_num'] = $taken_num;
            $batch[$key]['warehouse_num'] = $warehouse_num; 
            $batch[$key]['shortage_num'] = $shortage_num;
            $batch[$key]['need_amount']= number_format($need_amount, 2, '.', '');
            $batch[$key]['actual_amount'] = number_format($actual_amount, 2, '.', '');

        }
        
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>intval($recordsTotal),'recordsFiltered'=>intval($recordsFiltered),'data'=>array_values($batch))); 

    }

    /**
     * @name  批次详情表管理
     * @author zjh 2017-08-09
     */

    function batch_detail_manage()
    { 
        if (IS_POST){
            if(isset($_POST['query_time']) && isset($_POST['query_endtime'])){

                $start_time = $_POST['query_time'];
                $end_time = $_POST['query_endtime'];
            }
        }

        if(!isset($start_time) || !isset($end_time)){
        	$day = date("Y-m-d",time());
        	$day_stamp = strtotime($day);
            $start_time = date("Y-m-d H:i:s",$day_stamp);   // 选一天内
            $end_time = date("Y-m-d H:i:s",$day_stamp+86399);
        }

        $this->assign('show_print',true);   //显示打印的js
        $this->assign('start_time',$start_time);  // 开始时间
        $this->assign('end_time',$end_time);  // 结束时间
        // $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.batch_detail_manage.html');

    }

    /**
     * @name  获取需要显示的标签
     * @author zjh 2017-08-09
     * @param $batch 批次的信息
     * @param $start 分页的开始
     * @param $page_per 每页的数量
     * @param $need_goods_id 需要的标签warehouse id
     * @param $need_batch_id 输出的批次id
     */
    function _get_need_tags($batch,$start,$page_per,&$need_goods_id,&$need_batch_id)
    {
    	    
    	$tags_total_num = 0;    // 总共拥有的表签数
        $last_tags_total_num = 0;    // 不包含当前批次的标签总数

        $content_array = array();    // 包含的warehouse id 数组

		$need_total_num = 0;          // 是否达到数量要求

		$first_cut = true;      // 前一截：是否需要对content_array进行截取
		$last_cut = true;      // 后一截：是否需要对content_array进行截取

        foreach ($batch as $key => $value) {

        	if($last_cut == true){
	        	if($need_total_num >= $page_per){  //是否大于每一页需要的数量，是，则截取
	        		
	        		rsort($content_array);
	        		$offset = count($content_array) - ($need_total_num - $page_per);
	        		// 回退修改最后一个元素
	        		$need_goods_id[$temp_batch_id] = array_slice($content_array, $offset);  // 获取需要输出的id
	        		
	        		$last_cut = false;
	        	}

	        	if($last_cut == true){ 
	        		$need_batch_id[] = $value['batch_id'];     // 获取批次id
	        	}
	        	
        	}

        	$content_array = explode(',', $value['content']);
        	$tags_total_num += count($content_array);

        	if($first_cut == true){

        		if($tags_total_num > $start)   // 分页，获取数据
            	{
            		$offset = $start - $last_tags_total_num;

            		// 对 $content_array 进行id由大到小的排序
            		rsort($content_array);
            		$need_goods_id[$value['batch_id']] = array_slice($content_array, $offset);  // 获取需要输出的id
            		$need_total_num += count($content_array) - $offset;

            		$first_cut = false;   // 不需要再截取了
            	}

        	}else if($last_cut == true){

				// 对 $content_array 进行id由大到小的排序
        		rsort($content_array);
        		$need_goods_id[$value['batch_id']] = $content_array;  // 获取需要输出的id
        		$need_total_num += count($content_array);            		

        	}
        	

        	$last_tags_total_num += count($content_array);
        	$temp_batch_id = $value['batch_id'];
        }

        return $tags_total_num;
    }


    /**
     * @name  获取缺货记录
     * @param $batch_id  批次id组
     * @author zjh 2017-08-09
     */
    function _get_shortage_record($batch_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$shortage_record = $this->_shortage_record_mod->find(array(
    		'conditions'=>"bh_id = {$bh_id} AND ".db_create_in($batch_id,'batch_id'),

        ));

    	$return = array();  // 记录返回的数据,按批次分组,再按warehouse id分组

        foreach ($shortage_record as $key => $value) {
        	$return[$value['batch_id']][$value['goods_id']] = $value;

        }

        return $return;
    }

    /**
     * @name  异步获取批次详情
     * @author zjh 2017-08-09
     */


    /**
     * @name  异步获取批次详情
     * @author zjh 2017-08-09
     */
    function get_pipe_batch_detail()
    {
        $bh_id =  $this->visitor->get('has_behalf');

        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
        
        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                case 0:$orderSql = " assign_time ".$order_dir;break;
                default:$orderSql = ' assign_time DESC';
            }
        }
        
        $recordsTotal = 0;
        $recordsFiltered = 0;
        
        $from = strtotime($_GET['query_time']);
        $to = strtotime($_GET['query_endtime']);

        // 获取代发所拥有的市场
        $bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);  

        // 列出市场id
        $markets_array = array();
        foreach ($bh_markets as $key => $value) {
            $markets_array[] = $value['mk_id'];
        }

        //获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
        $refund_order_ids = $this->get_refunds_orders();
        $conditions_refund = "";
        if(!empty($refund_order_ids))
        {
           $conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);    
        }

        // 把当前时间范围内的所有批次全取出来
        if(strlen($search) == 0){  

        	$batch = $this->_goods_taker_batch_mod->find(array(
	            'conditions'=>"bh_id = {$bh_id} AND assign_time between $from and $to",
	            'count'=>true,
	            'order'=>$orderSql." ,assign_time DESC",
	        ));
        }else{  // 优先搜索批次（除此之外，下面还可以搜索 标签号和订单号）

        	$batch = $this->_goods_taker_batch_mod->find(array(
	            'conditions'=>"bh_id = {$bh_id} AND batch_id = '$search' ",
	            'count'=>true,
	            'order'=>$orderSql." ,assign_time DESC",
	        ));

	        if(empty($batch)){  // 找不到，则去找标签号和订单号

	        	// $batch = $this->_goods_taker_batch_mod->find(array(
		        //     'conditions'=>"bh_id = {$bh_id} AND assign_time between $from and $to",
		        //     'count'=>true,
		        //     'order'=>$orderSql." ,assign_time DESC",
		        // ));

	        	// 不限定时间，效率会很低
		        $batch = $this->_goods_taker_batch_mod->find(array(
		            'conditions'=>"bh_id = {$bh_id} ",
		            'count'=>true,
		            'order'=>$orderSql." ,assign_time DESC",
		        ));
	        }else{
	        	$search = '';
	        }
        }
    	

        // 统计批次的标签数目
        $need_goods_id = array();
        $tags_total_num = $this->_get_need_tags($batch,$start,$page_per,$need_goods_id,$need_batch_id);

        // 获取缺货记录
        $shortage_record = $this->_get_shortage_record($need_batch_id);

        $recordsTotal = $recordsFiltered = $tags_total_num;  // 向网页下发总数

        $lang = Lang::get('goods_shortage_info');    // 获取缺货信息

        // 缺货的相关信息
        $shortage_info = array(

            BEHALF_GOODS_TOMORROW => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['tomorrow'],
                'arrive_time'=>$lang['tomorrow_arrive'],
                'remark'=>''),

            BEHALF_GOODS_AFTERNOON => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['afternoon'],
                'arrive_time'=>$lang['afternoon_arrive'],
                'remark'=>''),

            BEHALF_GOODS_UNSURE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['unsure'],
                'arrive_time'=>$lang['unsure_arrive'],
                'remark'=>''),

            BEHALF_GOODS_STOP_TAKING => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['stop_taking'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>$lang['stop_taking_remark']),

            BEHALF_GOODS_UNFORMED => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_unformed'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_UNSALE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_unsale'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_SKU_UNSALE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_sku_unsale'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_ERROR => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['info_wrong'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_PRICE_ERROR => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['price_error'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_ERROR2 => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['other_wrong'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_IMPERFECT => array(            
                'shortage'=>1,
                'shortage_reason'=>$lang['imperfect'],
                'arrive_time'=>$lang['imperfect_arrive'],
                'remark'=>''),

        );

        foreach ($need_goods_id as $key => $value) {

        	if(strlen($search) == 0){  // 有搜索时不执行，这样效率才会更高
            	
	        	// 分别取出每一批次对应的商品
	            $batch_goods[$key] = $this->_goods_warehouse_mod->find(array(
	                'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($markets_array,'gwh.market_id')." AND ".db_create_in($value,'gwh.id'),
	                'fields'=>'gwh.goods_spec_id,gwh.store_address,gwh.batch_id,gwh.order_add_time,gwh.order_sn,gwh.goods_no,gwh.market_name,gwh.store_name,gwh.goods_sku,gwh.goods_specification,gwh.goods_status,gwh.goods_price,gwh.real_price,gwh.taker_id,order_alias.pay_time,order_alias.status,order_alias.order_id',
	                'count'=>true,
	                'order'=>"gwh.id DESC",
	                'join'=>'belongs_to_order,belongs_to_orderthird',
	            )); 

	        }
	        
	        if(strlen($search) > 0)
	        { 

	        	$batch_goods[$key] = $this->_goods_warehouse_mod->find(array(
	                'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($markets_array,'gwh.market_id')." AND ".db_create_in($value,'gwh.id')." AND ( gwh.order_sn = '$search' OR gwh.goods_no = '$search' )",
	                'fields'=>'gwh.goods_spec_id,gwh.store_address,gwh.batch_id,gwh.order_add_time,gwh.order_sn,gwh.goods_no,gwh.market_name,gwh.store_name,gwh.goods_sku,gwh.goods_specification,gwh.goods_status,gwh.goods_price,gwh.real_price,gwh.taker_id,order_alias.pay_time,order_alias.status,order_alias.order_id',
	                'count'=>true,
	                'order'=>"gwh.id DESC",
	                'join'=>'belongs_to_order,belongs_to_orderthird',
	            )); 
	        
	        }  
	        
        }

        $goods = array();   // 记录要输出的商品标签信息
        $key = 0;
        foreach ($batch_goods as $batch_id => $goods_info) {

        	foreach ($goods_info as $k => $value) {
        		
        		$goods[$key] = $value;

        		// 获取分配时间、批次号、拿货员真实姓名  (从批次内获取)
        		$goods[$key]['assign_time'] = $batch[$batch_id]['assign_time'];
        		$goods[$key]['batch_id'] = $batch_id;
        		$goods[$key]['real_name'] = $batch[$batch_id]['real_name'];

        		// 初始化
	            $goods[$key]['number'] = 1;   // 数量，恒为1
	            $goods[$key]['has_taken'] = 0;  //app已拿
	            $goods[$key]['warehouse'] = 0;   // 入仓
	            $goods[$key]['shortage'] = 0;   //缺货

	            $goods[$key]['shortage_reason'] = '';   //缺货原因
	            $goods[$key]['remark'] = '';   //备注

	            // 格式化一下下单日期
            	$goods[$key]['assign_time'] = date('Y-m-d H:i:s',$goods[$key]['assign_time']);

            	// 格式化一下下单日期
            	$goods[$key]['pay_time'] = date('Y-m-d H:i:s',$value['pay_time']+date('Z'));
 
	            if($value['batch_id'] == $batch_id){  // 最后一次分配的情况

	            	// 已拿货、入仓、缺货的相关信息
		            if($value['goods_status'] == BEHALF_GOODS_READY || $value['goods_status'] == BEHALF_GOODS_SEND || $value['goods_status'] == BEHALF_GOODS_REBACK|| $value['goods_status'] == BEHALF_GOODS_BACKING || $value['goods_status'] == BEHALF_GOODS_REBACK_FAIL){  // 已入仓或者已发货或者已退货

		                $goods[$key]['has_taken'] = 1;
		                $goods[$key]['warehouse'] = 1;

		            }else if ($value['goods_status'] == BEHALF_GOODS_READY_APP){  // app已拿

		                $goods[$key]['has_taken'] = 1;
		  
		            //明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货
		            }else if(array_key_exists($value['goods_status'], $shortage_info)){

		                $goods[$key]['shortage'] = $shortage_info[$value['goods_status']]['shortage'];
		                $goods[$key]['shortage_reason'] = $shortage_info[$value['goods_status']]['shortage_reason'];
		                $goods[$key]['arrive_time'] = $shortage_info[$value['goods_status']]['arrive_time'];
		                $goods[$key]['remark'] = $shortage_info[$value['goods_status']]['remark']; 

		            }else if ($value['goods_status'] == BEHALF_GOODS_ADJUST) {  //已换款

		            	$goods[$key]['shortage'] = 1;

		                $goods[$key]['remark'] = $lang['goods_adjust_remark']; 

		            }else if ($value['goods_status'] == BEHALF_GOODS_CANCEL){  // 商品已取消

		            	$goods[$key]['shortage'] = 1;

		                $goods[$key]['remark'] = $lang['goods_cancel_remark']; 

		            }else if ($value['status'] == ORDER_CANCELED ){  //订单取消
		                $goods[$key]['shortage'] = 1;
		                // $goods[$key]['remark'] = $lang['order_cancel'];
		            }

		            if ($value['status'] == ORDER_CANCELED ){  //订单取消

		            	$goods[$key]['remark'] = $lang['order_cancel'];
		            }
		            // 记录订单退款中的状态
		            if (in_array($value['order_id'], $refund_order_ids)){
		                // $goods[$key]['shortage'] = 1;
		                $goods[$key]['remark'] = $lang['goods_refund_remark'];
		            }

	            }else{  // 前几次分配的情况（如果存在多次分配）

		            $goods[$key]['shortage'] = 1;   //缺货
		            $shortage_status = $shortage_record[$batch_id][$value['id']]['shortage_status'];
		            $real_price = $shortage_record[$batch_id][$value['id']]['real_price'];

		            $goods[$key]['shortage_reason'] = $shortage_info[$shortage_status]['shortage_reason'];;   //缺货原因
		            $goods[$key]['remark'] = $shortage_info[$shortage_status]['remark'];   //备注

		            // 修改拿货价（用历史的替换现在的） zjh 2017/8/11 主要针对残次品
		            $goods[$key]['real_price'] = $real_price;
	            }	     
	            
        		$key++;
        	}

        }

        $recordsTotal = $recordsFiltered = $key;
        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>intval($recordsTotal),'recordsFiltered'=>intval($recordsFiltered),'data'=>array_values($goods))); 
    }


    /**
     * @name  sku明细管理
     * @author zjh 2017-07-13
     */

    function sku_manage()
    { 
        if (IS_POST){
            if(isset($_POST['query_time']) && isset($_POST['query_endtime'])){

                $start_time = $_POST['query_time'];
                $end_time = $_POST['query_endtime'];
            }
        }

        if(!isset($start_time) || !isset($end_time)){
            // $start_time = date("Y-m-d H:i:s",time());
            // $end_time = date("Y-m-d H:i:s",time());

            $day = date("Y-m-d",time());
        	$day_stamp = strtotime($day);
            $start_time = date("Y-m-d H:i:s",$day_stamp);   // 选一天内
            $end_time = date("Y-m-d H:i:s",$day_stamp+86399);
        }

        $this->assign('show_print',true);   //显示打印的js
        $this->assign('start_time',$start_time);  // 开始时间
        $this->assign('end_time',$end_time);  // 结束时间
        // $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.sku_manage.html');

    }


    /**
     * @name  异步获取sku明细
     * @author zjh 2017-07-17
     */
    function get_pipe_sku()
    {
        $bh_id =  $this->visitor->get('has_behalf');

        $start = intval($_GET['start']);
        $page_per = intval($_GET['length']);
        //search
        $search = trim($_GET['search']['value']);
        //order
        $order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
        $order_dir = $_GET['order']['0']['dir'];//asc desc 升序或者降序
        
        //拼接排序sql
        $orderSql = "";
        if(isset($order_column)){
            $i = intval($order_column);
            switch($i){
                case 0:$orderSql = " order_alias.pay_time ".$order_dir;break;
                case 1:$orderSql = " gwh.batch_id ".$order_dir;break;
                case 13:$orderSql = " gwh.goods_price ".$order_dir;break;
                case 14:$orderSql = " gwh.real_price ".$order_dir;break;
                default:$orderSql = ' gwh.taker_time DESC';
            }
        }
        
        $recordsTotal = 0;
        $recordsFiltered = 0;
        
        $from = strtotime($_GET['query_time']);
        $to = strtotime($_GET['query_endtime']);

        if($_GET['query_time'] == $_GET['query_endtime']){

            $from = 0;
        }

        // 获取代发所拥有的市场
        $bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);  

        // 列出市场id
        $markets_array = array();
        foreach ($bh_markets as $key => $value) {
            $markets_array[] = $value['mk_id'];
        }

        //获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
        $refund_order_ids = $this->get_refunds_orders();
        $conditions_refund = "";
        if(!empty($refund_order_ids))
        {
           $conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);    
        }

        if(strlen($search) == 0){  // 有搜索时不执行，这样效率才会更高
        	// zjh 2017/8/8 把退款中的$conditions_refund删掉，不过滤退款中状态的商品
            $goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"gwh.bh_id = {$bh_id} AND ( gwh.taker_time between $from and $to ) AND gwh.goods_status != ".BEHALF_GOODS_PREPARED." AND ".db_create_in($markets_array,'market_id'),
                'fields'=>'gwh.goods_spec_id,gwh.store_address,gwh.batch_id,gwh.order_add_time,gwh.order_sn,gwh.goods_no,gwh.market_name,gwh.store_name,gwh.goods_sku,gwh.goods_specification,gwh.goods_status,gwh.goods_price,gwh.real_price,gwh.taker_id,order_alias.pay_time,order_alias.status,order_alias.order_id',
                'count'=>true,
                'order'=>$orderSql." ,gwh.taker_time DESC,order_alias.pay_time DESC",
                'join'=>'belongs_to_order,belongs_to_orderthird',
                'limit'=>"{$start},{$page_per}"
            )); 

            $recordsTotal = $recordsFiltered = $this->_goods_warehouse_mod->getCount();
        }
        
        if(strlen($search) > 0)
        { 
            $timestamp = strtotime($search);  // 将日期转换成时间戳
            $one_day = strtotime(date('Y-m-d',$timestamp+date('Z')));
            $one_second_before_next_day = strtotime(date('Y-m-d',$timestamp+date('Z'))) + 86399;

            $goods = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"gwh.bh_id = {$bh_id} AND gwh.goods_status != ".BEHALF_GOODS_PREPARED." AND ( gwh.goods_sku like '$search%' OR gwh.order_sn = '$search' OR gwh.goods_no = '$search' OR ( order_alias.pay_time between $one_day and $one_second_before_next_day ) ) AND ".db_create_in($markets_array,'market_id'),
                'fields'=>'gwh.goods_spec_id,gwh.store_address,gwh.batch_id,gwh.order_add_time,gwh.goods_no,gwh.order_sn,gwh.market_name,gwh.store_name,gwh.goods_sku,gwh.goods_specification,gwh.goods_status,gwh.goods_price,gwh.real_price,gwh.taker_id,order_alias.pay_time,order_alias.status,order_alias.order_id',
                'count'=>true,
                'order'=>$orderSql." ,gwh.taker_time DESC,order_alias.pay_time DESC",
                'join'=>'belongs_to_order,belongs_to_orderthird',
                'limit'=>"{$start},{$page_per}"
            )); 

            $recordsTotal = $recordsFiltered = $this->_goods_warehouse_mod->getCount();

            // 查拿货员
            if(!$goods){
                // 在warehouse里面找不到，则找拿货员id
                $temp_taker_id = array_values($temp_taker_id);
        
                $member_mod = &m('member');
                $tmp_user_id = $member_mod->find(array(
                    'conditions'=> "user_name like '%$search%'",
                    'fields' => 'user_id',
                ));

                $user_id = array();
                foreach ($tmp_user_id as $key => $value) {
                    $user_id[] = $value['user_id'];
                }

                // 查拿货员不限天数
                if(empty($user_id)){

                    $goods = array();
                    $recordsTotal = $recordsFiltered  = 0;

                }else{
                    $goods = $this->_goods_warehouse_mod->find(array(
                        'conditions'=>"gwh.bh_id = {$bh_id} AND gwh.goods_status != ".BEHALF_GOODS_PREPARED." AND ".db_create_in($user_id , 'taker_id')." AND ".db_create_in($markets_array,'market_id'),
                        'fields'=>'gwh.goods_spec_id,gwh.store_address,gwh.batch_id,gwh.order_add_time,gwh.goods_no,gwh.order_sn,gwh.market_name,gwh.store_name,gwh.goods_sku,gwh.goods_specification,gwh.goods_status,gwh.goods_price,gwh.real_price,gwh.taker_id,order_alias.pay_time,order_alias.status,order_alias.order_id',
                        'count'=>true,
                        'order'=>$orderSql." ,gwh.taker_time DESC,order_alias.pay_time DESC",
                        'join'=>'belongs_to_order,belongs_to_orderthird',
                        'limit'=>"{$start},{$page_per}"
                    )); 

                    $recordsTotal = $recordsFiltered  = $this->_goods_warehouse_mod->getCount();
                } 
            }
        }


        $lang = Lang::get('goods_shortage_info');    // 获取缺货信息

        $temp_taker_id = array();     // 记录拿货员id
        $wrong_remark_id = array();     // 记录信息错误和已下架
        $goods_spec_id = array();     // 记录商品规格id

        // 缺货的相关信息
        $shortage_info = array(

            BEHALF_GOODS_TOMORROW => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['tomorrow'],
                'arrive_time'=>$lang['tomorrow_arrive'],
                'remark'=>''),

            BEHALF_GOODS_AFTERNOON => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['afternoon'],
                'arrive_time'=>$lang['afternoon_arrive'],
                'remark'=>''),

            BEHALF_GOODS_UNSURE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['unsure'],
                'arrive_time'=>$lang['unsure_arrive'],
                'remark'=>''),

            BEHALF_GOODS_STOP_TAKING => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['stop_taking'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>$lang['stop_taking_remark']),

            BEHALF_GOODS_UNFORMED => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_unformed'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_UNSALE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_unsale'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_SKU_UNSALE => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['goods_sku_unsale'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_ERROR => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['info_wrong'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_PRICE_ERROR => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['price_error'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_ERROR2 => array(
                'shortage'=>1,
                'shortage_reason'=>$lang['other_wrong'],
                'arrive_time'=>$lang['stop_taking_arrive'],
                'remark'=>''),

            BEHALF_GOODS_IMPERFECT => array(            
                'shortage'=>1,
                'shortage_reason'=>$lang['imperfect'],
                'arrive_time'=>$lang['imperfect_arrive'],
                'remark'=>''),

        );

        foreach ($goods as $key => $value) {

            // 初始化
            $goods[$key]['number'] = 1;   // 数量，恒为1
            $goods[$key]['has_taken'] = 0;  //app已拿
            $goods[$key]['warehouse'] = 0;   // 入仓
            $goods[$key]['shortage'] = 0;   //缺货

            $goods[$key]['shortage_reason'] = '';   //缺货原因
            $goods[$key]['arrive_time'] = '';  //到货时间
            $goods[$key]['remark'] = '';   //备注

            $goods[$key]['taker_name'] = '';   //拿货员名字

            // 记录商品规格id
            $goods_spec_id[] = $value['goods_spec_id'];
          

            // 已拿货、入仓、缺货的相关信息
            if($value['goods_status'] == BEHALF_GOODS_READY || $value['goods_status'] == BEHALF_GOODS_SEND || $value['goods_status'] == BEHALF_GOODS_REBACK|| $value['goods_status'] == BEHALF_GOODS_BACKING || $value['goods_status'] == BEHALF_GOODS_REBACK_FAIL){  // 已入仓或者已发货或者已退货

                $goods[$key]['has_taken'] = 1;
                $goods[$key]['warehouse'] = 1;

            }else if ($value['goods_status'] == BEHALF_GOODS_READY_APP){  // app已拿

                $goods[$key]['has_taken'] = 1;
  
            //明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货
            }else if(array_key_exists($value['goods_status'], $shortage_info)){

                $goods[$key]['shortage'] = $shortage_info[$value['goods_status']]['shortage'];
                $goods[$key]['shortage_reason'] = $shortage_info[$value['goods_status']]['shortage_reason'];
                $goods[$key]['arrive_time'] = $shortage_info[$value['goods_status']]['arrive_time'];
                $goods[$key]['remark'] = $shortage_info[$value['goods_status']]['remark']; 

                if($value['goods_status'] != BEHALF_GOODS_STOP_TAKING && $value['status'] != ORDER_CANCELED){  //跳过停止拿货或订单取消的备注
                    
                    $wrong_remark_id[] = $value['id'];     // 记录缺货的warehouse id 用于获取相关备注
                }

            }else if ($value['goods_status'] == BEHALF_GOODS_ADJUST) {  //已换款

            	$goods[$key]['shortage'] = 1;

                $goods[$key]['remark'] = $lang['goods_adjust_remark']; 

            } else if ($value['goods_status'] == BEHALF_GOODS_CANCEL){  // 商品已取消

            	$goods[$key]['shortage'] = 1;

                $goods[$key]['remark'] = $lang['goods_cancel_remark']; 

            }else if ($value['status'] == ORDER_CANCELED){  // 记录订单已取消的状态
                $goods[$key]['shortage'] = 1;
                // $goods[$key]['remark'] = $lang['order_cancel'];
            }

             if ($value['status'] == ORDER_CANCELED ){  //订单取消

            	$goods[$key]['remark'] = $lang['order_cancel'];
            }

            // 过滤重复的taker_id
            $temp_taker_id[$value['taker_id']] = $value['taker_id'];

            // 格式化一下下单日期
            $goods[$key]['pay_time'] = date('Y-m-d H:i:s',$value['pay_time']+date('Z'));

            // 取档口号
            $index = strrpos($goods[$key]['store_address'],' ');
            $goods[$key]['store_no'] = substr($goods[$key]['store_address'], $index);

            // 处理货号
            $goods[$key]['goods_sku'] = rtrim($goods[$key]['goods_sku'],'#');

            // 记录订单退款中的状态
            if (in_array($value['order_id'], $refund_order_ids)){
                // $goods[$key]['shortage'] = 1;
                $goods[$key]['remark'] = $lang['goods_refund_remark'];
            }

        }

        // 获取商品规格
        $goods_spec = $this->_goods_spec_mod ->find(array(

            'conditions' => db_create_in($goods_spec_id,'spec_id'),
            'fields' => "spec_id,spec_1,spec_2"
        ));
        
        // 获取缺货的备注
        $temp_wrong_remark = $this->_goods_warn->find(array(
                'conditions'=>db_create_in($wrong_remark_id,'goods_id'),
                'fields'=>'goods_id,remark',
                'order'=>"add_time ASC"
        ));

        // 过滤或重新组装一下缺货备注
        $wrong_remark = array();
        if ($temp_wrong_remark){
            foreach ($temp_wrong_remark as $key => $value) {
                $wrong_remark[$value['goods_id']] = $value['remark'];
            }
        }
        
        // 取拿货员名字
        $temp_taker_id = array_values($temp_taker_id);
        
        $member_mod = &m('member');
        $user_name = $member_mod->find(array(
            'conditions'=> db_create_in($temp_taker_id , 'user_id'),
            'fields' => 'user_name,real_name',
        ));

        //加入拿货员名以及 信息错和已下架的备注   // 加入颜色和尺码
        foreach ($goods as $key => $value) {

            $goods[$key]['color'] = $goods_spec[$value['goods_spec_id']]['spec_1'];
            $goods[$key]['size'] = $goods_spec[$value['goods_spec_id']]['spec_2'];

            $goods[$key]['taker_name'] = $user_name[$value['taker_id']]['user_name'];
            $goods[$key]['real_name'] = $user_name[$value['taker_id']]['real_name'];

            if(!empty($wrong_remark[$value['id']])){

                $goods[$key]['remark'] = $wrong_remark[$value['id']];    // 添加缺货的备注
            }
            
        }

        echo ecm_json_encode(array('draw'=>intval($_GET['draw']),'recordsTotal'=>intval($recordsTotal),'recordsFiltered'=>intval($recordsFiltered),'data'=>array_values($goods))); 
    }


    /**
     * @name  拿货标签统计
     * @author zjh 2017-07-13
     */

    function tags_stat()
    {
        $bh_id =  $this->visitor->get('has_behalf');

        //建立缺货相关的状态数组(明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货)
        $shortage_array = array(BEHALF_GOODS_TOMORROW,BEHALF_GOODS_AFTERNOON,BEHALF_GOODS_UNSURE,BEHALF_GOODS_STOP_TAKING,BEHALF_GOODS_UNFORMED,BEHALF_GOODS_UNSALE,BEHALF_GOODS_SKU_UNSALE,BEHALF_GOODS_ERROR,BEHALF_GOODS_PRICE_ERROR,BEHALF_GOODS_ERROR2,BEHALF_GOODS_CANCEL,BEHALF_GOODS_ADJUST);
        
        // 重置标签
        $this->_reset_tags();

        //获取代发未处理退款申请的订单id（有退款申请的订单先处理后拿货）
        $refund_order_ids = $this->get_refunds_orders();
        $conditions_refund = "";
        if(!empty($refund_order_ids))
        {
           $conditions_refund = " AND order_alias.order_id NOT ".db_create_in($refund_order_ids);    
        }

        // 获取代发所拥有的市场
        $bh_markets = $this->_behalf_mod->getRelatedData('has_market',$bh_id);  

        // 列出市场id
        $markets_array = array();
        foreach ($bh_markets as $key => $value) {
            $markets_array[] = $value['mk_id'];
        }

        $goods = $this->_goods_warehouse_mod->find(array(
                    'conditions'=>"gwh.bh_id = {$bh_id} AND ".db_create_in($markets_array,'market_id')." AND ".db_create_in(array(BEHALF_GOODS_IMPERFECT,BEHALF_GOODS_PREPARED),'gwh.goods_status')." AND order_alias.status= ".ORDER_ACCEPTED.$conditions_refund,
                    'fields'=>'gwh.id,order_alias.pay_time,order_alias.status,gwh.update_time',
                    'count'=>true,
                    'join'=>'belongs_to_order,belongs_to_orderthird',
        ));

        $unassign_num = $this->_goods_warehouse_mod->getCount();   // 已经下单，并且没分配的商品数量

        if(!$unassign_num){
            $unassign_num = 0;
        }

        // 截止当天16:00的没分配商品数量
        $time = date('Y-m-d H:i:s',time());
        $tmp_time = date('Y-m-d',time());
        $time_16 = strtotime($tmp_time.' 16:00:00');
        $unassign_num_before_16 = 0;
        foreach ($goods as $key => $value) {
            // 同时剔除换款时间大于16点的
            if($value['pay_time']+date("Z") <= $time_16 && (empty($value['update_time']) || $value['update_time'] <= $time_16)){
                $unassign_num_before_16++;
            }
        }

        // 只计算当天已分配的数量
        // $timestamp = strtotime("2017-7-15");
        $timestamp = time();
        $one_day = strtotime(date('Y-m-d',$timestamp));
        $one_second_before_next_day = strtotime(date('Y-m-d',$timestamp)) + 86399;

        $batch = $this->_goods_taker_batch_mod->find(array(
                'conditions'=>"bh_id = {$bh_id} AND assign_time between $one_day and $one_second_before_next_day",
                'count'=>true,
        ));

        // 截止16:00前分配的数量
        $tmp_goods_id =array();   // 记录当天所有已分派的商品
        $assign_num_before_16 = 0;
        $assign_num = 0;     //不截止16点
        foreach ($batch as $key => $value) {
            
            if($value['assign_time'] <= $time_16){

                $assign_num_before_16 += $value['goods_count'];
            }

            $assign_num += $value['goods_count'];

            $tmp = explode(',', $value['content']);
            $tmp_goods_id = array_merge($tmp_goods_id,$tmp);

        }

        // 统计新分配接口的数据
        // $goods_2 = $this->_goods_warehouse_mod->find(array(
        //             'conditions'=>"bh_id = {$bh_id} AND goods_status != ".BEHALF_GOODS_PREPARED." AND ".db_create_in($tmp_goods_id,'id'),
        //             'count'=>true,
        //             'fields'=>'goods_status',
        // ));

        //统计所有分配接口的数据  
        // zjh 去掉 $conditions_refund
        $goods_2 = $this->_goods_warehouse_mod->find(array(
                'conditions'=>"gwh.bh_id = {$bh_id} AND ( gwh.taker_time between $one_day and $one_second_before_next_day ) AND gwh.goods_status != ".BEHALF_GOODS_PREPARED." AND ".db_create_in($markets_array,'gwh.market_id'),
                'fields'=>'gwh.goods_status,order_alias.status',
                'count'=>true,
                'join'=>'belongs_to_order,belongs_to_orderthird',
        )); 

        // $assign_num = $this->_goods_warehouse_mod->getCount();   // 获取已经分配的数量（扣除状态被重置的标签）

        // 统计拿货中、缺货、已入仓的商品数量
        $taking = 0;  // 记录拿货中
        $shortage = 0;  // 记录缺货
        $warehouse = 0;  //记录入仓

        foreach ($goods_2 as $key => $value) {
            
            if($value['goods_status'] == BEHALF_GOODS_DELIVERIES && $value['status'] != ORDER_CANCELED){  // 拿货中的数量

                $taking++;

            }else if($value['goods_status'] == BEHALF_GOODS_READY || $value['goods_status'] == BEHALF_GOODS_SEND || $value['goods_status'] == BEHALF_GOODS_REBACK|| $value['goods_status'] == BEHALF_GOODS_BACKING || $value['goods_status'] == BEHALF_GOODS_REBACK_FAIL){  //已入仓

                $warehouse++;

            //明天有，下午有，不确定，停止拿货，未生产，整件下架，sku下架，档口信息有误，商品价格错，自定义缺货
            }else if(in_array($value['goods_status'], $shortage_array) || $value['status'] == ORDER_CANCELED){

                $shortage++;

            }
        }

        $this->assign('time',$time );     //当前时间

        $this->assign('unassign_num',$unassign_num);   // 实时待拿货数量
        $this->assign('taking',$taking);   // 拿货中数量
        $this->assign('warehouse',$warehouse);   // 入仓数量
        $this->assign('shortage',$shortage);   // 缺货数量

        $this->assign('unassign_num_before_16',$unassign_num_before_16);   // 截止16:00实时待拿货数量
        $this->assign('assign_num_before_16',$assign_num_before_16);   // 截止16:00实时已分配数量
        $this->assign('assign_num',$assign_num);   // 不截止16:00实时已分配数量

        $should_assign_num_before_16 = $unassign_num_before_16 + $assign_num_before_16;
        $this->assign('should_assign_num_before_16',$should_assign_num_before_16);    // 应分配数量 = 截至16点待拿货数量 + 截至16点已分配数量

        // $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('order_manage');
        $this->display('behalf.goods.tags_stat.html');
    }
    
    /**
     *  处理需要寄回的商品
     *  @param int order_id
     *  @author tanaq
     */
    public function handle_postback(){
        $this->_behalf_client->handle_postback();
    }


    /**
     * @name  角色管理
     * @author zjh 2017-08-02
     */
    function role_manage()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	// 获取代发下所有角色
     	$roles = $this->_get_all_role();

     	$parent_ids = array();
     	foreach ($roles as $key => $value) {
     		if($value['parent_id'] != 0){
     			$parent_ids[] = $value['parent_id'];
     		}
     	}

     	// 获取所有直属上级
     	$parent_roles = $this->_get_part_role($parent_ids);

     	// 获取当前代发信息
    	$bh_info = $this->_get_behalf_info();

    	// 获取直属上级的名称
     	foreach ($roles as $key => $value) {

     		if($value['parent_id'] == 0){   // 为0时，直属上级为代发
     			$roles[$key]['parent_name'] = $bh_info['bh_name'];
     		}else{
     			$roles[$key]['parent_name'] = $parent_roles[$value['parent_id']]['role_name'];
     		}
     		
     	}

     	$this->assign('roles',$roles);
    	// $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('setting');
        $this->display('behalf.role_manage.html');
    }

    /**
     * @name  处理角色的添加、编辑和移除
     * @author zjh 2017-08-03
     */
    function deal_role()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	// 特殊职能 (拿货，退货，质检，打包，打单 等)
    	$spec_function = $this->_employee_function();

    	$current_bind_func = 0;

    	// 获取代发信息
    	$bh_info = $this->_get_behalf_info();

    	if($_POST['operate'] == 'add'){

    		$this->assign('operate','add');  // 输出操作方式
    		$bh_roles_list = $this->_role_mod->get_list($bh_id);

    	}else if($_POST['operate'] == 'edit'){

     		$role = $this->_get_role($_POST['role_id']); 
     		if($role['parent_id'] != 0){
     			$parent_role = $this->_get_role($role['parent_id']);
     			$role['parent_name'] = $parent_role['role_name'];
     		}else{
     			$role['parent_name'] = $bh_info['bh_name'];
     		}
     	
     		$bh_roles_list = $this->_role_mod->get_list($bh_id);

     		$current_bind_func = $role['function'];    // 获取当前角色绑定的职能

     		$this->assign('operate','edit');  // 输出操作方式
     		$this->assign('role',$role);  // 输出role信息

    	}else if($_POST['operate'] == 'remove'){

    		// 删除角色
    		$remove_role = $this->_remove_role($_POST['role_id']);

    		if($remove_role)
			{
				$this->json_result(1,'删除成功');
			}
			else 
			{
				$this->json_error('删除失败');
			}

        	exit;
    	}
    	
    	// if(!$role){
    	// 	$role['parent_id'] = 0;
    	// }
    	// 获取所有角色构造的 html select option
    	$role_select = $this->_assembly_role_select($role['parent_id']);
    	// 获取拥有特殊职能的角色
    	$function_roles = $this->_get_function_role();

    	// 提取function字段值
    	$role_func = array();
    	foreach ($function_roles as $key => $value) {
    		$role_func[]  = $value['function'];
    	}

    	//获取还有哪些职能没有被角色绑定 加上当前角色绑定的职能
    	$func_unbind = array();
    	foreach ($spec_function as $key => $value) {
    		if(!in_array($value, $role_func) || $value == $current_bind_func){
    			$func_unbind[$key] = $value;
    		}
    	}

    	
    	// $priv_info = $this->_get_menu_for_priv();

    	// 获取角色所拥有的权限（A类）
    	$role_priv = $this->_get_role_priv($_POST['role_id']);
    	// 获取权限筛选信息
    	$priv_info = $this->_mark_role_in_priv($role_priv);

    	// 获取功能点权限（B类）
    	$menu_sub_priv = array();
    	if($role){
    		$sub_priv_array = $this->_menu_sub_priv->_set_menu_sub_priv();
    	
	    	foreach ($sub_priv_array as $key => $value) {
				if(is_array($value) && !empty($value)){
					$menu_sub_priv[$key] = $value;    // 清除空数组
				}
			}
    	}
    	

     	$this->assign('func_unbind',$func_unbind);   // 没有被绑定的职能 加上当前角色绑定的职能	
     	$this->assign('current_bind_func',$current_bind_func);  // 当前被绑定的职能
     	$this->assign('behalf_name',$bh_info['bh_name']);   // 代发名称	
     	$this->assign('role_select',$role_select);     // 角色组成的option
     	$this->assign('priv_info',$priv_info);     // 权限筛选信息
     	$this->assign('menu_sub_priv',$menu_sub_priv);     // 次级权限
    	// $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('setting');
        $this->display('behalf.deal_role.html');
    }


    /**
     * @name  执行处理操作(增加或编辑)
     * @author zjh 2017-08-03
     */
    function do_deal_role()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	if($_POST['operate'] == 'add'){

    		$existing_role = $this->_role_name_existing($_POST['role_name'],$_POST['role_parent']);

    		if($existing_role){
    			echo json_encode(array('code' =>-1 ,'msg' => '已经存在该角色名称!'));
    			exit;
    		}

    		// 转化数据以适应添加选项
    		$data = $_POST;
    		$data['parent_id'] = $_POST['role_parent'];
    		$data['priv'] = implode(',', $_POST['priv']);


    		$add_role = $this->_add_role($_POST['role_id'],$data);

    		if($add_role){
        		echo json_encode(array('code' =>0 ,'msg' => '操作成功!'));
        	}else {
        		echo json_encode(array('code' =>-1 ,'msg' => '操作失败!'));
        	}

    	}else if($_POST['operate'] == 'edit'){

    		if($_POST['role_name'] != $_POST['origin_role_name']){  // 不是原来的名称，则需要判断新名称是否已经存在

    			$existing_role = $this->_role_name_existing($_POST['role_name'],$_POST['role_parent']);

	    		if($existing_role){
	    			echo json_encode(array('code' =>-1 ,'msg' => '已经存在该角色名称!'));
	    			exit;
	    		}
    		}

    		// 转化数据以适应编辑选项
    		$data = $_POST;
    		$data['parent_id'] = $_POST['role_parent'];
    		$data['priv'] = implode(',', $_POST['priv']);

    		
    		$edit_role = $this->_edit_role($_POST['role_id'],$data);

    		if($edit_role){
				echo json_encode(array('code' =>0 ,'msg' => '操作成功!'));
			}else {
				echo json_encode(array('code' =>-1 ,'msg' => '操作失败!'));
			}  
    	}
    }

     /**
     * @name  获取次级权限(B类权限)
     * @author zjh 2017-08-06
     */
    function get_this_menu_sub_priv()
    {
    	// 获取菜单下的功能点权限（B类）
    	$menu_sub_priv = array();

		$menu_sub_priv_array = $this->_menu_sub_priv->_set_menu_sub_priv();
	
    	foreach ($menu_sub_priv_array as $key => $value) {
			if(is_array($value) && !empty($value) && $key == $_GET['menu_name']){
				$menu_sub_priv = $value;  
			}
		}
    	
    	// 获取当前用户所有的次级权限
    	$user_sub_priv_array = $this->_get_role_sub_priv($_GET['role_id']);

    	$user_sub_priv = array();    // 该菜单下的权限
    	foreach ($user_sub_priv_array as $key => $value) {
    		if($key == $_GET['menu_name']){
    			$user_sub_priv = $value;
    		}
    	}

    	echo json_encode(array('code' =>0 ,'msg' => '操作成功','menu_priv'=>$menu_sub_priv,'user_priv'=>$user_sub_priv));
    }

    /**
     * @name  编辑次级权限(B类权限)
     * @author zjh 2017-08-06
     */
    function edit_this_menu_sub_priv()
    {

    	// 获取当前编辑的角色所有的次级权限
    	$user_sub_priv_array = $this->_get_role_sub_priv($_POST['role_id']);

    	if(empty($_POST['sub_priv'])){

    		unset($user_sub_priv_array[$_POST['menu_name']]);  // 如果为空，则清除该数组项

    	}else{
    		$user_sub_priv_array[$_POST['menu_name']] = $_POST['sub_priv'];    // 替换
    	}
	    
	
    	$str_sub_priv = serialize($user_sub_priv_array);
    	$data['sub_priv'] = $str_sub_priv;

    	$edit_role = $this->_edit_role_sub_priv($_POST['role_id'],$data);

    	if($edit_role){
    		echo json_encode(array('code' =>0 ,'msg' => '操作成功'));
    	}else{
    		echo json_encode(array('code' =>-1 ,'msg' => '操作失败'));
    	}
    }


    /**
     * @name  检查角色名称是否已经存在（同一级别里判断）
     * @author zjh 2017-08-05
     * @param $role_name  角色名称, $parent_id 所属上级的id
     */
    function _role_name_existing($role_name,$parent_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$roles = $this->_role_mod->find(array(
 			'conditions'=>"r_p.bh_id = behalf.bh_id AND behalf.bh_id = {$bh_id} AND r_p.parent_id = {$parent_id} AND r_p.role_name = '{$role_name}' ",
 			'fields'=>'r_p.*,behalf.*',
 			'join'=>'belongs_to_behalf'
 		)); 

 		return $roles;
    }


    /**
     * @name  获取代发信息
     * @author zjh 2017-08-03
     */
    function _get_behalf_info()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$bh_info = $this->_behalf_mod->get(array(

    		'conditions'=>"bh_id = {$bh_id} AND bh_allowed = 1"
    	));

    	return $bh_info;
    }

    /**
     * @name  获取所有拥有职能的角色（字段 function 不为0）
     * @author zjh 2017-08-04
     */

    function _get_function_role()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$roles = $this->_role_mod->find(array(
 			'conditions'=>"r_p.bh_id = behalf.bh_id AND behalf.bh_id = {$bh_id} AND r_p.function != 0",
 			'fields'=>'r_p.*,behalf.*',
 			'join'=>'belongs_to_behalf'
 		)); 

 		return $roles;
    }

    /**
     * @name  获取角色
     * @author zjh 2017-08-03
     * @param $role_id  角色id
     */
    function _get_role($role_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$role = $this->_role_mod->get(array(
 			'conditions'=>"r_p.bh_id = behalf.bh_id AND behalf.bh_id = {$bh_id} AND r_p.role_id = ".$role_id,
 			'fields'=>'r_p.*,behalf.*',
 			'join'=>'belongs_to_behalf'
 		)); 

 		return $role;
    }

    /**
     * @name  获取部分角色
     * @author zjh 2017-08-05
     * @param $role_id  角色id数组
     */
    function _get_part_role($role_id_array)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$part_roles = $this->_role_mod->find(array(
 			'conditions'=>"r_p.bh_id = behalf.bh_id AND behalf.bh_id = {$bh_id} AND ".db_create_in($role_id_array,'r_p.role_id'),
 			'fields'=>'r_p.*,behalf.*',
 			'join'=>'belongs_to_behalf'
 		)); 

 		return $part_roles;
    }

    /**
     * @name  获取当前代发下所有角色
     * @author zjh 2017-08-03
     */
    function _get_all_role()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$roles = $this->_role_mod->find(array(
 			'conditions'=>"r_p.bh_id = behalf.bh_id AND behalf.bh_id = {$bh_id}",
 			'fields'=>'r_p.*,behalf.*',
 			'join'=>'belongs_to_behalf'
 		)); 

 		return $roles;
    }

    /**
     * @name  添加角色
     * @author zjh 2017-08-03
     * @param $role_id  角色id
     * @param $data , 需要的数据数组，如$_POST、$_GET 等的数据
     */
    function _add_role($role_id,$data)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	// 先获取，用于判断是否已经存在
    	$role = $this->_get_role($data['role_id']); 

 		if($role){  // 角色存在时，转换为编辑模式

 			$edit_role = $this->_edit_role($role_id,$data);

 			return $edit_role;

 		}else{  // 添加

 			$edit_data = array(

				'role_name' => $data['role_name'],
				'describtion' => $data['describtion'],
				'parent_id' => $data['parent_id'],
	            'priv' => $data['priv'],
	            'bh_id'=>$bh_id,
	            'function'=>$data['function']
			);
			
     		// 插入数据
    		$add_role = $this->_role_mod ->add($edit_data);

    		return $add_role;
        	
	 	}

    }

    /**
     * @name  编辑角色
     * @author zjh 2017-08-03
     * @param $role_id  角色id
     * @param $data , 需要的数据数组，如$_POST、$_GET 等的数据
     */
    function _edit_role($role_id,$data)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

		$edit_data = array(

			'role_name' => $data['role_name'],
			'describtion' => $data['describtion'],
			'parent_id' => $data['parent_id'],
	        'priv' => $data['priv'],
	        'bh_id'=>$bh_id,
	        'function'=>$data['function']
		);
		
		$conditions = 'role_id = '.$role_id;

		// 编辑数据
		$edit_role = $this->_role_mod ->edit($conditions,$edit_data);

		return $edit_role;     	 	

    }

    /**
     * @name  编辑角色的次级权限
     * @author zjh 2017-08-06
     * @param $role_id  角色id
     * @param $data , 需要的数据数组，如$_POST、$_GET 等的数据
     */
    function _edit_role_sub_priv($role_id,$data)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

		$edit_data = array(

	        'sub_priv' => $data['sub_priv'],
		);
		
		$conditions = 'role_id = '.$role_id;

		// 编辑数据
		$edit_role = $this->_role_mod ->edit($conditions,$edit_data);

		return $edit_role;     	 	

    }

    /**
     * @name  删除角色 同时删除包含的子角色和删除角色下绑定的账号
     * @author zjh 2017-08-03
     * @param $role_id  角色id
     */
    function _remove_role($role_id)
    {	
    	$bh_id =  $this->visitor->get('has_behalf');
		
    	// 将所有角色先取出来
    	$roles = $this->_get_all_role();
    	// 获取角色下的子孙角色
    	$this->_traverse_role($roles,$role_id,$get_children);

    	// 获取所有role_id
    	$role_array = array();
    	$role_array[] = $role_id;   // 先将父角色填入数组
    	foreach ($get_children as $key => $value) {
    		$role_array[] = $value['role_id'];
    	}

    	$conditions = db_create_in($role_array,'role_id');

		// 删除角色
    	$remove_roles = $this->_role_mod -> drop($conditions);

    	// 删除角色下绑定的员工账号
    	$remove_role_employee = $this->_remove_role_employee($role_array);

    	return $remove_roles;
    }

    /**
     * @name  遍历某角色下的所有子孙角色
     * @author zjh 2017-08-04
     * @param $role_id  角色id
     */
    function _traverse_role($roles,$role_id,&$get_children=array())
    {
    	foreach ($roles as $key => $value) {
    		if($value['parent_id'] == $role_id){

    			$get_children[$key] = $value;
    			$this->_traverse_role($roles,$value['role_id'],$get_children);   				
    		}
    	}
    }

     /**
     * @name  获取角色的层级
     * @author zjh 2017-08-04
     * @param $role_id  角色id
     */
    function _get_role_level($role_id)
    {
    	// 将所有角色先取出来
    	$roles = $this->_get_all_role();

    	// 递归遍历
    	$level = $this->_get_role_level_recursive($roles,$role_id);

    	return $level;
    }

     /**
     * @name  获取所有角色与层级的关联数组
     * @author zjh 2017-08-04
     */
    function _get_role_level_array()
    {	
    	// 将所有角色先取出来
    	$roles = $this->_get_all_role();

    	$role_level = array();    // 角色与层级的关联数组
    	foreach ($roles as $key => $value) {

    		$level = $this->_get_role_level_recursive($roles,$value['role_id']);
    		$role_level[$value['role_id']] = $level;
    		
    	}
    
    	return $role_level;
    }

    /**
     * @name  递归查询角色层级
     * @author zjh 2017-08-04
     * @param $roles  所有角色
     * @param $role_id  角色id
     * @param $level  层级,默认从0开始
     * @return 某角色的层级
     */
    function _get_role_level_recursive($roles,$role_id,$level=0)
    {
    	$count = $level;

    	if($role_id == 0){  // 找到最顶层了
    		
    		return $count;

    	}else{

    		$count++;
    	}

    	foreach ($roles as $key => $value) {
    		if($value['role_id'] == $role_id){

    			return $this->_get_role_level_recursive($roles,$value['parent_id'],$count);   				
    		}
    	}

    	return 0;   // 传入的role_id 不存在于$roles ,则返回0
    }


    /**
     * @name  组装角色的 html select option
     * @author zjh 2017-08-04
     * @param $selected 被选中的role_id
     */
    function _assembly_role_select($selected=0)
    {
    	// 将所有角色先取出来
    	$roles = $this->_get_all_role();

    	// 获取角色与层级的关联数组
    	$role_level = $this->_get_role_level_array();

    	// 重构角色的排列顺序
    	$refactor_roles =array();
    	$this->_get_refactor_roles($roles,$refactor_roles);
    

    	$select = '';

    	 foreach ($refactor_roles AS $var)
        {
            $select .= '<option value="' . $var['role_id'] . '" ';
            $select .= ($selected == $var['role_id']) ? "selected='ture'" : '';
            $select .= '>';
            if ($role_level[$var['role_id']] > 1)
            {
                $select .= str_repeat('&nbsp;', ($role_level[$var['role_id']]-1) * 4);
            }
            $select .= htmlspecialchars(addslashes($var['role_name'])) . '</option>';
        }

        return $select;
    }

    /**
     * @name  重构角色的排列顺序，按二叉树的前序遍历方式
     * @author zjh 2017-08-04
     * @param $roles 角色组
     * @param $refactor_roles 得到的重构数组
     * @param $parent_id 角色父id
     */
    function _get_refactor_roles($roles,&$refactor_roles=array(),$parent_id=0)
    {
    	
    	foreach ($roles as $key => $value) {
    		if($value['parent_id'] == $parent_id){

    			$refactor_roles[$key]=$value;
    			// print_r($refactor_roles);
    			$this->_get_refactor_roles($roles,$refactor_roles,$value['role_id']);  
		
    		}
    	}

    }

    /**
     * @name  员工账号管理
     * @author zjh 2017-08-04
     */
    function employee_account()
    {	
    	// 获取代发下所有员工账号
    	$members = $this->_get_behalf_employees();

    	if(IS_POST){
    		$this->_search_members();	
    		
    	}

    	// 获取所有角色构造的 html select option
		$role_select = $this->_assembly_role_select();

		$this->assign('role_select',$role_select);
    	
    	$this->assign('members',$members);
    	// $this->_import_css_js('dt');
        $this->_import_css_js('dtall');
        $this->_assign_leftmenu('setting');
        $this->display('behalf.employee.manage.html');
    }

     /**
     * @name  操作员工账号
     * @author zjh 2017-08-04
     */
	function deal_employee()
	{
		$bh_id =  $this->visitor->get('has_behalf');

		if($_GET['operate'] == 'add'){

			$add_employee = $this->_bind_role_employee($_GET['employee_id'],$_GET['role_id']);

			if($add_employee == -1)
			{
				$this->json_error('添加失败,角色不存在');
			}
			else if($add_employee == -2)
			{
				$this->json_error('添加失败,本代发或者别的代发已经绑定过了这个账号');
			}
			else if($add_employee)
			{
				$this->json_result(1,'添加成功');
			}
			else 
			{
				$this->json_error('添加失败');
			}

		}else if($_GET['operate'] == 'remove'){

			$remove_employee = $this->_unbind_role_employee($_GET['employee_id'],$_GET['role_id']);

			if($remove_employee)
			{
				$this->json_result(1,'删除成功');
			}
			else 
			{
				$this->json_error('删除失败');
			}

		}else if($_GET['operate'] == 'edit'){

			$edit_employee = $this->_edit_employee_info($_GET);

			if($edit_employee)
			{
				$this->json_result(1,'编辑成功');
			}
			else 
			{
				$this->json_error('编辑失败');
			}

		}

	}

	/**
     * @name  编辑员工的信息
     * @author zjh 2017-08-04
     */
    function _edit_employee_info($data)
    {
        if($data['employee_id']){

            $bh_id = $this->visitor->get('has_behalf');

            $member_mod = &m('member');

            $edit_data = array(

                'real_name' => $_GET['real_name'] ,
                'phone_mob' => $_GET['tel'],
            );
        
            $conditions = 'user_id = '.$data['employee_id'];

            $edit_employee = $member_mod->edit($conditions, $edit_data);

            // 改变角色与员工的绑定
            $change_bind = $this->_change_bind_role_employee($data['employee_id'],$data['role_id']);

            return $edit_employee && $change_bind;

        }
    }

     /**
     * 搜索会员账号
     */
    function _search_members()
    {
    	$bh_id =  $this->visitor->get('has_behalf');

		$user_name = isset($_POST['user_name']) && $_POST['user_name']?trim($_POST['user_name']):'';
		if(empty($user_name))
		{
			$this->json_error('user name empty!');
			return ;
		}
		$infos = Lang::get('unvalid_user_name');
		$member_info = ms()->user->_local_get(array('conditions'=>"user_name='{$user_name}'"));

		$all_behalf = $this->_behalf_mod->find(array(
			'conditions'=>"bh_allowed = 1",
		));

		// 获取所有代发id
		$behalf_ids = array();
		foreach ($all_behalf as $key => $value) {
			$behalf_ids[] = $value['bh_id'];
		}

		if(in_array($member_info['user_id'], $behalf_ids)) // 不能指定代发为员工账号,代发拥有所有的权限
		{
			$infos = Lang::get('behalf_not_allow_to_employee');
			$member_info = array();
		}

		if($member_info['user_id'] == $this->visitor->get('user_id')) // 不能指定自己为员工账号
		{
			$infos = Lang::get('self_not_allow_to_employee');
			$member_info = array();
		}	
		
		$this->assign('show_member',true);
		$this->assign('info_type',empty($member_info)?'warning':'info');
		$this->assign('infos',$infos);
		$this->assign('member_info',$member_info);
    	
    }

    /**
     * @name  获取员工账号
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     */
    function _get_employee($employee_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$employee = $this->_employee_role_mod->get(array(
     		'conditions'=>'er_r.employee_id = member.user_id AND er_r.employee_id = '.$employee_id,
     		'fields'=>'er_r.*,member.*',
     		'join'=>'belongs_to_member'
     	));

 		return $employee;
    }

    /**
     * @name  获取员工账号与角色的绑定信息
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     */
    function _get_role_employee($employee_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$employee = $this->_employee_role_mod->get(array(
     		'conditions'=>"er_r.role_id = r_p.role_id AND r_p.bh_id = {$bh_id} AND er_r.employee_id = ".$employee_id,
     		'fields'=>'er_r.*,r_p.*',
     		'join'=>'belongs_to_role'
     	));

 		return $employee;
    }

    /**
     * @name  获取员工账号与角色的绑定信息,用于判断是否已经存在绑定了，一个账号只能绑定一个代发
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     */
    function _existing_role_employee($employee_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$employee = $this->_employee_role_mod->find(array(
     		'conditions'=>"er_r.role_id = r_p.role_id AND er_r.employee_id = ".$employee_id,
     		'fields'=>'er_r.*,r_p.*',
     		'join'=>'belongs_to_role'
     	));

 		return $employee;
    }

    /**
     * @name  转换绑定
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     */
    function _change_bind_role_employee($employee_id,$role_id)
    {
    	//先获取原role_id
    	$employee = $this->_get_role_employee($employee_id);

    	if($employee['role_id'] == $role_id){  // 一致，则不作处理
    		return $employee;
    	}

    	//解除旧的绑定
    	$remove_employee =$this->_unbind_role_employee($employee_id,$employee['role_id']);

    	// 建立新的绑定
    	$add_employee = $this->_bind_role_employee($employee_id,$role_id);

    	return $employee && $remove_employee && $add_employee;
    }

     /**
     * @name  获取一个员工账号的所有信息
     * @author zjh 2017-08-04
     * @param $employee_id  员工id
     */
    function _get_employee_detail($employee_id)
    {

    	$bh_id =  $this->visitor->get('has_behalf');

    	$employee_detail = $this->_employee_role_mod->get(array(
     		'conditions'=>"er_r.employee_id = member.user_id AND er_r.role_id = r_p.role_id AND r_p.bh_id = {$bh_id} AND er_r.employee_id = {$employee_id}",
     		'fields'=>'er_r.*,member.*,r_p.*',
     		'join'=>'belongs_to_member,belongs_to_role',
     		'order'=>'er_r.role_id ASC'
     	));

 		return $employee_detail;
    }


    /**
     * @name  获取代发下所有的员工账号
     * @author zjh 2017-08-04
     * @param $employee_id  员工id
     */
    function _get_behalf_employees()
    {

    	$bh_id =  $this->visitor->get('has_behalf');

    	$employees = $this->_employee_role_mod->find(array(
     		'conditions'=>"er_r.employee_id = member.user_id AND er_r.role_id = r_p.role_id AND r_p.bh_id = {$bh_id}",
     		'fields'=>'er_r.*,member.*,r_p.*',
     		'join'=>'belongs_to_member,belongs_to_role',
     		'order'=>'er_r.role_id ASC'
     	));

 		return $employees;
    }

    /**
     * @name  将员工账号与角色绑定
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     * @param $role_id  角色id
     */
    function _bind_role_employee($employee_id,$role_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	// 先判断是否存在角色
    	$role = $this->_get_role($role_id);

    	if(!$role){ // 角色不存在，返回-1，绑定失败
    		return -1;
    	}

    	// 先获取，用于判断是否已经存在绑定
    	$employee = $this->_existing_role_employee($employee_id);

 		if($employee){  // 绑定已存在，返回-2，绑定失败

 			return -2;

 		}else{  // 添加

 			$edit_data = array(

				'employee_id' => $employee_id,
				'role_id' => $role_id
			);
			
     		// 插入数据
    		$add_employee = $this->_employee_role_mod ->add($edit_data);

    		return $add_employee;
        	
	 	}

    }

    /**
     * @name  解除员工账号与角色的绑定
     * @author zjh 2017-08-03
     * @param $employee_id  员工id
     * @param $role_id  角色id
     */
    function _unbind_role_employee($employee_id,$role_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');
		
		$conditions = 'employee_id = '.$employee_id.' AND role_id = '.$role_id;

		// 删除数据
    	$remove_employee = $this->_employee_role_mod -> drop($conditions);
 	 	
    	return $remove_employee;

    }

    /**
     * @name  删除某角色下所有的账号
     * @author zjh 2017-08-04
     * @param $role_id  角色id
     */
    function _remove_role_employee($role_id_array)
    {
    	$bh_id =  $this->visitor->get('has_behalf');
		
		$conditions = db_create_in($role_id_array,'role_id');

		// 删除数据
    	$remove_employee = $this->_employee_role_mod -> drop($conditions);
 	 	
    	return $remove_employee;

    }

	 /**
	 * @name  获取菜单信息用于权限处理(A类)
	 * @description A类权限，针对于菜单 | B类权限，针对于菜单下的功能点
	 * @author zjh 2017-08-03
	 * @return 菜单通过一定格式化处理的信息
	 */
    function _get_menu_for_priv()
    {
    	
    	$menu_info = $this-> _get_leftmenu();

    	$priv_info = array();
    	foreach ($menu_info as $key => $menus) {
    		foreach ($menus as $k => $menu) {
    			if(isset($menu['submenu']) && is_array($menu['submenu']) && isset($menu['text'])){
    				foreach ($menu as $k1 => $submenu) {
    					if(is_array($submenu) && $k1 == 'submenu'){
    						foreach ($submenu as $k2 => $item) {
    							$priv_info[$menu['text']][$item['text']]['url'] = $item['url'];
    							$priv_info[$menu['text']][$item['text']]['name'] = $item['name'];
    						}
    						
    					}
    						
    				}
    				
    			}
    		}
    	}

    	return $priv_info;	
    }

    /**
	 * @name  根据url的act对应的权限查找顶层菜单
	 * @author zjh 2017-08-05
	 * @param $name  权限标识符，也是菜单的name
	 * @return 顶层菜单的标识符
	 */
    function _get_top_menu_val($name)
    {
    	
    	$menu_info = $this-> _get_leftmenu();

    	$top_menu = 'dashboard';  //默认
    	foreach ($menu_info as $key => $menus) {
    		foreach ($menus as $k => $menu) {
    			if(isset($menu['submenu']) && is_array($menu['submenu']) && isset($menu['text'])){
    				foreach ($menu as $k1 => $submenu) {
    					if(is_array($submenu) && $k1 == 'submenu'){
    						foreach ($submenu as $k2 => $item) {

    							if($item['name'] == $name){
    								$top_menu = $key;
    								return $top_menu;
    							}

    						}
    						
    					}
    						
    				}
    				
    			}
    		}
    	}

    	return $top_menu;	
    }

    /**
	 * @name  菜单name和url的act的关联数组
	 * @author zjh 2017-08-03
	 * @return name 和 act 的关联数组
	 */
    function _get_name_act_for_priv()
    {

    	$priv_info = $this->_get_menu_for_priv();

    	$name_act = array();   // 菜单name和url的act的关联数组
    	foreach ($priv_info as $key => $value) {
    		foreach ($value as $k => $v) {
    			$index = strrpos($v['url'],'act=');
  				$menu_url_act = substr($v['url'],$index+4);
    			$name_act[$v['name']] = trim($menu_url_act);
    		}
    	}

    	return $name_act;
    }

    /**
	 * @name  标记角色所拥有的权限，1为有，0为没有
	 * @author zjh 2017-08-05
	 * @param $role_priv_array 角色拥有的权限数组
	 * @return 含有某角色标记的权限菜单组
	 */
    function _mark_role_in_priv($role_priv_array)
    {
    	$priv_info = $this->_get_menu_for_priv();

    	foreach ($priv_info as $key => $value) {
    		foreach ($value as $k => $v) {
    			if(in_array('all', $role_priv_array)){  // all 代表拥有所有权限
    				$priv_info[$key][$k]['mark'] = 1;
    			}else if(in_array($v['name'], $role_priv_array)){  // 角色拥有该权限，标记为1
    				$priv_info[$key][$k]['mark'] = 1;
    			}else{
    				$priv_info[$key][$k]['mark'] = 0;
    			}	   			
    		}
    	}

    	return $priv_info;
    }

    /**
	 * @name  检测当前用户的权限
	 * @author zjh 2017-08-03
	 * @return 
	 */
    function _detect_priv()
    {
    	// 判断当前用户是不是代发
    	$is_behalf = $this-> _user_is_behalf();

    	if($is_behalf){   // 如果当前用户是代发，则不作权限处理
    		return;
    	}

    	// 判断当前用户是否为员工
    	$bh_id =  $this->visitor->get('has_behalf');
    	if(!$bh_id){  // 没有代发id，则代表不是员工，不作处理，由后面进行跳转
    		return;
    	}

    	// zjh 当前用户没有的权限
		$priv_array = $this->_get_user_no_priv();
		$this->assign('priv',$priv_array);

    	//获取当前url所对应的权限
    	$current_url_priv = $this->_get_url_priv();

    	if($current_url_priv == ''){  // 当前的url不是通过点击后台菜单的，则不作处理
    		// 留给后面的功能点做更细的权限处理
    		return;
    	}

    	//获取当前用户所拥有的权限
    	$user_priv = $this->_get_user_priv();

    	// 判断用户是否拥有当前url对应的权限,all代表拥有所有权限
    	if(in_array($current_url_priv, $user_priv) || in_array('all', $user_priv)){
    		// 拥有权限，不作处理，直接返回。
    		return;
    	}else{
    		// 不拥有权限
    		// 处理：限制进入
    		// echo '<script type="text/javascript">alert("抱歉...你没有权限访问该页面，请联系管理员");history.go(-1)</script>';
    		$top_menu = $this->_get_top_menu_val($current_url_priv);  
        	$this->_assign_leftmenu($top_menu);

			$this->assign('navtime',gmtime());
			$this->assign('login_name',$this->visitor->get('user_id') == $this->visitor->get('has_behalf')? Lang::get('behalf_manager'):Lang::get('employee_manager'));
			$this->assign('user_name',$this->visitor->get('user_name'));

        	$this->assign('login_type','admin');
        	$this->display('behalf.no_permission_show.html');
    		exit;
    	}
    }

    /**
	 * @name 判断当前用户是否为代发
	 * @author zjh 2017-08-03
	 * @return true,false
	 */
    function _user_is_behalf()
    {
    	$user_id =  $this->visitor->get('user_id');

    	$bh = $this->_behalf_mod->get(array(
    		'conditions'=>"bh_id = {$user_id} AND bh_allowed = 1",
    	));

    	if($bh){
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
	 * @name  获取当前url所对应的权限
	 * @author zjh 2017-08-03
	 * @return 权限
	 */
    function _get_url_priv()
    {
    	// 获取当前url的act
    	$act= empty($_GET['act']) ? 'index' : trim($_GET['act']);

    	// 找出当前url所对应的权限标识符（即菜单name）
    	$name_act = $this->_get_name_act_for_priv();

    	$current_url_priv = '';
    	foreach ($name_act as $key => $value) {
    		if($value == $act){
    			$current_url_priv = $key;
    		}
    	}

    	return $current_url_priv;
    }

    /**
	 * @name  获取角色所拥有的权限(A类权限)
	 * @author zjh 2017-08-05
	 * @param $role_id  角色id
	 * @return 权限数组
	 */
    function _get_role_priv($role_id)
    {
    	$role = $this->_get_role($role_id);

    	$role_priv = explode(',', $role['priv']);

    	return $role_priv;
    }

    /**
	 * @name  获取当前用户所拥有的权限
	 * @author zjh 2017-08-03
	 * @return 权限
	 */
    function _get_user_priv()
    {
    	$user_id =  $this->visitor->get('user_id');
    	// 获取员工账号与角色的绑定信息
    	$employee_info = $this->_get_role_employee($user_id);
    	// 获取当前用户所拥有的权限标识符列表
    	$user_priv = explode(',', $employee_info['priv']);

    	return $user_priv;
    }

    /**
	 * @name  获取当前编辑的角色的权限(B类权限)
	 * @author zjh 2017-08-06
	 * @param $role_id 角色id
	 * @return 权限
	 */
    function _get_role_sub_priv($role_id)
    {
    	$user_id =  $this->visitor->get('user_id');

    	$role = $this->_get_role($role_id);
    	// 获取当前角色所拥有的权限标识符列表
    	// 解序列化
    	$user_sub_priv = unserialize($role['sub_priv']);

    	return $user_sub_priv;
    }

    /**
	 * @name  获取当前用户没有的权限
	 * @author zjh 2017-08-05
	 * @return 权限
	 */
    function _get_user_no_priv()
    {
    	// 判断当前用户是不是代发
    	$is_behalf = $this-> _user_is_behalf();

    	if($is_behalf){   // 如果当前用户是代发，拥有所有权限
    		return;
    	}

    	// 菜单name和url的act的关联数组
    	$name_act = $this->_get_name_act_for_priv();

    	// 获取键值(所有菜单权限)
    	$name_priv = array_keys($name_act);

    	// 获取当前用户所拥有的权限
    	$user_priv = $this->_get_user_priv();

    	// 获取当前用户没有的权限
    	$user_no_priv = array();
    	foreach ($name_priv as $key => $value) {
    		if(!in_array($value, $user_priv)){  // 不在用户权限里面
    			
    			$user_no_priv[] = $value;
    		}	
    	}

    	return $user_no_priv;
    }


    /**
	 * @name  获取拥有某种特殊职能的员工账号
	 * @author zjh 2017-08-05
	 * @param $spec_func 特殊职能 
	 * (拿货< BEHALF_TAKE_GOODS >，
	 * 	退货< BEHALF_RETURN_GOODS >，
	 * 	质检< BEHALF_QUALITY_INSPECT >，
	 * 	打包< BEHALF_PACKAGING >，
	 * 	打单< BEHALF_PRINT_MAN >
	 * 等)
	 * @return 员工账号组
	 */
    function _get_spec_func_employees($spec_func)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$employees = $this->_employee_role_mod->find(array(
     		'conditions'=>"er_r.employee_id = member.user_id AND er_r.role_id = r_p.role_id AND r_p.bh_id = {$bh_id} AND r_p.function = {$spec_func}",
     		'fields'=>'er_r.*,member.*,r_p.*',
     		'join'=>'belongs_to_member,belongs_to_role',
     		'order'=>'er_r.role_id ASC'
     	));
    	// print_r($employees);exit;
 		return $employees;
    }



	/**
	 * 针对订单的收入退货统计表
	 */
    function finance_list(){
		$this->_get_finance_source();
		$this->_import_css_js('dtall');
		$this->_assign_leftmenu('finance_manage');
		$this->display('behalf.finance.list.html');


	}


	function _get_finance_source(){
		$bh_id = $this->visitor->get('has_behalf');
		$lists = $this->_order_mod->find(array(
			'conditions' => "order_alias.bh_id={$bh_id} AND ".db_create_in(array(ORDER_SHIPPED) , 'order_alias.status')." AND FROM_UNIXTIME(ship_time,'%Y-%m-%d') = CURDATE()  ",
			'join' => 'has_orderextm',

		));

		foreach($lists as &$v){
			$goods_list = $this->_goods_warehouse_mod->find(array(
				'conditions' => " NOT ".db_create_in(array( BEHALF_GOODS_CANCEL),'gwh.goods_status')." AND bh_id={$bh_id} AND gwh.order_id={$v['order_id']}",

			));
			$v['goods_diff_fee'] = 0;
			foreach($goods_list as $goods){
				if($goods['real_price'] > 0){
					$v['goods_diff_fee'] += $goods['price'] - $goods['real_price'];
				}
			}




			$refund_goods = $this->_goods_warehouse_mod->find(array(
				'conditions' => db_create_in(array( BEHALF_GOODS_REBACK ),'gwh.goods_status')." AND bh_id={$bh_id} AND gwh.order_id={$v['order_id']}",
				'join' => 'has_refundgoods',
			));
			foreach($refund_goods as $goods){
				$v['diff_fee'] += $goods['price'] - $goods['th_price'];
				$v['back_fee'] += $goods['back_fee'];
			}
			$v['back_count'] = count( $refund_goods );
			$v['goods_fee'] = array_sum($refund_goods);

		}
		$this->assign('data' , $lists);
	}

	function profit_list(){
		$this->_get_profit_source();
		$this->_import_css_js('dtall');
		$this->_assign_leftmenu('profit_list');
		$this->display('behalf.profit.list.html');

	}

	function _get_profit_source(){

	}
}



?>
