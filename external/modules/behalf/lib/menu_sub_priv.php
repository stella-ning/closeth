<?php

/**
 * 设置菜单对应页面内的功能点权限（B类权限） <A类权限：菜单权限>
 * ============================================================================
 * 比如：快递费用设置页面，功能点有，新增配送区域，编辑配送区域，移除配送区域等，可以分别给它们设定权限，从而限制某些角色的操作
 *
 * 设置方式：使用函数 _set_menu_sub_priv , '菜单的name' => array('功能点1的权限标识符' => '功能点1名称', '功能点2的权限标识符' => '功能点2名称'，...)
 * 
 * 功能点的标识符和名称均是自定义的
 * 
 * 不添加，则相当于不设定权限，该菜单页面的所有功能点均对拥有该菜单页面权限的角色开放
 *
 * 找寻菜单的name：在 external/modules/behalf/index.module.base.php 内找到函数 function _get_leftmenu($menu); 即可找到对应菜单的name。
 *
 * ============================================================================
 * $Author: zjh
 * 
*/

class MenuSubPriv extends BehalfBaseModule
{ 

	/**
	 * @name  添加并设置页面内功能点的权限（相当于给某个功能点添加权限）
	 * @author zjh 2017-08-02
	*/
	function _set_menu_sub_priv()
	{
		$array = array(

			// 菜单 订单列表（例子）
			'order_list' => array(

				'add_order' => '下单',
				'remove_order' => '退单',
				// 自定义功能点对应的权限标识符（数组的key）和名称（数组的value）
			),

			// 菜单 设置快递费用的 name
			'set_delivery_fee' => array(

				'add_shipping_area' => '添加配送区域',          // 自定义功能点的权限标识符和对应的名称， '权限标识符' => '名称'
				'edit_shipping_area' => '编辑配送区域',
				'remove_shipping_area' => '移除配送区域',
			),

			// 可继续添加

		);

		return $array;
	}


	/**
	 * @name 检测当前登录用户是否拥有该功能点的权限
	 * @description 如果需要对某个功能点设定权限，则在_set_menu_sub_priv 函数内加上标识符，然后在对应的功能点处理函数内添加以下函数(_detect_sub_priv)，用来判断当前登录用户是否拥有该功能点的权限。
	 * @param $menu_name 功能点所在菜单的name （external/modules/behalf/index.module.base.php 内找到函数 function _get_leftmenu($menu); ）
	 * @param $priv_str 功能点的权限标识符
	 * @return true 拥有权限，false，没有权限
	 * @author zjh 2017-08-02
	*/
	function _detect_sub_priv($menu_name,$priv_str)
	{
		// 获取所有B类权限
		$priv_array = $this->_set_menu_sub_priv();

		$this_menu_priv = array();   // 获取菜单下所设置权限
		foreach ($priv_array as $key => $value) {
			if(is_array($value) && !empty($value) && $key == $menu_name){

				foreach ($value as $k => $v) {
					$this_menu_priv[] = $k;
				}
			}
		}

		// 输入参数的权限标识符，不在该菜单所设置的范围内，则返回true，相当于不作权限限制（默认开放）
		if(!in_array($priv_str, $this_menu_priv)){  

			return true;
		}

		// 获取当前用户所拥有的B类权限
		$user_sub_priv = $this->_get_user_sub_priv();

		$user_this_menu_priv = array();   // 用户在该菜单下的权限
		foreach ($user_sub_priv as $key => $value) {
			if(is_array($value) && !empty($value) && $key == $menu_name){

				foreach ($value as $k => $v) {
					$user_this_menu_priv[] = $v;
				}
			}
		}


		// empty 用户没有在当前菜单所划分的权限限制范围内，代表权限默认开放
		if(in_array($priv_str, $user_this_menu_priv) || empty($user_this_menu_priv)){   //当前用户拥有这个权限

			return true;
		}else{

			return false;
		}

	}


	/**
	 * 使用案例：
	 * 比如在文件 external/modules/behalf/index.module.php 内有一个函数 function _remove_shipping_area($delivery,$sa_id); 在快递费设置页面中（对应的菜单name为：set_delivery_fee），它的作用是移除配送区域。
	 * 如果要对这个功能点添加权限，那么需要在函数 _remove_shipping_area 内添加一行类似于下面的代码：
	 *
	 *	function _remove_shipping_area($delivery,$sa_id)
	 	{
			if(!$this->_menu_sub_priv->_detect_sub_priv('set_delivery_fee','remove_shipping_area')){  // 没有权限
				
				// 提示用户没有权限
				$this->show_message('你没有删除配送区域的权限！请联系管理员');
				return;
			}

	 		.....
	 
	 	}
	 *
	 * ps：在使用_detect_sub_priv('remove_shipping_area') 函数之前，请记得先在函数_set_menu_sub_priv() 内添加权限标识符 'remove_shipping_area' ，用以给该功能点添加权限
	*/
	


	 /**
	 * @name  获取当前用户所拥有的权限(B类权限)
	 * @author zjh 2017-08-06
	 * @return 权限
	 */
    function _get_user_sub_priv()
    {
    	$user_id =  $this->visitor->get('user_id');
    	// 获取员工账号与角色的绑定信息
    	$employee_info = $this->_get_role_employee($user_id);
    	// 获取当前用户所拥有的权限标识符列表
    	// 解序列化
    	$user_sub_priv = unserialize($employee_info['sub_priv']);

    	return $user_sub_priv;
    }

    /**
     * @name  获取员工账号与角色的绑定信息
     * @author zjh 2017-08-06
     * @param $employee_id  员工id
     */
    function _get_role_employee($employee_id)
    {
    	$bh_id =  $this->visitor->get('has_behalf');

    	$employee = $this->_employee_role_mod->get(array(
     		'conditions'=>"er_r.role_id = r_p.role_id AND r_p.bh_id = {$bh_id} AND er_r.employee_id = ".$employee_id,
     		'fields'=>'er_r.*,r_p.*',
     		'join'=>'belongs_to_role'
     	));

 		return $employee;
    }

}


?>