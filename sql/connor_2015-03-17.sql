alter table ecm_order_vendor add (
  receiver_phone varchar(100) null,
  receiver_city varchar(100) null,
  receiver_district varchar(100) null,
  buyer_email varchar(100) null,
  receiver_zip varchar(10) null,
  shipping_type varchar(10) null,
  total_fee decimal(10,2) unsigned,
  discount_fee decimal(10,2) unsigned,
  payment decimal(10,2) unsigned);
