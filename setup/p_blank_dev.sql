/*
 Navicat Premium Data Transfer

 Source Server         : localdb
 Source Server Type    : MySQL
 Source Server Version : 50163
 Source Host           : localhost
 Source Database       : p_blank_dev

 Target Server Type    : MySQL
 Target Server Version : 50163
 File Encoding         : utf-8

 Date: 11/05/2013 15:11:20 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `project`
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `project_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_txt` varchar(64) NOT NULL,
  `project_desc` text,
  `project_lead` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `crea_dtm` datetime NOT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` bigint(20) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Records of `project`
-- ----------------------------
BEGIN;
INSERT INTO `project` VALUES ('1', 'Example', 'This is an example', '143', '2', '0000-00-00 00:00:00', '0', '2013-10-10 14:40:50', '129', '1'), ('2', 'Example', 'This is an example', '0', '2', '0000-00-00 00:00:00', '0', '2013-07-30 15:48:49', '87', '1'), ('3', 'asdf', 'asd', '43', '2', '2013-07-08 12:10:54', '89', '2013-07-15 11:00:01', '89', '1'), ('4', 'xyz', 'xyz', '87', '2', '2013-07-09 10:02:22', '89', '2013-07-10 16:16:16', '89', '1'), ('5', 'test', 'test', '43', '2', '2013-07-10 15:44:08', '89', '2013-07-10 16:16:19', '89', '1'), ('6', 'test', 'test', '43', '2', '2013-07-10 15:44:22', '89', '2013-07-10 16:18:47', '89', '1'), ('7', 'test', 'test', '43', '2', '2013-07-10 15:45:02', '89', '2013-09-16 14:55:57', '7', '1'), ('8', 'test', 'test', '7', '2', '2013-07-10 15:45:49', '89', '2013-09-16 14:56:02', '7', '1'), ('9', 'test', 'test', '7', '2', '2013-07-10 15:46:17', '89', '2013-10-10 14:40:54', '129', '1'), ('10', 'adf', 'asdf', '7', '2', '2013-07-10 15:46:48', '89', '2013-10-10 14:40:58', '129', '1'), ('11', 'adf', 'asdf', '7', '2', '2013-07-10 15:49:52', '89', '2013-09-16 14:56:22', '7', '1'), ('12', 'asdf', 'adf', '37', '2', '2013-07-10 15:52:19', '89', '2013-10-10 14:41:00', '129', '1'), ('13', 'new project', 'new', '7', '2', '2013-07-10 15:56:33', '89', '2013-09-16 14:56:34', '7', '1'), ('14', 'new project', 'new', '7', '2', '2013-07-10 15:58:06', '89', '2013-08-16 09:31:16', '2', '1'), ('15', 'new project', 'new', '7', '2', '2013-07-10 16:00:21', '89', '2013-10-10 14:41:03', '129', '1'), ('16', 'james', 'james', '89', '2', '2013-07-10 16:01:57', '89', '2013-09-16 14:56:48', '7', '1'), ('17', 'james', 'james', '89', '2', '2013-07-10 16:02:37', '89', '2013-10-10 14:41:06', '129', '1'), ('18', 'james', 'james', '89', '2', '2013-07-10 16:03:28', '89', '2013-07-10 16:16:29', '89', '1'), ('19', 'Subbu', 'Subbu dsc', '7', '2', '2013-07-11 09:40:29', '89', '2013-07-15 10:22:52', '89', '1'), ('20', 'adsf', 'asdf', '0', '2', '2013-07-15 11:20:07', '89', '2013-07-30 15:48:54', '87', '1'), ('21', 'Test32', 'Test12', '99', '2', '2013-10-10 15:56:37', '129', '2013-10-14 15:37:45', '129', '1'), ('22', 'Test', 'Test', '2', '2', '2013-10-10 15:59:11', '129', '2013-10-14 11:02:05', '129', '1'), ('23', 'tesr', 'test', '89', '2', '2013-10-17 15:24:09', '129', '2013-10-17 15:53:59', '129', '1'), ('24', '0', 'fdsfasf', '101', '2', '2013-10-17 15:24:37', '129', '2013-10-17 15:56:25', '129', '1'), ('25', 'asdfasd', 'fasdfasdf', '101', '2', '2013-10-17 15:25:28', '129', '2013-10-17 15:54:41', '129', '1'), ('26', 'aldsbfkjsa', 'aknskldnfln', '2', '2', '2013-10-17 15:54:58', '129', '2013-10-17 15:58:10', '129', '0');
COMMIT;

-- ----------------------------
--  Table structure for `project_user`
-- ----------------------------
DROP TABLE IF EXISTS `project_user`;
CREATE TABLE `project_user` (
  `project_usr_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `crea_usr_id` bigint(20) NOT NULL,
  `crea_dtm` datetime NOT NULL,
  `updt_usr_id` bigint(20) NOT NULL,
  `updt_dtm` datetime NOT NULL,
  PRIMARY KEY (`project_usr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- ----------------------------
--  Records of `project_user`
-- ----------------------------
BEGIN;
INSERT INTO `project_user` VALUES ('123', '26', '39', '129', '2013-10-17 15:58:10', '129', '2013-10-17 15:58:10'), ('122', '26', '99', '129', '2013-10-17 15:58:10', '129', '2013-10-17 15:58:10'), ('121', '26', '87', '129', '2013-10-17 15:58:10', '129', '2013-10-17 15:58:10'), ('120', '26', '89', '129', '2013-10-17 15:58:10', '129', '2013-10-17 15:58:10');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
