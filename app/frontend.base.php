<?php

/**
 *    前台控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class FrontendApp extends ECBaseApp
{
    function __construct()
    {
        $this->FrontendApp();
    }
    function FrontendApp()
    {
        Lang::load(lang_file('common'));
        Lang::load(lang_file(APP));
        parent::__construct();

        // 判断商城是否关闭
        if (!Conf::get('site_status'))
        {
            $this->show_warning(Conf::get('closed_reason'));
            exit;
        }
        # 在运行action之前，无法访问到visitor对象
    }
    function _config_view()
    {
        parent::_config_view();
        $this->_view->template_dir  = ROOT_PATH . '/themes';
        $this->_view->compile_dir   = ROOT_PATH . '/temp/compiled/mall';
        $this->_view->res_base      = SITE_URL . '/themes';
        $this->_config_seo(array(
            'title' => Conf::get('site_title'),
            'description' => Conf::get('site_description'),
            'keywords' => Conf::get('site_keywords')
        ));
    }
    function display($tpl)
    {
        $cart =& m('cart');
		//获取购物车商品种类
        $this->assign('cart_goods_kinds', $cart->get_kinds(SESS_ID, is_object($this->visitor)?$this->visitor->get('user_id'):0));
		//获取购物车商品数量
		$this->assign('cart_goods_quantity', $cart->get_quantity(SESS_ID, is_object($this->visitor)?$this->visitor->get('user_id'):0));
        /* 新消息 */
        $this->assign('new_message', isset($this->visitor) ? $this->_get_new_message() : '');

        import('init.lib');
        $init = new Init_FrontendApp();
        $this->assign('carts_top', $init->_get_carts_top(SESS_ID, is_object($this->visitor)?$this->visitor->get('user_id'):0));

       /* 所有商品类目，头部通用  position: 给弹出层设置高度，使得页面效果美观 */
       $position = array('0px','-39px','-50px','-80px','-100px','-170px','-200px','-100px','-100px','-100px','-100px','-100px','-100px','-100px');
       $this->assign('header_gcategories',$init->_get_header_gcategories(0,$position,1));// 参数说明（二级分类显示数量,弹出层位置,品牌是否为推荐）


        /* 热门搜素 tyioocom */
        $this->assign('hot_keywords', $this->_get_hot_keywords());

        $this->assign('navs', $this->_get_navs());  // 自定义导航
        $this->assign('acc_help', ACC_HELP);        // 帮助中心分类code
        $this->assign('site_title', Conf::get('site_title'));
        $this->assign('site_logo', Conf::get('site_logo'));
        $this->assign('statistics_code', Conf::get('statistics_code')); // 统计代码
        $current_url = explode('/', $_SERVER['REQUEST_URI']);
        $count = count($current_url);
        $this->assign('current_url',  $count > 1 ? $current_url[$count-1] : $_SERVER['REQUEST_URI']);// 用于设置导航状态(以后可能会有问题)
        parent::display($tpl);
    }

    /* 热门搜素 tyioocom */
    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        foreach ($keywords as $key=>$value)
        {
            $keywords[$key]=explode('|', $value);
        }
        return $keywords;
    }

    function login()
    {
        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');

            return;
        }
        if (!IS_POST)
        {
            if (!empty($_GET['ret_url']))
            {
                $ret_url = trim($_GET['ret_url']);
            }
            else
            {
                if (isset($_SERVER['HTTP_REFERER']))
                {
                    $ret_url = $_SERVER['HTTP_REFERER'];
                }
                else
                {
                    $ret_url = SITE_URL . '/index.php';
                }
            }

            /* 防止登陆成功后跳转到登陆、退出的页面 */
            $ret_url = strtolower($ret_url);
            if (str_replace(array('act=login', 'act=logout',), '', $ret_url) != $ret_url)
            {
                $ret_url = SITE_URL . '/index.php';
            }

            if (Conf::get('captcha_status.login'))
            {
                $this->assign('captcha', 1);
            }
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js'));
            $this->assign('ret_url', rawurlencode($ret_url));
            $this->_curlocal(LANG::get('user_login'));
            $this->_config_seo('title', Lang::get('user_login') . ' - ' . Conf::get('site_title'));
            $this->display('login.html');
            /* 同步退出外部系统 */
            if (!empty($_GET['synlogout']))
            {
                $ms =& ms();
                echo $synlogout = $ms->user->synlogout();
            }
        }
        else
        {
            if (Conf::get('captcha_status.login') && base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
            {
                $this->show_warning('captcha_failed');

                return;
            }

            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];

            $ms =& ms();
            $user_id = $ms->user->auth($user_name, $password);
            //登陆主站的同时对token进行更新



            if (!$user_id)
            {
                /* 未通过验证，提示错误信息 */
                $this->show_warning($ms->user->get_error());
                return;
            }
            else
            {
                /* 通过验证，执行登陆操作 */
                $this->_do_login($user_id);

                /* 同步登陆外部系统 */
                $synlogin = $ms->user->synlogin($user_id);
            }

            $this->show_message(Lang::get('login_successed') . $synlogin,
                'back_before_login', rawurldecode($_POST['ret_url']),
                'enter_member_center', 'index.php?app=member'
            );
        }
    }



    //通过 api 登陆网站
    function loginApi(){


        $user_name = trim($_POST['user_name']);
        $password  = $_POST['password'];

        $ms =& ms();
        $user_id = $ms->user->auth($user_name, $password);
        if (!$user_id)
        {    $data['code'] = 500;
            /* 未通过验证，提示错误信息 */
            $data['msg'] = $ms->user->get_error();
            echo json_encode($data);
            return;
        }
        else
        {
            /* 通过验证，执行登陆操作 */
            $this->_do_login($user_id);

            /* 同步登陆外部系统 */
            $synlogin = $ms->user->synlogin($user_id);
        }
        $member_info =ms()->user->_local_get($user_id);
        $data['data'] = array('user_id'=>$user_id,'behalf' => $member_info['behalf_goods_taker']);
        $data['code'] = 0;
        $data['msg']  = 'success';
        echo json_encode($data);
        return;
    }

    function pop_warning ($msg, $dialog_id = '',$url = '')
    {
        if($msg == 'ok')
        {
            if(empty($dialog_id))
            {
                $dialog_id = APP . '_' . ACT;
            }
            if (!empty($url))
            {
                echo "<script type='text/javascript'>window.parent.location.href='".$url."';</script>";
            }
            echo "<script type='text/javascript'>window.parent.js_success('" . $dialog_id ."');</script>";
        }
        else
        {
            header("Content-Type:text/html;charset=".CHARSET);
            $msg = is_array($msg) ? $msg : array(array('msg' => $msg));
            $errors = '';
            foreach ($msg as $k => $v)
            {
                $error = $v[obj] ? Lang::get($v[msg]) . " [" . Lang::get($v[obj]) . "]" : Lang::get($v[msg]);
                $errors .= $errors ? "<br />" . $error : $error;
            }
            echo "<script type='text/javascript'>window.parent.js_fail('" . $errors . "');</script>";
        }
    }

    function logout()
    {
        $this->visitor->logout();

        /* 跳转到登录页，执行同步退出操作 */
        header("Location: index.php?app=member&act=login&synlogout=1");
        return;
    }

    /* 执行登录动作 */
    function _do_login($user_id)
    {
        $mod_user =& m('member');
        $user_info = $mod_user->get(array(
            'conditions'    => "user_id = '{$user_id}'",
            'join'          => 'has_store',                 //关联查找看看是否有店铺
           'fields'        => 'user_id, user_name, reg_time, last_login, last_ip, store_id, behalf_goods_taker as taker_id',
         //   'fields' => 'user_id, user_name, reg_time, last_login, last_ip, store_id',
        ));

        /*代发信息*/

        $model_behalf =& m('behalf');
        $behalf_info = $model_behalf->get("bh_id = '{$user_id}'");

        if(!empty($behalf_info) && !empty($user_info))
        {
            $user_info['has_behalf'] = $behalf_info['bh_id'];
            $user_info['pass_behalf'] = $behalf_info['bh_allowed'];
        }

        //token信息
        $model_token = & m('membertoken');
        $token_info = $model_token->get("user_id='{$user_id}'");
        if(!empty($token_info) && !empty($user_info)){
            $user_info['token'] = $token_info['token'];
        }else{
            //通过userid获取token  设置token

            $data = array('expires_in' => time() + 30 * 3600 * 24,
                'user_id'  => $user_id,);
            $data['token'] = md5(http_build_query($data));
            $data['behalf_id'] = $behalf_info['bh_id'];
            $model_token->add($data);
            $user_info['token'] = $data['token'];
            unset($data);

        }

        /* 店铺ID */
        $my_store = empty($user_info['store_id']) ? 0 : $user_info['store_id'];

        /* 保证基础数据整洁 */
        //unset($user_info['store_id']);

        /* 分派身份 */
        $this->visitor->assign($user_info);

        /* 更新用户登录信息 */
        $mod_user->edit("user_id = '{$user_id}'", "last_login = '" . gmtime()  . "', last_ip = '" . real_ip() . "', logins = logins + 1");

                /*商付通v2.2.1 自动注册开通 开始*/
                $db=&db();
                $my_money_row=$db->getAll("select * from ".DB_PREFIX."my_money where user_id='$user_id'");
                if(empty($my_money_row))
                {
                        $member_row=$db->getrow("select * from ".DB_PREFIX."member where user_id='$user_id'");
                        //商付通 添加自动开通
                        $my_money_mod =& m('my_money');
                        $money_data=array(
                        'user_id'=>$member_row['user_id'],
                        'user_name'=>$member_row['user_name'],
                        'zf_pass'=>'',
//			'zf_pass'=>$member_row['password'],
                        'add_time'=>gmtime(),
                        );
                        $my_money_mod->add($money_data);
                }
                /*商付通v2.2.1 自动注册开通 结束*/
        /* 更新购物车中的数据 */
        $mod_cart =& m('cart');
        $mod_cart->edit("(user_id = '{$user_id}' OR session_id = '" . SESS_ID . "') AND store_id <> '{$my_store}'", array(
            'user_id'    => $user_id,
            'session_id' => SESS_ID,
        ));

        /* 去掉重复的项 */
        $cart_items = $mod_cart->find(array(
            'conditions'    => "user_id='{$user_id}' GROUP BY spec_id",
            'fields'        => 'COUNT(spec_id) as spec_count, spec_id, rec_id',
        ));
        if (!empty($cart_items))
        {
            foreach ($cart_items as $rec_id => $cart_item)
            {
                if ($cart_item['spec_count'] > 1)
                {
                    $mod_cart->drop("user_id='{$user_id}' AND spec_id='{$cart_item['spec_id']}' AND rec_id <> {$cart_item['rec_id']}");
                }
            }
        }
    }

    /* 取得导航 */
    function _get_navs()
    {
        $cache_server =& cache_server();
        $key = 'common.navigation';
        $data = $cache_server->get($key);
        if($data === false)
        {
            $data = array(
                'header' => array(),
                'middle' => array(),
                'footer' => array(),
            );
            $nav_mod =& m('navigation');
            $rows = $nav_mod->find(array(
                'order' => 'type, sort_order',
            ));
            foreach ($rows as $row)
            {
                $data[$row['type']][] = $row;
            }
            $cache_server->set($key, $data, 86400);
        }

        return $data;
    }

    /**
     *    获取JS语言项
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function jslang()
    {
        $lang = Lang::fetch(lang_file('jslang'));
        parent::jslang($lang);
    }

    /**
     *    视图回调函数[显示小挂件]
     *
     *    @author    Garbin
     *    @param     array $options
     *    @return    void
     */
    function display_widgets($options)
    {
        $area = isset($options['area']) ? $options['area'] : '';
        $page = isset($options['page']) ? $options['page'] : '';
        if (!$area || !$page)
        {
            return;
        }
        include_once(ROOT_PATH . '/includes/widget.base.php');

        /* 获取该页面的挂件配置信息 */
        $widgets = get_widget_config($this->_get_template_name(), $page);

        /* 如果没有该区域 */
        if (!isset($widgets['config'][$area]))
        {
            return;
        }

        /* 将该区域内的挂件依次显示出来 */
        foreach ($widgets['config'][$area] as $widget_id)
        {
            $widget_info = $widgets['widgets'][$widget_id];
            $wn     =   $widget_info['name'];
            $options=   $widget_info['options'];

            $widget =& widget($widget_id, $wn, $options);
            $widget->display();
        }
    }

    /**
     *    获取当前使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        return 'default';
    }

    /**
     *    获取当前使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        return 'default';
    }

    /**
     *    当前位置
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _curlocal($arr)
    {
        $curlocal = array(array(
            'text'  => Lang::get('index'),
            'url'   => SITE_URL . '/index.php',
        ));
        if (is_array($arr))
        {
            $curlocal = array_merge($curlocal, $arr);
        }
        else
        {
            $args = func_get_args();
            if (!empty($args))
            {
                $len = count($args);
                for ($i = 0; $i < $len; $i += 2)
                {
                    $curlocal[] = array(
                        'text'  =>  $args[$i],
                        'url'   =>  $args[$i+1],
                    );
                }
            }
        }

        $this->assign('_curlocal', $curlocal);
    }
    function _init_visitor()
    {
        $this->visitor =& env('visitor', new UserVisitor());
    }

    /**
     * 首页静态化
     * by tiq
     */
    function outhtml($tpl)
    {
        $cart =& m('cart');
        $this->assign('cart_goods_kinds', $cart->get_kinds(SESS_ID, $this->visitor->get('user_id')));

        /*
        import('init.lib');
        $init = new Init_FrontendApp();
        $this->assign('carts_top', $init->_get_carts_top(SESS_ID, $this->visitor->get('user_id')));
        */
        $this->assign('index', 1);
        /* 热门搜素 tyioocom */
        $this->assign('hot_keywords', $this->_get_hot_keywords());
        /*站点城市*/

        $this->assign('currentCitysite',$this->_get_current_citysite());
        $this->assign('currentCitysiteName',$this->_get_current_citysitename());
        $this->assign('citysiteList',$this->_get_citysites_except_current_citysite());
        //echo "city#";
        $this->assign('navs', $this->_get_navs());  // 自定义导航
        $this->assign('acc_help', ACC_HELP);        // 帮助中心分类code
        $this->assign('site_title', Conf::get('site_title'));
        $this->assign('site_logo', Conf::get('site_logo'));
        $this->assign('statistics_code', Conf::get('statistics_code')); // 统计代码
       // echo "statics_code#";
        $current_url = explode('/', $_SERVER['REQUEST_URI']);
        $count = count($current_url);
        $this->assign('current_url',  $count > 1 ? $current_url[$count-1] :$_SERVER['REQUEST_URI']);// 用于设置导航状态(以后可能会有问题)
        //echo $count."<br>";
        return parent::outhtml($tpl);
    }
}
/**
 *    前台访问者
 *
 *    @author    Garbin
 *    @usage    none
 */
