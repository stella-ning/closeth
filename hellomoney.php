<?php
 	  require_once("includes/Alipay/corefunction.php");
   require_once("includes/Alipay/md5function.php");
    require_once("includes/Alipay/notify.php");
    require_once("includes/Alipay/submit.php");
        require_once("data/config.alipay.php");
        // 计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();

        if ($verify_result) {
            $trade_no = $_GET ['out_trade_no'];
            $total_fee = $_GET ['total_fee'];
            $title = $_GET['subject'];
            $order_no = $_GET['trade_no'];

            if ($_GET ['trade_status'] == 'TRADE_FINISHED' || $_GET ['trade_status'] == 'TRADE_SUCCESS') {
                if ($_GET ['trade_status'] == 'TRADE_SUCCESS') {
                    $trade_status = 1;
                } else if ($_GET ['trade_status'] == 'TRADE_FINISHED') {
                    $trade_status = 2;
                }
                $mysign = md5($trade_no.'duanxiongwen');
                header("location: http://www.51zwd.com/index.php?app=my_money&act=returnurlTwo&mysign=".$mysign."&trade_status=".$_GET['trade_status']."&out_trade_no=" . $trade_no . "&total_fee=" . $total_fee . "&subject=" . $title . "&trade_no=" . $order_no);
            }
        } else {
           // echo 'it is wrong!';
        }
    ?>