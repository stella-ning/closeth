<?php

/* 订单发送缺货短信次数*/
class OrdermessageModel extends BaseModel
{
    var $table  = 'order_message';
    var $prikey = 'order_id';
    var $_name  = 'ordermessage';
    var $_relation = array(
        // 一个退款只能属于一个订单
        'belongs_to_order' => array(
            'model'         => 'order',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'order_id',
            'reverse'       => 'has_ordermessage',
        ),
    );
}

?>