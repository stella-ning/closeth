<?php

/* 订单 order */
class OrderPackModel extends BaseModel
{
    var $table  = 'order_pack';
    var $alias  = 'order_pack';
    var $prikey = 'order_id';

	var $_relation = array(
		// 一个第三方只能属于一个订单
		'belongs_to_order' => array(
			'model'         => 'order',
			'type'          => BELONGS_TO,
			'foreign_key'   => 'order_id',
			'reverse'       => 'has_orderpack',
		),
		'has_goodswarehouse'=>array(
			'model'         => 'goodswarehouse',
			'type'          => HAS_MANY,
			'foreign_key'   => 'order_id',
			'dependent'     => true
		),
	);


}

?>
