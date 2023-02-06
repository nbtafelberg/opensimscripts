/*
 Navicat MySQL Data Transfer

 Source Server         : Wolf Territories
 Source Server Type    : MariaDB
 Source Server Version : 100332
 Source Host           : grid.wolfterritories.org:3306
 Source Schema         : grid

 Target Server Type    : MariaDB
 Target Server Version : 100332
 File Encoding         : 65001

 Date: 19/04/2022 14:31:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for regions
-- ----------------------------
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `regionname` varchar(200) DEFAULT NULL,
  `servername` varchar(255) DEFAULT NULL,
  `xpos` int(200) DEFAULT NULL,
  `ypos` int(200) DEFAULT NULL,
  `estatename` varchar(200) DEFAULT NULL,
  `owner` varchar(200) DEFAULT NULL,
  `shortname` varchar(200) DEFAULT NULL,
  `createdate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uuid` varchar(200) DEFAULT uuid(),
  `port` int(80) DEFAULT NULL,
  `databasename` varchar(79) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `regionname` (`regionname`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for regions_copy1
-- ----------------------------
DROP TABLE IF EXISTS `regions_copy1`;
CREATE TABLE `regions_copy1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `regionname` varchar(200) DEFAULT NULL,
  `servername` varchar(255) DEFAULT NULL,
  `xpos` varchar(200) DEFAULT NULL,
  `ypos` varchar(200) DEFAULT NULL,
  `estatename` varchar(200) DEFAULT NULL,
  `owner` varchar(200) DEFAULT NULL,
  `shortname` varchar(200) DEFAULT NULL,
  `createdate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uuid` varchar(200) DEFAULT uuid(),
  `port` int(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `regionname` (`regionname`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for servers
-- ----------------------------
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `servername` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
