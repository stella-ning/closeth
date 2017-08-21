<?php

/* 订单退款*/
class ClaimGoodsModel extends BaseModel
{
    var $table  = 'claim_goods';
    var $prikey = 'goods_id';
    var $_name  = 'claimgoods';

    var $_relation = array(

        'belongs_to_claimorder' => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_claimgoods',
            'model'         => 'claimorder',
        )
    );
	
	
}

?>