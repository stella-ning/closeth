alter table ecm_order_vendor add ecm_order_id int(10) unsigned null comment '对应的ecmall中的订单号';
alter table ecm_order_vendor add index ecm_order_id(ecm_order_id);
