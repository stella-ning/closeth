/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : ecmall51

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2015-09-18 20:16:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ecm_alitrade`
-- ----------------------------
DROP TABLE IF EXISTS `ecm_alitrade`;
CREATE TABLE `ecm_alitrade` (
  `ali_trade_no` varchar(32) NOT NULL,
  `total_fee` float(28,2) NOT NULL,
  `createtime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `trade_status` int(32) NOT NULL default 0,
  `title` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ali_trade_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
