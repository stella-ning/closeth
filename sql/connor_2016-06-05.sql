DROP TABLE IF EXISTS `ecm_store_rule`;
CREATE TABLE IF NOT EXISTS `ecm_store_rule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='店铺规则';

DROP TABLE IF EXISTS `ecm_store_restore`;
CREATE TABLE IF NOT EXISTS `ecm_store_restore` (
  `store_id` int(10) unsigned NOT NULL,
  `reason` text NOT NULL,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0', -- 0 apply 1 pass 2 reject
  `remarks` varchar(255) NULL,
  `last_update` int(10) unsigned NOT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='店铺恢复申请';
