DROP TABLE IF EXISTS `ecm_store_rates`;
CREATE TABLE IF NOT EXISTS `ecm_store_rates` (
  `store_id` int(10) unsigned NOT NULL,
  `lack_rate` float(5,2) unsigned NOT NULL,
  `back_rate` float(5,2) unsigned NOT NULL,
  `praise_rate` float(5,2) unsigned NOT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='店铺统计';
