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
$verify_result = $alipayNotify->verifyReturn();
$params = http_build_query($_GET);
header("Content-type:text/html;charset=utf-8");
echo '<div class="content">';
if ($verify_result) {
    // header('Location: index.php?app=my_money&act=direct_alipay_return&'.$params);
    echo '<span  class="s">支付成功，到账可能会有延迟，请稍后查询订单状态。如有问题，请联系客服处理。</span><br/>';
    echo '<a class="backhome" href="http://www.51zwd.com">回到首页</a>';
    echo '<a class="backorder" href="http://www.51zwd.com/index.php?app=buyer_order">订单列表</a><br/>';
} else {
    echo '<span>支付失败，请联系客服处理。</span><br/>';
    echo '<a class="backhome" href="http://www.51zwd.com">回到首页</a>';
    echo '<a class="backorder" href="http://www.51zwd.com/index.php?app=buyer_order">订单列表</a><br/>';
    Log::write(
        "fail to verify sign, post:{$params} ");
}
echo "<div>";
?>
<style>
.content{width:1200px;margin:0 auto;text-align:center;line-height:36px;color:#f44;font-weight:500}
.backhome{text-decoration:none;background-color:#760;color:#fff;padding:5px 12px;border-radius:3px;margin-right:10px}
.backorder{text-decoration:none;background-color:#00C0EF;color:white;padding:5px 12px;border-radius:3px}
.s{color:green}
</style>