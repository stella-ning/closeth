<?php

/* 订单退款*/
class ClaimOrderModel extends BaseModel
{
    var $table  = 'claim_order';
    var $prikey = 'order_id';
    var $_name  = 'claimorder';

    var $_relation = array(
        'has_claimgoods' => array(
            'model'         => 'claimorder',
            'type'          => HAS_MANY,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        'belongs_to_order' => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_claimorder',
            'model'         => 'order',
        ),
        'has_tuihuobatchgoods' => array(
            'model'        => 'tuihuobatchgoods',
            'type'         => HAS_AND_BELONGS_TO_MANY,
            'middle_table' => 'claim_goods',    //中间表名称
            'foreign_key'  => 'order_id',
            'reverse'      => 'belongs_to_claim_order', //反向关系名称
        ),

    );
	
	
}

?>