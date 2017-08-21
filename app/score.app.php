<?php 
define ('PORCESS_TOTAL' , 10) ; //最大子进程数
define ('PROCESS_GOODS' , 10000); //单进程最大处理数
define ('MAIN_PROCESS_GOODS' , 1000000) ; //主进程一共最多处理1000000万

class ScoreApp extends MallbaseApp {
    
    public function ScoreApp(){
        $this->_m_statis_goods = & m('statisgoods');
        $this->_m_goods =& m('goods');
        $this->_m_goodsstatisct = & m('goodsstatistics');
        $this->_bm_goods = & bm('goods');
        $this->_m_goods_warehouse = & m('goodswarehouse');

        $this->_s_cache = & cache_server();
        $this->_proc_loc_file_prefix  = 'proc_score_job_';
        $this->_proc_loc_file_path = ROOT_PATH.'/temp/';
        
        $this->_main_pocess_serial_key = date('Ymd').'_main_pocess_serial';
        
    }
    
    public function test(){
        header("Content-type: text/html; charset=utf-8");
        
        if ($_POST) {
            echo '<pre>';
            $stime = time();
            $results = $this->_m_goods->db->getAll($_POST ['sql_str']);
            echo 'time: '.(time() - $stime) . ' <br>';
            
            var_dump($results);
        }else {
            echo '<form action="" method="post">
                    SQL:<input type="text" name="sql_str" value="" size="100"><br>
                    <input type="submit" value="Submit">
                  </form>';
        }
        return ;
    }
    
    /**
     * JOB主进程。必须在命令行下执行
     */
    public function job(){
        set_time_limit(0);
        
        if (!function_exists('pcntl_fork')) {
            die("pcntl_fork not existing");
        }
        
//         $this->_s_cache->delete($this->_main_pocess_serial_key);
        
        
        $this->_main_pocess_serial = intval($this->_s_cache->get($this->_main_pocess_serial_key));
        $this->_s_cache->set($this->_main_pocess_serial_key, $this->_main_pocess_serial + 1 , 86400);
        
         //当天第一次启动，应清空进程锁
        if ( $this->_main_pocess_serial === 0 ) {
           $this->clearLock();
        }
        
        $statis_info = $this->_m_statis_goods->get(array('order' => 'id desc'));
        

        //最大浏览量写入缓存,12个小时
        $this->score_max_view = $statis_info['max_views'];
        $this->score_max_sales = $statis_info['max_sales'] ;
        $this->score_max_behalf =  $statis_info['max_behalf'];
//         $total = $statis_info ['total_online_goods'];
        
        $total = $this->_m_goods->getOne('select max(goods_id) from ecm_goods');
        
        //当天已经处理完毕
        if ($this->_main_pocess_serial * MAIN_PROCESS_GOODS > $total){
            return ;
        }
        
        //初始化偏移量
        $offset = $this->_main_pocess_serial * MAIN_PROCESS_GOODS ;
        $this->_wirte_log('JOB 启动。序号：'. $this->_main_pocess_serial);

        $total -= $offset;
        //最多开启10个进程
        $childprocess = 0;
        
        $n_main_process_goods = 0;
        while(true) {
            if ($total <= 0) {
                //处理完毕退出循环
                break;
            }
            
            if ($n_main_process_goods >= MAIN_PROCESS_GOODS){
                break;
            }
            
            //检查进程数
//             $wc = `ps -ef| grep 'app=score&act=jobchild' | grep -v grep |wc -l`;
            $num_jobchild = $this->readLockNum();
            
//             echo $num_jobchild ."\n";
            
            if ($num_jobchild >= PORCESS_TOTAL){
                //进程已满，等待5秒后退出
                sleep(5);
                continue;
            }
            //非阻塞多进程并发处理

            $nPID = pcntl_fork();
            
            if ($nPID == -1) {
                //错误处理：创建子进程失败时返回-1.
                die('could not fork');
            } else if ($nPID) {
                //父进程会得到子进程号，所以这里是父进程执行的逻辑
                
                $total -= PROCESS_GOODS;
                $offset += PROCESS_GOODS;
                $n_main_process_goods += PROCESS_GOODS;
                
                $childprocess++;
                sleep(1);
            }
            else {
                $pid = posix_getpid();
                $this->createLock($pid);
                
                $cfg = parse_url(DB_CONFIG);
                $cfg['path'] = str_replace('/', '', $cfg['path']);
                $charset = (CHARSET == 'utf-8') ? 'utf8' : CHARSET;
                $db = new cls_mysql();
                $db->cache_dir = ROOT_PATH. '/temp/query_caches/';
                $db->connect($cfg['host']. ':' .$cfg['port'], $cfg['user'],$cfg['pass'], $cfg['path'], $charset);
                
                $this->_m_statis_goods = & m('statisgoods');
                $this->_m_statis_goods->db = $db;
                
                $this->_m_goods =& m('goods');
                $this->_m_goods->db = $db;
                
                $this->_m_goodsstatisct = & m('goodsstatistics');
                $this->_m_goodsstatisct->db = $db;
                
                $this->_bm_goods = & bm('goods');
                $this->_bm_goods->db = $db;
                
                $this->_m_goods_warehouse = & m('goodswarehouse');
                $this->_m_goods_warehouse->db = $db;
                
                //子进程开始处理
                $this->jobchild($offset);
//                 exec(PHP_CLI.' -f '.ROOT_PATH.'/cli.php "app=score&act=jobchild&offset='.$offset.'"');
//              
                $this->delLock($pid);
                return ; // 执行完后退出
            }
        }
        
        $n = 0;
        while ($n < $childprocess) {
            $nStatus = -1;
            $nPID = pcntl_wait($nStatus, WNOHANG);
            if ($nPID > 0) {
                ++$n;
            }
        }
        
        //执行完毕后 ，更新分数
//         $this->_m_goods->db->query('update ecm_goods set sort_order=score');
        $this->_wirte_log('JOB 结束。序号：'. $this->_main_pocess_serial .' 。共启动了 ' . $childprocess .'个进程');
        echo 'DONE';
        return ;
    }
    
