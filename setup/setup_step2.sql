-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 11, 2013 at 03:18 PM
-- Server version: 5.1.68
-- PHP Version: 5.3.22

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `p_common`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE IF NOT EXISTS `application` (
  `application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `application_nm` varchar(200) CHARACTER SET latin1 NOT NULL,
  `application_desc` varchar(1000) CHARACTER SET latin1 DEFAULT NULL,
  `application_url` varchar(1024) CHARACTER SET latin1 DEFAULT NULL,
  `application_landing_url` varchar(1024) DEFAULT NULL,
  `project_admin_url` varchar(1024) CHARACTER SET latin1 DEFAULT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `application_application_nm_idx` (`application_nm`),
  KEY `application_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `application_role`
--

CREATE TABLE IF NOT EXISTS `application_role` (
  `application_role_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) NOT NULL,
  `application_role_nm` varchar(200) CHARACTER SET latin1 NOT NULL,
  `application_parent_role` bigint(20) NOT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`application_role_id`),
  KEY `appl_role_appl_id_fk` (`application_id`),
  KEY `appl_role_deleted_idx` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_nm` varchar(128) CHARACTER SET latin1 NOT NULL,
  `customer_logo_url` varchar(1024) CHARACTER SET latin1 DEFAULT NULL,
  `customer_desc` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_customer_nm_idx` (`customer_nm`),
  KEY `customer_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_application`
--

CREATE TABLE IF NOT EXISTS `customer_application` (
  `customer_application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`customer_application_id`),
  KEY `customer_application_customer_fk` (`customer_id`),
  KEY `customer_application_application_fk` (`application_id`),
  KEY `customer_application_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_domain`
--

CREATE TABLE IF NOT EXISTS `customer_domain` (
  `customer_domain_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `domain_nm` varchar(256) NOT NULL,
  `customer_self_signup` varchar(256) DEFAULT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`customer_domain_id`),
  KEY `customer_domain_customer_fk` (`customer_id`),
  KEY `customer_domain_deleted_idx` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customer_user`
--

CREATE TABLE IF NOT EXISTS `customer_user` (
  `customer_user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`customer_user_id`),
  KEY `customer_user_customer_fk` (`customer_id`),
  KEY `customer_user_user_fk` (`user_id`),
  KEY `customer_user_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_nm` varchar(255) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `user_abbr` varchar(32) DEFAULT NULL,
  `salt` varchar(50) DEFAULT NULL,
  `last_pw_change` datetime DEFAULT NULL,
  `user_full_nm` varchar(255) DEFAULT NULL,
  `onetimepad` varchar(200) DEFAULT NULL,
  `updt_usr_id` bigint(20) NOT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_user_nm_idx` (`user_nm`),
  KEY `user_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=45 ;


--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `user_insert`;
DELIMITER //
CREATE TRIGGER `user_insert` AFTER INSERT ON `user`
 FOR EACH ROW insert into user_ver select * from user where user_id = NEW.user_id
//
DELIMITER ;
DROP TRIGGER IF EXISTS `user_update`;
DELIMITER //
CREATE TRIGGER `user_update` AFTER UPDATE ON `user`
 FOR EACH ROW insert into user_ver select * from user where user_id = NEW.user_id
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_application`
--

CREATE TABLE IF NOT EXISTS `user_application` (
  `user_application_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `application_id` bigint(20) NOT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_application_id`),
  UNIQUE KEY `user_application_unique_idx` (`user_id`,`application_id`),
  KEY `user_application_user_fk` (`user_id`),
  KEY `user_application_application_fk` (`application_id`),
  KEY `user_application_deleted_idx` (`deleted`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=74 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_application_role`
--

CREATE TABLE IF NOT EXISTS `user_application_role` (
  `user_application_role_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `application_role_id` bigint(20) NOT NULL,
  `crea_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `crea_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updt_usr_id` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_application_role_id`),
  KEY `user_application_role_user_fk` (`user_id`),
  KEY `user_application_role_application_role_fk` (`application_role_id`),
  KEY `user_application_role_deleted_idx` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_ver`
--

CREATE TABLE IF NOT EXISTS `user_ver` (
  `user_id` bigint(20) NOT NULL,
  `user_nm` varchar(255) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `user_abbr` varchar(32) DEFAULT NULL,
  `salt` varchar(50) DEFAULT NULL,
  `last_pw_change` datetime DEFAULT NULL,
  `user_full_nm` varchar(255) DEFAULT NULL,
  `onetimepad` varchar(200) DEFAULT NULL,
  `updt_usr_id` bigint(20) NOT NULL,
  `updt_dtm` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application_role`
--
ALTER TABLE `application_role`
  ADD CONSTRAINT `appl_role_appl_id_fk` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_application`
--
ALTER TABLE `customer_application`
  ADD CONSTRAINT `customer_application_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_application_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_domain`
--
ALTER TABLE `customer_domain`
  ADD CONSTRAINT `customer_domain_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_user`
--
ALTER TABLE `customer_user`
  ADD CONSTRAINT `customer_user_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_application`
--
ALTER TABLE `user_application`
  ADD CONSTRAINT `user_application_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_application_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`) ON UPDATE CASCADE;

--
-- Constraints for table `user_application_role`
--
ALTER TABLE `user_application_role`
  ADD CONSTRAINT `user_application_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_application_role_ibfk_2` FOREIGN KEY (`application_role_id`) REFERENCES `application_role` (`application_role_id`) ON UPDATE CASCADE;
