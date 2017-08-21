
ALTER TABLE `ecm_goods`
ADD COLUMN `score`  int NULL DEFAULT 0 COMMENT '商品评分' AFTER `delivery_weight`;

CREATE TABLE `ecm_statis_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_online_goods` int(11) DEFAULT '0' COMMENT '线上商品总数',
  `max_views` int(11) DEFAULT '0' COMMENT '最大浏览量',
  `max_sales` int(11) DEFAULT '0' COMMENT '最大销售产品量',
  `max_behalf` int(11) DEFAULT '0' COMMENT '最大代发数',
  `ins_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
