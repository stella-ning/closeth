alter table ecm_paylog add column `customer_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
add column `status`  int(32) NOT NULL DEFAULT 0 ;