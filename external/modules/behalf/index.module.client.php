<?php
/**
 * 代发客户关系管理
 * @author tanaiquan
 * @ 2015-12-30
 */
class BehalfClientModule extends BehalfBaseModule
{
	function __construct()
	{
		$this->BehalfClientModule();
	}
	
	function BehalfClientModule()
	{
		parent::__construct();	
	}
	
	function _run_action()
	{
		parent::_run_action();
	}
	
	/**
	 *  下过单的会员
	 */
	public function member_list()
	{
		$bh_id = $this->visitor->get('has_behalf');
		
		$user_name = $_GET['uname'] ? trim($_GET['uname']) : '';
		$search_uname = '';
		if ($user_name)
		{
		    $search_uname = " AND m.user_name like '%".$user_name."%' ";
		    $this->assign('uname',$user_name);
		}
		
		$order = in_array($_GET['order'],array('orders','pay_time'))?$_GET['order']:'orders';
		
		$page = $this->_get_page();
		
		$model_member = & m('member');
		$model_membervip = & m('membervip');
		/* $members = $model_member->find(array(
			'conditions'=>db_create_in(array_keys($member_ids),'user_id'),
			'limit'=>$page['limit'],
			'count'=>true	
		)); */
		
		/* $sql = "SELECT m.user_name,m.real_name,m.phone_mob,m.im_qq,m.im_aliww,m.user_id,count(o.buyer_id) as orders FROM "
		    .$this->_order_mod->table ." as o LEFT JOIN ".$model_member->table 
		    ." as m ON o.buyer_id = m.user_id WHERE o.bh_id='{$bh_id}' AND o.status "
		    .db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED))." GROUP BY buyer_id ORDER BY orders DESC LIMIT ".$page['limit']; */
		/* $sql = "SELECT m.user_name,m.real_name,m.phone_mob,m.im_qq,m.im_aliww,m.user_id,o.orders FROM "
		    ."((SELECT *,count(buyer_id) as orders FROM ".$this->_order_mod->table."  WHERE bh_id='{$bh_id}' AND status "
		        .db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED))." GROUP BY buyer_id) as a "
		         ."left join (SELECT buyer_id,max(pay_time) FROM ".$this->_order_mod->table."  WHERE bh_id='{$bh_id}' AND status "
		        .db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED))." GROUP BY buyer_id) as b ON a.buyer_id = b.buyer_id AND "
		            ."a.pay_time=b.pay_time) as o "
		    ." LEFT JOIN ".$model_member->table 
		    ." as m ON o.buyer_id = m.user_id  ORDER BY o.orders DESC LIMIT ".$page['limit']; */
		 $sql ="SELECT m.user_name,m.real_name,m.phone_mob,m.im_qq,m.im_aliww,m.user_id,o.orders,o.pay_time,v.level FROM "
		     ."(SELECT a.buyer_id,b.orders,b.pay_time FROM ".$this->_order_mod->table." a "
		        ." inner join (SELECT t.buyer_id,max(pay_time) pay_time,count(t.buyer_id) as orders FROM ".$this->_order_mod->table." t  WHERE bh_id='{$bh_id}' AND status "
		       .db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED))." GROUP BY t.buyer_id) b ON a.buyer_id = b.buyer_id AND "
		           ."a.pay_time=b.pay_time )"
		    ." as o , ".$model_member->table 
		    ." as m , ".$model_membervip->table 
		    ." as v WHERE o.buyer_id = m.user_id AND m.user_id=v.user_id AND v.bh_id={$bh_id} {$search_uname} ORDER BY o.$order DESC LIMIT ".$page['limit'];
		$members = $this->_order_mod->getAll($sql); 
		
		//dump($members);
		/* dump($buyers);
		
		$members = $this->_order_mod->find(array(
			'conditions'=>"order_alias.bh_id='{$bh_id}' AND order_alias.status=".ORDER_FINISHED,
			'fields'=>'member.phone_mob,member.im_qq,member.im_aliww,buyer_id',
		    'join'=>'belongs_to_user',
			'limit'=>$page['limit'],
			//'count'=>true	
		)); 
		
		dump($members); */
		
		
		
		$sql1 ="SELECT count(*) FROM "
		     ."(SELECT a.buyer_id,b.orders,b.pay_time FROM ".$this->_order_mod->table." a "
		        ." inner join (SELECT t.buyer_id,max(pay_time) pay_time,count(t.buyer_id) as orders FROM ".$this->_order_mod->table." t  WHERE bh_id='{$bh_id}' AND status "
		       .db_create_in(array(ORDER_ACCEPTED,ORDER_SHIPPED,ORDER_FINISHED))." GROUP BY t.buyer_id) b ON a.buyer_id = b.buyer_id AND "
		           ."a.pay_time=b.pay_time )"
		    ." as o , ".$model_member->table 
		    ." as m , ".$model_membervip->table 
		    ." as v WHERE o.buyer_id = m.user_id AND m.user_id=v.user_id AND v.bh_id={$bh_id}  {$search_uname} ";
		$item_count = $this->_order_mod->getCol($sql1);
		
		if($members)
		{
		    foreach ($members as $key=>$vm)
		    {
		        
		        if(time() - $vm['pay_time'] > 10*24*60*60)
		        {
		            $members[$key]['red'] = 1;
		        }
		    }
		} 
		//dump($members);
		//统计超过10天未下单的用户
		
		
		
		$page ['item_count'] = current($item_count);
		$this->_format_page ( $page );
		
		$page['start_number'] = (intval($page['curr_page'])-1)*intval($page['pageper'])+1;
		if($page['start_number'] + $page['pageper'] <= $page['item_count'])
		{
		    $page['end_number'] = intval($page['start_number'])-1 + intval($page['pageper']) ;
		}
		else
		{
		    $page['end_number'] = intval($page['start_number'])-1 + floor(intval($page['item_count']) % intval($page['pageper']));
		}
		/* $page ['item_count'] = $model_member->getCount ();
		$this->_format_page ( $page );
		$page['start_number'] = (intval($page['curr_page'])-1)*intval($page['pageper'])+1;
		if($page['start_number'] + $page['pageper'] <= $page['item_count'])
		{
			$page['end_number'] = intval($page['start_number']) + intval($page['pageper']) -1;
		}
		else
		{
			$page['end_number'] = intval($page['start_number']) - 1 + floor(intval($page['item_count']) % intval($page['pageper']));
		} */
		$this->_assign_leftmenu('client_manage');
		$this->_import_css_js('dt');
		$this->assign ( 'page_info', $page );
		$this->assign('members',$members);
		$this->display('behalf.client.members.list.html');
		//return $members;
	}
	
	/**
	 * 档口黑名单
	 */
	public function store_black_list()
	{
	   // $rows = isset($_GET['length']) && $_GET['length'] ? intval(trim($_GET['length'])):10;
	   // $page = $this->_get_page($rows);
	    $bh_id = $this->visitor->get('has_behalf');
	    $store_id = $_GET['sid'];
	    //加入黑名单
	    if($_GET['type'] == 'add')
	    {
	       if(!empty($store_id) && is_numeric($store_id))
	       {
	          $this->_behalf_mod->createRelation("has_blacklist_stores",$bh_id,array($store_id));    
	       }
	       
	    }
	    //解除黑名单
	    if($_GET['type'] == 'dismiss')
	    {	        
	        $this->_behalf_mod->unlinkRelation("has_blacklist_stores",$bh_id,array($store_id));
	    }
	    
	   // $black_list = $this->_behalf_mod->getRelatedData("has_blacklist_stores",$bh_id,array('limit'=>$page['limit']));
	    
	    
	    //$page ['item_count'] = $this->_behalf_mod->getCount ();
	    /* $this->_format_page ( $page );
	    $page['start_number'] = (intval($page['curr_page'])-1)*intval($page['pageper'])+1;
	    if($page['start_number'] + $page['pageper'] <= $page['item_count'])
	    {
	        $page['end_number'] = intval($page['start_number']) + intval($page['pageper']) -1;
	    }
	    else
	    {
	        $page['end_number'] = intval($page['start_number']) - 1 + floor(intval($page['item_count']) % intval($page['pageper']));
	    } */
	    
	    
	    
	    $this->_assign_leftmenu('client_manage');
	    $this->_import_css_js('dt');
	    //$this->assign ( 'page_info', $page );
	   // $this->assign('black_list',$black_list);
	   // $this->assign ( 'page_info', $page );
	    $this->display('behalf.client.store.black_list.html');
	}
	
	public function new_clients_stats()
	{
	    $query_time = is_date($_POST['query_time'])?strtotime($_POST['query_time']):time();
	    $start_time = mktime(0,0,0,date('m',$query_time),date('d',$query_time),date('Y',$query_time)) - date('Z');
	    $end_time = $start_time + 24*60*60;
	    
	    $bh_id = $this->visitor->get('has_behalf');
	    
	    $before_clients = $this->_order_mod->getCol("SELECT DISTINCT buyer_id FROM ".$this->_order_mod->table." WHERE bh_id = {$bh_id} AND add_time < {$start_time}");
	    $query_clients = $this->_order_mod->getCol("SELECT DISTINCT buyer_id FROM ".$this->_order_mod->table." WHERE bh_id = {$bh_id} AND add_time >= {$start_time} AND add_time < {$end_time}");
	    
	    $before_clients = array_filter(array_unique($before_clients));
	    $query_clients = array_filter(array_unique($query_clients));
	    
	    
	    $new_clients_count = 0;
	    if($query_clients)
	    {
	        foreach ($query_clients as $buyer_id)
	        {
	            if(!in_array($buyer_id, $before_clients))
	            {
	                $new_clients_count ++ ;
	            }
	        }
	    }
	    
	   // $this->assign("st",$start_time."=".date("Y-m-d H:i:s",$start_time));
	    $this->assign("before_clients",count($before_clients));
	    $this->assign("total_clients",count($query_clients));
	    $this->assign("new_clients", $new_clients_count);
	    $this->assign("stats_time",date("Y-m-d",$query_time));
	    $this->_assign_leftmenu('client_manage');
	    $this->_import_css_js('dt');
	    $this->display('behalf.client.new_clients.daily_stats.html');
	}
	public function vip_switch()
	{
	  
	    $state = $_POST['state'] == 'true' ? 1 :0 ;
	    
	    $result = $this->_behalf_mod->edit($this->_bh_id,array('vip_clients_discount'=>$state));
	    
	    $this->json_result($result);
	}
	/**
	 *   @name 设置vip优惠
	 *   @param
	 *   @return
	 *   @author tanaq@51zwd.com
	 */	
	public function vip_discount()
	{
	    //运费优惠
	    $vip1 = !empty($_POST['vip1']) ? number_format(floatval($_POST['vip1']),2) : 0.00;
	    $vip2 = !empty($_POST['vip2']) ? number_format(floatval($_POST['vip2']),2) : $vip1;
	    
	    //vip2不能少于vip1的优惠
	    $vip2 < $vip1 && $vip2 = $vip1;  
	    
	    //件数升级vip
	    $vip1_amount = intval($_POST['vip1_amount']) < 1 ? 1 : intval($_POST['vip1_amount']);
	    $vip2_amount = intval($_POST['vip2_amount']) < $vip1_amount ? ( $vip1_amount + 10) : intval($_POST['vip2_amount']);
	    
	    //服务费优惠
	    $vip1_service_fee = !empty($_POST['vip1_service_fee']) ? number_format(floatval($_POST['vip1_service_fee']),2) : 0.00;
	    $vip2_service_fee = !empty($_POST['vip2_service_fee']) ? number_format(floatval($_POST['vip2_service_fee']),2) : $vip1_service_fee;
	    
	    //vip2 service fee 不能少于 vip1
	    $vip2_service_fee < $vip1_service_fee && $vip2_service_fee = $vip1_service_fee;
	    
	    //vip1:1.00:10|vip2:2.00:14
	    $vip_confs = "vip1:$vip1:$vip1_amount:$vip1_service_fee|vip2:$vip2:$vip2_amount:$vip2_service_fee";
	    
	    $this->_behalf_mod->edit($this->_bh_id,array('vip_clients_conf'=>$vip_confs));
	    
	    //$this->vip_update();
	    $this->vip_conf();
	}
	/**
	 * @name 显示VIP客户优惠设置
	 * @param 
	 * @return 
	 * @author tanaq@51zwd.com
	 * @todo 指明应改进或没有完成的地方
	 */
	public function vip_conf()
	{
	    
	    $behalf_info = $this->_behalf_mod->get($this->_bh_id); 
	    $data = array();
	    if($behalf_info['vip_clients_conf'])
	    { 
	        //vip1:1.00:10|vip2:2.00:14
	        $confs = explode("|", $behalf_info['vip_clients_conf']);
	        //去除空元素
	        $confs = array_unique(array_filter($confs));
	        foreach ($confs as $conf)
	        {
	            $conf_result = explode(":", $conf);
	            //运费金额
	            $data[$conf_result[0]] = number_format($conf_result[1],2);	            
	            //数量
	            $data[$conf_result[0].'_amount'] = $conf_result[2];
	            //服务费
	            $data[$conf_result[0].'_service_fee'] = $conf_result[3];
	        }
	    }
	    
	    $this->assign("behalf_info",$behalf_info);
	    $this->assign("vips",$data);
	    
	    $this->_assign_leftmenu('client_manage');
	    $this->_import_css_js('dt');
	    $this->display('behalf.client.discount_conf.html');
	}
	/**
	 * 更新订单数及vip level
	 */
	public function vip_update()
	{
	   	            
	    $orders = $this->_order_mod->getAll("SELECT buyer_id,count(order_id) as count FROM ".$this->_order_mod->table 
	        ." WHERE bh_id = {$this->_bh_id} AND status= ".ORDER_FINISHED." GROUP BY buyer_id"); 
	    
	    if($orders)
	    {
	        $mod_vips = & m('membervip');
	        $mod_behalf = & m('behalf');
    
            $behalf_info = $mod_behalf->get($this->_bh_id);
            
            $vip1_orders = 0;
            $vip2_orders = 0;
            
            if($behalf_info['vip_clients_discount'] && !empty($behalf_info['vip_clients_conf']))
            {             
                $confs = explode('|', $behalf_info['vip_clients_conf']);
                foreach ($confs as $conf)
                {
                    $tmp_conf = explode(":", $conf);
                    if($tmp_conf[0] == 'vip1') $vip1_orders = $tmp_conf[2];
                    if($tmp_conf[0] == 'vip2') $vip2_orders = $tmp_conf[2];
                }
                
            }
	        
	        foreach ($orders as $order)
	        {
	            $vip_info = $mod_vips->get("user_id = {$order['buyer_id']} AND bh_id={$this->_bh_id}");
	           
	            $level = 0;
	            if($vip_info['orders']  >= $vip1_orders && $vip_info['orders']  < $vip2_orders)
	                $level = 1;
	            elseif($vip_info['orders']  >= $vip2_orders)
	                $level = 2;
	           /*  $level = 0;
	            if($order['count'] >= VIPONE_ORDERS && $order['count'] < VIPTWO_ORDERS)
	                $level = 1;
	            elseif($order['count'] >= VIPTWO_ORDERS)
	               $level = 2; */
	            
	            if(empty($vip_info))
	            {	                
	                $mod_vips->add(array('orders'=>$order['count'],'user_id'=>$order['buyer_id'],'bh_id'=>$this->_bh_id));
	            }
	            else 
	            {
	                if($vip_info['vip_reason'] == 'auto')
	                {
	                    $mod_vips->edit("user_id = {$order['buyer_id']} AND bh_id={$order['bh_id']}",array('level'=>$level,'orders'=>$order['count']));
	                }
	                else
	                {
	                    $mod_vips->edit("user_id = {$order['buyer_id']} AND bh_id={$order['bh_id']}",array('orders'=>$order['count']));
	                }
	                
	               // $mod_vips->edit("user_id={$order['buyer_id']} and bh_id={$this->_bh_id}",array('orders'=>$order['count'],'level'=>$level));
	            }
	        }
	        //echo ecm_json_encode($vip1_orders.":".$vip2_orders."#".$behalf_info['vip_clients_conf']);
	    }  
	    
	    //
	}
	/**
	 * vip 列表
	 */
	public function vip_list()
	{
	    $this->_assign_leftmenu('client_manage');
	    $this->_import_css_js('dt');
	    $this->display('behalf.client.vip.list.html');
	}
	
	public function vip_grade()
	{
	    $user_id = $_GET['uid'];
	    
	    $mod_membervip = & m('membervip');
	    
	    $vip_info = $mod_membervip->get("user_id={$user_id} AND bh_id={$this->_bh_id}");
	    
	    if($vip_info['level'] < 2)
	    {
	        $mod_membervip->edit("user_id={$user_id} AND bh_id={$this->_bh_id}",array('level'=>($vip_info['level']+1),'vip_reason'=>'behalf','vip_add_time'=>gmtime()));
	    }
	    
	    $this->member_list();
	}
	
	/**
	 * @todo 目前只允许申请寄回一次
	 */
	public function handle_postback(){
	    $order_id = isset($_GET['order_id']) ? intval(trim($_GET['order_id'])) : 0;
	    $order =  $this->_order_mod->get("order_id=$order_id");
	    
	    $mod_postback = & m('behalfgoodspostback');
	    $order_postback = $mod_postback->get("order_id=$order_id");
	    
	    if(empty($order_postback)){
	        echo "apply  empty";
	        return;
	    }
	    
	    $warehouse_ids = array_filter(explode(",", $order_postback['warehouse_ids']));
	    
	    $mod_warehouse = & m('goodswarehouse');
	    $goods_list = $mod_warehouse->find(array(
	        'conditions'=>db_create_in($warehouse_ids,"id")
	    ));
	    
	    if(empty($goods_list)){
	        echo "goods  empty";
	        return;
	    }
	    
	    $is_backfailedgoods = true;
	    foreach ($goods_list as $goods){
	        if($goods['goods_status'] != BEHALF_GOODS_REBACK_FAIL){
	            $is_backfailedgoods = false;
	        }
	    }
	    
	    if(!$is_backfailedgoods){
	        echo "goods not real";
	        return;
	    }
	    
	    if(!IS_POST){
	        
	        $this->assign("order_postback",$order_postback);
	        $this->assign("goods_list",$goods_list);
	        $this->display("behalf.client.handle.postback.html");
	    }else{
	        $status = isset($_POST['status']) ? trim($_POST['status']) : 0;
	        if($status == 2){
	            $mod_postback->edit("id = {$order_postback['id']}",array("status"=>$status,"ship_time"=>gmtime()));
	            echo Lang::get("caozuo_success");
	        }elseif($status == 1){
	            $shipping_unit = isset($_POST['shipping_unit']) ? trim($_POST['shipping_unit']) : '';
	            $invoice = isset($_POST['invoice']) ? trim($_POST['invoice']) : '';
	            $mod_postback->edit("id = {$order_postback['id']}",array("status"=>$status,"shipping_unit"=>$shipping_unit,"invoice"=>$invoice,"ship_time"=>gmtime()));
	            echo Lang::get("caozuo_success");
	        }else{
	            echo Lang::get("caozuo_failed");
	        }
	    }
	    
	}
	
	
}

?>