class UserVisitor extends BaseVisitor
{
    var $_info_key = 'user_info';

    /**
     *    退出登录
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function logout()
    {
        /* 将购物车中的相关项的session_id置为空 */
        $mod_cart =& m('cart');
        $mod_cart->edit("user_id = '" . $this->get('user_id') . "'", array(
            'session_id' => '',
        ));

        /* 退出登录 */
        parent::logout();
    }
}
/**
 *    商城控制器基类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MallbaseApp extends FrontendApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && in_array(APP, array('apply','bhapply',"cart")))
        {

             if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please','user_not_login');//user_not_login 在页面知道是用户没登录 by tiq 2015-04-26
                return;
            }
        }

        parent::_run_action();
    }

    function _config_view()
    {
        parent::_config_view();

        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/themes/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}";
    }

    /* 取得支付方式实例 */
    function _get_payment($code, $payment_info)
    {
        include_once(ROOT_PATH . '/includes/payment.base.php');
        include(ROOT_PATH . '/includes/payments/' . $code . '/' . $code . '.payment.php');
        $class_name = ucfirst($code) . 'Payment';

        return new $class_name($payment_info);
    }

    /**
     *   获取当前所使用的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $template_name = Conf::get('template_name');
        if (!$template_name)
        {
            $template_name = 'default';
        }

        return $template_name;
    }

    /**
     *    获取当前模板中所使用的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $style_name = Conf::get('style_name');
        if (!$style_name)
        {
            $style_name = 'default';
        }

        return $style_name;
    }
}

/**
 *    购物流程子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class ShoppingbaseApp extends MallbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }

        parent::_run_action();
    }
}

/**
 *    用户中心子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class MemberbaseApp extends MallbaseApp
{
    function _run_action()
    {
        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register','returnurlThree', 'check_user', 'has_login', 'loginWithTaobao', 'taobaoAuthBack', 'loginWithAlibaba', 'alibabaAuthBack', 'direct_alipay_return', 'direct_alipay_notify','loginApi')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please','user_not_login');//user_not_login 在页面知道是用户没登录 by tiq 2015-04-26
                return;
            }
        }

        parent::_run_action();
    }
    /**
     *    当前选中的菜单项
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curitem($item)
    {
        $this->assign('has_store', $this->visitor->get('has_store'));
        $this->assign('_member_menu', $this->_get_member_menu());
        $this->assign('_curitem', $item);
    }
    /**
     *    当前选中的子菜单
     *
     *    @author    Garbin
     *    @param     string $item
     *    @return    void
     */
    function _curmenu($item)
    {
        $_member_submenu = $this->_get_member_submenu();
        foreach ($_member_submenu as $key => $value)
        {
            $_member_submenu[$key]['text'] = $value['text'] ? $value['text'] : Lang::get($value['name']);
        }
        $this->assign('_member_submenu', $_member_submenu);
        $this->assign('_curmenu', $item);
    }
    /**
     *    获取子菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_submenu()
    {
        return array();
    }
    /**
     *    获取用户中心全局菜单列表
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function _get_member_menu()
    {
        $menu = array();

        /* 我的ECMall */
        $menu['my_ecmall'] = array(
            'name'  => 'my_ecmall',
            'text'  => Lang::get('my_ecmall'),
            'submenu'   => array(
                'overview'  => array(
                    'text'  => Lang::get('overview'),
                    'url'   => 'index.php?app=member',
                    'name'  => 'overview',
                    'icon'  => 'ico1',
                ),
                'my_profile'  => array(
                    'text'  => Lang::get('my_profile'),
                    'url'   => 'index.php?app=member&act=profile',
                    'name'  => 'my_profile',
                    'icon'  => 'ico2',
                ),
                'message'  => array(
                    'text'  => Lang::get('message'),
                    'url'   => 'index.php?app=message&act=newpm',
                    'name'  => 'message',
                    'icon'  => 'ico3',
                ),
               /*  'friend'  => array(
                    'text'  => Lang::get('friend'),
                    'url'   => 'index.php?app=friend',
                    'name'  => 'friend',
                    'icon'  => 'ico4',
                ), */
                'behalf_signed'  => array(
                                        'text'  => Lang::get('behalf_signed'),
                                        'url'   => 'index.php?app=my_favorite&type=sbehalf',
                                        'name'  => 'behalf_signed',
                                        'icon'  => 'ico16',
                ),
                /*
                'my_credit'  => array(
                    'text'  => Lang::get('my_credit'),
                    'url'   => 'index.php?app=member&act=credit',
                    'name'  => 'my_credit',
                ),*/
//                                'jifenduihuan'  => array(
//                    'text'  => '积分管理',
//                    'url'   => 'index.php?app=my_money&act=jifen',
//                    'name'  => 'jifenduihuan',
//                    'icon'  => 'ico6',
//                                        ),
//                                'jiaoyichaxun'  => array(
//                    'text'  => '资金管理',
//                    'url'   => 'index.php?app=my_money&act=loglist',
//                    'name'  => 'jiaoyichaxun',
//                    'icon'  => 'ico13',
//                ),
            ),
        );

