<?php

/* 快递 */
class DeliveryModel extends BaseModel
{
    var $table  = 'delivery';
    var $prikey = 'dl_id';
    var $_name  = 'delivery';

    var $_relation = array(
    		// 快递和代发是多对多的关系
    		'belongs_to_behalf' => array(
    				'model'         => 'behalf',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'behalf_delivery',
    				'foreign_key'   => 'dl_id',
    				'reverse'       => 'has_delivery',
    		),

            // zjh 一个快递可以有多个配送区域（这里的配送区域可自定义管辖区域，如：配送区域：两广地区；管辖的区域可以为：广东广州，广东深圳，广西桂林等）
            'has_shipping_area' => array(
                    'model'         => 'shipping_area',
                    'type'          => HAS_MANY,
                    'middle_table'  => 'behalf_shipping_area',
                    'foreign_key'   => 'dl_id',
                    'reverse'       => 'belongs_to_delivery',
            ),

    );
    
    /*
     * 判断名称是否唯一
    */
    function unique($dl_name, $dl_id = 0)
    {
    	$conditions = "dl_name = '" . $dl_name . "' AND dl_id != ".$dl_id."";
    	//dump($conditions);
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    function getDLName($dl_id)
    {
         $delivery = $this->get($dl_id);
         return $delivery['dl_name'];
    }
  

}

?>