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
DROP TABLE IF EXISTS `ecm_batchtrans`;
CREATE TABLE `ecm_batchtrans` (
  `flownumber` varchar(32) NOT NULL,
  `account` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `ori_money` float(28,2) NOT NULL DEFAULT 0,
  `now_money` float(28,2) NOT NULL DEFAULT 0,
  `sitesave_money` float(28,2) NOT NULL DEFAULT 0,
  `aliget_money` float(28,2) NOT NULL DEFAULT 0,
  `flag` varchar(5) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `tradeno` varchar(32) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `finishtime` varchar(32) DEFAULT NULL,
  `batchnum` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`flownumber`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
