ALTER TABLE `ecm_goods` ADD `service_shipa` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ʵ��' AFTER `price` ;

ALTER TABLE `ecm_store` ADD `service_daifa` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'һ������' AFTER `enable_radar` ,
ADD `service_tuixian` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '���ֽ�' AFTER `service_daifa` ,
ADD `service_huankuan` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '������' AFTER `service_tuixian` ;