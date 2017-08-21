<?php

/* 库位表 stock */
class StockModel extends BaseModel
{
    var $table  = 'stock';
    var $alias  = 'stock';
    var $prikey = 'id';
    var $_name  = 'stock';
	var $_relation = array(
		//  	当前代码只适用一个库位只针对一个订单  代码需要修改
		'has_orderstock' => array(
			'model'         => 'orderstock',
			'type'          => HAS_MANY,
			'foreign_key'   => 'stock_id',

		),

	);


}

?>
