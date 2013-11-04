-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 06, 2013 at 12:32 AM
-- Server version: 5.1.65
-- PHP Version: 5.3.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `p_application`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_txt` varchar(64) NOT NULL,
  `project_desc` text,
  `customer_id` bigint(20) NOT NULL,
  `create_dtm` datetime NOT NULL,
  `create_user_id` bigint(20) NOT NULL,
  `update_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_user_id` bigint(20) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `column_meta`
--

CREATE TABLE IF NOT EXISTS `column_meta` (
  `column_meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `grid_nm` varchar(256) NOT NULL,
  `column_nm` varchar(256) NOT NULL,
  `column_alias_nm` varchar(256) DEFAULT NULL,
  `column_width` bigint(20) DEFAULT NULL,
  `column_editor_type_nm` varchar(256) DEFAULT NULL,
  `view_role_nm` varchar(256) DEFAULT NULL,
  `edit_role_nm` varchar(256) DEFAULT NULL,
  `crea_dtm` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `crea_usr_id` varchar(32) DEFAULT NULL,
  `updt_dtm` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `updt_usr_id` varchar(32) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`column_meta_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=254 ;



--
-- Table structure for table `example`
--

CREATE TABLE IF NOT EXISTS `example` (
  `example_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `Int1` int(11) DEFAULT NULL,
  `Varchar1` varchar(255) DEFAULT NULL,
  `Text1` text,
  `Date1` date DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`example_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



INSERT INTO `projects` (`project_id`, `project_txt`, `project_desc`, `customer_id`, `create_dtm`, `create_user_id`, `update_dtm`, `update_user_id`, `deleted`) VALUES
(1, 'Example', 'This is an example', 2, '0000-00-00 00:00:00', 0, '2013-04-15 14:45:36', 0, 0);