/* 商付通v2.2.1 导航开始 */
        $menu['shangfutong'] = array(
            'name'  => 'shangfutong',
            'text'  => Lang::get('shangfutong'),
            'submenu'   => array(

                'jiaoyichaxun'  => array(
                    'text'  => Lang::get('jiaoyichaxun'),
                    'url'   => 'index.php?app=my_money&act=loglist',
                    'name'  => 'jiaoyichaxun',
                    'icon'  => 'ico5',
                ),

                'chongzhichaxun'  => array(
                    'text'  => Lang::get('chongzhichaxun'),
                    'url'   => 'index.php?app=my_money&act=paylist',
                    'name'  => 'chongzhichaxun',
                    'icon'  => 'ico13',
                ),

                'tixianshenqing'  => array(
                    'text'  => Lang::get('tixianshenqing'),
                    'url'   => 'index.php?app=my_money&act=txlist',
                    'name'  => 'tixianshenqing',
                    'icon'  => 'ico6',
                ),

                'zhanghushezhi'  => array(
                    'text'  => Lang::get('zhanghushezhi'),
                    'url'   => 'index.php?app=my_money&act=mylist',
                    'name'  => 'zhanghushezhi',
                    'icon'  => 'ico11',
                ),
            ),
        );
        /* 商付通 导航结束 */

        /* 我是买家 */
        $menu['im_buyer'] = array(
            'name'  => 'im_buyer',
            'text'  => Lang::get('im_buyer'),
            'submenu'   => array(
                'my_order'  => array(
                    'text'  => Lang::get('my_order'),
                    'url'   => 'index.php?app=buyer_order',
                    'name'  => 'my_order',
                    'icon'  => 'ico5',
                ),
                'order_manage_taobao' => array(
                    'text'  => Lang::get('order_manage_taobao'),
                    'url'   => 'index.php?app=taobao_order&vendor=0',
                    'name'  => 'order_manage_taobao',
                    'icon'  => 'ico10',
                ),
                //import order add by tanaiquan 2015-07-05
                /* 'my_im_order'=>array(
                    'text'=>Lang::get('my_im_order'),
                    'url'=>'index.php?app=buyer_order&act=im_order',
                    'name'=>'my_im_order',
                    'icon'=>'ico22'
                ),
                'order_manage_import' => array(
                    'text' => Lang::get('order_manage_import'),
                    'url'   => 'index.php?app=taobao_order&vendor=1',
                    'name'  => 'order_manage_import',
                    'icon'  => 'ico10',
                ), */
//                'my_groupbuy'  => array(
//                    'text'  => Lang::get('my_groupbuy'),
//                    'url'   => 'index.php?app=buyer_groupbuy',
//                    'name'  => 'my_groupbuy',
//                    'icon'  => 'ico21',
//                ),
//                'my_question' =>array(
//                    'text'  => Lang::get('my_question'),
//                    'url'   => 'index.php?app=my_question',
//                    'name'  => 'my_question',
//                    'icon'  => 'ico17',
//
//                ),
                'my_favorite'  => array(
                    'text'  => Lang::get('my_favorite'),
                    'url'   => 'index.php?app=my_favorite',
                    'name'  => 'my_favorite',
                    'icon'  => 'ico6',
                ),
                'my_address'  => array(
                    'text'  => Lang::get('my_address'),
                    'url'   => 'index.php?app=my_address',
                    'name'  => 'my_address',
                    'icon'  => 'ico7',
                ),
                'my_excel'  => array(
                    'text'  => Lang::get('my_excel'),
                    'url'   => 'index.php?app=my_excel',
                    'name'  => 'my_excel',
                    'icon'  => 'ico8',
                ),
//                'my_coupon'  => array(
//                    'text'  => Lang::get('my_coupon'),
//                    'url'   => 'index.php?app=my_coupon',
//                    'name'  => 'my_coupon',
//                    'icon'  => 'ico20',
//                ),
            ),
        );

        /*我是拿货员 */
       /*  if($this->visitor->get('taker_id'))
        {
            $menu['im_taker']= array(
                    'name' => 'im_taker',
                    'text' => Lang::get('im_taker'),
                    'submenu' => array(
                            'taker_system'=>array(
                                    'text'=> Lang::get('taker_system'),
                                    'url' => 'index.php?module=behalf',
                                    'name' => 'taker_system',
                                    'icon' => 'ico5'
                            )
                    )
            );
        } */

        /*我要备货 tanaiquan 2015-10-21*/
        /*
        $menu['im_stock']= array(
            'name' => 'im_stock',
            'text' => Lang::get('im_stock'),
            'submenu' => array(
                'stock_order'=>array(
                   'text'=> Lang::get('stock_order'),
                    'url' => 'index.php?app=my_stock',
                    'name' => 'stock_order',
                    'icon' => 'ico5'
                ),
                'my_warehouse'=>array(
                    'text'=> Lang::get('my_warehouse'),
                    'url'=> 'index.php?app=my_warehouse',
                    'name'=>'my_warehouse',
                    'icon'=>'ico11'
                ),
                'stock_notice'=>array(
                    'text'=>Lang::get('stock_notice'),
                    'url'=>'',
                    'name'=>'stock_notice',
                    'icon'=>'ico12'
                )
            )
        );*/


        if (!$this->visitor->get('has_store') && Conf::get('store_allow'))
        {
            /* 没有拥有店铺，且开放申请，则显示申请开店链接 */
            /*$menu['im_seller'] = array(
                'name'  => 'im_seller',
                'text'  => Lang::get('im_seller'),
                'submenu'   => array(),
            );

            $menu['im_seller']['submenu']['overview'] = array(
                'text'  => Lang::get('apply_store'),
                'url'   => 'index.php?app=apply',
                'name'  => 'apply_store',
            );*/
            $menu['overview'] = array(
                'text' => Lang::get('apply_store'),
                'url'  => 'index.php?app=apply');
        }
        if ($this->visitor->get('has_store'))
        {
            $store_mod =& m('store');
            $store = $store_mod->get($this->visitor->get('user_id'));
            if ($store['state'] == '2' && $store['close_reason'] == 'rules')
            {
                $menu['restore'] = array(
                    'text' => Lang::get('restore_store'),
                    'url'  => 'index.php?app=restore');
            }
        }
        /*用户中心菜单是否开户代发申请*/
        if(!$this->visitor->get('has_behalf') && Conf::get('behalf_allow'))
        {
                $menu['has_behalf'] = array(
                                'text' => Lang::get('apply_behalf'),
                                'url' => 'index.php?app=bhapply',
                );
        }
        if($this->visitor->get('has_behalf') && !$this->visitor->get('pass_behalf'))
        {
                $menu['wait_behalf'] = array(
                                        'text' => Lang::get('wait_behalf')
                        );
        }

        if($this->visitor->get('taker_id') || $this->visitor->get('pass_behalf'))
        {
            $menu['behalf_system'] = array(
                    'text' => Lang::get('behalf_system'),
                    'url' => 'index.php?module=behalf',
            );
        }

        /* 代发菜单开始*/
        if($this->visitor->get('pass_behalf'))
        {
                $menu['im_behalf'] = array(
                                'name'  => 'im_behalf',
                                'text'  => Lang::get('im_behalf'),
                                'submenu'   => array(
                                                /* 'behalf_order'  => array(
                                                                'text'  => Lang::get('behalf_order'),
                                                                'url'   => 'index.php?app=behalf_member',
                                                                'name'  => 'my_order',
                                                                'icon'  => 'ico10',
                                                ), */
                                                /* 'behalf_delivery'  => array(
                                                                'text'  => Lang::get('behalf_delivery'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_delivery',
                                                                'name'  => 'behalf_delivery',
                                                                'icon'  => 'ico9',
                                                ),
                                                 'my_behalf_delivery_fee'  => array(
                                                                'text'  => Lang::get('my_behalf_delivery_fee'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_delivery_fee',
                                                                'name'  => 'behalf_delivery_fee',
                                                                'icon'  => 'ico22',
                                                ),
                                                'my_behalf_market'  => array(
                                                                'text'  => Lang::get('my_behalf_market'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_behalf_market',
                                                                'name'  => 'my_behalf_market',
                                                                'icon'  => 'ico14',
                                                ), */
                                                'my_behalfpayment' => array(
                                                                                        'text'  => Lang::get('my_payment'),
                                                                                        'url'   => 'index.php?app=my_behalf_payment',
                                                                                        'name'  => 'my_payment',
                                                                                        'icon'  => 'ico13',
                                                )
                                               /*  'my_behalf_account'  => array(
                                                                'text'  => Lang::get('my_behalf_account'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_behalf_account',
                                                                'name'  => 'my_behalf_account',
                                                                'icon'  => 'ico12',
                                                ),
                                                'my_behalf'  => array(
                                                                'text'  => Lang::get('my_behalf'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_behalf',
                                                                'name'  => 'my_behalf',
                                                                'icon'  => 'ico11',
                                                ),
                                                'my_behalf_print'  => array(
                                                                'text'  => Lang::get('my_behalf_print'),
                                                                'url'   => 'index.php?app=behalf_print&act=index',
                                                                'name'  => 'my_behalf_print',
                                                                'icon'  => 'printFav',
                                                                'target'=>'_blank'
                                                ),
                                                'new_behalf_system'  => array(
                                                                'text'  => Lang::get('new_behalf_system'),
                                                                'url'   => 'index.php?module=behalf',
                                                                'name'  => 'new_behalf_system',
                                                                'icon'  => 'ico16',
                                                                'target'=>'_blank'
                                                ), */
                                                /*
                                                'behalf_question' =>array(
                                                                'text'  => Lang::get('behalf_question'),
                                                                'url'   => 'index.php?app=behalf_question',
                                                                'name'  => 'my_question',
                                                                'icon'  => 'ico18',

                                                ),
                                                'behalf_fee'  => array(
                                                                'text'  => Lang::get('behalf_fee'),
                                                                'url'   => 'index.php?app=behalf_member&act=set_fee',
                                                                'name'  => 'my_behalf_fee',
                                                                'icon'  => 'ico7',
                                                ),*/
                                                /* 'behalf_coupon'  => array(
                                                                'text'  => Lang::get('behalf_coupon'),
                                                                'url'   => 'index.php?app=behalf_coupon',
                                                                'name'  => 'my_coupon',
                                                                'icon'  => 'ico19',
                                                ), */
                                ),
                );
        }
        /*代发菜单结束*/
        if ($this->visitor->get('manage_store'))
        {
            /* 指定了要管理的店铺 */
            $menu['im_seller'] = array(
                'name'  => 'im_seller',
                'text'  => Lang::get('im_seller'),
                'submenu'   => array(),
            );

            $menu['im_seller']['submenu']['my_goods'] = array(
                    'text'  => Lang::get('my_goods'),
                    'url'   => 'index.php?app=my_goods',
                    'name'  => 'my_goods',
                    'icon'  => 'ico8',
            );
//            $menu['im_seller']['submenu']['groupbuy_manage'] = array(
//                    'text'  => Lang::get('groupbuy_manage'),
//                    'url'   => 'index.php?app=seller_groupbuy',
//                    'name'  => 'groupbuy_manage',
//                    'icon'  => 'ico22',
//            );
//            $menu['im_seller']['submenu']['my_qa'] = array(
//                    'text'  => Lang::get('my_qa'),
//                    'url'   => 'index.php?app=my_qa',
//                    'name'  => 'my_qa',
//                    'icon'  => 'ico18',
//            );
            $menu['im_seller']['submenu']['my_category'] = array(
                    'text'  => Lang::get('my_category'),
                    'url'   => 'index.php?app=my_category',
                    'name'  => 'my_category',
                    'icon'  => 'ico9',
            );
            $menu['im_seller']['submenu']['order_manage'] = array(
                    'text'  => Lang::get('order_manage'),
                    'url'   => 'index.php?app=seller_order',
                    'name'  => 'order_manage',
                    'icon'  => 'ico10',
            );
            $menu['im_seller']['submenu']['my_store']  = array(
                    'text'  => Lang::get('my_store'),
                    'url'   => 'index.php?app=my_store',
                    'name'  => 'my_store',
                    'icon'  => 'ico11',
            );
            $menu['im_seller']['submenu']['my_theme']  = array(
                    'text'  => Lang::get('my_theme'),
                    'url'   => 'index.php?app=my_theme',
                    'name'  => 'my_theme',
                    'icon'  => 'ico12',
            );
            $menu['im_seller']['submenu']['my_payment'] =  array(
                    'text'  => Lang::get('my_payment'),
                    'url'   => 'index.php?app=my_payment',
                    'name'  => 'my_payment',
                    'icon'  => 'ico13',
            );
            /* 屏蔽掉原来的配送方式，改为 运费模板 psmb
            $menu['im_seller']['submenu']['my_shipping'] = array(
                    'text'  => Lang::get('my_shipping'),
                    'url'   => 'index.php?app=my_shipping',
                    'name'  => 'my_shipping',
                    'icon'  => 'ico14',
            );
            */
            $menu['im_seller']['submenu']['my_delivery'] = array(
                    'text'  => Lang::get('my_delivery'),
                    'url'   => 'index.php?app=my_delivery',
                    'name'  => 'my_delivery',
                    'icon'  => 'ico14',
            );

            $menu['im_seller']['submenu']['my_navigation'] = array(
                    'text'  => Lang::get('my_navigation'),
                    'url'   => 'index.php?app=my_navigation',
                    'name'  => 'my_navigation',
                    'icon'  => 'ico15',
            );
            $menu['im_seller']['submenu']['my_partner']  = array(
                    'text'  => Lang::get('my_partner'),
                    'url'   => 'index.php?app=my_partner',
                    'name'  => 'my_partner',
                    'icon'  => 'ico16',
            );
//            $menu['im_seller']['submenu']['coupon']  = array(
//                    'text'  => Lang::get('coupon'),
//                    'url'   => 'index.php?app=coupon',
//                    'name'  => 'coupon',
//                    'icon'  => 'ico19',
//            );
            $menu['im_seller']['submenu']['数据包管理'] = array(
                'text' => Lang::get('my_datapack'),
                'url' => 'index.php?app=my_datapack',
                'name' => 'my_datapack',
                'icon' => 'ico22');
            $menu['im_seller']['submenu']['my_stalllease'] = array(
                'text' => Lang::get('my_stalllease'),
                'url' => 'index.php?app=my_stalllease',
                'name' => 'my_stalllease',
                'icon' => 'ico5');

            //卖家推荐精品
            if(exist_brandarea($this->visitor->get('user_id')))
            {
                $menu['im_seller']['submenu']['my_recommend_brandarea'] = array(
                    'text' => Lang::get('my_recommend_brandarea'),
                    'url' => 'index.php?app=my_goods&act=recommend_brandarea',
                    'name' => 'my_recommend_brandarea',
                    'icon' => 'ico8');
            }

        }


        return $menu;
    }
}