    /**
     * 批量计算得分，并更新
     */
    public function jobchild($offset = 0) {
        $limit = PROCESS_GOODS;
        $goods_id = isset($_GET ['goods_id'] ) ? $_GET ['goods_id']  : '' ;
        $n = 0;
        
        if (!$goods_id ){
            $sql = 'select g.goods_id , g.add_time ,g.realpic, s.store_id , s.recommended 
                    from (select * from ecm_goods where goods_id >= '.$offset.' and goods_id < '.($offset + PROCESS_GOODS).' ) g
                    LEFT JOIN ecm_store s ON g.store_id = s.store_id
                    LEFT JOIN ecm_goods_statistics gs ON g.goods_id = gs.goods_id
                    where g.if_show = 1 AND g.closed = 0 AND g.default_spec > 0 AND s.state = 1
                    ';
            $goods_list = $this->_m_goods->getAll($sql); 
            
        }else {
            $sql = array(
                'join'=>'belongs_to_store',
                'conditions'=> 'g.if_show = 1 AND g.closed = 0 AND g.default_spec > 0 AND s.state = 1 and g.goods_id = '.$goods_id,
                'fields'=>'g.goods_id , g.add_time ,g.realpic, s.store_id , s.recommended ',
            );
            $statis_info = $this->_m_statis_goods->get(array('order' => 'id desc'));
            $this->score_max_view = $statis_info['max_views'];
            $this->score_max_sales = $statis_info['max_sales'] ;
            $this->score_max_behalf =  $statis_info['max_behalf'];
            
            $goods_list = $this->_m_goods->find($sql);
        }
        
        if ($goods_list) {
            foreach ($goods_list as $goods_info){
                $score = $this->calcscore($goods_info ['goods_id'], $this->score_max_view, $this->score_max_sales, $this->score_max_behalf, $goods_info);
                $this->_m_goods->edit(array('goods_id'=>$goods_info ['goods_id']) , array('score'=> $score));
            }
        }
        
        if (!$goods_id){
            $this->_wirte_log('子进程: '.posix_getpid().' 结束,offset: '.$offset.', 共处理 '.count($goods_list));
            unset($goods_list);
        }
    }
    
