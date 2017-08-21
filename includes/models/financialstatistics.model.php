<?php

/* 财务统计数据模型 */

class FinancialStatisticsModel extends BaseModel {

    var $table = 'financial_statistics';
    var $prikey = 'id';
    var $alias = 'financialstatistics';
    var $_name = 'financial_statistics';

    var $_relation = array(
        // 一个商品对应一条商品统计记录


    );

    private function get_statistics(){
        $date = date('Y-m-d' ,time());
        $data =  $this->get("date='{$date}'");

        if(empty($data)){
            $this->add(array('date'=>$date));
            return $this->get("date='{$date}'");
        }
        return $data;

    }

    /**
     * 备货商品   入统计
     * 更新为目标状态  BEHALF_GOODS_READY
     */
    function goods_success( ){
        $current_statistics = $this->get_statistics();

         $data = array(
            'warehouse_num' => $current_statistics['warehouse_num'] + 1 ,
         );
                //之前 状态 为  APP已拿时
         return  $this->edit($current_statistics['id'] ,$data );


    }

    /**
     * 缺货目前在APP端入统计数据在PC端不入统计
     *
     * 缺货商品 入统计
     * 更新为目标状态 缺货集
     */
    /*function goods_fail($old_status , $goods_id){
        $current_statistics = $this->get_statistics();
        $goods_warehouse_model = & m('goodswarehouse');
        $goods_info = $goods_warehouse_model->get($goods_id);


            if(in_array($old_status , array(BEHALF_GOODS_PREPARED , BEHALF_GOODS_READY_APP , BEHALF_GOODS_DELIVERIES))){
                $data = array(
                    'shortage_num' => $current_statistics['shortage_num'] + 1,
                );
                return  $this->edit($current_statistics['id'] ,$data );
            }elseif( in_array($old_status , array( BEHALF_GOODS_IMPERFECT, BEHALF_GOODS_UNFORMED , BEHALF_GOODS_UNSALE , BEHALF_GOODS_TOMORROW , BEHALF_GOODS_AFTERNOON , BEHALF_GOODS_ERROR , BEHALF_GOODS_ERROR2 ,BEHALF_GOODS_PRICE_ERROR )) ){
                return true;
            }elseif(in_array($old_status ,array( BEHALF_GOODS_READY ))){
                $data = array(
                    'taken_num'=>$current_statistics['taken_num'] - 1 ,
                    'warehouse_num'=>$current_statistics['warehouse_num'] - 1 ,
                    'amount' => $current_statistics['amount'] - $goods_info['goods_price'] ,
                    'actual_amount' => $current_statistics['actual_amount'] - ($goods_info['real_price'] > 0 ? $goods_info['real_price'] : $goods_info['goods_price']) ,
                    'shortage_num' => $current_statistics['shortage_num'] + 1,
                );
               return $this->edit($current_statistics['id']  , $data);
            }

        return false;
    }*/

    /**
     * 退货成功 入统计
     */
    function back_success($real_amount ){
        $current_statistics = $this->get_statistics();

        $data = array(
            'back_success_num' => $current_statistics['back_success_num'] + 1,
            'back_amount' => $current_statistics['back_amount'] + $real_amount ,
        );
        return   $this->edit($current_statistics['id'] , $data);

    }

    /**
     * 退货失败 入统计
     */
    function back_fail(){
        $current_statistics = $this->get_statistics();

        $data = array(
            'back_fail_num' => $current_statistics['back_success_num'] + 1,
        );
        return $this->edit($current_statistics['id'] , $data);
    }

    /**
     * 增加订单商品数量
     */
    function order_increase($num){
        $current_statistics = $this->get_statistics();
        $data = array(
            'order_num' => $current_statistics['order_num'] + $num,
        );

            return   $this->edit($current_statistics['id'] , $data );
    }



    /**
     * 减少订单商品数量
     */
    function order_cut($num){
        $current_statistics = $this->get_statistics();
        $data = array(
            'order_num' => $current_statistics['order_num'] - $num,
        );

        return   $this->edit($current_statistics['id'] , $data );
    }

    /**
     * 增加退货商品数量
     */
    function refund_increase($num){
        $current_statistics = $this->get_statistics();
        $data = array(
            'back_num' => $current_statistics['back_num'] + $num,
        );

        return   $this->edit($current_statistics['id'] , $data );
    }

    /**
     * 减少退货商品数量
     */
    function refund_cut($num){
        $current_statistics = $this->get_statistics();
        $data = array(
            'back_num' => $current_statistics['back_num'] - $num,
        );

        return   $this->edit($current_statistics['id'] , $data );
    }

}

?>