/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : yyladmin

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 05/05/2020 23:25:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yyl_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_menu`;
CREATE TABLE `yyl_admin_menu`  (
  `admin_menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `menu_pid` int(11) NOT NULL DEFAULT 0 COMMENT '菜单父级id',
  `menu_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单名称',
  `menu_url` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单链接',
  `menu_sort` int(6) NOT NULL DEFAULT 200 COMMENT '菜单排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_unauth` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否无需权限1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `insert_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_menu_id`) USING BTREE,
  INDEX `admin_menu_id`(`admin_menu_id`) USING BTREE,
  INDEX `menu_pid`(`menu_pid`, `menu_name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_menu
-- ----------------------------
INSERT INTO `yyl_admin_menu` VALUES (1, 0, '首页', 'admin/AdminIndex/index', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (2, 0, '系统设置', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (3, 2, '菜单管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (4, 2, '用户管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (5, 2, '权限管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (12, 2, '个人中心', 'admin/AdminUser/userCenter', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (13, 3, '菜单列表', 'admin/AdminMenu/menuList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (14, 3, '菜单添加', 'admin/AdminMenu/menuAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (15, 3, '菜单修改', 'admin/AdminMenu/menuEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (16, 3, '菜单删除', 'admin/AdminMenu/menuDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (17, 4, '用户列表', 'admin/AdminUser/userList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (18, 4, '用户添加', 'admin/AdminUser/userAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (19, 4, '用户修改', 'admin/AdminUser/userEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (20, 4, '用户删除', 'admin/AdminUser/userDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (22, 5, '权限列表', 'admin/AdminRule/ruleList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (23, 5, '权限添加', 'admin/AdminRule/ruleAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (24, 5, '权限修改', 'admin/AdminRule/ruleEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (25, 5, '权限删除', 'admin/AdminRule/ruleDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (27, 3, '菜单是否禁用', 'admin/AdminMenu/menuProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (28, 3, '菜单无需权限', 'admin/AdminMenu/menuUnauth', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (29, 4, '用户信息', 'admin/AdminUser/userInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (30, 4, '用户是否禁用', 'admin/AdminUser/userProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (31, 4, '用户权限分配', 'admin/AdminUser/userRule', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (32, 4, '用户密码重置', 'admin/AdminUser/userRepwd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (33, 5, '权限禁用', 'admin/AdminRule/ruleProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (36, 0, '实用工具', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (35, 4, '用户是否超管', 'admin/AdminUser/userSuperAdmin', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (37, 36, '生成随机字符', 'admin/AdminTool/randomStr', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (38, 36, '时间戳转换', 'admin/AdminTool/timestamp', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (39, 36, 'MD5加密', 'admin/AdminTool/md5Enc', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (40, 36, '生成二维码', 'admin/AdminTool/qrcode', 200, '0', '1', 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_rule`;
CREATE TABLE `yyl_admin_rule`  (
  `admin_rule_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `admin_menu_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单id',
  `rule_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权限名称',
  `rule_desc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限描述',
  `rule_sort` int(10) NULL DEFAULT 200 COMMENT '权限排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `insert_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_rule_id`) USING BTREE,
  INDEX `admin_rule_id`(`admin_rule_id`) USING BTREE,
  INDEX `rule_name`(`rule_name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_rule
-- ----------------------------
INSERT INTO `yyl_admin_rule` VALUES (1, '0', '管理员', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_rule` VALUES (2, '0', '技术', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_rule` VALUES (3, '0', '产品', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_rule` VALUES (4, '0', '操作', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_rule` VALUES (5, '0', '客服', '', 200, '0', 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_user`;
CREATE TABLE `yyl_admin_user`  (
  `admin_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `admin_rule_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限id',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账号',
  `nickname` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '昵称',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'static/img/favicon.ico' COMMENT '头像',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `sort` int(10) NULL DEFAULT 200 COMMENT '排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_super_admin` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否超级管理员1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `login_num` int(8) NULL DEFAULT 0 COMMENT '登录次数',
  `login_ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '登录IP',
  `login_time` datetime(0) NULL DEFAULT NULL COMMENT '登录时间',
  `logout_time` datetime(0) NULL DEFAULT NULL COMMENT '退出时间',
  `insert_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_user_id`) USING BTREE,
  INDEX `admin_user_id`(`admin_user_id`) USING BTREE,
  INDEX `username`(`username`, `password`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_user
-- ----------------------------
INSERT INTO `yyl_admin_user` VALUES (1, '', 'yyladmin', 'yyladmin', 'e10adc3949ba59abbe56e057f20f883e', 'static/img/favicon.ico', '', 200, '0', '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (2, '', 'skyselang', 'skyselang', 'e10adc3949ba59abbe56e057f20f883e', 'static/img/favicon.ico', '', 200, '0', '0', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
