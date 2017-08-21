<?php

/**
 *    合作伙伴控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class OrderApp extends BackendApp {

    /**
     * 管理
     *
     * @author Garbin
     * @param
     *            none
     * @return void
     */
    function index() {
        $search_options = array(
            'seller_name' => Lang::get('store_name'),
            'buyer_name' => Lang::get('buyer_name'),
            'payment_name' => Lang::get('payment_name'),
            'order_sn' => Lang::get('order_sn')
        );
        /* 默认搜索的字段是店铺名 */
        $field = 'seller_name';
        array_key_exists($_GET['field'], $search_options) && $field = $_GET['field'];
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $field, // 按用户名,店铺名,支付方式名称进行搜索
                'equal' => 'LIKE',
                'name' => 'search_name'
            ),
            array(
                'field' => 'status',
                'equal' => '=',
                'type' => 'numeric'
            ),
            array(
                'field' => 'add_time',
                'name' => 'add_time_from',
                'equal' => '>=',
                'handler' => 'gmstr2time'
            ),
            array(
                'field' => 'add_time',
                'name' => 'add_time_to',
                'equal' => '<=',
                'handler' => 'gmstr2time_end'
            ),
            array(
                'field' => 'order_amount',
                'name' => 'order_amount_from',
                'equal' => '>=',
                'type' => 'numeric'
            ),
            array(
                'field' => 'order_amount',
                'name' => 'order_amount_to',
                'equal' => '<=',
                'type' => 'numeric'
            )
        ));
        $model_order = & m('order');
        $model_behalf = & m('behalf');
        $page = $this->_get_page(10); // 获取分页信息
                                      // 更新排序
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (! in_array($order, array(
                'asc',
                'desc'
            ))) {
                $sort = 'add_time';
                $order = 'desc';
            }
        } else {
            $sort = 'add_time';
            $order = 'desc';
        }
        $behalfs = $model_behalf->find();
        $orders = $model_order->find(array(
            'conditions' => '1=1 ' . $conditions,
            'limit' => $page['limit'], // 获取当前页的数据
            'order' => "$sort $order",
            'count' => true
        )); // 允许统计
 // 找出所有商城的合作伙伴
        if (! empty($orders)) {
            foreach ($orders as $key => $order) {
                if (in_array($order['bh_id'], array_keys($behalfs))) {
                    $orders[$key]['bh_name'] = $behalfs[$order['bh_id']]['bh_name'];
                }
            }
        }
        $page['item_count'] = $model_order->getCount(); // 获取统计的数据
        $this->_format_page($page);
        $this->assign('filtered', $conditions ? 1 : 0); // 是否有查询条件
        $this->assign('order_status_list', array(
            ORDER_PENDING => Lang::get('order_pending'),
            ORDER_SUBMITTED => Lang::get('order_submitted'),
            ORDER_ACCEPTED => Lang::get('order_accepted'),
            ORDER_SHIPPED => Lang::get('order_shipped'),
            ORDER_FINISHED => Lang::get('order_finished'),
            ORDER_CANCELED => Lang::get('order_canceled')
        ));
        $this->assign('search_options', $search_options);
        $this->assign('page_info', $page); // 将分页信息传递给视图，用于形成分页条
        $this->assign('orders', $orders);
        $this->import_resource(array(
            'script' => 'inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'
        ));
        $this->display('order.index.html');
    }

    /**
     * 查看
     *
     * @author Garbin
     * @param
     *            none
     * @return void
     */
    function view() {
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (! $order_id) {
            $this->show_warning('no_such_order');
            
            return;
        }
        
        /* 获取订单信息 */
        $model_order = & m('order');
        $order_info = $model_order->get(array(
            'conditions' => $order_id,
            'join' => 'has_orderextm',
            'include' => array(
                'has_ordergoods'
            )
        )) // 取出订单商品

        ;
        
        if (! $order_info) {
            $this->show_warning('no_such_order');
            return;
        }
        $order_type = & ot($order_info['extension']);
        $order_detail = $order_type->get_order_detail($order_id, $order_info);
        $order_info['group_id'] = 0;
        if ($order_info['extension'] == 'groupbuy') {
            $groupbuy_mod = & m('groupbuy');
            $groupbuy = $groupbuy_mod->get(array(
                'fields' => 'groupbuy.group_id',
                'join' => 'be_join',
                'conditions' => "order_id = {$order_info['order_id']} "
            ));
            $order_info['group_id'] = $groupbuy['group_id'];
        }
        foreach ($order_detail['data']['goods_list'] as $key => $goods) {
            if (substr($goods['goods_image'], 0, 7) != 'http://') {
                $order_detail['data']['goods_list'][$key]['goods_image'] = SITE_URL . '/' . $goods['goods_image'];
            }
        }
        $this->assign('order', $order_info);
        $this->assign($order_detail['data']);
        $this->display('order.view.html');
    }

    /**
     * 取消订单
     * 限制为非代发订单使用
     *
     * @author tanaiquan 2017-02-16
     * @return void
     */
    function cancel_order() {
        /* 取消的和完成的订单不能再取消 */
        // list($order_id, $order_info) = $this->_get_valid_order_info(array(ORDER_SUBMITTED, ORDER_PENDING, ORDER_ACCEPTED, ORDER_SHIPPED));
        $order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
        if (! $order_id) {
            echo Lang::get('no_such_order');
        }
        $status = array(
            ORDER_SUBMITTED,
            ORDER_PENDING,
            ORDER_ACCEPTED,
            ORDER_SHIPPED
        );
        // $order_ids = explode(',', $order_id);
        if ($ext) {
            $ext = ' AND ' . $ext;
        }
        
        $model_order = &  m('order');
        /* 只有已发货的货到付款订单可以收货 */
        $order_info = $model_order->get(array(
            'conditions' => "order_id=" . $order_id . " AND status " . db_create_in($status) . $ext
        ));
        
        // $ids = array_keys($order_info);
        if (! $order_info || $order_info['bh_id']) {
            echo Lang::get('no_such_order');
            return;
        }
        
        $id = $order_id;
        $model_order->edit($id, array('status' => ORDER_CANCELED));
        if ($model_order->has_error()) {
            // $_erros = $model_order->get_error();
            // $error = current($_errors);
            // $this->json_error(Lang::get($error['msg']));
            // return;
            continue;
        }
        
        /* 商付通v2.2.1 更新商付通定单状态 开始 */
        
        $my_money_mod = & m('my_money');
        $my_moneylog_mod = & m('my_moneylog');
        $my_moneylog_row = $my_moneylog_mod->getrow("select * from " . DB_PREFIX . "my_moneylog where order_id='$id' and (caozuo='10' or caozuo='20') and s_and_z=1");
        $money = $my_moneylog_row['money']; // 定单价格
        $buy_user_id = $my_moneylog_row['buyer_id']; // 买家ID
        $sell_user_id = $my_moneylog_row['seller_id']; // 卖家ID
        if ($my_moneylog_row['order_id'] == $id) {
            $buy_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$buy_user_id'");
            $buy_money = $buy_money_row['money']; // 买家的钱
            
            $sell_money_row = $my_money_mod->getrow("select * from " . DB_PREFIX . "my_money where user_id='$sell_user_id'");
            $sell_money = $sell_money_row['money_dj']; // 卖家的冻结资金
            
            $new_buy_money = $buy_money + $money;
            $new_sell_money = $sell_money - $money;
            // 更新数据
            $my_money_mod->edit('user_id=' . $buy_user_id, array(
                'money' => $new_buy_money
            ));
            $my_money_mod->edit('user_id=' . $sell_user_id, array(
                'money_dj' => $new_sell_money
            ));
            // 更新商付通log为 定单已取消
            $my_moneylog_mod->edit('order_id=' . $id, array(
                'caozuo' => 30
            ));
        }
        
        /* 商付通v2.2.1 更新商付通定单状态 结束 */
        
        /* 加回订单商品库存 */
        $model_order->change_stock('+', $id);
        $cancel_reason = "administrator[" . $this->visitor->get('user_name') . "] cancel this order!";
        /* 记录订单操作日志 */
        
        $order_log = & m('orderlog');
        $order_log->add(array(
            'order_id' => $id,
            'operator' => addslashes($this->visitor->get('user_name')),
            'order_status' => order_status($order_info['status']),
            'changed_status' => order_status(ORDER_CANCELED),
            'remark' => $cancel_reason,
            'log_time' => gmtime()
        ));
        
        $this->show_message('success');
    }
    
    
}
?>
