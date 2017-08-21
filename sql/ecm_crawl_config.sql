CREATE TABLE IF NOT EXISTS `ecm_crawl_config` (
  `ip` VARCHAR(255) NOT NULL,
  `start_id` INT(10) UNSIGNED DEFAULT 0,
  `end_id` INT(10) UNSIGNED DEFAULT 0,
  `now_id` INT(10) UNSIGNED DEFAULT 0,
  `exit_code` INT(10) DEFAULT 0,
  `last_update` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
