<?php
/**
 * Created by zjh.
 * User: All
 * Date: 2017/6/29 0029
 * Time: 15:55
 * Title: 快递配送区域模型
 */

class Shipping_areaModel extends BaseModel
{
    var $table  = 'behalf_shipping_area';
    var $prikey = 'shipping_area_id';
    var $_name  = 'shipping_area';

    var $_relation = array(

        // zjh 配送区域归于快递的管理

        'belongs_to_delivery' => array(
            'model'         => 'delivery',
            'type'          => BELONGS_TO,
            'middle_table'  => 'behalf_delivery',
            'foreign_key'   => 'shipping_area_id',
            'reverse'       => 'has_shipping_area',
        )

    );
}