<?php

class GoodswarehouseModel extends BaseModel {
    var $table = 'goods_warehouse';
    var $alias  = 'gwh';
    var $prikey = 'id';
    var $_relation = array(
        'belongs_to_order' => array(
            'model'         => 'order',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'order_id',
            'reverse'       => 'has_goodswarehouse',
        ),
        'belongs_to_orderthird' => array(
            'model' => 'orderthird' ,
            'type'  => BELONGS_TO ,
            'foreign_key' => 'order_id',
            'reverse'   => 'has_goodswarehouse' ,
        ),
        'belongs_to_orderstock' => array(
            'model' => 'orderstock',
            'type'  => BELONGS_TO ,
            'foreign_key' => 'order_id',
            'reverse'       => 'has_goodswarehouse',
        ),
		'has_orderrefund'=>array(
    		 'model'         => 'orderrefund',
    		 'type'          => HAS_MANY,
    		 'foreign_key'   => 'order_id',
    		 'dependent'     => true,
        ),
        'has_refundgoods' => array(
            'model'         => 'th_batch_goods',
            'type'          => HAS_ONE ,
            'foreign_key' => 'gwh_id',
            'dependent'     => true,
        ),
    );
}