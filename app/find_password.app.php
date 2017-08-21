<?php
/**
 * 找回密码控制器
 * @author cheng
 */
class Find_passwordApp extends MallbaseApp
{
    var $_password_mod;
    var $_zfpassword_mod;
    function __construct()
    {
        $this->Find_passwordApp();
    }

    function Find_passwordApp()
    {
        parent::FrontendApp();
        $this->_password_mod = &m("member");
        $this->_zfpassword_mod = &m("my_money"); //商付通my_money表
    }

    /**
     * 显示文本框及处理提交的用户信息
     *
     */
    function index()
    {   
       if(!IS_POST)
       {
           $this->import_resource('jquery.plugins/jquery.validate.js');
           $this->display("find_password.html");
       }
       else
       {
           $addr = $_SERVER['HTTP_REFERER'];
			$where = empty($_POST['email']) ? $mobile = trim($_POST['mobile']) : $email = trim($_POST['email']);
           if (empty($_POST['username']) || empty($where) || empty($_POST['captcha']))
           { 
               $this->show_warning("unsettled_required",
                   'go_back', $addr);
               return ;
           }
		   
           if (base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
           {
               $this->show_warning("captcha_faild",
                   'go_back', $addr);
               return ;
           }
           $username = trim($_POST['username']); 
		   
           /* 简单验证是否是该用户 */
		    //$ms =& ms();     //连接用户系统
			//$info = $ms->user->get($username, true);
			$db = &db();
		   if(!empty($email)){
				$info = $db->getrow('SELECT `user_id`,`user_name`,`email` FROM '.DB_PREFIX.'member WHERE user_name="'.$username.'"');
			   
			   if (empty($info) || $info['email'] != $email)
			   {
				   $this->show_warning('not_exist',
					   'go_back', $addr);

				   return;
			   }

		   }elseif(!empty($mobile)){
			   //手机验证
			   $code = intval($_POST['mobileCode']);
			   if($code != $_SESSION['rCode']){
				   $this->show_warning('手机验证码错误',
					   'go_back', $addr);

				   return;
			   }
			   $info = $db->getrow('SELECT `user_id`,`user_name`,`phone_mob` FROM '.DB_PREFIX.'member WHERE user_name="'.$username.'"');
			   if (empty($info) || $info['phone_mob'] != $mobile)
			   {
				   $this->show_warning('手机号码错误',
					   'go_back', $addr);

				   return;
			   }
			    $word = $this->_rand();
				$md5word = md5($word);
				$res = $this->_password_mod->get($info['user_id']);
				if (empty($res))
				{
					$info['activation'] = $md5word;
					$this->_password_mod->add($info);
				}
				else
				{
					$this->_password_mod->edit($info['user_id'], array('activation' => "{$md5word}"));
				}
				//修改完成后跳转
        $url[0] = "/index.php?app=find_password&act=set_password&id=".$info['user_id']."&activation=".$word;
        $url[1] = "/index.php?app=find_password&act=set_zf_password&id=".$info['user_id']."&activation=".$word;
        $this->assign('url',$url);
        $this->display('change_password.html');exit;
		   }

            $word = $this->_rand();
            $md5word = md5($word);
            $res = $this->_password_mod->get($info['user_id']);
            if (empty($res))
            {
                $info['activation'] = $md5word;
                $this->_password_mod->add($info);
            }
            else
            {
                $this->_password_mod->edit($info['user_id'], array('activation' => "{$md5word}"));
            }
			
			//邮箱
			if(!empty($email)){
				if(strpos($username,"51t_")!== false || strpos($username,"51a_")!== false ){
					 $mail = get_mail('touser_find_password_onlyZ', array('user' => $info, 'word' => $word));
				}else{
					 $mail = get_mail('touser_find_password', array('user' => $info, 'word' => $word));
				}
			 //   $this->_mailto($email, addslashes($mail['subject']), addslashes($mail['message']));
			   $result =  $this->sendMail($email, addslashes($mail['subject']), addslashes($mail['message']));
			}
            $this->show_message("sendmail_success",
                    'back_index', 'index.php');

            return;
       }
    }
	
	function ajax_code(){
		
		$username = trim($_POST['username']);
		$mobile = trim($_POST['mobile']);
		$_SESSION['rCode'] = rand(1000,9999);
		$_SESSION['username'] = $username;
		if(empty($_SESSION['time'])){
			$_SESSION['count'] = 1;
			$_SESSION['time'] = time();
		}elseif($_SESSION['count'] >=35){
			$time = time() - $_SESSION['time'];
			if($time>1800){
				$_SESSION['time'] = time();
			}else{
				echo 501;exit;
			}
		}
		
		if(empty($username) && empty($mobile)){
			
			$this->import_resource('jquery.plugins/jquery.validate.js');
            $this->display("find_password.html");
		}else{
			/* 验证该用户 */
			$db = &db();
			$addr = $_SERVER['HTTP_REFERER'];
			$info = $db->getrow('SELECT `user_id`,`user_name` FROM '.DB_PREFIX.'member WHERE user_name="'.$username.'" AND phone_mob="'.$mobile.'"');
			if (empty($info)){
				
			   $this->show_warning('not_exist',
				   'go_back', $addr);

			   return;
		    }
			
			$word = $this->_rand();
            $md5word = md5($word);
            $res = $this->_password_mod->get($info['user_id']);
            if (empty($res))
            {
                $info['activation'] = $md5word;
                $this->_password_mod->add($info);
            }
            else
            {
                $this->_password_mod->edit($info['user_id'], array('activation' => "{$md5word}"));
            }
			$res = $this->sendSms($mobile,$_SESSION['rCode']);
			
			$_SESSION['count'] = $_SESSION['count']+1;
			unset($_SESSION['username']);
			
		}
	}
    
    function find_lft_password()
    {
       if(!IS_POST)
       {
           $this->import_resource('jquery.plugins/jquery.validate.js');
           $this->display("find_password.html");
       }
       else
       {
           $addr = $_SERVER['HTTP_REFERER'];
           if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['captcha']))
           {
               $this->show_warning("unsettled_required",
                   'go_back', $addr);
               return ;
           }
           if (base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
           {
               $this->show_warning("captcha_faild",
                   'go_back', $addr);
               return ;
           }
           $username = trim($_POST['username']);
           $email = trim($_POST['email']);

           /* 简单验证是否是该用户 */
           $ms =& ms();     //连接用户系统
           $info = $ms->user->get($username, true);
           if (empty($info) || $info['email'] != $email)
           {
               $this->show_warning('not_exist',
                   'go_back', $addr);

               return;
           }

            $word = $this->_rand();
            $md5word = md5($word);

            $this->_zfpassword_mod->edit('user_id='.$info['user_id'], array('zf_pass' => $md5word), true); //强制修改
            $ret = $this->_password_mod->edit($info['user_id'], array('activation' => $md5word));//随机产生一个
                     
            $mail = get_mail('touser_find_password', array('user' => $info, 'word' => $word));
        //    $this->_mailto($email, addslashes($mail['subject']), addslashes($mail['message']));
           $result =  $this->sendMail($email, addslashes($mail['subject']), addslashes($mail['message']));

            $this->show_message("sendmail_success",
                    'back_index', 'index.php');

            return;
       }
    }

