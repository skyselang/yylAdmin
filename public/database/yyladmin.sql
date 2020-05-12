/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : yyladmin

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 12/05/2020 20:45:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yyl_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_log`;
CREATE TABLE `yyl_admin_log`  (
  `admin_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `admin_user_id` int(11) NULL DEFAULT 0 COMMENT '用户id',
  `menu_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单链接',
  `request_method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求方式',
  `request_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求ip',
  `request_param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `insert_time` datetime(0) NULL DEFAULT NULL COMMENT '请求时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_log_id`) USING BTREE,
  INDEX `admin_log_id`(`admin_log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_log
-- ----------------------------
INSERT INTO `yyl_admin_log` VALUES (1, 0, 'admin/AdminLogin/login', 'POST', '127.0.0.1', 'a:2:{s:8:\"username\";s:8:\"yyladmin\";s:8:\"password\";s:6:\"123456\";}', 0, '2020-05-10 11:51:49', NULL);
INSERT INTO `yyl_admin_log` VALUES (2, 1, 'admin/AdminUser/userInfo', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"1\";}', 0, '2020-05-10 11:51:49', NULL);
INSERT INTO `yyl_admin_log` VALUES (3, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:52:01', NULL);
INSERT INTO `yyl_admin_log` VALUES (4, 1, 'admin/AdminTool/timestamp', 'POST', '127.0.0.1', 'a:4:{s:13:\"from_datetime\";s:0:\"\";s:12:\"to_timestamp\";s:0:\"\";s:14:\"from_timestamp\";s:0:\"\";s:11:\"to_datetime\";s:0:\"\";}', 0, '2020-05-10 11:52:03', NULL);
INSERT INTO `yyl_admin_log` VALUES (5, 1, 'admin/AdminTool/timestamp', 'POST', '127.0.0.1', 'a:4:{s:13:\"from_datetime\";s:19:\"2020-05-10 11:52:03\";s:12:\"to_timestamp\";s:10:\"1589082723\";s:14:\"from_timestamp\";s:0:\"\";s:11:\"to_datetime\";s:0:\"\";}', 0, '2020-05-10 11:52:03', NULL);
INSERT INTO `yyl_admin_log` VALUES (6, 1, 'admin/AdminTool/md5Enc', 'POST', '127.0.0.1', 'a:1:{s:3:\"str\";s:1:\"1\";}', 0, '2020-05-10 11:52:06', NULL);
INSERT INTO `yyl_admin_log` VALUES (7, 1, 'admin/AdminTool/qrcode', 'POST', '127.0.0.1', 'a:1:{s:10:\"qrcode_str\";s:1:\"1\";}', 0, '2020-05-10 11:52:09', NULL);
INSERT INTO `yyl_admin_log` VALUES (8, 1, 'admin/AdminUser/userCenter', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"1\";}', 0, '2020-05-10 11:52:21', NULL);
INSERT INTO `yyl_admin_log` VALUES (9, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 11:52:21', NULL);
INSERT INTO `yyl_admin_log` VALUES (10, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:1:\"1\";}', 0, '2020-05-10 11:52:25', NULL);
INSERT INTO `yyl_admin_log` VALUES (11, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:1:\"3\";}', 0, '2020-05-10 11:53:30', NULL);
INSERT INTO `yyl_admin_log` VALUES (12, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 11:53:37', NULL);
INSERT INTO `yyl_admin_log` VALUES (13, 1, 'admin/AdminUser/userCenter', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"1\";}', 0, '2020-05-10 11:53:58', NULL);
INSERT INTO `yyl_admin_log` VALUES (14, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 11:54:01', NULL);
INSERT INTO `yyl_admin_log` VALUES (15, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:2:\"12\";}', 0, '2020-05-10 11:54:07', NULL);
INSERT INTO `yyl_admin_log` VALUES (16, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:2:\"13\";}', 0, '2020-05-10 11:54:11', NULL);
INSERT INTO `yyl_admin_log` VALUES (17, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:2:\"13\";}', 0, '2020-05-10 11:54:15', NULL);
INSERT INTO `yyl_admin_log` VALUES (18, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:51', NULL);
INSERT INTO `yyl_admin_log` VALUES (19, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:53', NULL);
INSERT INTO `yyl_admin_log` VALUES (20, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:54', NULL);
INSERT INTO `yyl_admin_log` VALUES (21, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:54', NULL);
INSERT INTO `yyl_admin_log` VALUES (22, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:55', NULL);
INSERT INTO `yyl_admin_log` VALUES (23, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:55', NULL);
INSERT INTO `yyl_admin_log` VALUES (24, 1, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-10 11:54:56', NULL);
INSERT INTO `yyl_admin_log` VALUES (25, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 11:54:58', NULL);
INSERT INTO `yyl_admin_log` VALUES (26, 1, 'admin/AdminLog/logInfo', 'GET', '127.0.0.1', 'a:1:{s:12:\"admin_log_id\";s:2:\"24\";}', 0, '2020-05-10 11:55:01', NULL);
INSERT INTO `yyl_admin_log` VALUES (27, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 11:55:47', NULL);
INSERT INTO `yyl_admin_log` VALUES (28, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 11:56:09', NULL);
INSERT INTO `yyl_admin_log` VALUES (29, 1, 'admin/AdminUser/userList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 11:56:18', NULL);
INSERT INTO `yyl_admin_log` VALUES (30, 1, 'admin/AdminRule/ruleList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 11:56:48', NULL);
INSERT INTO `yyl_admin_log` VALUES (31, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 12:02:38', NULL);
INSERT INTO `yyl_admin_log` VALUES (32, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 12:46:27', NULL);
INSERT INTO `yyl_admin_log` VALUES (33, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 12:46:29', NULL);
INSERT INTO `yyl_admin_log` VALUES (34, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-10 12:46:30', NULL);
INSERT INTO `yyl_admin_log` VALUES (35, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 12:46:34', NULL);
INSERT INTO `yyl_admin_log` VALUES (36, 1, 'admin/AdminUser/userInfo', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"1\";}', 0, '2020-05-10 13:42:56', NULL);
INSERT INTO `yyl_admin_log` VALUES (37, 1, 'admin/AdminMenu/menuList', 'GET', '127.0.0.1', 'a:0:{}', 0, '2020-05-10 13:42:57', NULL);
INSERT INTO `yyl_admin_log` VALUES (38, 0, 'admin/AdminLogin/login', 'POST', '127.0.0.1', 'a:2:{s:8:\"username\";s:9:\"skyselang\";s:8:\"password\";s:6:\"123456\";}', 0, '2020-05-12 20:30:33', NULL);
INSERT INTO `yyl_admin_log` VALUES (39, 2, 'admin/AdminUser/userInfo', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"2\";}', 0, '2020-05-12 20:30:33', NULL);
INSERT INTO `yyl_admin_log` VALUES (40, 2, 'admin/AdminUser/userCenter', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"2\";}', 0, '2020-05-12 20:30:45', NULL);
INSERT INTO `yyl_admin_log` VALUES (41, 2, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-12 20:40:27', NULL);
INSERT INTO `yyl_admin_log` VALUES (42, 2, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-12 20:40:29', NULL);
INSERT INTO `yyl_admin_log` VALUES (43, 2, 'admin/AdminTool/randomStr', 'POST', '127.0.0.1', 'a:2:{s:10:\"random_ids\";a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}s:10:\"random_len\";s:2:\"12\";}', 0, '2020-05-12 20:40:31', NULL);
INSERT INTO `yyl_admin_log` VALUES (44, 2, 'admin/AdminTool/timestamp', 'POST', '127.0.0.1', 'a:4:{s:13:\"from_datetime\";s:0:\"\";s:12:\"to_timestamp\";s:0:\"\";s:14:\"from_timestamp\";s:0:\"\";s:11:\"to_datetime\";s:0:\"\";}', 0, '2020-05-12 20:41:32', NULL);
INSERT INTO `yyl_admin_log` VALUES (45, 2, 'admin/AdminTool/timestamp', 'POST', '127.0.0.1', 'a:4:{s:13:\"from_datetime\";s:19:\"2020-05-12 20:41:31\";s:12:\"to_timestamp\";s:10:\"1589287291\";s:14:\"from_timestamp\";s:0:\"\";s:11:\"to_datetime\";s:0:\"\";}', 0, '2020-05-12 20:41:34', NULL);
INSERT INTO `yyl_admin_log` VALUES (46, 2, 'admin/AdminTool/md5Enc', 'POST', '127.0.0.1', 'a:1:{s:3:\"str\";s:6:\"123456\";}', 0, '2020-05-12 20:41:48', NULL);
INSERT INTO `yyl_admin_log` VALUES (47, 2, 'admin/AdminUser/userCenter', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"2\";}', 0, '2020-05-12 20:42:13', NULL);
INSERT INTO `yyl_admin_log` VALUES (48, 2, 'admin/AdminLogin/logout', 'POST', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"2\";}', 0, '2020-05-12 20:42:17', NULL);
INSERT INTO `yyl_admin_log` VALUES (49, 0, 'admin/AdminLogin/login', 'POST', '127.0.0.1', 'a:2:{s:8:\"username\";s:8:\"yyladmin\";s:8:\"password\";s:6:\"123456\";}', 0, '2020-05-12 20:43:14', NULL);
INSERT INTO `yyl_admin_log` VALUES (50, 1, 'admin/AdminUser/userInfo', 'GET', '127.0.0.1', 'a:1:{s:13:\"admin_user_id\";s:1:\"1\";}', 0, '2020-05-12 20:43:14', NULL);
INSERT INTO `yyl_admin_log` VALUES (51, 1, 'admin/AdminLog/logList', 'GET', '127.0.0.1', 'a:2:{s:4:\"page\";s:1:\"1\";s:5:\"limit\";s:2:\"10\";}', 0, '2020-05-12 20:43:51', NULL);

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
) ENGINE = MyISAM AUTO_INCREMENT = 45 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_menu
-- ----------------------------
INSERT INTO `yyl_admin_menu` VALUES (1, 0, '首页', 'admin/AdminIndex/index', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (2, 0, '系统设置', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (3, 2, '菜单管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (4, 2, '用户管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (5, 2, '权限管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (12, 2, '个人中心', 'admin/AdminUser/userCenter', 199, '0', '1', 0, NULL, NULL, NULL);
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
INSERT INTO `yyl_admin_menu` VALUES (41, 2, '日志管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (42, 41, '日志列表', 'admin/AdminLog/logList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (43, 41, '日志信息', 'admin/AdminLog/logInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (44, 41, '日志删除', 'admin/AdminLog/logDele', 200, '0', '0', 0, NULL, NULL, NULL);

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
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账号',
  `nickname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
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
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of yyl_admin_user
-- ----------------------------
INSERT INTO `yyl_admin_user` VALUES (1, '', 'yyladmin', 'yyladmin', 'e10adc3949ba59abbe56e057f20f883e', 'static/img/favicon.ico', '', 200, '0', '0', 0, 2, '127.0.0.1', '2020-05-12 20:43:14', NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (2, '', 'skyselang', 'skyselang', 'e10adc3949ba59abbe56e057f20f883e', 'static/img/favicon.ico', '', 200, '0', '0', 0, 1, '127.0.0.1', '2020-05-12 20:30:33', '2020-05-12 20:42:17', NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
