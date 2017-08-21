<?php
define('NUM_PER_PAGE', 40);        // 每页显示数量
class SleaseApp extends MallbaseApp {
    /* 搜索档口租赁信息 */
    
    function index() {     	
        
        //$cache_server = & cache_server();
        //$indexkey = 'stall_lease_lists';
       // $data = $cache_server->get($indexkey);
        
        /* 分页信息 */
        $page = $this->_get_page(NUM_PER_PAGE);
        
           
            $mk_id = $_GET['mkid'];
            $stype = $_GET['stype'];
            $ssize = $_GET['ssize'];
            
            $mod_market = & m('market');
            $mod_stall = & m('stalllease');
            
            $market_infos = $mod_market->get_list(1);
            
            
            $conditions = '';
            if($mk_id){
                $result_floor = $mod_market->get_list($mk_id);
                !empty($result_floor) && $conditions .= db_create_in(array_keys($result_floor),'mk_id');
            }
            if($stype){
                if(empty($conditions)){
                    $conditions .= " stall_type = '".trim($stype)."'";
                }else{
                    $conditions .= " AND stall_type = '".trim($stype)."'";
                }                
            }
            if($ssize){
                if(empty($conditions)){
                    $conditions .= " stall_size = '".trim($ssize)."'";
                }else{
                    $conditions .= " AND stall_size = '".trim($ssize)."'";
                }
            }            
            
            /* 租赁信息列表 */
            
            $stall_list = $mod_stall->find(array(
                'conditions' => $conditions,
                'order'=>'pub_time DESC',
                'limit'=>$page['limit'],
                'count'=>true
            ));                     
            
            $total_found = $mod_stall->getCount();
          
        
        $this->assign('stall_list', $stall_list);
        
        if ($total_found) {
            $page['item_count'] = $total_found;
        } else {
            $page['item_count'] = $stats['total_count'];
        }
        $this->_format_page($page);
        
        $this->assign('page_info', $page);

        /* 取得导航 */
        $this->assign('navs', $this->_get_navs());

        $this->import_resource(array(
            'script' => 'jBox/jquery.jBox.src.js',
            'style' => 'jBox/jbox.css',
        ));

        /* 当前位置 */
        //$cate_id = isset($param['cate_id']) ? $param['cate_id'] : 0;
       // $this->_curlocal($this->_get_goods_curlocal($cate_id));

        /* 配置seo信息 */
        //$this->_config_seo($this->_get_seo_info('goods', $cate_id));      
       
        //$this->assign('filters', $this->_get_filter($param));
        $this->assign('markets',$market_infos);
        $this->display('stall.lease.wind.html');

    }

   

}

?>
