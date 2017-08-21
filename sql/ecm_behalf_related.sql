/* added by tanaiquan@51zwd.com */

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `ecm_behalf`;
CREATE TABLE IF NOT EXISTS `ecm_behalf` (
  `bh_id` int(10) unsigned NOT NULL COMMENT '编号',
  `bh_name` varchar(200) NOT NULL COMMENT '名称',
  `bh_logo` varchar(200) DEFAULT NULL COMMENT 'logo',
  `bh_qq` varchar(15) DEFAULT NULL COMMENT 'qq',
  `bh_ww` varchar(100) DEFAULT NULL COMMENT '旺旺',
  `bh_tel` varchar(20) DEFAULT NULL COMMENT '固话',
  `bh_wx` varchar(200) DEFAULT NULL COMMENT '微信号',
  `bh_wximage` varchar(200) DEFAULT NULL COMMENT '微信图片路径',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `bh_allowed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核',
  `bh_mark` text COMMENT '备注',
  `region_id` int(10) unsigned DEFAULT NULL COMMENT '地区编号',
  `region_name` varchar(100) DEFAULT NULL COMMENT '地区名称',
  `bh_address` varchar(255) NOT NULL COMMENT '详细地址',
  `zipcode` varchar(20) NOT NULL COMMENT '邮政编码',
  `recommended` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`bh_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代发团队';

-- --------------------------------------------------------

--
-- 表的结构 `ecm_market`
--
DROP TABLE IF EXISTS `ecm_market`;
CREATE TABLE IF NOT EXISTS `ecm_market` (
  `mk_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '市场编号',
  `mk_name` varchar(100) NOT NULL COMMENT '市场名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父类',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255',
  PRIMARY KEY (`mk_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='市场表' AUTO_INCREMENT=23 ;

--
-- 导出表中的数据 `ecm_market`
--

INSERT INTO `ecm_market` (`mk_id`, `mk_name`, `parent_id`, `sort_order`) VALUES
(1, '广州', 0, 255),
(2, '富丽', 1, 255),
(3, '女人街', 1, 255),
(4, '大西豪', 1, 255),
(5, '宝华', 1, 255),
(6, '佰润', 1, 255),
(7, '鼎宝', 1, 255),
(8, '国大', 1, 255),
(9, '柏美', 1, 255),
(10, '大时代', 1, 255),
(11, '新潮都', 1, 255),
(12, '非凡', 1, 255),
(13, '金马', 1, 255),
(14, '十三行', 1, 255),
(15, '西街', 1, 255),
(16, '南城', 1, 255),
(17, '新骊都', 1, 255),
(18, '鞋城', 1, 255),
(19, '机筑港', 1, 255),
(20, '万佳', 1, 255),
(21, '益民', 1, 255),
(22, '新百佳', 1, 255);


ALTER TABLE `ecm_store` ADD `mk_id` INT( 10 ) UNSIGNED NULL COMMENT '市场编号',
ADD `mk_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '市场名称';

ALTER TABLE `ecm_store` ADD INDEX ( `mk_id` ) ;

DROP TABLE IF EXISTS `ecm_delivery`;
CREATE TABLE IF NOT EXISTS `ecm_delivery` (
  `dl_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '快递编号',
  `dl_name` varchar(100) NOT NULL COMMENT '快递名称',
  `region_id` int(10) unsigned NOT NULL COMMENT '地区编号',
  `region_name` varchar(200) NOT NULL COMMENT '地区名称',
  `address` varchar(200) NOT NULL COMMENT '详细地址',
  `dl_desc` varchar(255) DEFAULT NULL COMMENT '描述',
  `recommended` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `if_show` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`dl_id`),
  KEY `dl_name` (`dl_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='快递表' AUTO_INCREMENT=10 ;


INSERT INTO `ecm_delivery` (`dl_id`, `dl_name`, `region_id`, `region_name`, `address`, `dl_desc`, `recommended`, `sort_order`, `if_show`) VALUES
(1, 'EMS', 284, '中国    湖北省    武汉', '汉正街', '全天发货', 0, 255, 1),
(2, '天天快递', 43, '中国    上海市    徐汇区', '无名路', '上午发货', 1, 255, 1),
(3, '优速快递', 4, '中国	北京市	东城', '', NULL, 1, 12, 1),
(4, '邮政平邮', 26, '中国	天津市	南开区', '汉正街', NULL, 0, 255, 1),
(5, '顺丰快递', 6, '中国	北京市	崇文', '华南理工大学', '华南理工大学', 0, 255, 1),
(6, '国通快递', 318, '中国	广东省	广州', '测试路', '专业代发---信誉、服务、责任、速度。每件2元代发，邮费实惠！共同发展。', 0, 255, 1),
(7, '中通速递', 318, '中国	广东省	广州', '测试路', '专业代发---信誉、服务、责任、速度。每件2元代发，邮费实惠！共同发展。', 0, 255, 1),
(8, '韵达快递', 318, '中国	广东省	广州', '测试路', '专业代发---信誉、服务、责任、速度。每件2元代发，邮费实惠！共同发展。', 0, 255, 1),
(9, '申通速递', 318, '中国	广东省	广州', '测试路', '专业代发---信誉、服务、责任、速度。每件2元代发，邮费实惠！共同发展。', 0, 255, 1);


DROP TABLE IF EXISTS `ecm_order_behalfs`;
CREATE TABLE IF NOT EXISTS `ecm_order_behalfs` (
  `rec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bh_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dl_id` int(10) unsigned NOT NULL DEFAULT '0',
  `evaluation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(255) NOT NULL,
  `credit_value` tinyint(1) NOT NULL DEFAULT '0',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`,`bh_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `ecm_market_behalf`;
CREATE TABLE IF NOT EXISTS `ecm_market_behalf` (
  `mk_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '市场编号',
  `bh_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '代发编号',
  PRIMARY KEY (`mk_id`,`bh_id`),
  KEY `bh_id` (`bh_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='市场与代发关联表';


DROP TABLE IF EXISTS `ecm_behalf_delivery`;
CREATE TABLE IF NOT EXISTS `ecm_behalf_delivery` (
  `bh_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '代发编号',
  `dl_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '快递编号',
  PRIMARY KEY (`bh_id`,`dl_id`),
  KEY `bh_id` (`dl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='快递与代发关联表';


DROP TABLE IF EXISTS `ecm_category_behalf`;
CREATE TABLE IF NOT EXISTS `ecm_category_behalf` (
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类编号',
  `bh_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '代发编号',
  `bh_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '代发收费',
  PRIMARY KEY (`cate_id`,`bh_id`),
  KEY `bh_id` (`bh_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品分类与代发收费';


ALTER TABLE `ecm_order_extm` ADD `bh_id` INT( 10 ) UNSIGNED NULL COMMENT '代发编号',
ADD `dl_id` INT( 10 ) UNSIGNED NULL COMMENT '快递编号';

ALTER TABLE `ecm_order_extm` ADD INDEX ( `bh_id` , `dl_id` ) ;