/**
 *    店铺管理子系统基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StoreadminbaseApp extends MemberbaseApp
{
    function _run_action()
    {
        // FIXME: do not hard code like this!!!
        if (APP == 'taobao_order') {
            parent::_run_action();
            return;
        }

        /* 只有登录的用户才可访问 */
        if (!$this->visitor->has_login && !in_array(ACT, array('login', 'register', 'check_user')))
        {
            if (!IS_AJAX)
            {
                header('Location:index.php?app=member&act=login&ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

                return;
            }
            else
            {
                $this->json_error('login_please');
                return;
            }
        }
        $referer = $_SERVER['HTTP_REFERER'];
        if (strpos($referer, 'act=login') === false)
        {
            $ret_url = $_SERVER['HTTP_REFERER'];
            $ret_text = 'go_back';
        }
        else
        {
            $ret_url = SITE_URL . '/index.php';
            $ret_text = 'back_index';
        }

        /* 检查是否是店铺管理员 */
        if (!$this->visitor->get('manage_store'))
        {
            /* 您不是店铺管理员 */
            $this->show_warning(
                'not_storeadmin',
                'apply_now', 'index.php?app=apply',
                $ret_text, $ret_url
            );

            return;
        }

        /* 检查是否被授权 */
        $privileges = $this->_get_privileges();
        if (!$this->visitor->i_can('do_action', $privileges))
        {
            $this->show_warning('no_permission', $ret_text, $ret_url);

            return;
        }

        /* 检查店铺开启状态 */
        $state = $this->visitor->get('state');
        if ($state == 0)
        {
            $this->show_warning('apply_not_agree', $ret_text, $ret_url);

            return;
        }
        elseif ($state == 2)
        {
            $this->show_warning('store_is_closed', $ret_text, $ret_url);

            return;
        }

        /* 检查附加功能 */
        if (!$this->_check_add_functions())
        {
            $this->show_warning('not_support_function', $ret_text, $ret_url);
            return;
        }

        parent::_run_action();
    }
    function _get_privileges()
    {
        $store_id = $this->visitor->get('manage_store');
        $privs = $this->visitor->get('s');

        if (empty($privs))
        {
            return '';
        }

        foreach ($privs as $key => $admin_store)
        {
            if ($admin_store['store_id'] == $store_id)
            {
                return $admin_store['privs'];
            }
        }
    }

    /* 获取当前店铺所使用的主题 */
    function _get_theme()
    {
        $model_store =& m('store');
        $store_info  = $model_store->get($this->visitor->get('manage_store'));
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($curr_template_name, $curr_style_name) = explode('|', $theme);
        return array(
            'template_name' => $curr_template_name,
            'style_name'    => $curr_style_name,
        );
    }

    function _check_add_functions()
    {
        $apps_functions = array( // app与function对应关系
            'seller_groupbuy' => 'groupbuy',
            'coupon' => 'coupon',
        );
        if (isset($apps_functions[APP]))
        {
            $store_mod =& m('store');
            $settings = $store_mod->get_settings($this->_store_id);
            $add_functions = isset($settings['functions']) ? $settings['functions'] : ''; // 附加功能
            if (!in_array($apps_functions[APP], explode(',', $add_functions)))
            {
                return false;
            }
        }
        return true;
    }
}

