<?php

/* 店铺控制器 */
class StoreApp extends BackendApp
{
    var $_store_mod;

    function __construct()
    {
        $this->StoreApp();
    }

    function StoreApp()
    {
        parent::__construct();
        $this->_store_mod =& m('store');
    }

    function index()
    {
        $conditions = empty($_GET['wait_verify']) ? "s.state <> '" . STORE_APPLYING . "'" : "s.state = '" . STORE_APPLYING . "'";
        $filter = $this->_get_query_conditions(array(
            array(
                'field' => 'store_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'im_ww',
                'equal' => 'like',
            ),
            array(
                'field' => 'sgrade',
            ),
        ));
        $owner_name = trim($_GET['owner_name']);
        if ($owner_name)
        {

            $filter .= " AND (user_name LIKE '%{$owner_name}%' OR owner_name LIKE '%{$owner_name}%') ";
        }
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'sort_order';
                $order = '';
            }
        }
        else
        {
            $sort  = 'sort_order';//'store_id';
            $order = 'asc';
        }

        $this->assign('filter', $filter);
        $conditions .= $filter;
        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions.' AND s.state <>'.STORE_CLOSED,
            'join'  => 'belongs_to_user,has_storebehalfarea,has_storerealityzone,has_storebrandarea,has_storebehalfchoice,has_storehm',
            'fields'=> 'this.*,member.user_name,sbehalfarea.state as behalf_area,srealityzone.state as realityzone,sbrandarea.state as brand_area,sbehalfchoice.state as behalf_choice,shm.state as behalf_hm',
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order,store_id desc"
        ));
        $sgrade_mod =& m('sgrade');
        $grades = $sgrade_mod->get_options();
        $this->assign('sgrades', $grades);

        $states = array(
            STORE_APPLYING  => LANG::get('wait_verify'),
            STORE_OPEN      => Lang::get('open'),
            STORE_CLOSED    => Lang::get('close'),
        );
        foreach ($stores as $key => $store)
        {
            $stores[$key]['sgrade'] = $grades[$store['sgrade']];
            $stores[$key]['state'] = $states[$store['state']];
            $certs = empty($store['certification']) ? array() : explode(',', $store['certification']);
            for ($i = 0; $i < count($certs); $i++)
            {
                $certs[$i] = Lang::get($certs[$i]);
            }
            $stores[$key]['certification'] = join('<br />', $certs);
        }
        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);

        $this->display('store.index.html');
    }

    /**
     * 代发区店铺管理
     */
    function behalf_area()
    {
        $conditions = empty($_GET['wait_verify']) ? "sbehalfarea.state <> '0'" : "sbehalfarea.state = '0'";

        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'has_storebehalfarea',
            'fields'=> 'this.*,sbehalfarea.state as bastate',
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'sort_order asc,store_id desc'

        ));

        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('store.behalfarea.html');
    }
    /**
     * 精选区店铺管理
     */
    function behalf_choice()
    {
        $conditions = empty($_GET['wait_verify']) ? "sbehalfchoice.state <> '0'" : "sbehalfchoice.state = '0'";

        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'has_storebehalfchoice',
            'fields'=> 'this.*,sbehalfchoice.state as bcstate',
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'sort_order asc,store_id desc'

        ));

        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('store.behalfchoice.html');
    }
    /**
     * 清除代发区档口
     */
    function drop_behalfarea()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }
        
        $ids = explode(',', $id);
       
        $model_storebehalfarea =& m('storebehalfarea');
        
        db()->query("DELETE FROM ".$model_storebehalfarea->table." WHERE store_id ".db_create_in($ids));             
        
        $this->show_message('drop_ok');
    }
    /**
     * 清除精选代发区档口
     */
    function drop_behalfchoice()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }
        
        $ids = explode(',', $id);
       
        $model_storebehalfchoice =& m('storebehalfchoice');
        
        db()->query("DELETE FROM ".$model_storebehalfchoice->table." WHERE store_id ".db_create_in($ids));             
        
        $this->show_message('drop_ok');
    }
    
    /**
     * 精品区店铺管理
     */
    function brand_area()
    {
        $conditions = empty($_GET['wait_verify']) ? "sbrandarea.state <> '0'" : "sbrandarea.state = '0'";

        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'has_storebrandarea',
            'fields'=> 'this.*,sbrandarea.state as bastate',
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'sort_order asc,store_id desc'

        ));

        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('store.brandarea.html');
    }
    /**
     * 实拍区店铺管理
     */
    function reality_zone()
    {
        $conditions = empty($_GET['wait_verify']) ? "srealityzone.state <> '0'" : "srealityzone.state = '0'";

        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'has_storerealityzone',
            'fields'=> 'this.*,srealityzone.state as realityzone',
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'sort_order asc,store_id desc'

        ));

        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('store.realityzone.html');
    }
    /**
     * 虎门区店铺管理
     */
    function behalf_hm()
    {
        $conditions = empty($_GET['wait_verify']) ? "shm.state <> '0'" : "shm.state = '0'";

        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'has_storehm',
            'fields'=> 'this.*,shm.state as behalf_hm',
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'sort_order asc,store_id desc'

        ));

        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit.js'));
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('store.behalf_hm.html');
    }
    /**
     * 清除虎门区档口
     */
    function drop_behalfhm()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }
    
        $ids = explode(',', $id);
         
        $model_storehm =& m('storehm');
    
        db()->query("DELETE FROM ".$model_storehm->table." WHERE store_id ".db_create_in($ids));
    
        $this->show_message('drop_ok');
    }

    function index_closed()
    {
        $conditions = empty($_GET['wait_verify']) ? "s.state <> '" . STORE_APPLYING . "'" : "s.state = '" . STORE_APPLYING . "'";
        $filter = $this->_get_query_conditions(array(
                array(
                        'field' => 'store_name',
                        'equal' => 'like',
                ),
                array(
                    'field' => 'im_ww',
                    'equal' => 'like',
                ),
                array(
                        'field' => 'sgrade',
                ),
        ));
        $owner_name = trim($_GET['owner_name']);
        if ($owner_name)
        {

            $filter .= " AND (user_name LIKE '%{$owner_name}%' OR owner_name LIKE '%{$owner_name}%') ";
        }
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'sort_order';
                $order = '';
            }
        }
        else
        {
            $sort  = 'sort_order';
            $order = 'asc';
        }

        $this->assign('filter', $filter);
        $conditions .= $filter;
        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
                'conditions' => $conditions.' AND s.state ='.STORE_CLOSED,
                'join'  => 'belongs_to_user',
                'fields'=> 'this.*,member.user_name',
                'limit' => $page['limit'],
                'count' => true,
                'order' => "$sort $order,store_id desc"
        ));
        $sgrade_mod =& m('sgrade');
        $grades = $sgrade_mod->get_options();
        $this->assign('sgrades', $grades);

        $states = array(
                STORE_APPLYING  => LANG::get('wait_verify'),
                STORE_OPEN      => Lang::get('open'),
                STORE_CLOSED    => Lang::get('close'),
        );
        foreach ($stores as $key => $store)
        {
            $stores[$key]['sgrade'] = $grades[$store['sgrade']];
            $stores[$key]['state'] = $states[$store['state']];
            $certs = empty($store['certification']) ? array() : explode(',', $store['certification']);
            for ($i = 0; $i < count($certs); $i++)
            {
            $certs[$i] = Lang::get($certs[$i]);
            }
            $stores[$key]['certification'] = join('<br />', $certs);
        }
        $this->assign('stores', $stores);

            $page['item_count'] = $this->_store_mod->getCount();
            $this->import_resource(array('script' => 'inline_edit.js'));
            $this->_format_page($page);
                    $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
                    $this->assign('page_info', $page);

                            $this->display('store.index_closed.html');
    }

    function test()
    {
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $grades = $sgrade_mod->find();
            if (!$grades)
            {
                $this->show_warning('set_grade_first');
                return;
            }
            $this->display('store.test.html');
        }
        else
        {
            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];

            /* 连接到用户系统 */
            $ms =& ms();
            $user = $ms->user->get($user_name, true);
            if (empty($user))
            {
                $this->show_warning('user_not_exist');
                return;
            }
            if ($_POST['need_password'] && !$ms->user->auth($user_name, $password))
            {
                $this->show_warning('invalid_password');

                return;
            }

            $store = $this->_store_mod->get_info($user['user_id']);
            if ($store)
            {
                if ($store['state'] == STORE_APPLYING)
                {
                    $this->show_warning('user_has_application');
                    return;
                }
                else
                {
                    $this->show_warning('user_has_store');
                    return;
                }
            }
            else
            {
                header("Location:index.php?app=store&act=add&user_id=" . $user['user_id']);
            }
        }
    }

    function add()
    {
        $user_id = $_GET['user_id'];
        if (!$user_id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        if (!IS_POST)
        {
            /* 取得会员信息 */
            $user_mod =& m('member');
            $user = $user_mod->get_info($user_id);
            $this->assign('user', $user);

            $this->assign('store', array('state' => STORE_OPEN, 'recommended' => 0, 'sort_order' => 65535, 'end_time' => 0));

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $this->assign('scategories', $this->_get_scategory_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            if (!$this->_store_mod->unique(trim($_POST['store_name'])))
            {
                $this->show_warning('name_exist');
                return;
            }
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
            {
                $this->show_warning($this->_store_mod->get_error());

                return;
            }
            $data = array(
                'store_id'     => $user_id,
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'region_name'  => $_POST['region_name'],
                'address'      => $_POST['address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'recommended'  => $_POST['recommended'],
                'sort_order'   => $_POST['sort_order'],
                'add_time'     => gmtime(),
                'domain'       => $domain,
            );
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            if ($this->_store_mod->add($data) === false)
            {
                $this->show_warning($this->_store_mod->get_error());
                return false;
            }

            $this->_store_mod->unlinkRelation('has_scategory', $user_id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $user_id, $cate_id);
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=store',
                'continue_add', 'index.php?app=store&amp;act=test'
            );
        }
    }

    function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
            if (!$store)
            {
                $this->show_warning('store_empty');
                return;
            }
            if ($store['certification'])
            {
                $certs = explode(',', $store['certification']);
                foreach ($certs as $cert)
                {
                    $store['cert_' . $cert] = 1;
                }
            }
            $this->assign('store', $store);

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->assign('scategories', $this->_get_scategory_options());

            $this->assign('markets',$this->_get_market_options());

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', array_values($scates));

            $this->assign('my_market',$store['mk_id']);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            /*
            if (!$this->_store_mod->unique(trim($_POST['store_name']), $id))
            {
                $this->show_warning('name_exist');
                return;
            }
            */
            $store_info = $this->_store_mod->get_info($id);
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if ($domain && $domain != $store_info['domain'])
            {
                if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
                {
                    $this->show_warning($this->_store_mod->get_error());

                    return;
                }
            }
            
            /*如果淘宝网址shop_http发生了改变，则先删除本店铺中采集的宝贝*/
            if(trim($store_info['shop_http']) != trim($_POST['shop_http']))
            {
                $mod_goods =& m('goods');
                $mod_goods->drop('store_id = '.$id.' AND good_http IS NOT NULL');
                if($mod_goods->has_error())
                {
                    $this->show_warning($mod_goods->get_error());
                    return;
                }
            }
            
            $mk_name = $this->_get_full_market_name(intval(trim($_POST['mk_id'])));
            $shop_mall_floor = explode(" ", $mk_name);

            $data = array(
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'dangkou_address' => $_POST['dangkou_address'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'region_name'  => $_POST['region_name'],
                'shop_http' => $_POST['shop_http'],
                'address'      => $_POST['dangkou_address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'sort_order'   => empty($_POST['sort_order'])?255:$_POST['sort_order'],
                'recommended'  => $_POST['recommended'],
                'im_wx'=> $_POST['im_wx'],
                'domain'       => $domain,
                'mk_id' => $_POST['mk_id'],
                'mk_name' => $mk_name,
                'shop_mall'=>$shop_mall_floor[0],
                'floor' => preg_replace('/f/i', '',$shop_mall_floor[1]),
                'im_qq'=>$_POST['im_qq'],
                'im_ww'=>$_POST['im_ww'],
                'see_price'=>$_POST['seeprice'],
            );
            $data['state'] == STORE_CLOSED && $data['close_reason'] = $_POST['close_reason'];
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            $old_info = $this->_store_mod->get_info($id); // 修改前的店铺信息
            $this->_store_mod->edit($id, $data);

            $this->_store_mod->unlinkRelation('has_scategory', $id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $id, $cate_id);
            }
            
            //更新商品价格
            if(trim($_POST['seeprice']) != trim($store_info['see_price']))
            {
                $this->_update_goods_price_by_seeprice($store_info['store_id'],trim($_POST['seeprice']));
            }

            //更新商家编码
            if($data['shop_mall'] && $data['address'])
            {
                generate_storeBM($id);
            }

            //重新生成微信图片
            if(isset($_POST['im_wx']))
            {
                if(file_exists(ROOT_PATH.'/data/qrcode/zwd51_s'.$id.'.png'))
                {
                    @unlink(ROOT_PATH.'/data/qrcode/zwd51_s'.$id.'.png');
                }
                else
                {
                    $success = generateQRfromQRCode(trim($_POST['im_wx']), 's'.$id);
                }
            }


            /* 如果修改了店铺状态，通知店主 */
           /*  if ($old_info['state'] != $data['state'])
            {
                $ms =& ms();
                if ($data['state'] == STORE_CLOSED)
                {
                    // 关闭店铺
                    $subject = Lang::get('close_store_notice');
                    //$content = sprintf(Lang::get(), $data['close_reason']);
                    $content = get_msg('toseller_store_closed_notify',array('reason' => $data['close_reason']));
                }
                else
                {
                    // 开启店铺
                    $subject = Lang::get('open_store_notice');
                    $content = Lang::get('toseller_store_opened_notify');
                }
                $ms->pm->send(MSG_SYSTEM, $old_info['store_id'], '', $content);
                $this->_mailto($old_info['email'], $subject, $content);
            } */

            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('edit_ok',
                'back_list',    'index.php?app=store&page=' . $ret_page,
                'edit_again',   'index.php?app=store&amp;act=edit&amp;id=' . $id
            );
        }
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();
       if (in_array($column ,array('recommended','sort_order','serv_sendgoods','serv_refund','serv_exchgoods','serv_addred','serv_realpic','im_msn')))
       {//im_msn 当做备注了,如2016
           $data[$column] = $value;
           $this->_store_mod->edit($id, $data);
           if(!$this->_store_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       elseif($column == 'behalf_area')
       {
           $data['state'] = $value;
           $model_storebehalfarea = & m('storebehalfarea');
           $bh_area_store = $model_storebehalfarea->get($id);
           if(empty($bh_area_store) && $value)
           {
               $model_storebehalfarea->add(array('store_id'=>$id,'state'=>$value,'category'=>'ht'));
           }
           else
           {
               $model_storebehalfarea->edit($id, $data);
           }

           if(!$model_storebehalfarea->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       elseif($column == 'brand_area')
       {
           $data['state'] = $value;
           $model_storebrandarea = & m('storebrandarea');
           $br_area_store = $model_storebrandarea->get($id);
           if(empty($br_area_store) && $value)
           {
               $model_storebrandarea->add(array('store_id'=>$id,'state'=>$value,'category'=>'ht'));
           }
           else
           {
               $model_storebrandarea->edit($id, $data);
           }

           if(!$model_storebrandarea->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       elseif($column == 'realityzone')
       {
           $data['state'] = $value;
           $model_storerealityzone = & m('storerealityzone');
           $realityzone_store = $model_storerealityzone->get($id);
           if(empty($realityzone_store) && $value)
           {
               $model_storerealityzone->add(array('store_id'=>$id,'state'=>$value,'category'=>'ht'));
           }
           else
           {
               $model_storerealityzone->edit($id, $data);
           }

           if(!$model_storerealityzone->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       elseif($column == 'behalf_hm')
       {
           $data['state'] = $value;
           $model_storehm = & m('storehm');
           $hm_store = $model_storehm->get($id);
           if(empty($hm_store) && $value)
           {
               $model_storehm->add(array('store_id'=>$id,'state'=>$value,'category'=>'ht'));
           }
           else
           {
               $model_storehm->edit($id, $data);
           }

           if(!$model_storehm->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       elseif($column == 'behalf_choice')
       {
           $data['state'] = $value;
           $model_storebehalfchoice = & m('storebehalfchoice');
           $behalfchoice_store = $model_storebehalfchoice->get($id);
           if(empty($behalfchoice_store) && $value)
           {
               $model_storebehalfchoice->add(array('store_id'=>$id,'state'=>$value,'category'=>'ht'));
           }
           else
           {
               $model_storebehalfchoice->edit($id, $data);
           }

           if(!$model_storebehalfchoice->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }

    function drop()
    {
        // add by tanaiquan 2015-07-12
        $this->show_warning('ban_to_delete');
        return ;
        //
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }

        $ids = explode(',', $id);
        foreach ($ids as $id)
        {
            $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
        }
        if (!$this->_store_mod->drop($ids))
        {
            $this->show_warning($this->_store_mod->get_error());
            return;
        }

        /* 通知店主 */
        /*
        $user_mod =& m('member');
        $users = $user_mod->find(array(
            'conditions' => "user_id" . db_create_in($ids),
            'fields'     => 'user_id, user_name, email',
        ));
        foreach ($users as $user)
        {
            $ms =& ms();
            $subject = Lang::get('drop_store_notice');
            $content = get_msg('toseller_store_droped_notify');
            $ms->pm->send(MSG_SYSTEM, $user['user_id'], $subject, $content);
            $this->_mailto($user['email'], $subject, $content);
        }
        */

        $this->show_message('drop_ok');
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_store_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

    /* 查看并处理店铺申请 */
    function view()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
            if (!$store)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $sgrade_mod =& m('sgrade');
            $sgrades = $sgrade_mod->get_options();
            $store['sgrade'] = $sgrades[$store['sgrade']];
            $this->assign('store', $store);

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', $scates);

            $this->display('store.view.html');
        }
        else
        {
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            /* 批准 */
            if (isset($_POST['agree']))
            {
                $this->_store_mod->edit($id, array(
                    'state'      => STORE_OPEN,
                    'add_time'   => gmtime(),
                    'sort_order' => 65535,
                ));

                $content = get_msg('toseller_store_passed_notify');
                $ms =& ms();
                $ms->pm->send(MSG_SYSTEM, $id, '', $content);
                $store_info = $this->_store_mod->get_info($id);
                $this->send_feed('store_created', array(
                    'user_id'   =>  $store_info['store_id'],
                    'user_name'   => $store_info['user_name'],
                    'store_url'   => SITE_URL . '/' . url('app=store&id=' . $store_info['store_id']),
                    'seller_name'   => $store_info['store_name'],
                ));
                $this->_hook('after_opening', array('user_id' => $id));
                $this->show_message('agree_ok',
                    'edit_the_store', 'index.php?app=store&amp;act=edit&amp;id=' . $id,
                    'back_list', 'index.php?app=store&wait_verify=1&page=' . $ret_page
                );
            }
            /* 拒绝 */
            elseif (isset($_POST['reject']))
            {
                $reject_reason = trim($_POST['reject_reason']);
                if (!$reject_reason)
                {
                    $this->show_warning('input_reason');
                    return;
                }

                $content = get_msg('toseller_store_refused_notify', array('reason' => $reject_reason));
                $ms =& ms();
                $ms->pm->send(MSG_SYSTEM, $id, '', $content);

                $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
                $this->_store_mod->drop($id);
                $this->show_message('reject_ok',
                    'back_list', 'index.php?app=store&wait_verify=1&page=' . $ret_page
                );
            }
            else
            {
                $this->show_warning('Hacking Attempt');
                return;
            }
        }
    }

    function batch_edit()
    {
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}"></script>');
            $this->display('store.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $ids = explode(',', $id);
            $data = array();
            if ($_POST['region_id'] > 0)
            {
                $data['region_id'] = $_POST['region_id'];
                $data['region_name'] = $_POST['region_name'];
            }
            if ($_POST['sgrade'] > 0)
            {
                $data['sgrade'] = $_POST['sgrade'];
            }
            if ($_POST['certification'])
            {
                $certs = array();
                if ($_POST['autonym'])
                {
                    $certs[] = 'autonym';
                }
                if ($_POST['material'])
                {
                    $certs[] = 'material';
                }
                $data['certification'] = join(',', $certs);
            }
            if ($_POST['recommended'] > -1)
            {
                $data['recommended'] = $_POST['recommended'];
            }
            if (trim($_POST['sort_order']))
            {
                $data['sort_order'] = intval(trim($_POST['sort_order']));
            }

            if (empty($data))
            {
                $this->show_warning('no_change_set');
                return;
            }

            $this->_store_mod->edit($ids, $data);
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('edit_ok',
                'back_list', 'index.php?app=store&page=' . $ret_page);
        }
    }

    /**
     * 批量更新商家编码
     * @author tanaiquan
     * @date 2015-06-25
     */
    function batch_update_store_bm()
    {
        $fail_ids=array();
        $ids = isset($_GET['id'])&&$_GET['id']?trim($_GET['id']):0;
        if(!$ids)
        {
            $this->show_warning('store_not_exist');
            return;
        }
        $ids = explode(',', $ids);
        $ids = array_filter($ids);
        $stores = $this->_store_mod->find(array(
                'conditions'=>db_create_in($ids,'store_id'),
                'fields'=>'shop_mall,address,store_name',
        ));
        if(!$stores)
        {
            $this->show_warning('store_not_exist');
            return;
        }
        foreach ($stores as $key=>$store)
        {
            if(!$store['shop_mall'] || !$store['address'])
            {
                $fail_ids[] = $key;
                foreach ($ids as $idkey=>$idvalue)
                {
                    if($idvalue == $key)
                    {
                        unset($ids[$idkey]);
                    }
                }
            }
        }
        if($ids)
        {
            foreach ($ids as $store_id)
            {
                //更新编码
                 generate_storeBM($store_id);
            }
        }

        if(!$fail_ids)
        {
            $this->show_message('update_store_bm_success');
            return;
        }
        else
        {
            $warning = '';
            foreach ($fail_ids as $store_id)
            {
                $warning .= $stores[$store_id]['store_name'];
            }
            $this->show_warning($warning);
            return;
        }
    }

    function close_store()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_such_store');
            return;
        }

        $ids = explode(',', $id);
        $this->_store_mod->edit($ids,array('state'=>STORE_CLOSED));

        /* 通知店主 */

        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        $this->show_message('edit_ok',
                'back_list', 'index.php?app=store&page=' . $ret_page);
    }

    function check_name()
    {
        $id         = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $store_name = empty($_GET['store_name']) ? '' : trim($_GET['store_name']);

        if (!$this->_store_mod->unique($store_name, $id))
        {
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

    /* 删除店铺相关图片 */
    function _drop_store_image($store_id)
    {
        $files = array();

        /* 申请店铺时上传的图片 */
        $store = $this->_store_mod->get_info($store_id);
        for ($i = 1; $i <= 3; $i++)
        {
            if ($store['image_' . $i])
            {
                $files[] = $store['image_' . $i];
            }
        }

        /* 店铺设置中的图片 */
        if ($store['store_banner'])
        {
            $files[] = $store['store_banner'];
        }
        if ($store['store_logo'])
        {
            $files[] = $store['store_logo'];
        }

        /* 删除 */
        foreach ($files as $file)
        {
            $filename = ROOT_PATH . '/' . $file;
            if (file_exists($filename))
            {
                @unlink($filename);
            }
        }
    }

    /* 取得店铺分类 */
    function _get_scategory_options()
    {
        $mod =& m('scategory');
        $scategories = $mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($scategories, 'cate_id', 'parent_id', 'cate_name');

        return $tree->getOptions();
    }

    /* 取得市场分类 */
    function _get_market_options()
    {
        $mod =& m('market');
        $markets = $mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($markets, 'mk_id', 'parent_id', 'mk_name');

        return $tree->getOptions();
    }

    function _get_full_market_name($mk_id)
    {
        $mod_market =& m('market');
        $mk_name = '';
        $temp_array = array();
        if(!empty($mk_id))
        {
            $temp_array = $mod_market->get_ancestor($mk_id);
            if(!empty($temp_array))
            {
                $temp_array = array_values($temp_array);
                unset($temp_array["0"]);
            }
            $temp_array = array_reverse($temp_array);
        }
        foreach ($temp_array as $key => $value)
        {
            $mk_name = $value['mk_name'] ." ".$mk_name;
        }
        if(strlen($mk_name))
        {
            $mk_name = substr($mk_name, 0,-1);
        }
        return $mk_name;
    }

    /**
     * 按信用值导出前多少个店铺数据
     */
    function expose_store()
    {
        $mod_market = &m('market');
        if(!IS_POST)
        {
            $markets = $mod_market->get_list(1);
            $this->assign("markets",$markets);
            $this->display('store.expose.html');
        }
        else
        {
            $amount = $_POST['amount'];
            $conditions = 'state='.STORE_OPEN;
            //在代发区的店铺
            if($_POST['unbeyound_behalf_area'])
            {
                $model_storebehalfarea = & m('storebehalfarea');
                $behalfarea_stores = $model_storebehalfarea->getCol("SELECT store_id FROM ".$model_storebehalfarea->table);
                //关闭与否
                if($_POST['closed_store'])
                {
                    $conditions = 'state<>'.STORE_APPLYING;
                }
                $conditions .= " AND s.store_id NOT ".db_create_in($behalfarea_stores);
            }
            //导出实拍区
            if($_POST['sp']){
                $mod_realzone = & m('storerealityzone');
                $rs_stores = $mod_realzone->find(array('conditions'=>"state=1"));
                $rs_stores_ids = array_keys($rs_stores);
                $stores = $this->_store_mod->findAll(array(
                    'conditions'=>$conditions." and store_id ".db_create_in($rs_stores_ids),
                    'fields'=>'s.store_id,s.mk_name,s.dangkou_address,s.store_name,s.tel,s.im_qq,s.praise_rate,s.credit_value',
                    //'limit'=>"0,$amount",
                    'order'=>'credit_value DESC',
                    'include'=>array(
                        'has_goods'=>array(
                            'fields'=>'goods_statistics.*',
                            'join'=>'has_goodsstatistics',
                        )
                    )
                ));
                
                $this->_to_csv($stores);
                return;
            }
            //非所有店铺数据
            if($amount != 'all')
            {
                $stores = $this->_store_mod->findAll(array(
                    'conditions'=>$conditions,
                    'fields'=>'s.store_id,s.mk_name,s.dangkou_address,s.store_name,s.tel,s.im_qq,s.praise_rate,s.credit_value',
                    'limit'=>"0,$amount",
                    'order'=>'credit_value DESC',
                    'include'=>array(
                        'has_goods'=>array(
                            'fields'=>'goods_statistics.*',
                            'join'=>'has_goodsstatistics',
                    )
                     )
                ));

                $this->_to_csv($stores);
            }
            else
            {
               $mids = $_POST['mids'];//要导出的市场id
               //$stores = array();//所有店铺
               if($mids)
               {
                   foreach ($mids as $mid)
                   {
                       $floors = $mod_market->get_list($mid);
                       if($floors)
                       {
                           //$fids = array();
                           foreach ($floors as $fid)
                           {
                               //$fids[] = $fid['mk_id'];

                               $f_stores = $this->_store_mod->findAll(array(
                                   'conditions'=>$conditions." AND s.mk_id = ".$fid['mk_id'],
                                   'fields'=>'s.store_id,s.mk_name,s.dangkou_address,s.store_name,s.tel,s.im_qq,s.praise_rate,s.credit_value',
                                   'order'=>'s.address ASC,dangkou_address ASC',
                                   'include'=>array(
                                       'has_goods'=>array(
                                           'fields'=>'goods_statistics.*',
                                           'join'=>'has_goodsstatistics',
                                       )
                                   )
                               ));

                               $this->_to_csv($f_stores,'floor_'.$fid['mk_id']);
                               unset($f_stores);
                           }



                       }
                   }

                   //dump($stores);

               }
               else
               {
                   echo 'No markets!';
                   return;
               }

            }

            unset($stores);
            //dump($stores);
        }
    }

    function _to_csv($stores,$filename='')
    {
        //按csv拼装数据
        if(!empty($stores))
        {
            foreach ($stores as $key=>$store)
            {
                $sales = 0;
                $collects = 0;
                $orders = 0;
                $oos = 0;
                if(!empty($store['g']))
                {
                    foreach ($store['g'] as $goods)
                        $sales += $goods['sales'];
                        $collects += $goods['collects'];
                        $orders += $goods['orders'];
                        $oos += $goods['oos'];
                }
                $stores[$key]['sales'] = $sales;
                $stores[$key]['collects'] = $collects;
                $stores[$key]['orders'] = $orders;
                $stores[$key]['oos'] = $oos;
                unset($stores[$key]['g']);
            }
        }
        $data = array();
        $data['store_id']='档口ID';
        $data['mk_name']='市场';
        $data['dangkou_address']='档口地址';
        $data['store_name']='档口名';
        $data['tel']='联系电话';
        $data['im_qq']='QQ';
        $data['praise_rate']='好评率';
        $data['credit_value']='信用度';
        $data['sales']='总销量';
        $data['collects']='商品总收藏数';
        $data['orders']='下单总次数';
        $data['oos']='缺货总件数';
        array_unshift($stores, $data);
        $this->export_to_csv($stores, 'store_data_'.$filename.'.csv','gbk');
    }

    function add_discount_model()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $model_storediscount =& m('storediscount');
        if (!IS_POST)
        {
            $storediscounts = $model_storediscount->find(array(
                'conditions'=>'store_id = '.$id,
                'order' =>'sort_order'
            ));

            $store = $this->_store_mod->get($id);
            $this->assign('store_discount',$storediscounts);
            $this->assign('store',$store);
            $this->display('store.discount.model.html');
        }
        else
        {
             $data = array();
             $data['store_id'] = intval($_POST['store_id']);
             if(!$this->_store_mod->get($data['store_id']))
             {
                 $this->show_warning('no_such_store');
                 return;
             }
             $data['first_price'] = floatval($_POST['first_price']);
             $data['end_price'] = floatval($_POST['end_price']);
             $data['discount'] = floatval($_POST['discount']);
             $data['type']='price';
             if($data['first_price']<=0 || $data['end_price'] <= 0 || $data['discount'] <= 0 || $data['first_price'] >= $data['end_price'])
             {
                 $this->show_warning('price is not valid!');
                 return;
             }
             $sd_id = $model_storediscount->add($data);
             if($model_storediscount->has_error())
             {
                 $this->show_warning($model_storediscount->get_error());
                 return;
             }
             $this->show_message('price_ok',
                 'continue_setup', 'index.php?app=store&act=add_discount_model&id=' . $id);
        }
    }
    /**
     * 更新代发拿货优惠
     */
    function update_sd()
    {
        $model_storediscount =& m('storediscount');
        $id = intval(trim($_POST['id']));
        $data = array();
             $data['store_id'] = intval($_POST['store_id']);
             if(!$this->_store_mod->get($data['store_id']))
             {
                 $this->show_warning('no_such_store');
                 echo ecm_json_encode(false);
                 return;
             }
             $data['first_price'] = floatval($_POST['first_price']);
             $data['end_price'] = floatval($_POST['end_price']);
             $data['discount'] = floatval($_POST['discount']);
             $data['sort_order'] = intval($_POST['sort_order']);
             if($data['first_price']<=0 || $data['end_price'] <= 0 || $data['discount'] <= 0 || $data['first_price'] >= $data['end_price'])
             {
                 $this->show_warning('price is not valid!');
                 echo ecm_json_encode(false);
                 return;
             }
             $sd_id = $model_storediscount->edit($id,$data);
             if($model_storediscount->has_error())
             {
                 $this->show_warning($model_storediscount->get_error());
                 echo ecm_json_encode(false);
                 return;
             }
             echo ecm_json_encode(true);
             //$this->show_message('price_ok','continue_setup', 'index.php?app=store&act=add_discount_model&id=' . $id);
    }

    function del_sd()
    {
        $model_storediscount =& m('storediscount');
        $id = intval(trim($_POST['id']));
        $model_storediscount->drop($id);
        if($model_storediscount->has_error())
        {
            $this->show_warning($model_storediscount->get_error());
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

    function restore_store()
    {
        $restore_mod =& m('storerestore');
        if (!IS_POST)
        {
            $page = $this->_get_page();
            $stores = $restore_mod->find(array(
                'conditions' => "restore.state = 0",
                'join' => 'has_store',
                'limit' => $page['limit'],
                'count' => true,
                'order' => "s.shop_mall, s.floor, s.address"
                                               ));
            $this->assign('stores', $stores);
            $page['item_count'] = $restore_mod->getCount();
            $this->_format_page($page);
            $this->assign('page_info', $page);
            $this->display('store.restore.html');
        }
        else
        {
            $nowtime = gmtime();
            $store_id = $_REQUEST['store_id'];
            if ($store_id)
            {
                $this->_store_mod->db->query("insert into ecm_store_log values ({$store_id}, {$nowtime}, 1, 1)");
                $this->_store_mod->db->query("update ecm_store_restore set state = 1, last_update = {$nowtime} where store_id = {$store_id}");
                $this->_store_mod->db->query("update ecm_store set state = 1, add_time = {$nowtime} where store_id = {$store_id}");
                echo ecm_json_encode(true);
            }
            else
            {
                echo ecm_json_encode(false);
            }
        }
    }
    /**
     * 更新商品价格
     */
    function _update_goods_price_by_seeprice($store_id,$see_price){
        if(empty($see_price)){
            $see_price = '实价';
        }        
        
        $goods_mod = & m('goods');
        $goodsspec_mod = & m('goodsspec');
        
        $goods_ids = array();
        
        $goods_list = $goods_mod->find(array(
            'conditions'=>"store_id={$store_id} and if_show=1 and closed=0"
        )) ;
        
        if($goods_list){
            foreach ($goods_list as $goods){
                if(!in_array($goods['goods_id'], $goods_ids)){
                    $goods_ids[] = $goods['goods_id'];
                }
                if(!empty($goods['taobao_price'])){
                    $goods_mod->edit("goods_id={$goods['goods_id']}",array('price'=>make_price_by_taobaoprice($goods['taobao_price'], $see_price))); 
                }
                if($goods_mod->has_error())
                {
                    continue;
                }
            }
            
            $goodsspec_list = $goodsspec_mod->find(array(
                'conditions'=>db_create_in($goods_ids,'goods_id')
            ));
            
            if($goodsspec_list){
                foreach ($goodsspec_list as $goodsspec){
                    if(!empty($goodsspec['taobao_price'])){
                        $goodsspec_mod->edit("spec_id={$goodsspec['spec_id']}",array('price'=>make_price_by_taobaoprice($goodsspec['taobao_price'], $see_price)));
                    }
                    if($goodsspec_mod->has_error())
                    {
                        continue;
                    }
                }
            }
        }
        
        return;
    }
}

?>
