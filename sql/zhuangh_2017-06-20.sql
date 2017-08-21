#订单退货功能优化
ALTER TABLE `ecm_order_refund`
ADD COLUMN `dl_status`  tinyint(2) NULL  DEFAULT 0 COMMENT '退件状态 已收到 未收到'  AFTER `dl_name`;

#订单标记有货必发
ALTER TABLE `ecm_order`
ADD COLUMN `fa`  tinyint(2) NULL  DEFAULT 0 COMMENT '订单有货必发'  AFTER `behalf_fee`;

#单号列表
ALTER TABLE `ecm_order_modeb`
ADD COLUMN `status`  tinyint(2) NOT NULL DEFAULT 0 AFTER `name`,
ADD COLUMN `bh_id`  int(10) NOT NULL AFTER `name`,
ADD COLUMN `add_time`  varchar(15) NOT NULL AFTER `status`,
ADD COLUMN `invoice`  varchar(20) NOT NULL AFTER `add_time`,
ADD COLUMN `expires`  varchar(15) NULL AFTER `invoice` ;

#  增加订单商品数据字段
ALTER TABLE `ecm_order`
ADD COLUMN `total_quantity`  int(8) NOT NULL  DEFAULT 0  AFTER `fa`;
