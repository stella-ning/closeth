<?php

define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/ecmall.php');
ecm_define(ROOT_PATH . '/data/config.inc.php');
import('log.lib');
require_once("includes/Alipay/corefunction.php");
require_once("includes/Alipay/md5function.php");
require_once("includes/Alipay/notify.php");
require_once("includes/Alipay/submit.php");
require_once("data/config.alipay.php");

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();
$params = http_build_query($_POST);
if ($verify_result) {
    $context_options = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/x-www-form-urlencoded\r\n".
            "Content-Length: ".strlen($params)."\r\n",
            'content' => $params));
    $context = stream_context_create($context_options);
    $result = file_get_contents('http://www.51zwd.com/index.php?app=my_money&act=direct_alipay_notify', false, $context);
    echo $result;
} else {
    Log::write(
        "fail to verify sign, post:{$params} ");
}

?>