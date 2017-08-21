DROP TABLE IF EXISTS `ecm_order_vendor`;
CREATE TABLE IF NOT EXISTS `ecm_order_vendor` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `seller_id` int(10) unsigned NOT NULL DEFAULT '0',
  `seller_name` varchar(100) DEFAULT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `receiver_name` varchar(100) DEFAULT NULL,
  `receiver_mobile` varchar(100) DEFAULT NULL,
  `receiver_address` varchar(100) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0', -- 0:unhandled,
  `vendor` tinyint(3) unsigned NOT NULL DEFAULT '0', -- 0:taobao,1:alibaba,2:paipai
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `handle_time` int(10) unsigned NOT NULL DEFAULT '0',
  `finished_time` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `post_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`order_id`),
  KEY `order_sn` (`order_sn`),
  KEY `seller_id` (`seller_id`),
  KEY `add_time` (`add_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方订单';

DROP TABLE IF EXISTS `ecm_goods_vendor`;
CREATE TABLE `ecm_goods_vendor` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin_goods_id` int(10) unsigned NULL DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `goods_name` varchar(255) NOT NULL DEFAULT '',
  `outer_iid` varchar(30) NOT NULL DEFAULT '',
  `spec_name_1` varchar(60) NOT NULL DEFAULT '',
  `spec_value_1` varchar(60) NOT NULL DEFAULT '',
  `spec_name_2` varchar(60) NOT NULL DEFAULT '',
  `spec_value_2` varchar(60) NOT NULL DEFAULT '',
  `default_image` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `num` int(10) unsigned NOT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `origin_goods_id` (`origin_goods_id`),
  KEY `outer_iid` (`outer_iid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='第三方宝贝';
