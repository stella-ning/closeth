alter table ecm_goods_vendor add good_http varchar(100) null;
alter table ecm_goods engine=innodb;
alter table ecm_goods_spec engine=innodb;
alter table ecm_goods_attr engine=innodb;
alter table ecm_goods_image engine=innodb;
alter table ecm_goods add key good_http(good_http);
