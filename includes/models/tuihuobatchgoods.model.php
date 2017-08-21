<?php

class TuihuobatchgoodsModel extends BaseModel {
    var $table = 'th_batch_goods';
    var $prikey = 'gwh_id';

    var $_relation = array(
        // 一个退货商品只能属于一个入库商品
        'belongs_to_warehouse' => array(
            'model'         => 'goodswarehouse',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'id',
            'reverse'       => 'has_refundgoods',
        ),

        'has_refundreason' => array(
            'model'         => 'refundreason',
            'type'          => HAS_ONE ,
            'foreign_key'   => 'goods_id',
            'reverse'       => 'has_refundreason',
        ),

        'has_claimgoods'    => array(
            'model'     => 'claimgoods',
            'type'      => HAS_ONE ,
            'foreign_key'   => 'goods_id',

        ),
        'belongs_to_order' => array(
            'model'        => 'order',
            'type'         => HAS_AND_BELONGS_TO_MANY,
            'middle_table' => 'goods_warehouse',    //中间表名称
            'foreign_key'  => 'id',
            'reverse'      => 'has_tuihuobatchgoods', //反向关系名称
        ),
        'belongs_to_claim_order' => array(
            'model'        => 'claimorder',
            'type'         => HAS_AND_BELONGS_TO_MANY,
            'middle_table' => 'claim_goods',    //中间表名称
            'foreign_key'  => 'goods_id',
            'reverse'      => 'has_tuihuobatchgoods', //反向关系名称
        ),

    );
    
}