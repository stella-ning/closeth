<?php
/**
 *  代发无法退货，客户申请寄回
 * @author tanaq
 *
 */
class BehalfgoodspostbackModel extends BaseModel
{
    var $table  = 'behalf_goods_postback';
    var $alias  = 'bh_goods_pb';
    var $prikey = 'id';
    var $_name  = 'bh_goods_postback';
	var $_relation = array(
		
	    // 一个申请寄回只能属于一个订单
	    'belongs_to_order' => array(
	        'model'         => 'order',
	        'type'          => BELONGS_TO,
	        'foreign_key'   => 'order_id',
	        'reverse'       => 'has_behalfgoodspostback',
	    ),

	);


}

?>
