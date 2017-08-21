<?php

/* 角色 role  zjh*/
class RoleModel extends BaseModel
{
    var $table  = 'behalf_role_priv';
    var $prikey = 'role_id';
    var $_name  = 'role_priv';
    var $alias  = 'r_p';

    /* 与其它模型之间的关系 */
    var $_relation = array(
        
    	//一个角色属于一个代发
    	'belongs_to_behalf' => array(
    		'model' =>'behalf',
            'type' =>BELONGS_TO,
            'foreign_key' => 'bh_id',
            'reverse' => 'has_role' 
    	),

        //一个角色拥有多个员工账号
        'has_employee_role_relation' => array(
            'model' =>'employee_role',
            'type' =>HAS_MANY,
            'foreign_key' => 'role_id',
            'reverse' => 'belongs_to_role' 
        )
    );

    /**
     * 取得角色分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @param int $bh_id  -1 代表不分代发，取所有角色
     * @return array
     */
    function get_list($bh_id = -1,$parent_id = -1)
    {
        $conditions = "1 = 1";
        $parent_id >= 0 && $conditions .= " AND parent_id = '$parent_id'";
        $bh_id >= 0 && $conditions .= " AND bh_id = '$bh_id'";

        return $this->find(array(
            'conditions' => $conditions,
            'order' => 'role_id',
        ));
    }
}

?>