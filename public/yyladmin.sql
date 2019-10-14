/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50529
 Source Host           : localhost:3306
 Source Schema         : yyladmin

 Target Server Type    : MySQL
 Target Server Version : 50529
 File Encoding         : 65001

 Date: 14/10/2019 22:51:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yy_admin
-- ----------------------------
DROP TABLE IF EXISTS `yy_admin`;
CREATE TABLE `yy_admin`  (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账号id',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '账号',
  `password` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `login_time` int(11) NULL DEFAULT 0 COMMENT '登录时间',
  PRIMARY KEY (`admin_id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yy_admin
-- ----------------------------
INSERT INTO `yy_admin` VALUES (1, 'yyladmin', NULL, 0);

SET FOREIGN_KEY_CHECKS = 1;