/**
 *    店铺控制器基础类
 *
 *    @author    Garbin
 *    @usage    none
 */
class StorebaseApp extends FrontendApp
{
    var $_store_id;

    /**
     * 设置店铺id
     *
     * @param int $store_id
     */
    function set_store($store_id)
    {
        $this->_store_id = intval($store_id);

        /* 有了store id后对视图进行二次配置 */
        $this->_init_view();
        $this->_config_view();
    }

    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();

        $this->_view->template_dir = ROOT_PATH . "/themes/store/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/store/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/themes/store/{$template_name}/styles/{$style_name}";
    }

    /**
     * 取得店铺信息
     */
    function get_store_data()
    {
        $cache_server =& cache_server();
        $key = 'function_get_store_data_' . $this->_store_id;
        $store = $cache_server->get($key);
        if ($store === false)
        {
            $store = $this->_get_store_info();
            if (empty($store))
            {
                $this->show_warning('the_store_not_exist');
                exit;
            }
            if ($store['state'] == 2)
            {
                $this->show_warning('the_store_is_closed');
                exit;
            }
            $step = intval(Conf::get('upgrade_required'));
            $step < 1 && $step = 5;
            $store_mod =& m('store');
            $store['credit_image'] = $this->_view->res_base . '/images/' . $store_mod->compute_credit($store['credit_value'], $step);

            //
            $store['rates'] = $store_mod->get_store_rates($store['store_id']);

            empty($store['store_logo']) && $store['store_logo'] = Conf::get('default_store_logo');
            $store['store_owner'] = $this->_get_store_owner();
            $store['store_navs']  = $this->_get_store_nav();
            $goods_mod =& m('goods');
            $store['goods_count'] = $goods_mod->get_count_of_store($this->_store_id);
            $store['store_gcates']= $this->_get_store_gcategory();
            $store['sgrade'] = $this->_get_store_grade('grade_name');
            $functions = $this->_get_store_grade('functions');
            $store['functions'] = array();
            if ($functions)
            {
                $functions = explode(',', $functions);
                foreach ($functions as $k => $v)
                {
                    $store['functions'][$v] = $v;
                }
            }
                        // cancel by tiq
                        /* $store['hot_saleslist'] = $this->_get_hot_saleslist();
                        $store['collect_goodslist'] = $this->_get_collect_goods();
                        $store['left_rec_goods'] = $this->_get_left_rec_goods($this->_store_id); */

                        /*店铺上新与下架统计*/
                        //$store['newclose_goods_static'] = $this->_get_newclose_goods_num($this->_store_id);

                        if(!empty($store['hot_search'])) {
                                $store['hot_search'] = explode(' ', $store['hot_search']);
                        }

                        $online_service = array();
                        if(isset($store['im_qq']) && !empty($store['im_qq'])){
                                $online_service['qq'][] = $store['im_qq'];
                        }
                        if(isset($store['im_ww']) && !empty($store['im_ww'])){
                                $online_service['ww'][] = $store['im_ww'];
                        }
                        if(!empty($store['online_service']))
                        {
                                $qqww = explode('|', $store['online_service']);
                                foreach($qqww as $key=>$val){
                                        if(!empty($val)){
                                                foreach(explode(';', $val) as $v){
                                                        if(!empty($v)){
                                                                $online_service[$key==0?'qq':'ww'][] = $v;
                                                        }
                                                }
                                        }
                                }
                                unset($store['online_service']);
                        }
                        $store['online_service'] = $online_service;


                        if(!empty($store['pic_slides'])){
                                $pic_slides = array();
                                $store['pic_slides'] = json_decode($store['pic_slides'],true);
                        }

                        $store['services'] = '';
                        $this->_handle_services($store, 'serv_refund', '退现金');
                        $this->_handle_services($store, 'serv_exchgoods', '可换货');
                        $this->_handle_services($store, 'serv_realpic', '实拍');
                        $this->_handle_services($store, 'serv_modpic', '模特实拍');
                        $this->_handle_services($store, 'serv_deltpic', '细节实拍');



            $cache_server->set($key, $store, 1800);
        }

        return $store;

    }

    /*取得n天内上新与下架商品ids*/
    function _get_newclose_goods_num($id)
    {
        $datearray = array();
        for($i=0;$i<5;$i++)
        {
                $day_start = gmmktime(0,0,0,date('m'),date('d')-$i,date('Y'));
                $day_end = gmmktime(0,0,0,date('m'),date('d')+1-$i,date('Y'))-1;
                $datearray[$i]['day_start'] = $day_start;
                $datearray[$i]['day_end'] = $day_end;
                $datearray[$i]['show_date'] = date('Y-m-d',$day_start);
        }

        $goods_mod =& bm('goods', array('_store_id' => $id));

        foreach ($datearray as $key=>$value)
        {
            $conditions = "";
            $conditions .= " AND add_time >= {$value['day_start']} AND add_time <= {$value['day_end']}";
            /*上新*/
            $new_goods_list = $goods_mod->find(array(
               'conditions' => "closed = 0 AND if_show = 1 AND default_spec > 0 ".$conditions,
               'fields'     => 'goods_id',
                'order'      => 'add_time desc',
            ));
            $new_goods_ids = "";
            if(!empty($new_goods_list))
            {
                foreach ($new_goods_list as $nval)
                {
                    $new_goods_ids .= "{$nval['goods_id']}".",";
                }
                $new_goods_ids = rtrim($new_goods_ids,",");
            }
            $datearray[$key]['new_goods_nums'] = count($new_goods_list);
            $datearray[$key]['new_goods_ids'] = $new_goods_ids;
            /*下架*/
            $close_goods_list = $goods_mod->find(array(
                        'conditions' => "closed = 0 AND if_show = 0 AND default_spec > 0 ".$conditions,
                        'fields'     => 'goods_id',
                        'order'      => 'add_time desc',
            ));
            $close_goods_ids = "";
            if(!empty($close_goods_list))
            {
                foreach ($close_goods_list as $cval)
                {
                        $close_goods_ids .= "{$cval['goods_id']}".",";
                }
                $close_goods_ids = rtrim($close_goods_ids,",");
            }
            $datearray[$key]['close_goods_nums'] = count($close_goods_list);
            $datearray[$key]['close_goods_ids'] = $close_goods_ids;

        }
        return $datearray;
   }

        function _handle_services($store, $service, $text) {
            if ($store[$service] == 1) {
                $store['services'] = $store['services'].$text.' ';
            }
        }

        function _get_hot_saleslist()
        {
           if (!$this->_store_id)
           {
              return array();
           }
           $goods_mod =& m('goods');
       $data = $goods_mod->find(array(
           'conditions' => "if_show = 1 AND store_id = '{$this->_store_id}' AND closed = 0 ",
           'order' => 'sales DESC',
           'fields' => 'g.goods_id, g.goods_name,goods.default_image,g.price,goods_statistics.sales',
           'join' => 'has_goodsstatistics',
           'limit' => 10,
       ));
           return $data;
        }
        function _get_collect_goods()
        {
        $goods_mod =& m('goods');
        $data = $goods_mod->find(array(
            'conditions' => "if_show = 1 AND store_id = '{$this->_store_id}' AND closed = 0 ",
            'order' => 'collects DESC',
                        'fields' => 'g.goods_id, g.goods_name,g.default_image,g.price,goods_statistics.collects',
                        'join'  => 'has_goodsstatistics',
            'limit' => 10,
        ));
                return $data;
        }
        function _get_left_rec_goods($id, $num = 5)
        {
                $goods_mod =& bm('goods', array('_store_id' => $id));
        $goods_list = $goods_mod->find(array(
            'conditions' => "closed = 0 AND if_show = 1",
                        'join'           => 'has_goodsstatistics',
            'fields'     => 'goods_name, default_image, price,sales',
            'order'      => 'collects desc, views desc,comments desc,sales desc,add_time desc',
            'limit'      => $num,
        ));
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
        }
        return $goods_list;
        }

    /* 取得店铺信息 */
    function _get_store_info()
    {
        if (!$this->_store_id)
        {
            /* 未设置前返回空 */
            return array();
        }
        static $store_info = null;
        if ($store_info === null)
        {
            $store_mod  =& m('store');
            $store_info = $store_mod->get_info($this->_store_id);
        }

        return $store_info;
    }

    /* 取得店主信息 */
    function _get_store_owner()
    {
        $user_mod =& m('member');
        $user = $user_mod->get($this->_store_id);

        return $user;
    }

    /* 取得店铺导航 */
    function _get_store_nav()
    {
        $article_mod =& m('article');
        return $article_mod->find(array(
            'conditions' => "store_id = '{$this->_store_id}' AND cate_id = '" . STORE_NAV . "' AND if_show = 1",
            'order' => 'sort_order',
            'fields' => 'title',
        ));
    }
    /*  取的店铺等级   */

    function _get_store_grade($field)
    {
        $store_info = $store_info = $this->_get_store_info();
        $sgrade_mod =& m('sgrade');
        $result = $sgrade_mod->get_info($store_info['sgrade']);
        return $result[$field];
    }
    /* 取得店铺分类 */
    function _get_store_gcategory()
    {
        $gcategory_mod =& bm('gcategory', array('_store_id' => $this->_store_id));
        $gcategories = $gcategory_mod->get_list(-1, true);
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getArrayList(0);
    }

    /**
     *    获取当前店铺所设定的模板名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_template_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $template_name;
    }

    /**
     *    获取当前店铺所设定的风格名称
     *
     *    @author    Garbin
     *    @return    string
     */
    function _get_style_name()
    {
        $store_info = $this->_get_store_info();
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($template_name, $style_name) = explode('|', $theme);

        return $style_name;
    }
}

/* 实现消息基础类接口 */
class MessageBase extends MallbaseApp {};

/* 实现模块基础类接口 */
class BaseModule  extends FrontendApp {};

/* 消息处理器 */
require(ROOT_PATH . '/eccore/controller/message.base.php');

?>
