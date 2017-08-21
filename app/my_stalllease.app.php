<?php

class My_stallleaseApp extends StoreadminbaseApp {
    var $_store_id;
    var $_store_mod;

    function __construct() {
        parent::__construct();
        $this->_store_id = intval($this->visitor->get('manage_store'));
        $this->_store_mod =& m('store');
    }

    function index() {
        $mod_stalllease = & m('stalllease');
        
        if (!IS_POST) {
            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_im_order'));
            $this->_curlocal(LANG::get('member_center'), 'index.php?app=my_stalllease',
                             LANG::get('my_stalllease'), 'index.php?app=my_stalllease',
                             LANG::get('my_stalllease'));
            $this->_curitem('my_stalllease');
            $this->_curmenu('my_stalllease');
            
            $stall_infos = $mod_stalllease->find(array(
                'conditions'=>"store_id = ".$this->_store_id,
                'order'=>'pub_time DESC'
            ));
            
            //$this->assign('store_id', $this->_store_id);
            $this->import_resource(array(
                'script' => array(
                    array(
                        'path' => 'dialog/dialog.js',
                        'attr' => 'id="dialog_js"',
                    ),
                    array(
                        'path' => 'jquery.ui/jquery.ui.js',
                        'attr' => '',
                    ),
                    array(
                        'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                        'attr' => '',
                    ),
                    array(
                        'path' => 'jquery.plugins/jquery.validate.js',
                        'attr' => '',
                    ),
                ),
                'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
            ));
            $this->assign('infos', $stall_infos);
           
            $this->display('my_stalllease.index.html');
        } else {
           
            $this->show_message('success', 'back_list', 'index.php?app=my_stalllease');
        }
    }

   function add()
   {
       $store_info = $this->_store_mod->get($this->_store_id);
       
       if (!IS_POST)
       {
           header('Content-Type:text/html;charset=' . CHARSET);    
           $this->import_resource(array(
               'script' => array(
                   array(
                       'path' => 'dialog/dialog.js',
                       'attr' => 'id="dialog_js"',
                   ),
                   array(
                       'path' => 'jquery.ui/jquery.ui.js',
                       'attr' => '',
                   ),
                   array(
                       'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                       'attr' => '',
                   ),
                   array(
                       'path' => 'jquery.plugins/jquery.validate.js',
                       'attr' => '',
                   ),
               ),
               'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
           ));
           $this->assign('store_info',$store_info);
           $this->display('my_stalllease.add.html');
       }
       else
       {
           $data = array(
               'store_id'=>$this->_store_id,
               'mk_id'=>$store_info['mk_id'],
               'mk_name'=>$store_info['mk_name'],
               'stall_addr'=>$store_info['address'],
               'stall_type'=>$_POST['stall_type'],
               'stall_size'=>$_POST['stall_size'],
               'pub_time'=>gmtime(),
               'end_time'=>strtotime($_POST['end_time']),
               'mobile'=>$_POST['mobile'],
               'detail'=>html_filter($_POST['detail'])
           );
           if($data['pub_time'] >$data['end_time'])
           {
               $this->pop_warning('end_time_gl_pub_time');
               return;
           }
          $mod_stall = & m('stalllease'); 
          $mod_stall->add($data);
          $this->pop_warning('ok','','index.php?app=my_stalllease');
       }
   }
   
   function drop()
   {
       $id = isset($_GET['id']) ? trim($_GET['id']) : '';
       if (!$id)
       {
           $this->show_warning('no_goods_to_drop');
           return;
       }
       
       $ids = explode(',', $id);
       $mod_stall = & m('stalllease');
       
       $mod_stall->drop($ids);
       $rows = $mod_stall->drop($ids);
       if ($mod_stall->has_error())
       {
           $this->show_warning($mod_stall->get_error());
           return;
       }
       
       $this->pop_warning('ok','','index.php?app=my_stalllease');
   }
}