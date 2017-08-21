<?php
define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/ecmall.php');
ecm_define(ROOT_PATH . '/data/config.inc.php');
import('log.lib');

              
    $params = http_build_query($_POST);
    Log::write($params);
    $context_options = array (
        'http' => array (
            'method' => 'POST',
            'header'=> "Content-type: application/x-www-form-urlencoded\r\n".
            "Content-Length: ".strlen($params)."\r\n",
            'content' => $params));
    $context = stream_context_create($context_options);
    $result = file_get_contents('http://www.51zwd.com/index.php?app=my_money&act=returnurlThree', false, $context);
    echo $result;
    ?>