    /**
     * 计算得分
     * @param unknown $goods_id
     * @return number
     */
    function calcscore($goods_id , $score_max_views  ,$score_max_sales ,  $score_max_behalf , $goods_info = null ){
        if (empty($goods_info) ){
            $sql = array(
                    'join'=>'belongs_to_store',
                    'conditions'=> 'g.if_show = 1 AND g.closed = 0 AND g.default_spec > 0 AND s.state = 1 And g.goods_id = '.$goods_id,
                    'fields'=>'g.goods_id , g.add_time , g.realpic ,s.store_id, s.recommended',
                );
            $goods_info = $this->_m_goods->get($sql);
        }
        
        //获取商品统计信息
        $goods_stats_info = $this->_m_goodsstatisct->get(array('conditions'=>'goods_id='.$goods_id));
        
        $score = 0;
        
        //点击量,占比15%
        $views = round($goods_stats_info['views'] / $score_max_views , 2 ) ;
        $score += $views* 15 ;

        
        //上架天数,占比 10%
        $days = ceil( (time() - $goods_info['add_time']) /86400);
        if ($days <= 3) {
            $new = 1;
        }else if ($days <= 7) {
            $new = 0.7;
        }else if ($days <= 30){
            $new = 0.5;
        }else {
            $new = 0.1;
        }
//         echo 'new:'.$new * 10  ."\n";
        $score += $new * 20;
        
        //代发量,占比10%
        $sql = array(
            'conditions'=> 'goods_id='.$goods_id,
            'fields'=> 'sum(goods_quantity) as goods_quantity'
        );
        
        $goods_warehouse = $this->_m_goods_warehouse->get($sql);
        $goods_warehouse = intval($goods_warehouse ['goods_quantity']);
        $score += round( $goods_warehouse / $score_max_behalf , 2 )  * 10;
        
        //实拍,占比 15%
        $goods_realpic = $goods_info['realpic'] ? 1:0;
//         echo 'realpic:'.$goods_realpic * 15 ."\n";
        $score  += $goods_realpic * 15;
        
        //优质客户，占比 10%
        $recommend = $goods_info['recommended'] ? 1:0;
//         echo 'recommend:' .$recommend * 10 ."\n";
        $score += $recommend * 10;
        
//         //广告客户，占比15%
        $advstore = (in_array($goods_info ['store_id'], $this->_get_adv_store())) ?　1: 0; 
//         echo 'advstore:' .$advstore * 15 ."\n";
        $score += $advstore * 15;
        
//         //售出数，占比15%
        $sales = round($goods_stats_info ['sales']/ $score_max_sales , 2) ;
//         echo 'sales:' .$sales * 10  ."\n";
        $score += $sales * 15;
        
        //计算减分项
        if($goods_stats_info['sales'] > 0)
        {
            //缺货率,占比10%
            $lack_rate  = round($goods_stats_info['oos']/$goods_stats_info['sales'],2);
//             echo 'lack_rate:' .$lack_rate * 10 ."\n";
            $score -= $lack_rate * 5;
            //退货率
            $back_rate  = round($goods_stats_info['backs']/$goods_stats_info['sales'],2) ;
//             echo 'back_rate:' .$back_rate * 10 ."\n";
            $score -= $back_rate * 5;
        }
        //返回整数排序值
        $score =  round($score , 0);
        return $score > 0 ? $score : 0;
    }
    
    //获取广告商户
    private function _get_adv_store(){
        $adv_store_ids = $this->_s_cache->get('adv_store_ids');
        if (empty($adv_store_ids)) {
            $adv_store_ids = '5808,9772,6527,7604,155600,10114,6527,5867,9038,149278,158398,156207,105851,148204,153569,5692,7241,154398,8131,13778,11925,91134,19306,113268,10896,10742,8715,12199,5889,5872,18318,6585,5474,5555,13687,7541';
            $this->_s_cache->set('adv_store_ids', $adv_store_ids);
        }
        return explode(',', $adv_store_ids);
    }
    
    //记录JOB日志
    private function _wirte_log($msg){
        $content = '['.date('Y-m-d H:i:s').'] '.$msg."\r\n";
        file_put_contents(ROOT_PATH.'/temp/logs/'.date('Ymd').'_job.score.log.txt', $content , FILE_APPEND);
    }
    
    private function _clear_log() {
        file_put_contents(ROOT_PATH.'/temp/logs/job.score.log.txt', '' );
    }
        
    private function createLock($pid) {
        file_put_contents(($this->_proc_loc_file_path).$this->_proc_loc_file_prefix.($this->_main_pocess_serial).'_'.$pid.'.lock' , $pid);
    }
    
    private function delLock($pid) {
        unlink(($this->_proc_loc_file_path).$this->_proc_loc_file_prefix . ($this->_main_pocess_serial).'_'.$pid.'.lock' );
    }
    
    /**
     * 清空进程锁
     */
    private function clearLock() {
        $iterator = new DirectoryIterator($this->_proc_loc_file_path);
        $n = 0;
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                if (!$fileinfo->isDot()) {
                    if(strstr($fileinfo->getFilename(), $this->_proc_loc_file_prefix)){
                        unlink($this->_proc_loc_file_path . $fileinfo->getFilename());
                    }
                }
            }
        }
    }
    
    private function readLockNum(){
        $iterator = new DirectoryIterator($this->_proc_loc_file_path);
        $n = 0;
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                if (!$fileinfo->isDot()) {
                    if(strstr($fileinfo->getFilename(), $this->_proc_loc_file_prefix .$this->_main_pocess_serial)){
                        $n++;
                    } 
                }
            }
        }
        return $n; 
    }
}
