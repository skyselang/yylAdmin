/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : yyladmin

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 15/10/2020 23:30:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yyl_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_log`;
CREATE TABLE `yyl_admin_log`  (
  `admin_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `admin_log_type` tinyint(1) NULL DEFAULT 2 COMMENT '1登录2操作3退出',
  `admin_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `admin_menu_id` int(11) NULL DEFAULT 0 COMMENT '菜单id',
  `request_method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求方式',
  `request_ip` varchar(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求ip',
  `request_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求国家',
  `request_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求省份',
  `request_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求城市',
  `request_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求区县',
  `request_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求地区',
  `request_isp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求ISP',
  `request_param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '请求时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_log_id`) USING BTREE,
  INDEX `admin_log_id`(`admin_log_id`) USING BTREE,
  INDEX `admin_user_id`(`admin_user_id`) USING BTREE,
  INDEX `request_isp`(`request_isp`) USING BTREE,
  INDEX `request_city`(`request_city`) USING BTREE,
  INDEX `request_province`(`request_province`) USING BTREE,
  INDEX `request_country`(`request_country`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_menu`;
CREATE TABLE `yyl_admin_menu`  (
  `admin_menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `menu_pid` int(11) NOT NULL DEFAULT 0 COMMENT '菜单父级id',
  `menu_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `menu_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单链接',
  `menu_sort` int(10) NOT NULL DEFAULT 200 COMMENT '菜单排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_unauth` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否无需权限1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_menu_id`) USING BTREE,
  INDEX `admin_menu_id`(`admin_menu_id`) USING BTREE,
  INDEX `menu_pid`(`menu_pid`, `menu_name`) USING BTREE,
  INDEX `menu_url`(`menu_url`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 112 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_menu
-- ----------------------------
INSERT INTO `yyl_admin_menu` VALUES (1, 0, '控制台', '', 250, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (2, 0, '系统管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (3, 88, '菜单管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (4, 88, '用户管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (5, 88, '角色管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (12, 2, '个人中心', '', 199, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (13, 3, '菜单列表', 'admin/AdminMenu/menuList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (14, 3, '菜单添加', 'admin/AdminMenu/menuAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (15, 3, '菜单修改', 'admin/AdminMenu/menuEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (16, 3, '菜单删除', 'admin/AdminMenu/menuDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (17, 4, '用户列表', 'admin/AdminUser/userList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (18, 4, '用户添加', 'admin/AdminUser/userAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (19, 4, '用户修改', 'admin/AdminUser/userEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (20, 4, '用户删除', 'admin/AdminUser/userDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (22, 5, '角色列表', 'admin/AdminRole/roleList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (23, 5, '角色添加', 'admin/AdminRole/roleAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (24, 5, '角色修改', 'admin/AdminRole/roleEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (25, 5, '角色删除', 'admin/AdminRole/roleDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (27, 3, '菜单是否禁用', 'admin/AdminMenu/menuProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (28, 3, '菜单无需权限', 'admin/AdminMenu/menuUnauth', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (29, 4, '用户信息', 'admin/AdminUser/userInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (30, 4, '用户是否禁用', 'admin/AdminUser/userProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (31, 4, '用户权限分配', 'admin/AdminUser/userRule', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (32, 4, '用户密码重置', 'admin/AdminUser/userPwd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (33, 5, '角色禁用', 'admin/AdminRole/roleProhibit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (36, 0, '实用工具', '', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (35, 4, '用户是否超管', 'admin/AdminUser/userSuperAdmin', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (37, 58, '随机字符串', 'admin/AdminTool/strRand', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (38, 58, '时间戳转换', 'admin/AdminTool/timeTran', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (40, 58, '生成二维码', 'admin/AdminTool/qrcode', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (41, 2, '日志管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (42, 41, '日志列表', 'admin/AdminLog/logList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (43, 41, '日志信息', 'admin/AdminLog/logInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (44, 41, '日志删除', 'admin/AdminLog/logDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (45, 12, '个人信息', 'admin/AdminUsers/usersInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (46, 12, '修改信息', 'admin/AdminUsers/usersEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (47, 12, '修改密码', 'admin/AdminUsers/usersPwd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (48, 12, '更换头像', 'admin/AdminUsers/usersAvatar', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (49, 1, '控制台', 'admin/AdminIndex/index', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (50, 36, '地图坐标拾取', 'admin/AdminTool/map', 150, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (51, 111, '登录', 'admin/AdminLogin/login', 160, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (52, 111, '退出', 'admin/AdminLogin/logout', 150, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (53, 2, '系统设置', '', 110, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (54, 12, '日志记录', 'admin/AdminUsers/usersLog', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (58, 36, '实用工具合集', 'admin/AdminTool/gather', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (56, 2, '访问统计', '', 120, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (64, 56, '日期统计', 'admin/AdminVisit/visitDate', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (60, 4, '用户权限明细', 'admin/AdminUser/userRuleInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (62, 53, '基本设置', 'admin/AdminSetting/settingBase', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (63, 58, '字符串转换', 'admin/AdminTool/strTran', 210, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (65, 56, '访问统计', 'admin/AdminVisit/visitStats', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (67, 56, '数量统计', 'admin/AdminVisit/visitCount', 220, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (71, 53, '缓存设置', 'admin/AdminSetting/settingCache', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (85, 53, 'Token设置', 'admin/AdminSetting/settingToken', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (73, 53, '验证码设置', 'admin/AdminSetting/settingVerify', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (75, 111, '验证码', 'admin/AdminLogin/verify', 170, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (88, 2, '权限管理', '', 210, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (87, 58, 'IP查询', 'admin/AdminTool/ipQuery', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (86, 58, '字节转换', 'admin/AdminTool/byteTran', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (111, 0, '登录退出', '', 200, '0', '0', 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_role`;
CREATE TABLE `yyl_admin_role`  (
  `admin_role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `admin_menu_ids` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单id',
  `role_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '权限描述',
  `role_sort` int(10) NULL DEFAULT 200 COMMENT '权限排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_role_id`) USING BTREE,
  INDEX `admin_rule_id`(`admin_role_id`) USING BTREE,
  INDEX `rule_name`(`role_name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_role
-- ----------------------------
INSERT INTO `yyl_admin_role` VALUES (1, '1,2,3,4,5,12,13,14,15,16,17,18,19,20,22,23,24,25,27,28,29,30,31,32,33,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,71,73,75,85,86,87,88,111', '管理员', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (2, '13,14,15,17,18,19,22,23,24,29,31,37,38,40,42,43,45,46,47,48,49,50,51,52,53,54,58,60,62,63,64,65,67,75,86,87', '技术', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (3, '1,2,3,4,5,12,13,15,17,18,19,22,23,29,36,37,38,40,41,42,43,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,75,86,87', '产品', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (4, '1,2,3,4,5,12,13,14,17,18,22,23,29,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,75,86,87', '运营', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (5, '1,2,3,4,5,12,13,17,22,36,37,38,40,41,42,45,46,47,48,49,50,51,52,54,56,58,62,63,64,65,67,75,86,87', '销售', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (6, '1,13,17,22,29,36,37,38,40,42,45,49,50,51,52,54,56,58,62,63,64,65,67,75,86,87,91,99', '操作', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (7, '', '客服', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (8, '', '财务', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (9, '', '行政', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (10, '13,17,22,29,37,38,40,42,45,49,50,51,52,54,58,62,63,64,65,67,75,86,87', '演示', '', 200, '0', 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_setting
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_setting`;
CREATE TABLE `yyl_admin_setting`  (
  `admin_setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设置id',
  `admin_verify` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '验证码设置',
  `admin_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'token设置',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`admin_setting_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '设置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_setting
-- ----------------------------
INSERT INTO `yyl_admin_setting` VALUES (1, 'a:7:{s:6:\"switch\";b:1;s:5:\"curve\";b:0;s:5:\"noise\";b:1;s:5:\"bgimg\";b:0;s:4:\"type\";s:1:\"1\";s:6:\"length\";s:1:\"4\";s:6:\"expire\";s:3:\"180\";}', 'a:2:{s:3:\"iss\";s:8:\"yylAdmin\";s:3:\"exp\";s:2:\"12\";}', NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_user`;
CREATE TABLE `yyl_admin_user`  (
  `admin_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `admin_role_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '角色id',
  `admin_menu_id` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单id',
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账号',
  `nickname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `email` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'static/img/favicon.ico' COMMENT '头像',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `sort` int(10) NULL DEFAULT 200 COMMENT '排序',
  `is_prohibit` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否禁用1是0否',
  `is_super_admin` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '是否超级管理员1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `login_num` int(10) NULL DEFAULT 0 COMMENT '登录次数',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录IP',
  `login_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录地区',
  `login_time` datetime(0) NULL DEFAULT NULL COMMENT '登录时间',
  `logout_time` datetime(0) NULL DEFAULT NULL COMMENT '退出时间',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_user_id`) USING BTREE,
  INDEX `admin_user_id`(`admin_user_id`) USING BTREE,
  INDEX `username`(`username`, `password`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_user
-- ----------------------------
INSERT INTO `yyl_admin_user` VALUES (1, '', '', 'skyselang', 'skyselang', 'e10adc3949ba59abbe56e057f20f883e', '', 'storage/admin/user/1/avatar.png?t=20200924000359', '超级管理员', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (2, '10', '0', 'yyladmin', 'yyladmin', 'e10adc3949ba59abbe56e057f20f883e', '', 'storage/admin/user/2/avatar.png?t=20200805092019', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (6, '10', '0', 'admin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (7, '10', '0', '12345', '12345', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (9, '10', '0', '123456', '123456', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
