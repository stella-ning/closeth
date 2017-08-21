DROP TABLE IF EXISTS `ecm_statistics`;
CREATE TABLE `ecm_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `user_name` varchar(50) DEFAULT '0',
  `money_dj` decimal(10,2) NOT NULL DEFAULT '0.00',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `statis` decimal(10,2) NOT NULL DEFAULT '0.00',
  `log_id` int(10) unsigned  DEFAULT NULL,
  `admin_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_USER_ID` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;