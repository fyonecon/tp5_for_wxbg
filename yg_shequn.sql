/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : yg_shequn

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-11-19 15:03:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zl_admin`
-- ----------------------------
DROP TABLE IF EXISTS `zl_admin`;
CREATE TABLE `zl_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` int(11) DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  `login_ip` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of zl_admin
-- ----------------------------
INSERT INTO `zl_admin` VALUES ('1', '张力', 'admin', 'CT1ZZFRuBGsGPgU/W24ENg==', '1', '13529171137', '178417451@qq.com', '2017-11-16 12:27:08', '127.0.0.1', '1', '2017-10-25 15:23:14', '2017-11-16 12:27:08');

-- ----------------------------
-- Table structure for `zl_admin_group`
-- ----------------------------
DROP TABLE IF EXISTS `zl_admin_group`;
CREATE TABLE `zl_admin_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(255) NOT NULL DEFAULT '',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zl_admin_group
-- ----------------------------
INSERT INTO `zl_admin_group` VALUES ('1', '超级管理员', '1', '0,0_0,0_1,1,1_0,1_1,1_2,2,2_0,2_1,2_2,3,3_0,4,4_0,4_1,4_2,4_3,4_4,4_5,5,5_0,5_1,5_2,5_3,5_4,5_5,5_6,5_7,5_8', '2017-02-12 13:22:56', '2017-05-15 11:20:30');
INSERT INTO `zl_admin_group` VALUES ('2', '管理员', '1', '0,0_0,2,2_0,2_1,2_2,2_3,2_4', '2017-02-12 14:55:31', '2017-07-31 22:48:33');

-- ----------------------------
-- Table structure for `zl_admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `zl_admin_log`;
CREATE TABLE `zl_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_time` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  `login_ip` varchar(50) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of zl_admin_log
-- ----------------------------
INSERT INTO `zl_admin_log` VALUES ('1', 'admin', 'admin123', '2017-11-05 10:18:28', '127.0.0.1', '2017-11-05 10:18:28', '2017-11-05 10:18:28');
INSERT INTO `zl_admin_log` VALUES ('2', 'admin', 'admin123', '2017-11-05 10:25:29', '127.0.0.1', '2017-11-05 10:25:29', '2017-11-05 10:25:29');
INSERT INTO `zl_admin_log` VALUES ('3', 'admin', 'admin123', '2017-11-05 18:21:39', '127.0.0.1', '2017-11-05 18:21:39', '2017-11-05 18:21:39');
INSERT INTO `zl_admin_log` VALUES ('4', 'admin	', 'admin123', '2017-11-07 13:58:47', '127.0.0.1', '2017-11-07 13:58:47', '2017-11-07 13:58:47');
INSERT INTO `zl_admin_log` VALUES ('5', 'admin', 'admin123', '2017-11-07 13:58:59', '127.0.0.1', '2017-11-07 13:58:59', '2017-11-07 13:58:59');
INSERT INTO `zl_admin_log` VALUES ('6', 'admin', 'admin123', '2017-11-14 13:15:21', '127.0.0.1', '2017-11-14 13:15:21', '2017-11-14 13:15:21');
INSERT INTO `zl_admin_log` VALUES ('7', 'admin', 'admin123', '2017-11-14 23:38:04', '127.0.0.1', '2017-11-14 23:38:04', '2017-11-14 23:38:04');
INSERT INTO `zl_admin_log` VALUES ('8', 'admin', 'admin123', '2017-11-15 12:28:10', '127.0.0.1', '2017-11-15 12:28:10', '2017-11-15 12:28:10');
INSERT INTO `zl_admin_log` VALUES ('9', 'admin', 'admin123', '2017-11-15 18:42:40', '127.0.0.1', '2017-11-15 18:42:40', '2017-11-15 18:42:40');
INSERT INTO `zl_admin_log` VALUES ('10', 'admin', 'admin123', '2017-11-15 19:31:23', '127.0.0.1', '2017-11-15 19:31:23', '2017-11-15 19:31:23');
INSERT INTO `zl_admin_log` VALUES ('11', 'admin', 'admin123', '2017-11-16 12:27:08', '127.0.0.1', '2017-11-16 12:27:08', '2017-11-16 12:27:08');

-- ----------------------------
-- Table structure for `zl_qun`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun`;
CREATE TABLE `zl_qun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '群名称',
  `key` varchar(50) NOT NULL COMMENT '标识符',
  `maxopen` int(11) NOT NULL DEFAULT '0' COMMENT '打开多少次切换',
  `maxtouch` int(11) NOT NULL DEFAULT '120' COMMENT '长按多少次切换',
  `default_img` varchar(255) DEFAULT NULL,
  `bg_img` varchar(255) DEFAULT NULL,
  `now_ewm_id` int(11) NOT NULL,
  `rukou` varchar(255) NOT NULL DEFAULT '0' COMMENT '入口',
  `controller` varchar(20) DEFAULT '0' COMMENT '控制器',
  `tongji1` varchar(255) DEFAULT NULL,
  `tongji2` varchar(255) DEFAULT NULL,
  `black_area` varchar(255) DEFAULT NULL,
  `black_ip` varchar(255) DEFAULT NULL,
  `black_name` varchar(255) DEFAULT NULL,
  `get_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否获取用户信息',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  UNIQUE KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zl_qun
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_black`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_black`;
CREATE TABLE `zl_qun_black` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(30) NOT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of zl_qun_black
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_blackip`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_blackip`;
CREATE TABLE `zl_qun_blackip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of zl_qun_blackip
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_ewm`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_ewm`;
CREATE TABLE `zl_qun_ewm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qun_id` tinyint(4) NOT NULL COMMENT '群id',
  `ewm_id` int(11) NOT NULL,
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `opens` int(11) NOT NULL DEFAULT '0' COMMENT '打开人次',
  `touchs` int(11) NOT NULL DEFAULT '0' COMMENT '长按人次',
  `imgs` int(11) DEFAULT '0',
  `members` int(11) DEFAULT '0',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qunid_ewm_id` (`qun_id`,`ewm_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zl_qun_ewm
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_hosts`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_hosts`;
CREATE TABLE `zl_qun_hosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(3) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of zl_qun_hosts
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_rukou`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_rukou`;
CREATE TABLE `zl_qun_rukou` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qun_id` int(11) NOT NULL,
  `host301` varchar(150) NOT NULL,
  `host200` varchar(150) NOT NULL,
  `rukou` varchar(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `qun_id` (`qun_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of zl_qun_rukou
-- ----------------------------

-- ----------------------------
-- Table structure for `zl_qun_user`
-- ----------------------------
DROP TABLE IF EXISTS `zl_qun_user`;
CREATE TABLE `zl_qun_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(28) NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `ip2` varchar(100) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `ua` varchar(255) DEFAULT NULL,
  `rukou` varchar(10) DEFAULT NULL,
  `qun_id` tinyint(4) NOT NULL DEFAULT '0',
  `ewm_id` int(11) NOT NULL DEFAULT '0' COMMENT '二维码id',
  `opens` int(11) NOT NULL DEFAULT '0' COMMENT '打开次数',
  `touchs` int(11) NOT NULL DEFAULT '0' COMMENT '长按次数',
  `black` int(11) NOT NULL DEFAULT '0',
  `blackip` int(11) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `qunid_openid` (`openid`,`qun_id`) USING BTREE,
  KEY `qunid_ewmid` (`qun_id`,`ewm_id`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE,
  KEY `qunid_ewmid_openid` (`openid`,`qun_id`,`ewm_id`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE,
  KEY `ip2` (`ip2`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zl_qun_user
-- ----------------------------