    /**
     * 显示设置密码及处理提交的新密码信息
     *
     */
    function set_password()
    {
    	   if (!isset($_GET['id']) || !isset($_GET['activation']) || empty($_GET['activation']))
          {
              $this->show_warning("request_error",
                  'back_index', 'index.php');
              return ;
          }
          $id = intval(trim($_GET['id']));
          $activation = trim($_GET['activation']);
          $res = $this->_password_mod->get_info($id);
		
          if (md5($activation) != $res['activation'])
          {
              $this->show_warning("invalid_link",
                  'back_index', 'index.php');
              return ;
          }
        if (!IS_POST)
        {
           /*if (!isset($_GET['id']) || !isset($_GET['activation']) || empty($_GET['activation']))
            {
                $this->show_warning("request_error",
                    'back_index', 'index.php');
                return ;
            }
            $id = intval(trim($_GET['id']));
            $activation = trim($_GET['activation']);
            $res = $this->_password_mod->get_info($id);
            if (md5($activation) != $res['activation'])
            {
                $this->show_warning("invalid_link",
                    'back_index', 'index.php');
                return ;
            }
            **/
            $this->import_resource('jquery.plugins/jquery.validate.js');
            $this->display("set_password.html");
        }
        else
        {
        	  
            if (empty($_POST['new_password']) || empty($_POST['confirm_password']))
            {
                $this->show_warning("unsettled_required");
                return ;
            }
            if (trim($_POST['new_password']) != trim($_POST['confirm_password']))
            {
                $this->show_warning("password_not_equal");
                return ;
            }
            $password = trim($_POST['new_password']);
            $passlen = strlen($password);
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            $id = intval($_GET['id']);
            $word = $this->_rand();
            $md5word = md5($word);

            $ms =& ms();        //连接用户系统
            $ms->user->edit($id, '', array('password' => $password), true); //强制修改
            if ($ms->user->has_error())
            {
                $this->show_warning($ms->user->get_error());

                return;
            }
            $ret = $this->_password_mod->edit($id, array('activation' => $md5word));

            $this->show_message("edit_success",
                'login_in', 'index.php?app=member&act=login',
                'back_index', 'index.php');
            return ;
        }

    }

