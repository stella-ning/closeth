ALTER TABLE `ecm_goods` ADD `service_shipa` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '实拍' AFTER `price` ;

ALTER TABLE `ecm_store` ADD `service_daifa` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '一件代发' AFTER `enable_radar` ,
ADD `service_tuixian` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '退现金' AFTER `service_daifa` ,
ADD `service_huankuan` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '包换款' AFTER `service_tuixian` ;