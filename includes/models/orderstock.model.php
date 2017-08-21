<?php

/* 订单 order */
class OrderStockModel extends BaseModel
{
	var $table  = 'order_stock';
	var $alias  = 'order_stock';
	var $prikey = 'order_id';

	var $_relation = array(

		'belongs_to_order' => array(
			'model'         => 'order',
			'type'          => BELONGS_TO,
			'foreign_key'   => 'order_id',
			'reverse'       => 'has_orderstock',
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
