CREATE TABLE IF NOT EXISTS `ecm_member_auth` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `vendor` tinyint(3) unsigned NOT NULL DEFAULT '0', -- 0:taobao,1:alibaba,2:paipai
  `access_token` VARCHAR(255) DEFAULT NULL,
  `expires_in` INT(10) DEFAULT 0, -- in seconds
  `refresh_token` VARCHAR(255) DEFAULT NULL,
  `re_expires_in` INT(10) DEFAULT 0,
  `r1_expires_in` INT(10) DEFAULT 0,
  `r2_expires_in` INT(10) DEFAULT 0,
  `w1_expires_in` INT(10) DEFAULT 0,
  `w2_expires_in` INT(10) DEFAULT 0,
  `vendor_user_nick` VARCHAR(255) DEFAULT NULL,
  `vendor_user_id` VARCHAR(255) DEFAULT NULL,
  `sub_vendor_user_id` VARCHAR(255) DEFAULT NULL,
  `sub_vendor_user_nick` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
