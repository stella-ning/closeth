<?php

/* 员工employee 和 角色role 的关系模型  zjh*/
class Employee_roleModel extends BaseModel
{
    var $table  = 'employee_role_relation';
    var $prikey = 'employee_id';
    var $_name  = 'employee_role';
    var $alias  = 'er_r';

    /* 与其它模型之间的关系 */
    var $_relation = array(
        
    	//一个员工账号属于一个角色
    	'belongs_to_role' => array(
    		'model' =>'role',
            'type' =>BELONGS_TO,
            'foreign_key' => 'role_id',
            'reverse' => 'has_employee_role_relation' 
    	),

        //一个员工账号属于一个用户
        'belongs_to_member' => array(
            'model' =>'member',
            'type' =>BELONGS_TO,
            'foreign_key' => 'user_id',
            'reverse' => 'has_employee' 
        )
    );

    
}

?>