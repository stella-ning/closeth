<?php

/* 虎门区*/
class StorehmModel extends BaseModel
{
    var $table  = 'store_hm';
    var $prikey = 'store_id';
    var $alias  = 'shm';
    var $_name  = 'storehm';
    var $_relation = array(
        
         'belongs_to_store' => array(
            'model'         => 'store',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'store_id',
            'reverse'       => 'has_storehm',
        )
    );
}

?>