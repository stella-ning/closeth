alter table ecm_store add column
`serv_refund` int(2) NOT NULL DEFAULT '0' ,add column
`serv_exchgoods` int(2) NOT NULL DEFAULT '0',add column
  `serv_sendgoods` int(2) NOT NULL DEFAULT '0',add column
  `serv_realpic` int(2) NOT NULL DEFAULT '0',add column
  `serv_addred` int(2) NOT NULL DEFAULT '0',add column
  `serv_modpic` int(2) NOT NULL DEFAULT '0',add column
  `serv_deltpic` int(2) NOT NULL DEFAULT '0',add column
  `serv_probexch` int(2) NOT NULL DEFAULT '0', add column
  `serv_golden` int(2) NOT NULL, add column   
  `shop_mall` varchar(100) NOT NULL DEFAULT '',add column 
  `floor` varchar(20) NOT NULL DEFAULT '',add column 
  `see_price` varchar(20) NOT NULL DEFAULT '',add column 
   `shop_http` varchar(100) NOT NULL DEFAULT '',add column    
   `has_link` tinyint(1) NOT NULL DEFAULT '0',  modify column
   `hot_search` varchar(255) default '',modify column
  `business_scope` varchar(50) default '',modify column
  `online_service` varchar(255) default '',modify column
  `hotline` varchar(255) default '',modify column
  `pic_slides` text