DROP TABLE IF EXISTS `ecm_store_log`;
CREATE TABLE IF NOT EXISTS `ecm_store_log` (
  `store_id` int(10) unsigned NOT NULL,
  `action_time` int(10) unsigned NOT NULL DEFAULT 0,
  `action_type` tinyint(3) unsigned NOT NULL DEFAULT 0, -- 0: close, 1: restore
  `rule_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='店铺操作日志';