	//商付通支付密码修改开始
    function set_zf_password()
    {
        if (!IS_POST)
        {
            if (!isset($_GET['id']) || !isset($_GET['activation']) || empty($_GET['activation']))
            {
                $this->show_warning("request_error",
                    'back_index', 'index.php');
                return ;
            }
            $id = intval(trim($_GET['id']));
            $activation = trim($_GET['activation']);
            $res = $this->_password_mod->get_info($id);
            if (md5($activation) != $res['activation'])
            {
                $this->show_message("invalid_link",
                    'back_index', 'index.php');
                return ;
            }
            $this->import_resource('jquery.plugins/jquery.validate.js');
            $this->display("my_money_zf.password.html");
        }
        else
        {
            if (empty($_POST['new_password']) || empty($_POST['confirm_password']))
            {
                $this->show_warning("unsettled_required");
                return ;
            }
            if (trim($_POST['new_password']) != trim($_POST['confirm_password']))
            {
                $this->show_warning("password_not_equal");
                return ;
            }
            $password = trim($_POST['new_password']);
            $passlen = strlen($password);
            if ($passlen < 6 || $passlen > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            $id = intval($_GET['id']);
            $word = $this->_rand();
            $md5word = md5($word);


            $this->_zfpassword_mod->edit('user_id='.$id, array('zf_pass' => md5($password)), true); //强制修改


            $ret = $this->_password_mod->edit($id, array('activation' => $md5word));//随机产生一个

            $this->show_message('edit_zf_success','edit_success','index.php?app=member');
            return ;
        }

    }
	//商付通支付密码修改 结束
    
    /**
     * 构造15位的随机字符串
     *
     * @return string | 生成的字符串
     */
    function _rand()
    {
        $word = $this->generate_code();
        return $word;
    }

    function generate_code($len = 15)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0, $count = strlen($chars); $i < $count; $i++)
        {
            $arr[$i] = $chars[$i];
        }

        mt_srand((double) microtime() * 1000000);
        shuffle($arr);
        $code = substr(implode('', $arr), 5, $len);
        return $code;
    }
    
    /**
     * 检查邮件地址填写是否符合规范，并到DNS验证是否存在。
     * @param 邮件地址 $mail
     */
    private function exist_mail($mail)
    {
        if(!is_email($mail)) return false;
        return  checkdnsrr(array_pop(explode('@', $mail)),'MX');
    }
}
?>
