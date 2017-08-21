<?php
define('ROOT_PATH', dirname(__FILE__));

include(ROOT_PATH . '/eccore/ecmall.php');

//php可执行文件
define ('PHP_CLI' ,  '/alidata/server/php/bin/php');

/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');

/* 启动ECMall */
$config = array(
    'default_app'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  ROOT_PATH . '/app',
    'external_libs' =>  array(
        ROOT_PATH . '/includes/global.lib.php',
        ROOT_PATH . '/includes/libraries/time.lib.php',
        ROOT_PATH . '/includes/ecapp.base.php',
        ROOT_PATH . '/includes/plugin.base.php',
        ROOT_PATH . '/app/frontend.base.php',
        ROOT_PATH . '/includes/subdomain.inc.php',
        ROOT_PATH . '/includes/sphinxapi.php',
    ));

require(ROOT_PATH . '/eccore/controller/app.base.php');     //基础控制器类
require(ROOT_PATH . '/eccore/model/model.base.php');   //模型基础类

if (!empty($config['external_libs']))
{
    foreach ($config['external_libs'] as $lib)
    {
        require($lib); 
    }
}

/* 请求转发 */
$default_app = $config['default_app'];
$default_act = $config['default_act'];

$params = preg_params($argv[1]);

global $params;

$app    = isset($params['app']) ? preg_replace('/(\W+)/', '', $params['app']) : $default_app;
$act    = isset($params['act']) ? trim($params['act']) : $default_act;
$app_file = $config['app_root'] . "/{$app}.app.php";
if (!is_file($app_file))
{
    exit('Missing controller');
}

require($app_file);
define('APP', $app);
define('ACT', $act);
$app_class_name = ucfirst($app) . 'App';

/* 实例化控制器 */
$app     = new $app_class_name();
c($app);
$app->do_action($act);        //转发至对应的Action
$app->destruct();

/*
 * 格式化参数
 */
function preg_params($param_str){
    $arrTemp = explode('&', $param_str);
    $params = array();
    foreach ($arrTemp as $val) {
        $tmp = explode('=', $val);
        $params [$tmp[0]] = $tmp[1];
    }
    return $params;
}

?>
