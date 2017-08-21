DROP TABLE IF EXISTS `ecm_logistics_company`;
CREATE TABLE `ecm_logistics_company` (
  `id` varchar(20) NOT NULL COMMENT '物流公司ID',
  `code` varchar(20) NOT NULL COMMENT '物流公司code',
  `name` varchar(100) NOT NULL COMMENT '物流公司名称',
  `reg_mail_no` varchar(500) NULL COMMENT '物流公司对应的运单规则',
  KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='淘宝物流表';
