<?php

/* 订单 order */
class OrderThirdModel extends BaseModel
{
    var $table  = 'order_third';
    var $alias  = 'order_third';
    var $prikey = 'order_id';
   // var $_name  = 'orderthird';
	var $_relation = array(
		// 一个第三方只能属于一个订单
		'belongs_to_order' => array(
			'model'         => 'order',
			'type'          => BELONGS_TO,
			'foreign_key'   => 'order_id',
			'reverse'       => 'has_orderthird',
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
