alter table ecm_order_vendor add ecm_order_id int(10) unsigned null comment '��Ӧ��ecmall�еĶ�����';
alter table ecm_order_vendor add index ecm_order_id(ecm_order_id);
