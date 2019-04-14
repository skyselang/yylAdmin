/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : yylblog

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 08/04/2019 21:24:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ya_admin
-- ----------------------------
DROP TABLE IF EXISTS `ya_admin`;
CREATE TABLE `ya_admin`  (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'yylAdmin' COMMENT '昵称',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '账号',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '密码',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0.0.0.0' COMMENT '登录ip',
  `login_num` int(11) NULL DEFAULT 0 COMMENT '登录次数',
  `device` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '登录设备',
  `login_time` datetime NULL DEFAULT '0000-00-00 00:00:00' COMMENT '登录时间',
  `create_time` datetime NULL DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `exit_time` datetime NULL DEFAULT '0000-00-00 00:00:00' COMMENT '退出时间',
  PRIMARY KEY (`admin_id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ya_admin
-- ----------------------------
INSERT INTO `ya_admin` VALUES (1, 'yylAdmin', 'yyladmin@qq.com', 'yyladmin', '0827df0fd834daa31e9aec7396379033', '127.0.0.1', 84, '{\"os\":\"windows\",\"ie\":false,\"weixin\":false,\"android\":false,\"ios\":false}', '2019-04-07 22:55:33', '0000-00-00 00:00:00', '2019-04-05 09:37:19', '2019-04-07 20:43:01');

-- ----------------------------
-- Table structure for ya_image
-- ----------------------------
DROP TABLE IF EXISTS `ya_image`;
CREATE TABLE `ya_image`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 338 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ya_news
-- ----------------------------
DROP TABLE IF EXISTS `ya_news`;
CREATE TABLE `ya_news`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '动态id',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'IGCA国际高尔夫旅游' COMMENT '标题',
  `author` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'IGCA' COMMENT '作者',
  `keywords` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'igca' COMMENT '关键词',
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'igca' COMMENT '简介描述',
  `image` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '图片',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '类型1图文2链接3图集',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `wwwurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '链接',
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图集',
  `court_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '球场id',
  `look` int(11) NULL DEFAULT 12 COMMENT '阅读数',
  `good` int(11) NULL DEFAULT 0 COMMENT '点赞数',
  `discuss` int(11) NULL DEFAULT 0 COMMENT '评论数',
  `sort` int(11) NULL DEFAULT 110 COMMENT '排序',
  `is_frame` tinyint(1) NULL DEFAULT 1 COMMENT '1上架-1下架',
  `is_dele` tinyint(1) NULL DEFAULT 1 COMMENT '1正常-1删除',
  `delete_time` int(11) NULL DEFAULT 0 COMMENT '删除时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '修改时间',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 262 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '新闻' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ya_region
-- ----------------------------
DROP TABLE IF EXISTS `ya_region`;
CREATE TABLE `ya_region`  (
  `region_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `region_pid` int(11) NULL DEFAULT 0 COMMENT '父级id',
  `region_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `region_sort` int(10) NULL DEFAULT 200 COMMENT '排序',
  `is_delete` tinyint(2) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  PRIMARY KEY (`region_id`) USING BTREE,
  INDEX `id`(`region_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '树形表格' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of ya_region
-- ----------------------------
INSERT INTO `ya_region` VALUES (1, 0, '北京', 200, 0);
INSERT INTO `ya_region` VALUES (2, 0, '天津', 200, 0);
INSERT INTO `ya_region` VALUES (3, 0, '上海', 200, 0);
INSERT INTO `ya_region` VALUES (4, 0, '重庆', 200, 0);
INSERT INTO `ya_region` VALUES (5, 0, '河北', 200, 0);
INSERT INTO `ya_region` VALUES (6, 0, '山西', 200, 0);
INSERT INTO `ya_region` VALUES (7, 0, '辽宁', 200, 0);
INSERT INTO `ya_region` VALUES (8, 0, '吉林', 200, 0);
INSERT INTO `ya_region` VALUES (9, 0, '黑龙江', 200, 0);
INSERT INTO `ya_region` VALUES (10, 0, '江苏', 200, 0);
INSERT INTO `ya_region` VALUES (11, 0, '浙江', 200, 0);
INSERT INTO `ya_region` VALUES (12, 0, '安徽', 200, 0);
INSERT INTO `ya_region` VALUES (13, 0, '福建', 200, 0);
INSERT INTO `ya_region` VALUES (14, 0, '江西', 200, 0);
INSERT INTO `ya_region` VALUES (15, 0, '山东', 200, 0);
INSERT INTO `ya_region` VALUES (16, 0, '河南', 200, 0);
INSERT INTO `ya_region` VALUES (17, 0, '湖北', 200, 0);
INSERT INTO `ya_region` VALUES (18, 0, '湖南', 200, 0);
INSERT INTO `ya_region` VALUES (19, 0, '广东', 200, 0);
INSERT INTO `ya_region` VALUES (20, 0, '海南', 200, 0);
INSERT INTO `ya_region` VALUES (21, 0, '四川', 200, 0);
INSERT INTO `ya_region` VALUES (22, 0, '贵州', 200, 0);
INSERT INTO `ya_region` VALUES (23, 0, '云南', 200, 0);
INSERT INTO `ya_region` VALUES (24, 0, '陕西', 200, 0);
INSERT INTO `ya_region` VALUES (25, 0, '甘肃', 200, 0);
INSERT INTO `ya_region` VALUES (26, 0, '青海', 200, 0);
INSERT INTO `ya_region` VALUES (27, 0, '台湾', 200, 0);
INSERT INTO `ya_region` VALUES (28, 0, '内蒙古', 200, 0);
INSERT INTO `ya_region` VALUES (29, 0, '广西', 200, 0);
INSERT INTO `ya_region` VALUES (30, 0, '西藏', 200, 0);
INSERT INTO `ya_region` VALUES (31, 0, '宁夏', 200, 0);
INSERT INTO `ya_region` VALUES (32, 0, '新疆', 200, 0);
INSERT INTO `ya_region` VALUES (33, 0, '香港', 200, 0);
INSERT INTO `ya_region` VALUES (34, 0, '澳门', 200, 0);
INSERT INTO `ya_region` VALUES (35, 1, '东城区', 200, 0);
INSERT INTO `ya_region` VALUES (36, 1, '西城区', 200, 0);
INSERT INTO `ya_region` VALUES (37, 1, '朝阳区', 200, 0);

SET FOREIGN_KEY_CHECKS = 1;
