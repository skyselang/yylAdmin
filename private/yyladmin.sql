/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50529
 Source Host           : localhost:3306
 Source Schema         : yyladmin

 Target Server Type    : MySQL
 Target Server Version : 50529
 File Encoding         : 65001

 Date: 20/01/2022 15:16:25
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
  `menu_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `menu_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单链接',
  `menu_sort` int(10) NULL DEFAULT 250 COMMENT '菜单排序',
  `is_disable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_unauth` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否无需权限1是0否',
  `is_unlogin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否无需登录1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_menu_id`) USING BTREE,
  INDEX `admin_menu_id`(`admin_menu_id`) USING BTREE,
  INDEX `menu_pid`(`menu_pid`, `menu_name`) USING BTREE,
  INDEX `menu_url`(`menu_url`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 495 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_menu
-- ----------------------------
INSERT INTO `yyl_admin_menu` VALUES (1, 0, '控制台', '', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (3, 88, '菜单管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (4, 88, '用户管理', '', 180, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (5, 88, '角色管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (12, 88, '个人中心', '', 130, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (13, 3, '菜单列表', 'admin/admin.Menu/list', 260, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (14, 3, '菜单添加', 'admin/admin.Menu/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (15, 3, '菜单修改', 'admin/admin.Menu/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (16, 3, '菜单删除', 'admin/admin.Menu/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (17, 4, '用户列表', 'admin/admin.User/list', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (18, 4, '用户添加', 'admin/admin.User/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (19, 4, '用户修改', 'admin/admin.User/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (20, 4, '用户删除', 'admin/admin.User/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (22, 5, '角色列表', 'admin/admin.Role/list', 260, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (23, 5, '角色添加', 'admin/admin.Role/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (24, 5, '角色修改', 'admin/admin.Role/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (25, 5, '角色删除', 'admin/admin.Role/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (27, 3, '菜单是否禁用', 'admin/admin.Menu/disable', 160, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (28, 3, '菜单是否无需权限', 'admin/admin.Menu/unauth', 170, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (29, 4, '用户信息', 'admin/admin.User/info', 280, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (30, 4, '用户是否禁用', 'admin/admin.User/disable', 110, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (31, 4, '用户权限分配', 'admin/admin.User/rule', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (32, 4, '用户密码重置', 'admin/admin.User/pwd', 140, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (33, 5, '角色禁用', 'admin/admin.Role/disable', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (35, 4, '用户是否超管', 'admin/admin.User/super', 125, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (37, 58, '随机字符串', 'admin/admin.Utils/strrand', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (38, 58, '时间戳转换', 'admin/admin.Utils/timestamp', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (40, 58, '生成二维码', 'admin/admin.Utils/qrcode', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (41, 88, '用户日志', '', 140, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (42, 41, '用户日志列表', 'admin/admin.UserLog/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (43, 41, '用户日志信息', 'admin/admin.UserLog/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (44, 41, '用户日志删除', 'admin/admin.UserLog/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (45, 12, '我的信息', 'admin/admin.UserCenter/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (46, 12, '修改信息', 'admin/admin.UserCenter/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (47, 12, '修改密码', 'admin/admin.UserCenter/pwd', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (49, 1, '首页', 'admin/Index/index', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (51, 111, '登录', 'admin/admin.Login/login', 160, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (52, 111, '退出', 'admin/admin.Login/logout', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (53, 0, '系统管理', '', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (54, 12, '我的日志', 'admin/admin.UserCenter/log', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (58, 53, '实用工具', 'admin/admin.Utils/utils', 160, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (63, 58, '字符串转换', 'admin/admin.Utils/strtran', 290, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (71, 188, '缓存设置', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (73, 188, '验证码设置', '', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (75, 111, '验证码', 'admin/admin.Login/captcha', 170, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (85, 188, 'Token设置', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (86, 58, '字节转换', 'admin/admin.Utils/bytetran', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (87, 58, 'IP信息', 'admin/admin.Utils/ipinfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (88, 0, '权限管理', '', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (111, 0, '登录退出', '', 100, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (113, 3, '菜单角色', 'admin/admin.Menu/role', 140, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (114, 3, '菜单用户', 'admin/admin.Menu/user', 130, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (115, 5, '角色用户', 'admin/admin.Role/user', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (116, 41, '用户日志统计', 'admin/admin.UserLog/stat', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (117, 12, '我的设置', 'admin/admin.UserCenter/setting', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (118, 3, '菜单角色解除', 'admin/admin.Menu/roleRemove', 135, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (119, 3, '菜单用户解除', 'admin/admin.Menu/userRemove', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (120, 5, '角色用户解除', 'admin/admin.Role/userRemove', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (122, 58, '服务器信息', 'admin/admin.Utils/server', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (123, 156, '会员管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (124, 123, '会员列表', 'admin/Member/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (125, 123, '会员信息', 'admin/Member/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (126, 123, '会员添加', 'admin/Member/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (127, 123, '会员修改', 'admin/Member/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (128, 123, '会员删除', 'admin/Member/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (129, 123, '会员重置密码', 'admin/Member/repwd', 200, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (130, 123, '会员是否禁用', 'admin/Member/disable', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (132, 186, '接口管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (133, 132, '接口列表', 'admin/Api/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (134, 132, '接口信息', 'admin/Api/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (135, 132, '接口添加', 'admin/Api/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (136, 132, '接口修改', 'admin/Api/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (137, 132, '接口删除', 'admin/Api/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (138, 132, '接口是否禁用', 'admin/Api/disable', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (139, 132, '接口是否无需登录', 'admin/Api/unlogin', 125, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (140, 156, '会员日志', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (141, 140, '会员日志列表', 'admin/MemberLog/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (142, 140, '会员日志信息', 'admin/MemberLog/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (143, 140, '会员日志删除', 'admin/MemberLog/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (144, 140, '会员日志统计', 'admin/MemberLog/stat', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (150, 186, '地区管理', '', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (151, 150, '地区列表', 'admin/Region/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (152, 150, '地区信息', 'admin/Region/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (153, 150, '地区添加', 'admin/Region/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (154, 150, '地区修改', 'admin/Region/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (155, 150, '地区删除', 'admin/Region/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (156, 0, '会员管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (157, 186, '设置管理', '', 100, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (158, 3, '菜单信息', 'admin/admin.Menu/info', 255, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (170, 157, '验证码设置', '', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (171, 157, 'Token设置', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (172, 1, '会员统计', 'admin/Index/member', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (173, 53, '接口文档', 'admin/admin.Apidoc/apidoc', 180, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (186, 0, '设置管理', '', 155, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (187, 5, '角色信息', 'admin/admin.Role/info', 255, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (188, 53, '系统管理', '', 150, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (189, 41, '用户日志清除', 'admin/admin.UserLog/clear', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (190, 186, '微信设置', '', 130, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (191, 190, '公众号设置信息', 'admin/SettingWechat/offiInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (192, 190, '公众号设置修改', 'admin/SettingWechat/offiEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (193, 190, '小程序设置信息', 'admin/SettingWechat/miniInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (194, 190, '小程序设置修改', 'admin/SettingWechat/miniEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (196, 73, '验证码设置信息', 'admin/admin.Setting/captchaInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (197, 73, '验证码设置修改', 'admin/admin.Setting/captchaEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (198, 71, '缓存设置信息', 'admin/admin.Setting/cacheInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (199, 71, '缓存设置清除', 'admin/admin.Setting/cacheClear', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (200, 85, 'Token设置信息', 'admin/admin.Setting/tokenInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (201, 85, 'Token设置修改', 'admin/admin.Setting/tokenEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (202, 171, 'Token设置信息', 'admin/Setting/tokenInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (203, 171, 'Token设置修改', 'admin/Setting/tokenEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (204, 170, '验证码设置信息', 'admin/Setting/captchaInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (205, 170, '验证码设置修改', 'admin/Setting/captchaEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (215, 3, '菜单是否无需登录', 'admin/admin.Menu/unlogin', 210, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (217, 188, '日志设置', '', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (218, 217, '日志设置信息', 'admin/admin.Setting/logInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (219, 217, '日志设置修改', 'admin/admin.Setting/logEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (220, 157, '日志设置', '', 120, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (221, 220, '日志设置信息', 'admin/Setting/logInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (222, 220, '日志设置修改', 'admin/Setting/logEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (223, 157, '接口设置', '', 100, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (224, 223, '接口设置信息', 'admin/Setting/apiInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (225, 223, '接口设置修改', 'admin/Setting/apiEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (226, 188, '接口设置', '', 110, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (227, 226, '接口设置信息', 'admin/admin.Setting/apiInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (228, 226, '接口设置修改', 'admin/admin.Setting/apiEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (229, 140, '会员日志清除', 'admin/MemberLog/clear', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (230, 0, '内容管理', '', 160, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (283, 230, '内容管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (284, 283, '内容列表', 'admin/cms.Content/list', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (285, 283, '内容信息', 'admin/cms.Content/info', 290, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (286, 283, '内容添加', 'admin/cms.Content/add', 270, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (287, 283, '内容修改', 'admin/cms.Content/edit', 260, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (288, 283, '内容删除', 'admin/cms.Content/dele', 256, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (289, 283, '分类列表', 'admin/cms.Content/category', 310, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (291, 283, '内容是否置顶', 'admin/cms.Content/istop', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (292, 283, '内容是否热门', 'admin/cms.Content/ishot', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (293, 283, '内容是否推荐', 'admin/cms.Content/isrec', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (294, 283, '内容是否隐藏', 'admin/cms.Content/ishide', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (295, 283, '内容回收站', 'admin/cms.Content/recover', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (296, 283, '内容回收站恢复', 'admin/cms.Content/recoverReco', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (297, 283, '内容回收站删除', 'admin/cms.Content/recoverDele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (298, 230, '内容分类', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (299, 298, '内容分类列表', 'admin/cms.Category/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (300, 298, '内容分类信息', 'admin/cms.Category/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (301, 298, '内容分类添加', 'admin/cms.Category/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (302, 298, '内容分类修改', 'admin/cms.Category/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (303, 298, '内容分类删除', 'admin/cms.Category/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (308, 298, '内容分类是否隐藏', 'admin/cms.Category/ishide', 130, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (381, 230, '留言管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (382, 381, '留言列表', 'admin/cms.Comment/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (383, 381, '留言信息', 'admin/cms.Comment/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (384, 381, '留言添加', 'admin/cms.Comment/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (385, 381, '留言修改', 'admin/cms.Comment/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (386, 381, '留言删除', 'admin/cms.Comment/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (387, 381, '留言已读', 'admin/cms.Comment/isread', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (388, 381, '留言回收站', 'admin/cms.Comment/recover', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (389, 381, '留言回收站恢复', 'admin/cms.Comment/recoverReco', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (390, 381, '留言回收站删除', 'admin/cms.Comment/recoverDele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (391, 230, '内容设置', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (392, 391, '内容设置信息', 'admin/cms.Setting/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (393, 391, '内容设置修改', 'admin/cms.Setting/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (396, 1, '内容统计', 'admin/Index/cms', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (397, 0, '文件管理', '', 157, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (398, 397, '文件管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (399, 398, '分组列表', 'admin/file.File/group', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (400, 398, '文件列表', 'admin/file.File/list', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (401, 398, '文件信息', 'admin/file.File/info', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (402, 398, '文件添加', 'admin/file.File/add', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (403, 398, '文件修改', 'admin/file.File/edit', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (404, 398, '文件删除', 'admin/file.File/dele', 350, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (405, 398, '文件是否禁用', 'admin/file.File/disable', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (406, 398, '文件修改分组', 'admin/file.File/editgroup', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (407, 398, '文件修改类型', 'admin/file.File/edittype', 300, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (410, 397, '文件分组', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (411, 410, '文件分组列表', 'admin/file.Group/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (412, 410, '文件分组信息', 'admin/file.Group/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (413, 410, '文件分组添加', 'admin/file.Group/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (414, 410, '文件分组修改', 'admin/file.Group/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (415, 410, '文件分组删除', 'admin/file.Group/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (416, 410, '文件分组是否禁用', 'admin/file.Group/disable', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (417, 398, '文件回收站', 'admin/file.File/recover', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (418, 398, '文件回收站恢复', 'admin/file.File/recoverReco', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (419, 398, '文件回收站删除', 'admin/file.File/recoverDele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (420, 1, '文件统计', 'admin/Index/file', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (421, 397, '文件设置', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (422, 421, '文件设置信息', 'admin/file.Setting/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (423, 421, '文件设置修改', 'admin/file.Setting/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (424, 1, '总数统计', 'admin/Index/count', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (426, 123, '会员统计', 'admin/Member/stat', 100, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (427, 188, '系统设置', '', 100, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (428, 427, '系统设置信息', 'admin/admin.Setting/systemInfo', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (429, 427, '系统设置修改', 'admin/admin.Setting/systemEdit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (430, 111, '设置信息', 'admin/admin.Login/setting', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (432, 298, '内容分类修改上级', 'admin/cms.Category/pid', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (433, 283, '内容修改分类', 'admin/cms.Content/cate', 255, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (434, 123, '会员修改地区', 'admin/Member/region', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (435, 53, '公告管理', '', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (436, 435, '公告列表', 'admin/admin.Notice/list', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (437, 435, '公告信息', 'admin/admin.Notice/info', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (438, 435, '公告添加', 'admin/admin.Notice/add', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (439, 435, '公告修改', 'admin/admin.Notice/edit', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (440, 435, '公告删除', 'admin/admin.Notice/dele', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (441, 435, '公告是否开启', 'admin/admin.Notice/isopen', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (442, 1, '公告', 'admin/Index/notice', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (443, 132, '接口修改上级', 'admin/Api/pid', 130, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (444, 3, '菜单修改上级', 'admin/admin.Menu/pid', 215, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (445, 5, '菜单列表', 'admin/admin.Role/menu', 270, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (492, 435, '公告开启时间', 'admin/admin.Notice/opentime', 250, 0, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (493, 398, '文件修改域名', 'admin/file.File/editdomain', 270, 0, 0, 0, 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_notice
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_notice`;
CREATE TABLE `yyl_admin_notice`  (
  `admin_notice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告id',
  `admin_user_id` int(11) NULL DEFAULT 0 COMMENT '用户id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `color` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '#606266' COMMENT '标题颜色',
  `type` tinyint(1) NULL DEFAULT 1 COMMENT '类型',
  `sort` int(1) NULL DEFAULT 250 COMMENT '排序',
  `intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '简介',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `is_open` tinyint(1) NULL DEFAULT 1 COMMENT '是否开启1是0否',
  `open_time_start` datetime NOT NULL COMMENT '开启开始时间',
  `open_time_end` datetime NOT NULL COMMENT '开启结束时间',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_notice_id`) USING BTREE,
  INDEX `admin_message_id`(`admin_notice_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公告' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_notice
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_role`;
CREATE TABLE `yyl_admin_role`  (
  `admin_role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `admin_menu_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '菜单id',
  `role_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `role_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '角色描述',
  `role_sort` int(10) NULL DEFAULT 250 COMMENT '角色排序',
  `is_disable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_role_id`) USING BTREE,
  INDEX `admin_rule_id`(`admin_role_id`) USING BTREE,
  INDEX `rule_name`(`role_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_role
-- ----------------------------
INSERT INTO `yyl_admin_role` VALUES (1, ',,', '管理员', '', 250, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (2, ',1,13,17,22,29,37,38,40,42,43,45,49,51,52,54,63,75,86,87,111,113,114,115,116,117,122,124,125,126,133,134,141,142,144,151,152,158,172,173,187,191,193,196,198,204,218,221,224,227,284,285,286,289,291,292,293,294,299,300,382,383,384,388,392,396,399,400,401,402,411,412,417,420,422,424,426,428,430,436,437,442,445,', '演示', '', 250, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (3, ',,', '前端', '', 250, 0, 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_setting
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_setting`;
CREATE TABLE `yyl_admin_setting`  (
  `admin_setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设置id',
  `system_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'yylAdmin' COMMENT '系统简称',
  `page_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'yylAdmin极简后台管理系统' COMMENT '页面标题',
  `logo_id` int(11) NULL DEFAULT 0 COMMENT 'logo图片id',
  `favicon_id` int(11) NULL DEFAULT 0 COMMENT 'favicon图标id',
  `login_bg_id` int(11) NULL DEFAULT 0 COMMENT '登录背景图id',
  `token_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'Token名称',
  `token_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'Token密钥',
  `token_exp` int(5) NULL DEFAULT 12 COMMENT 'Token有效时间（小时）',
  `captcha_switch` tinyint(1) NULL DEFAULT 0 COMMENT '验证码开关：1开启0关闭',
  `captcha_type` tinyint(1) NULL DEFAULT 1 COMMENT '验证码类型：1数字，2字母，3数字字母，4算术，5中文',
  `log_switch` tinyint(1) NULL DEFAULT 1 COMMENT '日志记录开关：1开启0关闭',
  `log_save_time` int(11) NULL DEFAULT 0 COMMENT '日志保留时间，0永久保留',
  `api_rate_num` int(5) NULL DEFAULT 3 COMMENT '接口请求速率（次数）',
  `api_rate_time` int(5) NULL DEFAULT 1 COMMENT '接口请求速率（时间）',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`admin_setting_id`) USING BTREE,
  INDEX `admin_setting_id`(`admin_setting_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_setting
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_user`;
CREATE TABLE `yyl_admin_user`  (
  `admin_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `admin_role_ids` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '角色id，逗号,隔开',
  `admin_menu_ids` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单id，逗号,隔开',
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '账号',
  `nickname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `phone` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '手机',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `avatar_id` int(11) NULL DEFAULT 0 COMMENT '头像id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `sort` int(10) NULL DEFAULT 250 COMMENT '排序',
  `is_disable` tinyint(1) NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_super` tinyint(1) NULL DEFAULT 0 COMMENT '是否超管1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `login_num` int(10) NULL DEFAULT 0 COMMENT '登录次数',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录IP',
  `login_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录地区',
  `login_time` datetime NULL DEFAULT NULL COMMENT '登录时间',
  `logout_time` datetime NULL DEFAULT NULL COMMENT '退出时间',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_user_id`) USING BTREE,
  INDEX `username`(`username`, `password`) USING BTREE,
  INDEX `admin_user_id`(`admin_user_id`) USING BTREE,
  INDEX `email`(`email`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_user
-- ----------------------------
INSERT INTO `yyl_admin_user` VALUES (1, ',,', ',,', 'skyselang', 'skyselang', 'e10adc3949ba59abbe56e057f20f883e', '', '', 0, '超管', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (2, ',2,', ',,', 'yyladmin', 'yyladmin', 'e10adc3949ba59abbe56e057f20f883e', NULL, '', 0, '', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (3, ',2,', ',,', 'admin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', NULL, '', 0, '', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (4, ',2,', ',,', 'demo', '演示', 'e10adc3949ba59abbe56e057f20f883e', '', '', 0, '', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (5, ',2,', ',,', 'php', '拍簧片', 'e10adc3949ba59abbe56e057f20f883e', NULL, '', 0, '', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (6, ',2,', ',,', 'test', '测试', 'e10adc3949ba59abbe56e057f20f883e', '', '', 0, '', 200, 0, 0, 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_user_log
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_user_log`;
CREATE TABLE `yyl_admin_user_log`  (
  `admin_user_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户日志id',
  `admin_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `admin_menu_id` int(11) NULL DEFAULT 0 COMMENT '菜单id',
  `log_type` tinyint(1) NULL DEFAULT 2 COMMENT '1登录2操作3退出',
  `request_method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求方式',
  `request_ip` varchar(130) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求ip',
  `request_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求国家',
  `request_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求省份',
  `request_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求城市',
  `request_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求区县',
  `request_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求地区',
  `request_isp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求ISP',
  `request_param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
  `response_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '返回码',
  `response_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '返回描述',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  INDEX `admin_user_log_id`(`admin_user_log_id`) USING BTREE,
  INDEX `request_isp`(`request_isp`) USING BTREE,
  INDEX `admin_menu_id`(`admin_menu_id`) USING BTREE,
  INDEX `admin_user_id`(`admin_user_id`) USING BTREE,
  INDEX `request_city`(`request_city`(191)) USING BTREE,
  INDEX `request_province`(`request_province`(191)) USING BTREE,
  INDEX `request_country`(`request_country`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '日志' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_admin_user_log
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_api
-- ----------------------------
DROP TABLE IF EXISTS `yyl_api`;
CREATE TABLE `yyl_api`  (
  `api_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '接口id',
  `api_pid` int(11) NOT NULL DEFAULT 0 COMMENT '接口pid',
  `api_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '接口名称',
  `api_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '接口链接',
  `api_sort` int(10) NULL DEFAULT 250 COMMENT '接口排序',
  `is_unlogin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否无需登录1是0否',
  `is_disable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`api_id`) USING BTREE,
  INDEX `api_id`(`api_id`) USING BTREE,
  INDEX `api_pid`(`api_pid`, `api_name`(191)) USING BTREE,
  INDEX `api_url`(`api_url`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 62 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '接口' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_api
-- ----------------------------
INSERT INTO `yyl_api` VALUES (1, 0, '登录退出', '', 250, 0, 1, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (2, 1, '验证码', 'index/Login/captcha', 300, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (3, 1, '登录（账号）', 'index/Login/login', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (4, 1, '退出', 'index/Login/logout', 120, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (5, 0, '会员中心', '', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (6, 5, '我的信息', 'index/Member/info', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (7, 5, '修改信息', 'index/Member/edit', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (8, 5, '上传头像', 'index/Member/avatar', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (9, 5, '修改密码', 'index/Member/pwd', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (10, 5, '日志记录', 'index/Member/log', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (11, 0, '注册', '', 260, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (12, 0, '地区', '', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (13, 12, '地区列表', 'index/Region/list', 300, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (14, 12, '地区信息', 'index/Region/info', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (15, 12, '地区树形', 'index/Region/tree', 280, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (19, 1, '登录（公众号）', 'index/Login/offi', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (20, 11, '验证码', 'index/Register/captcha', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (21, 11, '注册', 'index/Register/register', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (22, 1, '登录（小程序）', 'index/Login/mini', 130, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (23, 16, '新闻分类', 'index/cms.News/category', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (24, 0, '首页', '', 270, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (25, 24, '首页', 'index/Index/index', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (26, 1, '登录（公众号）回调', 'index/Login/officallback', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (27, 24, 'index', 'index/', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (28, 0, '微信', '', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (29, 28, '微信公众号接入', 'index/Wechat/access', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (30, 0, '内容', '', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (35, 30, '内容', '', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (41, 35, '分类列表', 'index/cms.Content/category', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (42, 35, '内容列表', 'index/cms.Content/list', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (43, 35, '内容信息', 'index/cms.Content/info', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (57, 30, '留言', '', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (58, 57, '留言提交', 'index/cms.Comment/add', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (59, 30, '设置', '', 250, 0, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (60, 59, '设置信息', 'index/cms.Setting/info', 250, 1, 0, 0, NULL, NULL, NULL);
INSERT INTO `yyl_api` VALUES (61, 5, '绑定手机（小程序）', 'index/Member/bindPhoneMini', 250, 0, 0, 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_cms_category
-- ----------------------------
DROP TABLE IF EXISTS `yyl_cms_category`;
CREATE TABLE `yyl_cms_category`  (
  `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category_pid` int(11) NULL DEFAULT 0 COMMENT '分类父级id',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '标题',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '描述',
  `img_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片id，逗号,隔开',
  `imgs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片',
  `sort` int(11) NULL DEFAULT 250 COMMENT '排序',
  `is_hide` tinyint(1) NULL DEFAULT 0 COMMENT '是否隐藏1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`category_id`) USING BTREE,
  INDEX `cms_category_id`(`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '内容分类' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_cms_category
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_cms_comment
-- ----------------------------
DROP TABLE IF EXISTS `yyl_cms_comment`;
CREATE TABLE `yyl_cms_comment`  (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '留言id',
  `call` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '称呼',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机',
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '电话',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `qq` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'QQ',
  `wechat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '微信',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `is_unread` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否未读1是0否',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `read_time` datetime NULL DEFAULT NULL COMMENT '已读时间',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`comment_id`) USING BTREE,
  INDEX `comment_id`(`comment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '留言' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_cms_comment
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_cms_content
-- ----------------------------
DROP TABLE IF EXISTS `yyl_cms_content`;
CREATE TABLE `yyl_cms_content`  (
  `content_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '内容id',
  `category_id` int(11) NULL DEFAULT 0 COMMENT '内容分类id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '标题',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '描述',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `img_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片id，逗号,隔开',
  `imgs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片',
  `file_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '附件id，逗号,隔开',
  `files` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '附件',
  `videos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '视频',
  `video_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '视频id，逗号,隔开',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '链接',
  `sort` int(11) NULL DEFAULT 250 COMMENT '排序',
  `hits` int(11) NULL DEFAULT 0 COMMENT '点击量',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶1是0否',
  `is_hot` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否热门1是0否',
  `is_rec` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐1是0否',
  `is_hide` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否隐藏1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`content_id`) USING BTREE,
  INDEX `content_id`(`content_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '内容' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_cms_content
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_cms_setting
-- ----------------------------
DROP TABLE IF EXISTS `yyl_cms_setting`;
CREATE TABLE `yyl_cms_setting`  (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '内容设置id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '标题',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '描述',
  `logo_id` int(11) NULL DEFAULT 0 COMMENT 'logo id',
  `icp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备案号',
  `copyright` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '版权',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '地址',
  `tel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '手机',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `qq` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'QQ',
  `wechat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '微信',
  `off_acc_id` int(11) NULL DEFAULT 0 COMMENT '公众号id',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`setting_id`) USING BTREE,
  INDEX `setting_cms_id`(`setting_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '内容设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_cms_setting
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_file
-- ----------------------------
DROP TABLE IF EXISTS `yyl_file`;
CREATE TABLE `yyl_file`  (
  `file_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `group_id` int(11) NULL DEFAULT 0 COMMENT '分组id',
  `storage` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'local' COMMENT '存储方式：local本地(服务器)，qiniu七牛云Kodo，aliyun阿里云OSS，tencent腾讯云COS',
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '访问域名',
  `file_md5` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '文件MD5',
  `file_hash` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '文件散列（sha1）',
  `file_type` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'image' COMMENT '文件类型：image图片，video视频，audio音频，word文档，other其它',
  `file_name` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '文件名称',
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '文件路径',
  `file_size` int(11) NULL DEFAULT 0 COMMENT '文件大小，单位字节(b)',
  `file_ext` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '文件后缀',
  `sort` int(11) NULL DEFAULT 250 COMMENT '排序',
  `is_front` tinyint(1) NULL DEFAULT 0 COMMENT '是否前台上传',
  `is_disable` tinyint(1) NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`file_id`) USING BTREE,
  INDEX `file_id`(`file_id`) USING BTREE,
  INDEX `file_hash`(`file_hash`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_file
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_file_group
-- ----------------------------
DROP TABLE IF EXISTS `yyl_file_group`;
CREATE TABLE `yyl_file_group`  (
  `group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文件分组id',
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名称',
  `group_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '分组描述',
  `group_sort` int(11) NULL DEFAULT 250 COMMENT '分组排序',
  `is_disable` tinyint(1) NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`group_id`) USING BTREE,
  INDEX `file_group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件分组' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_file_group
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_file_setting
-- ----------------------------
DROP TABLE IF EXISTS `yyl_file_setting`;
CREATE TABLE `yyl_file_setting`  (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `storage` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'local' COMMENT '存储方式：local本地(服务器)，qiniu七牛云Kodo，aliyun阿里云OSS，tencent腾讯云COS',
  `qiniu_access_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '七牛云Kodo，AccessKey',
  `qiniu_secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '七牛云Kodo，SecretKey',
  `qiniu_bucket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '七牛云Kodo，空间名称',
  `qiniu_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '七牛云Kodo，访问域名',
  `aliyun_access_key_id` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '阿里云OSS，AccessKey ID',
  `aliyun_access_key_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '阿里云OSS，AccessKey Secret',
  `aliyun_endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '阿里云OSS，地域节点',
  `aliyun_bucket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '阿里云OSS，Bucket 名称',
  `aliyun_bucket_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '阿里云OSS，Bucket 域名',
  `tencent_secret_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '腾讯云COS，SecretId',
  `tencent_secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '腾讯云COS，SecretKey',
  `tencent_bucket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '腾讯云COS，存储桶名称',
  `tencent_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '腾讯云COS，所属地域',
  `tencent_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '腾讯云COS，访问域名',
  `baidu_access_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '百度云BOS，AccessKey',
  `baidu_secret_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '百度云BOS，SecretKey',
  `baidu_bucket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '百度云BOS，Bucket 名称',
  `baidu_endpoint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '百度云BOS，所属地域',
  `baidu_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '百度云BOS，官方域名',
  `image_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'jpg,png,jpeg,ico' COMMENT '允许上传的图片后缀，多个逗号\",\"隔开',
  `image_size` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '1' COMMENT '允许上传的图片大小，单位MB',
  `video_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'mp4,avi,mkv,flv' COMMENT '允许上传的视频后缀，多个逗号\",\"隔开',
  `video_size` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '50' COMMENT '允许上传的视频大小，单位MB',
  `audio_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'mp3,aac,wav' COMMENT '允许上传的音频后缀，多个逗号\",\"隔开',
  `audio_size` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '允许上传的音频大小，单位MB',
  `word_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'doc,docx,xls,xlsx,ppt,pptx' COMMENT '允许上传的文档后缀，多个逗号\",\"隔开',
  `word_size` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '5' COMMENT '允许上传的文档大小，单位MB',
  `other_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'zip,rar' COMMENT '允许上传的其它文件后缀，多个逗号\",\"隔开',
  `other_size` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '30' COMMENT '允许上传的其它文件大小，单位MB',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`setting_id`) USING BTREE,
  INDEX `setting_cms_id`(`setting_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_file_setting
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_member
-- ----------------------------
DROP TABLE IF EXISTS `yyl_member`;
CREATE TABLE `yyl_member`  (
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员id',
  `member_wechat_id` int(11) NULL DEFAULT 0 COMMENT '会员微信id',
  `avatar_id` int(11) NULL DEFAULT 0 COMMENT '头像id',
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `nickname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `phone` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '手机',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮箱',
  `region_id` int(10) NULL DEFAULT 0 COMMENT '地区id',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `sort` int(10) NULL DEFAULT 250 COMMENT '排序',
  `reg_channel` tinyint(1) NULL DEFAULT 1 COMMENT '注册渠道1Web2公众号3小程序4安卓5苹果',
  `is_disable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用1是0否',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `login_num` int(10) NULL DEFAULT 0 COMMENT '登录次数',
  `login_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录IP',
  `login_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '登录地区',
  `login_time` datetime NULL DEFAULT NULL COMMENT '登录时间',
  `logout_time` datetime NULL DEFAULT NULL COMMENT '退出时间',
  `create_time` datetime NULL DEFAULT NULL COMMENT '注册时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`member_id`) USING BTREE,
  INDEX `username`(`username`, `password`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `email`(`email`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_member
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_member_log
-- ----------------------------
DROP TABLE IF EXISTS `yyl_member_log`;
CREATE TABLE `yyl_member_log`  (
  `member_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员日志id',
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '会员id',
  `log_type` tinyint(1) NULL DEFAULT 3 COMMENT '日志类型：1注册2登录3操作4退出',
  `api_id` int(11) NULL DEFAULT 0 COMMENT '接口id',
  `request_method` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求方式',
  `request_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求IP',
  `request_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求国家',
  `request_province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求省份',
  `request_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求城市',
  `request_area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求区县',
  `request_region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求地区',
  `request_isp` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '请求ISP',
  `request_param` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求参数',
  `response_code` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '返回码',
  `response_msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '返回描述',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`member_log_id`) USING BTREE,
  INDEX `request_isp`(`request_isp`) USING BTREE,
  INDEX `member_log_id`(`member_log_id`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `request_city`(`request_city`(191)) USING BTREE,
  INDEX `request_province`(`request_province`(191)) USING BTREE,
  INDEX `request_country`(`request_country`(191)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员日志' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_member_log
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_member_wechat
-- ----------------------------
DROP TABLE IF EXISTS `yyl_member_wechat`;
CREATE TABLE `yyl_member_wechat`  (
  `member_wechat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员微信id',
  `member_id` int(11) NULL DEFAULT 0 COMMENT '会员id',
  `headimgurl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '头像',
  `openid` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'openid',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(1) NULL DEFAULT 0 COMMENT '性别：0未知1男2女',
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '国家',
  `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '省份',
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '城市',
  `language` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '语言',
  `privilege` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '特权信息',
  `unionid` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'unionid',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`member_wechat_id`) USING BTREE,
  INDEX `member_id`(`member_id`) USING BTREE,
  INDEX `openid`(`openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员微信' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_member_wechat
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_region
-- ----------------------------
DROP TABLE IF EXISTS `yyl_region`;
CREATE TABLE `yyl_region`  (
  `region_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '地区id',
  `region_pid` int(11) NULL DEFAULT 0 COMMENT 'pid',
  `region_path` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '路径',
  `region_level` tinyint(1) NULL DEFAULT 1 COMMENT '级别',
  `region_name` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `region_pinyin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '拼音',
  `region_jianpin` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '简拼',
  `region_initials` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '首字母',
  `region_citycode` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '区号',
  `region_zipcode` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '邮编',
  `region_longitude` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '经度',
  `region_latitude` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '纬度',
  `region_sort` int(10) NULL DEFAULT 2250 COMMENT '排序',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '0正常1删除',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`region_id`) USING BTREE,
  INDEX `region_id`(`region_id`) USING BTREE,
  INDEX `region_name`(`region_name`) USING BTREE,
  INDEX `region_pid`(`region_pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 659006102 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '地区' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_region
-- ----------------------------
INSERT INTO `yyl_region` VALUES (11, 0, '11', 1, '北京', 'BeiJing', 'BJ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (12, 0, '12', 1, '天津', 'TianJin', 'TJ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (13, 0, '13', 1, '河北省', 'HeBeiSheng', 'HBS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (14, 0, '14', 1, '山西省', 'ShanXiSheng', 'SXS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (15, 0, '15', 1, '内蒙古自治区', 'NeiMengGuZiZhiQu', 'NMGZZQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (21, 0, '21', 1, '辽宁省', 'LiaoNingSheng', 'LNS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (22, 0, '22', 1, '吉林省', 'JiLinSheng', 'JLS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (23, 0, '23', 1, '黑龙江省', 'HeiLongJiangSheng', 'HLJS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (31, 0, '31', 1, '上海', 'ShangHai', 'SH', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (32, 0, '32', 1, '江苏省', 'JiangSuSheng', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (33, 0, '33', 1, '浙江省', 'ZheJiangSheng', 'ZJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (34, 0, '34', 1, '安徽省', 'AnHuiSheng', 'AHS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (35, 0, '35', 1, '福建省', 'FuJianSheng', 'FJS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (36, 0, '36', 1, '江西省', 'JiangXiSheng', 'JXS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (37, 0, '37', 1, '山东省', 'ShanDongSheng', 'SDS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (41, 0, '41', 1, '河南省', 'HeNanSheng', 'HNS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (42, 0, '42', 1, '湖北省', 'HuBeiSheng', 'HBS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (43, 0, '43', 1, '湖南省', 'HuNanSheng', 'HNS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (44, 0, '44', 1, '广东省', 'GuangDongSheng', 'GDS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (45, 0, '45', 1, '广西壮族自治区', 'GuangXiZhuangZuZiZhiQu', 'GXZZZZQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (46, 0, '46', 1, '海南省', 'HaiNanSheng', 'HNS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (50, 0, '50', 1, '重庆', 'ChongQing', 'CQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (51, 0, '51', 1, '四川省', 'SiChuanSheng', 'SCS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (52, 0, '52', 1, '贵州省', 'GuiZhouSheng', 'GZS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (53, 0, '53', 1, '云南省', 'YunNanSheng', 'YNS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (54, 0, '54', 1, '西藏自治区', 'XiZangZiZhiQu', 'XZZZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (61, 0, '61', 1, '陕西省', 'ShanXiSheng', 'SXS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (62, 0, '62', 1, '甘肃省', 'GanSuSheng', 'GSS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (63, 0, '63', 1, '青海省', 'QingHaiSheng', 'QHS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (64, 0, '64', 1, '宁夏回族自治区', 'NingXiaHuiZuZiZhiQu', 'NXHZZZQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (65, 0, '65', 1, '新疆维吾尔自治区', 'XinJiangWeiWuErZiZhiQu', 'XJWWEZZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (82, 0, '82', 1, '香港特别行政区', 'XiangGangTeBieXingZhengQu', 'XGTBXZQ', 'X', '00852', '999077', '114.182343', '22.295803', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (83, 0, '83', 1, '澳门特别行政区', 'AoMenTeBieXingZhengQu', 'AMTBXZQ', 'A', '00853', '999078', '113.552565', '22.200858', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (88, 0, '88', 1, '台湾省', 'TaiWanSheng', 'TWS', 'T', '00886', '222', '121.566349', '25.047042', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1101, 11, '11,1101', 2, '北京市', 'BeiJingShi', 'BJS', 'S', '010', '100000', '116.402544', '39.915378', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1201, 12, '12,1201', 2, '天津市', 'TianJinShi', 'TJS', 'S', '022', '300000', '117.208214', '39.133908', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1301, 13, '13,1301', 2, '石家庄市', 'ShiJiaZhuangShi', 'SJZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1302, 13, '13,1302', 2, '唐山市', 'TangShanShi', 'TSS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1303, 13, '13,1303', 2, '秦皇岛市', 'QinHuangDaoShi', 'QHDS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1304, 13, '13,1304', 2, '邯郸市', 'HanDanShi', 'HDS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1305, 13, '13,1305', 2, '邢台市', 'XingTaiShi', 'XTS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1306, 13, '13,1306', 2, '保定市', 'BaoDingShi', 'BDS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1307, 13, '13,1307', 2, '张家口市', 'ZhangJiaKouShi', 'ZJKS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1308, 13, '13,1308', 2, '承德市', 'ChengDeShi', 'CDS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1309, 13, '13,1309', 2, '沧州市', 'CangZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1310, 13, '13,1310', 2, '廊坊市', 'LangFangShi', 'LFS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1311, 13, '13,1311', 2, '衡水市', 'HengShuiShi', 'HSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1401, 14, '14,1401', 2, '太原市', 'TaiYuanShi', 'TYS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1402, 14, '14,1402', 2, '大同市', 'DaTongShi', 'DTS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1403, 14, '14,1403', 2, '阳泉市', 'YangQuanShi', 'YQS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1404, 14, '14,1404', 2, '长治市', 'ChangZhiShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1405, 14, '14,1405', 2, '晋城市', 'JinChengShi', 'JCS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1406, 14, '14,1406', 2, '朔州市', 'ShuoZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1407, 14, '14,1407', 2, '晋中市', 'JinZhongShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1408, 14, '14,1408', 2, '运城市', 'YunChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1409, 14, '14,1409', 2, '忻州市', 'XinZhouShi', 'XZS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1410, 14, '14,1410', 2, '临汾市', 'LinFenShi', 'LFS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1411, 14, '14,1411', 2, '吕梁市', 'LyuLiangShi', 'LLS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1501, 15, '15,1501', 2, '呼和浩特市', 'HuHeHaoTeShi', 'HHHTS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1502, 15, '15,1502', 2, '包头市', 'BaoTouShi', 'BTS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1503, 15, '15,1503', 2, '乌海市', 'WuHaiShi', 'WHS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1504, 15, '15,1504', 2, '赤峰市', 'ChiFengShi', 'CFS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1505, 15, '15,1505', 2, '通辽市', 'TongLiaoShi', 'TLS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1506, 15, '15,1506', 2, '鄂尔多斯市', 'EErDuoSiShi', 'EEDSS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1507, 15, '15,1507', 2, '呼伦贝尔市', 'HuLunBeiErShi', 'HLBES', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1508, 15, '15,1508', 2, '巴彦淖尔市', 'BaYanNaoErShi', 'BYNES', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1509, 15, '15,1509', 2, '乌兰察布市', 'WuLanChaBuShi', 'WLCBS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1522, 15, '15,1522', 2, '兴安盟', 'XingAnMeng', 'XAM', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1525, 15, '15,1525', 2, '锡林郭勒盟', 'XiLinGuoLeMeng', 'XLGLM', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (1529, 15, '15,1529', 2, '阿拉善盟', 'ALaShanMeng', 'ALSM', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2101, 21, '21,2101', 2, '沈阳市', 'ShenYangShi', 'SYS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2102, 21, '21,2102', 2, '大连市', 'DaLianShi', 'DLS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2103, 21, '21,2103', 2, '鞍山市', 'AnShanShi', 'ASS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2104, 21, '21,2104', 2, '抚顺市', 'FuShunShi', 'FSS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2105, 21, '21,2105', 2, '本溪市', 'BenXiShi', 'BXS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2106, 21, '21,2106', 2, '丹东市', 'DanDongShi', 'DDS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2107, 21, '21,2107', 2, '锦州市', 'JinZhouShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2108, 21, '21,2108', 2, '营口市', 'YingKouShi', 'YKS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2109, 21, '21,2109', 2, '阜新市', 'FuXinShi', 'FXS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2110, 21, '21,2110', 2, '辽阳市', 'LiaoYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2111, 21, '21,2111', 2, '盘锦市', 'PanJinShi', 'PJS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2112, 21, '21,2112', 2, '铁岭市', 'TieLingShi', 'TLS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2113, 21, '21,2113', 2, '朝阳市', 'ZhaoYangShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2114, 21, '21,2114', 2, '葫芦岛市', 'HuLuDaoShi', 'HLDS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2201, 22, '22,2201', 2, '长春市', 'ChangChunShi', 'CCS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2202, 22, '22,2202', 2, '吉林市', 'JiLinShi', 'JLS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2203, 22, '22,2203', 2, '四平市', 'SiPingShi', 'SPS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2204, 22, '22,2204', 2, '辽源市', 'LiaoYuanShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2205, 22, '22,2205', 2, '通化市', 'TongHuaShi', 'THS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2206, 22, '22,2206', 2, '白山市', 'BaiShanShi', 'BSS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2207, 22, '22,2207', 2, '松原市', 'SongYuanShi', 'SYS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2208, 22, '22,2208', 2, '白城市', 'BaiChengShi', 'BCS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2224, 22, '22,2224', 2, '延边朝鲜族自治州', 'YanBianChaoXianZuZiZhiZhou', 'YBCXZZZZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2301, 23, '23,2301', 2, '哈尔滨市', 'HaErBinShi', 'HEBS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2302, 23, '23,2302', 2, '齐齐哈尔市', 'QiQiHaErShi', 'QQHES', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2303, 23, '23,2303', 2, '鸡西市', 'JiXiShi', 'JXS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2304, 23, '23,2304', 2, '鹤岗市', 'HeGangShi', 'HGS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2305, 23, '23,2305', 2, '双鸭山市', 'ShuangYaShanShi', 'SYSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2306, 23, '23,2306', 2, '大庆市', 'DaQingShi', 'DQS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2307, 23, '23,2307', 2, '伊春市', 'YiChunShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2308, 23, '23,2308', 2, '佳木斯市', 'JiaMuSiShi', 'JMSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2309, 23, '23,2309', 2, '七台河市', 'QiTaiHeShi', 'QTHS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2310, 23, '23,2310', 2, '牡丹江市', 'MuDanJiangShi', 'MDJS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2311, 23, '23,2311', 2, '黑河市', 'HeiHeShi', 'HHS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2312, 23, '23,2312', 2, '绥化市', 'SuiHuaShi', 'SHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (2327, 23, '23,2327', 2, '大兴安岭地区', 'DaXingAnLingDiQu', 'DXALDQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3101, 31, '31,3101', 2, '上海市', 'ShangHaiShi', 'SHS', 'S', '021', '2000000', '121.462415', '31.242974', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3201, 32, '32,3201', 2, '南京市', 'NanJingShi', 'NJS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3202, 32, '32,3202', 2, '无锡市', 'WuXiShi', 'WXS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3203, 32, '32,3203', 2, '徐州市', 'XuZhouShi', 'XZS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3204, 32, '32,3204', 2, '常州市', 'ChangZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3205, 32, '32,3205', 2, '苏州市', 'SuZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3206, 32, '32,3206', 2, '南通市', 'NanTongShi', 'NTS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3207, 32, '32,3207', 2, '连云港市', 'LianYunGangShi', 'LYGS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3208, 32, '32,3208', 2, '淮安市', 'HuaiAnShi', 'HAS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3209, 32, '32,3209', 2, '盐城市', 'YanChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3210, 32, '32,3210', 2, '扬州市', 'YangZhouShi', 'YZS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3211, 32, '32,3211', 2, '镇江市', 'ZhenJiangShi', 'ZJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3212, 32, '32,3212', 2, '泰州市', 'TaiZhouShi', 'TZS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3213, 32, '32,3213', 2, '宿迁市', 'SuQianShi', 'SQS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3301, 33, '33,3301', 2, '杭州市', 'HangZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3302, 33, '33,3302', 2, '宁波市', 'NingBoShi', 'NBS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3303, 33, '33,3303', 2, '温州市', 'WenZhouShi', 'WZS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3304, 33, '33,3304', 2, '嘉兴市', 'JiaXingShi', 'JXS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3305, 33, '33,3305', 2, '湖州市', 'HuZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3306, 33, '33,3306', 2, '绍兴市', 'ShaoXingShi', 'SXS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3307, 33, '33,3307', 2, '金华市', 'JinHuaShi', 'JHS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3308, 33, '33,3308', 2, '衢州市', 'QuZhouShi', 'QZS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3309, 33, '33,3309', 2, '舟山市', 'ZhouShanShi', 'ZSS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3310, 33, '33,3310', 2, '台州市', 'TaiZhouShi', 'TZS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3311, 33, '33,3311', 2, '丽水市', 'LiShuiShi', 'LSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3401, 34, '34,3401', 2, '合肥市', 'HeFeiShi', 'HFS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3402, 34, '34,3402', 2, '芜湖市', 'WuHuShi', 'WHS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3403, 34, '34,3403', 2, '蚌埠市', 'BengBuShi', 'BBS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3404, 34, '34,3404', 2, '淮南市', 'HuaiNanShi', 'HNS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3405, 34, '34,3405', 2, '马鞍山市', 'MaAnShanShi', 'MASS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3406, 34, '34,3406', 2, '淮北市', 'HuaiBeiShi', 'HBS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3407, 34, '34,3407', 2, '铜陵市', 'TongLingShi', 'TLS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3408, 34, '34,3408', 2, '安庆市', 'AnQingShi', 'AQS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3410, 34, '34,3410', 2, '黄山市', 'HuangShanShi', 'HSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3411, 34, '34,3411', 2, '滁州市', 'ChuZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3412, 34, '34,3412', 2, '阜阳市', 'FuYangShi', 'FYS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3413, 34, '34,3413', 2, '宿州市', 'SuZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3415, 34, '34,3415', 2, '六安市', 'LuAnShi', 'LAS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3416, 34, '34,3416', 2, '亳州市', 'BoZhouShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3417, 34, '34,3417', 2, '池州市', 'ChiZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3418, 34, '34,3418', 2, '宣城市', 'XuanChengShi', 'XCS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3501, 35, '35,3501', 2, '福州市', 'FuZhouShi', 'FZS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3502, 35, '35,3502', 2, '厦门市', 'XiaMenShi', 'XMS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3503, 35, '35,3503', 2, '莆田市', 'PuTianShi', 'PTS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3504, 35, '35,3504', 2, '三明市', 'SanMingShi', 'SMS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3505, 35, '35,3505', 2, '泉州市', 'QuanZhouShi', 'QZS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3506, 35, '35,3506', 2, '漳州市', 'ZhangZhouShi', 'ZZS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3507, 35, '35,3507', 2, '南平市', 'NanPingShi', 'NPS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3508, 35, '35,3508', 2, '龙岩市', 'LongYanShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3509, 35, '35,3509', 2, '宁德市', 'NingDeShi', 'NDS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3601, 36, '36,3601', 2, '南昌市', 'NanChangShi', 'NCS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3602, 36, '36,3602', 2, '景德镇市', 'JingDeZhenShi', 'JDZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3603, 36, '36,3603', 2, '萍乡市', 'PingXiangShi', 'PXS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3604, 36, '36,3604', 2, '九江市', 'JiuJiangShi', 'JJS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3605, 36, '36,3605', 2, '新余市', 'XinYuShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3606, 36, '36,3606', 2, '鹰潭市', 'YingTanShi', 'YTS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3607, 36, '36,3607', 2, '赣州市', 'GanZhouShi', 'GZS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3608, 36, '36,3608', 2, '吉安市', 'JiAnShi', 'JAS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3609, 36, '36,3609', 2, '宜春市', 'YiChunShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3610, 36, '36,3610', 2, '抚州市', 'FuZhouShi', 'FZS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3611, 36, '36,3611', 2, '上饶市', 'ShangRaoShi', 'SRS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3701, 37, '37,3701', 2, '济南市', 'JiNanShi', 'JNS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3702, 37, '37,3702', 2, '青岛市', 'QingDaoShi', 'QDS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3703, 37, '37,3703', 2, '淄博市', 'ZiBoShi', 'ZBS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3704, 37, '37,3704', 2, '枣庄市', 'ZaoZhuangShi', 'ZZS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3705, 37, '37,3705', 2, '东营市', 'DongYingShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3706, 37, '37,3706', 2, '烟台市', 'YanTaiShi', 'YTS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3707, 37, '37,3707', 2, '潍坊市', 'WeiFangShi', 'WFS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3708, 37, '37,3708', 2, '济宁市', 'JiNingShi', 'JNS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3709, 37, '37,3709', 2, '泰安市', 'TaiAnShi', 'TAS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3710, 37, '37,3710', 2, '威海市', 'WeiHaiShi', 'WHS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3711, 37, '37,3711', 2, '日照市', 'RiZhaoShi', 'RZS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3713, 37, '37,3713', 2, '临沂市', 'LinYiShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3714, 37, '37,3714', 2, '德州市', 'DeZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3715, 37, '37,3715', 2, '聊城市', 'LiaoChengShi', 'LCS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3716, 37, '37,3716', 2, '滨州市', 'BinZhouShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (3717, 37, '37,3717', 2, '菏泽市', 'HeZeShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4101, 41, '41,4101', 2, '郑州市', 'ZhengZhouShi', 'ZZS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4102, 41, '41,4102', 2, '开封市', 'KaiFengShi', 'KFS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4103, 41, '41,4103', 2, '洛阳市', 'LuoYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4104, 41, '41,4104', 2, '平顶山市', 'PingDingShanShi', 'PDSS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4105, 41, '41,4105', 2, '安阳市', 'AnYangShi', 'AYS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4106, 41, '41,4106', 2, '鹤壁市', 'HeBiShi', 'HBS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4107, 41, '41,4107', 2, '新乡市', 'XinXiangShi', 'XXS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4108, 41, '41,4108', 2, '焦作市', 'JiaoZuoShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4109, 41, '41,4109', 2, '濮阳市', 'PuYangShi', 'PYS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4110, 41, '41,4110', 2, '许昌市', 'XuChangShi', 'XCS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4111, 41, '41,4111', 2, '漯河市', 'TaHeShi', 'THS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4112, 41, '41,4112', 2, '三门峡市', 'SanMenXiaShi', 'SMXS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4113, 41, '41,4113', 2, '南阳市', 'NanYangShi', 'NYS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4114, 41, '41,4114', 2, '商丘市', 'ShangQiuShi', 'SQS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4115, 41, '41,4115', 2, '信阳市', 'XinYangShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4116, 41, '41,4116', 2, '周口市', 'ZhouKouShi', 'ZKS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4117, 41, '41,4117', 2, '驻马店市', 'ZhuMaDianShi', 'ZMDS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4201, 42, '42,4201', 2, '武汉市', 'WuHanShi', 'WHS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4202, 42, '42,4202', 2, '黄石市', 'HuangShiShi', 'HSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4203, 42, '42,4203', 2, '十堰市', 'ShiYanShi', 'SYS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4205, 42, '42,4205', 2, '宜昌市', 'YiChangShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4206, 42, '42,4206', 2, '襄阳市', 'XiangYangShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4207, 42, '42,4207', 2, '鄂州市', 'EZhouShi', 'EZS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4208, 42, '42,4208', 2, '荆门市', 'JingMenShi', 'JMS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4209, 42, '42,4209', 2, '孝感市', 'XiaoGanShi', 'XGS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4210, 42, '42,4210', 2, '荆州市', 'JingZhouShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4211, 42, '42,4211', 2, '黄冈市', 'HuangGangShi', 'HGS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4212, 42, '42,4212', 2, '咸宁市', 'XianNingShi', 'XNS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4213, 42, '42,4213', 2, '随州市', 'SuiZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4228, 42, '42,4228', 2, '恩施土家族苗族自治州', 'EnShiTuJiaZuMiaoZuZiZhiZhou', 'ESTJZMZZZZ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4301, 43, '43,4301', 2, '长沙市', 'ChangShaShi', 'CSS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4302, 43, '43,4302', 2, '株洲市', 'ZhuZhouShi', 'ZZS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4303, 43, '43,4303', 2, '湘潭市', 'XiangTanShi', 'XTS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4304, 43, '43,4304', 2, '衡阳市', 'HengYangShi', 'HYS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4305, 43, '43,4305', 2, '邵阳市', 'ShaoYangShi', 'SYS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4306, 43, '43,4306', 2, '岳阳市', 'YueYangShi', 'YYS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4307, 43, '43,4307', 2, '常德市', 'ChangDeShi', 'CDS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4308, 43, '43,4308', 2, '张家界市', 'ZhangJiaJieShi', 'ZJJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4309, 43, '43,4309', 2, '益阳市', 'YiYangShi', 'YYS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4310, 43, '43,4310', 2, '郴州市', 'ChenZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4311, 43, '43,4311', 2, '永州市', 'YongZhouShi', 'YZS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4312, 43, '43,4312', 2, '怀化市', 'HuaiHuaShi', 'HHS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4313, 43, '43,4313', 2, '娄底市', 'LouDiShi', 'LDS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4331, 43, '43,4331', 2, '湘西土家族苗族自治州', 'XiangXiTuJiaZuMiaoZuZiZhiZhou', 'XXTJZMZZZZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4401, 44, '44,4401', 2, '广州市', 'GuangZhouShi', 'GZS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4402, 44, '44,4402', 2, '韶关市', 'ShaoGuanShi', 'SGS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4403, 44, '44,4403', 2, '深圳市', 'ShenZhenShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4404, 44, '44,4404', 2, '珠海市', 'ZhuHaiShi', 'ZHS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4405, 44, '44,4405', 2, '汕头市', 'ShanTouShi', 'STS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4406, 44, '44,4406', 2, '佛山市', 'FoShanShi', 'FSS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4407, 44, '44,4407', 2, '江门市', 'JiangMenShi', 'JMS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4408, 44, '44,4408', 2, '湛江市', 'ZhanJiangShi', 'ZJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4409, 44, '44,4409', 2, '茂名市', 'MaoMingShi', 'MMS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4412, 44, '44,4412', 2, '肇庆市', 'ZhaoQingShi', 'ZQS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4413, 44, '44,4413', 2, '惠州市', 'HuiZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4414, 44, '44,4414', 2, '梅州市', 'MeiZhouShi', 'MZS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4415, 44, '44,4415', 2, '汕尾市', 'ShanWeiShi', 'SWS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4416, 44, '44,4416', 2, '河源市', 'HeYuanShi', 'HYS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4417, 44, '44,4417', 2, '阳江市', 'YangJiangShi', 'YJS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4418, 44, '44,4418', 2, '清远市', 'QingYuanShi', 'QYS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4419, 44, '44,4419', 2, '东莞市', 'DongGuanShi', 'DGS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4420, 44, '44,4420', 2, '中山市', 'ZhongShanShi', 'ZSS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4451, 44, '44,4451', 2, '潮州市', 'ChaoZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4452, 44, '44,4452', 2, '揭阳市', 'JieYangShi', 'JYS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4453, 44, '44,4453', 2, '云浮市', 'YunFuShi', 'YFS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4501, 45, '45,4501', 2, '南宁市', 'NanNingShi', 'NNS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4502, 45, '45,4502', 2, '柳州市', 'LiuZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4503, 45, '45,4503', 2, '桂林市', 'GuiLinShi', 'GLS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4504, 45, '45,4504', 2, '梧州市', 'WuZhouShi', 'WZS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4505, 45, '45,4505', 2, '北海市', 'BeiHaiShi', 'BHS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4506, 45, '45,4506', 2, '防城港市', 'FangChengGangShi', 'FCGS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4507, 45, '45,4507', 2, '钦州市', 'QinZhouShi', 'QZS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4508, 45, '45,4508', 2, '贵港市', 'GuiGangShi', 'GGS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4509, 45, '45,4509', 2, '玉林市', 'YuLinShi', 'YLS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4510, 45, '45,4510', 2, '百色市', 'BaiSeShi', 'BSS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4511, 45, '45,4511', 2, '贺州市', 'HeZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4512, 45, '45,4512', 2, '河池市', 'HeChiShi', 'HCS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4513, 45, '45,4513', 2, '来宾市', 'LaiBinShi', 'LBS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4514, 45, '45,4514', 2, '崇左市', 'ChongZuoShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4601, 46, '46,4601', 2, '海口市', 'HaiKouShi', 'HKS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4602, 46, '46,4602', 2, '三亚市', 'SanYaShi', 'SYS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4603, 46, '46,4603', 2, '三沙市', 'SanShaShi', 'SSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (4604, 46, '46,4604', 2, '儋州市', 'DanZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5001, 50, '50,5001', 2, '重庆市', 'ChongQingShi', 'CQS', 'S', '023', '400000', '106.547391', '29.587079', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5101, 51, '51,5101', 2, '成都市', 'ChengDuShi', 'CDS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5103, 51, '51,5103', 2, '自贡市', 'ZiGongShi', 'ZGS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5104, 51, '51,5104', 2, '攀枝花市', 'PanZhiHuaShi', 'PZHS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5105, 51, '51,5105', 2, '泸州市', 'LuZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5106, 51, '51,5106', 2, '德阳市', 'DeYangShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5107, 51, '51,5107', 2, '绵阳市', 'MianYangShi', 'MYS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5108, 51, '51,5108', 2, '广元市', 'GuangYuanShi', 'GYS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5109, 51, '51,5109', 2, '遂宁市', 'SuiNingShi', 'SNS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5110, 51, '51,5110', 2, '内江市', 'NeiJiangShi', 'NJS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5111, 51, '51,5111', 2, '乐山市', 'LeShanShi', 'LSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5113, 51, '51,5113', 2, '南充市', 'NanChongShi', 'NCS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5114, 51, '51,5114', 2, '眉山市', 'MeiShanShi', 'MSS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5115, 51, '51,5115', 2, '宜宾市', 'YiBinShi', 'YBS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5116, 51, '51,5116', 2, '广安市', 'GuangAnShi', 'GAS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5117, 51, '51,5117', 2, '达州市', 'DaZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5118, 51, '51,5118', 2, '雅安市', 'YaAnShi', 'YAS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5119, 51, '51,5119', 2, '巴中市', 'BaZhongShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5120, 51, '51,5120', 2, '资阳市', 'ZiYangShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5132, 51, '51,5132', 2, '阿坝藏族羌族自治州', 'ABaZangZuQiangZuZiZhiZhou', 'ABZZQZZZZ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5133, 51, '51,5133', 2, '甘孜藏族自治州', 'GanZiZangZuZiZhiZhou', 'GZZZZZZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5134, 51, '51,5134', 2, '凉山彝族自治州', 'LiangShanYiZuZiZhiZhou', 'LSYZZZZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5201, 52, '52,5201', 2, '贵阳市', 'GuiYangShi', 'GYS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5202, 52, '52,5202', 2, '六盘水市', 'LiuPanShuiShi', 'LPSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5203, 52, '52,5203', 2, '遵义市', 'ZunYiShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5204, 52, '52,5204', 2, '安顺市', 'AnShunShi', 'ASS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5205, 52, '52,5205', 2, '毕节市', 'BiJieShi', 'BJS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5206, 52, '52,5206', 2, '铜仁市', 'TongRenShi', 'TRS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5223, 52, '52,5223', 2, '黔西南布依族苗族自治州', 'QianXiNanBuYiZuMiaoZuZiZhiZhou', 'QXNBYZMZZZZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5226, 52, '52,5226', 2, '黔东南苗族侗族自治州', 'QianDongNanMiaoZuDongZuZiZhiZhou', 'QDNMZDZZZZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5227, 52, '52,5227', 2, '黔南布依族苗族自治州', 'QianNanBuYiZuMiaoZuZiZhiZhou', 'QNBYZMZZZZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5301, 53, '53,5301', 2, '昆明市', 'KunMingShi', 'KMS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5303, 53, '53,5303', 2, '曲靖市', 'QuJingShi', 'QJS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5304, 53, '53,5304', 2, '玉溪市', 'YuXiShi', 'YXS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5305, 53, '53,5305', 2, '保山市', 'BaoShanShi', 'BSS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5306, 53, '53,5306', 2, '昭通市', 'ZhaoTongShi', 'ZTS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5307, 53, '53,5307', 2, '丽江市', 'LiJiangShi', 'LJS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5308, 53, '53,5308', 2, '普洱市', 'PuErShi', 'PES', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5309, 53, '53,5309', 2, '临沧市', 'LinCangShi', 'LCS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5323, 53, '53,5323', 2, '楚雄彝族自治州', 'ChuXiongYiZuZiZhiZhou', 'CXYZZZZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5325, 53, '53,5325', 2, '红河哈尼族彝族自治州', 'HongHeHaNiZuYiZuZiZhiZhou', 'HHHNZYZZZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5326, 53, '53,5326', 2, '文山壮族苗族自治州', 'WenShanZhuangZuMiaoZuZiZhiZhou', 'WSZZMZZZZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5328, 53, '53,5328', 2, '西双版纳傣族自治州', 'XiShuangBanNaDaiZuZiZhiZhou', 'XSBNDZZZZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5329, 53, '53,5329', 2, '大理白族自治州', 'DaLiBaiZuZiZhiZhou', 'DLBZZZZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5331, 53, '53,5331', 2, '德宏傣族景颇族自治州', 'DeHongDaiZuJingPoZuZiZhiZhou', 'DHDZJPZZZZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5333, 53, '53,5333', 2, '怒江傈僳族自治州', 'NuJiangLiSuZuZiZhiZhou', 'NJLSZZZZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5334, 53, '53,5334', 2, '迪庆藏族自治州', 'DiQingZangZuZiZhiZhou', 'DQZZZZZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5401, 54, '54,5401', 2, '拉萨市', 'LaSaShi', 'LSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5402, 54, '54,5402', 2, '日喀则市', 'RiKaZeShi', 'RKZS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5403, 54, '54,5403', 2, '昌都市', 'ChangDuShi', 'CDS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5404, 54, '54,5404', 2, '林芝市', 'LinZhiShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5405, 54, '54,5405', 2, '山南市', 'ShanNanShi', 'SNS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5406, 54, '54,5406', 2, '那曲市', 'NaQuShi', 'NQS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (5425, 54, '54,5425', 2, '阿里地区', 'ALiDiQu', 'ALDQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6101, 61, '61,6101', 2, '西安市', 'XiAnShi', 'XAS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6102, 61, '61,6102', 2, '铜川市', 'TongChuanShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6103, 61, '61,6103', 2, '宝鸡市', 'BaoJiShi', 'BJS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6104, 61, '61,6104', 2, '咸阳市', 'XianYangShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6105, 61, '61,6105', 2, '渭南市', 'WeiNanShi', 'WNS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6106, 61, '61,6106', 2, '延安市', 'YanAnShi', 'YAS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6107, 61, '61,6107', 2, '汉中市', 'HanZhongShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6108, 61, '61,6108', 2, '榆林市', 'YuLinShi', 'YLS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6109, 61, '61,6109', 2, '安康市', 'AnKangShi', 'AKS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6110, 61, '61,6110', 2, '商洛市', 'ShangLuoShi', 'SLS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6201, 62, '62,6201', 2, '兰州市', 'LanZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6202, 62, '62,6202', 2, '嘉峪关市', 'JiaYuGuanShi', 'JYGS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6203, 62, '62,6203', 2, '金昌市', 'JinChangShi', 'JCS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6204, 62, '62,6204', 2, '白银市', 'BaiYinShi', 'BYS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6205, 62, '62,6205', 2, '天水市', 'TianShuiShi', 'TSS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6206, 62, '62,6206', 2, '武威市', 'WuWeiShi', 'WWS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6207, 62, '62,6207', 2, '张掖市', 'ZhangYeShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6208, 62, '62,6208', 2, '平凉市', 'PingLiangShi', 'PLS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6209, 62, '62,6209', 2, '酒泉市', 'JiuQuanShi', 'JQS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6210, 62, '62,6210', 2, '庆阳市', 'QingYangShi', 'QYS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6211, 62, '62,6211', 2, '定西市', 'DingXiShi', 'DXS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6212, 62, '62,6212', 2, '陇南市', 'LongNanShi', 'LNS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6229, 62, '62,6229', 2, '临夏回族自治州', 'LinXiaHuiZuZiZhiZhou', 'LXHZZZZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6230, 62, '62,6230', 2, '甘南藏族自治州', 'GanNanZangZuZiZhiZhou', 'GNZZZZZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6301, 63, '63,6301', 2, '西宁市', 'XiNingShi', 'XNS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6302, 63, '63,6302', 2, '海东市', 'HaiDongShi', 'HDS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6322, 63, '63,6322', 2, '海北藏族自治州', 'HaiBeiZangZuZiZhiZhou', 'HBZZZZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6323, 63, '63,6323', 2, '黄南藏族自治州', 'HuangNanZangZuZiZhiZhou', 'HNZZZZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6325, 63, '63,6325', 2, '海南藏族自治州', 'HaiNanZangZuZiZhiZhou', 'HNZZZZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6326, 63, '63,6326', 2, '果洛藏族自治州', 'GuoLuoZangZuZiZhiZhou', 'GLZZZZZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6327, 63, '63,6327', 2, '玉树藏族自治州', 'YuShuZangZuZiZhiZhou', 'YSZZZZZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6328, 63, '63,6328', 2, '海西蒙古族藏族自治州', 'HaiXiMengGuZuZangZuZiZhiZhou', 'HXMGZZZZZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6401, 64, '64,6401', 2, '银川市', 'YinChuanShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6402, 64, '64,6402', 2, '石嘴山市', 'ShiZuiShanShi', 'SZSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6403, 64, '64,6403', 2, '吴忠市', 'WuZhongShi', 'WZS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6404, 64, '64,6404', 2, '固原市', 'GuYuanShi', 'GYS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6405, 64, '64,6405', 2, '中卫市', 'ZhongWeiShi', 'ZWS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6501, 65, '65,6501', 2, '乌鲁木齐市', 'WuLuMuQiShi', 'WLMQS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6502, 65, '65,6502', 2, '克拉玛依市', 'KeLaMaYiShi', 'KLMYS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6504, 65, '65,6504', 2, '吐鲁番市', 'TuLuFanShi', 'TLFS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6505, 65, '65,6505', 2, '哈密市', 'HaMiShi', 'HMS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6523, 65, '65,6523', 2, '昌吉回族自治州', 'ChangJiHuiZuZiZhiZhou', 'CJHZZZZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6527, 65, '65,6527', 2, '博尔塔拉蒙古自治州', 'BoErTaLaMengGuZiZhiZhou', 'BETLMGZZZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6528, 65, '65,6528', 2, '巴音郭楞蒙古自治州', 'BaYinGuoLengMengGuZiZhiZhou', 'BYGLMGZZZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6529, 65, '65,6529', 2, '阿克苏地区', 'AKeSuDiQu', 'AKSDQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6530, 65, '65,6530', 2, '克孜勒苏柯尔克孜自治州', 'KeZiLeSuKeErKeZiZiZhiZhou', 'KZLSKEKZZZZ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6531, 65, '65,6531', 2, '喀什地区', 'KaShiDiQu', 'KSDQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6532, 65, '65,6532', 2, '和田地区', 'HeTianDiQu', 'HTDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6540, 65, '65,6540', 2, '伊犁哈萨克自治州', 'YiLiHaSaKeZiZhiZhou', 'YLHSKZZZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6542, 65, '65,6542', 2, '塔城地区', 'TaChengDiQu', 'TCDQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (6543, 65, '65,6543', 2, '阿勒泰地区', 'ALeTaiDiQu', 'ALTDQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8200, 82, '82,8200', 2, '香港岛', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8201, 82, '82,8201', 2, '九龙', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8202, 82, '82,8202', 2, '新界', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8300, 83, '83,8300', 2, '澳门半岛', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8301, 83, '83,8301', 2, '澳门外岛', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8800, 88, '88,8800', 2, '台北市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8801, 88, '88,8801', 2, '新北市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8802, 88, '88,8802', 2, '桃园市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8803, 88, '88,8803', 2, '台中市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8804, 88, '88,8804', 2, '台南市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8805, 88, '88,8805', 2, '高雄市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8806, 88, '88,8806', 2, '基隆市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8807, 88, '88,8807', 2, '新竹市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8808, 88, '88,8808', 2, '嘉义市', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8809, 88, '88,8809', 2, '新竹县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8810, 88, '88,8810', 2, '苗栗县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8811, 88, '88,8811', 2, '彰化县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8812, 88, '88,8812', 2, '南投县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8813, 88, '88,8813', 2, '云林县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8814, 88, '88,8814', 2, '嘉义县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8815, 88, '88,8815', 2, '屏东县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8816, 88, '88,8816', 2, '宜兰县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8817, 88, '88,8817', 2, '花莲县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8818, 88, '88,8818', 2, '台东县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (8819, 88, '88,8819', 2, '澎湖县', '', '', '', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110101, 1101, '11,1101,110101', 3, '东城区', 'DongChengQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110102, 1101, '11,1101,110102', 3, '西城区', 'XiChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110105, 1101, '11,1101,110105', 3, '朝阳区', 'ChaoYangQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110106, 1101, '11,1101,110106', 3, '丰台区', 'FengTaiQu', 'FTQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110107, 1101, '11,1101,110107', 3, '石景山区', 'ShiJingShanQu', 'SJSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110108, 1101, '11,1101,110108', 3, '海淀区', 'HaiDianQu', 'HDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110109, 1101, '11,1101,110109', 3, '门头沟区', 'MenTouGouQu', 'MTGQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110111, 1101, '11,1101,110111', 3, '房山区', 'FangShanQu', 'FSQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110112, 1101, '11,1101,110112', 3, '通州区', 'TongZhouQu', 'TZQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110113, 1101, '11,1101,110113', 3, '顺义区', 'ShunYiQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110114, 1101, '11,1101,110114', 3, '昌平区', 'ChangPingQu', 'CPQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110115, 1101, '11,1101,110115', 3, '大兴区', 'DaXingQu', 'DXQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110116, 1101, '11,1101,110116', 3, '怀柔区', 'HuaiRouQu', 'HRQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110117, 1101, '11,1101,110117', 3, '平谷区', 'PingGuQu', 'PGQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110118, 1101, '11,1101,110118', 3, '密云区', 'MiYunQu', 'MYQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (110119, 1101, '11,1101,110119', 3, '延庆区', 'YanQingQu', 'YQQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120101, 1201, '12,1201,120101', 3, '和平区', 'HePingQu', 'HPQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120102, 1201, '12,1201,120102', 3, '河东区', 'HeDongQu', 'HDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120103, 1201, '12,1201,120103', 3, '河西区', 'HeXiQu', 'HXQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120104, 1201, '12,1201,120104', 3, '南开区', 'NanKaiQu', 'NKQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120105, 1201, '12,1201,120105', 3, '河北区', 'HeBeiQu', 'HBQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120106, 1201, '12,1201,120106', 3, '红桥区', 'HongQiaoQu', 'HQQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120110, 1201, '12,1201,120110', 3, '东丽区', 'DongLiQu', 'DLQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120111, 1201, '12,1201,120111', 3, '西青区', 'XiQingQu', 'XQQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120112, 1201, '12,1201,120112', 3, '津南区', 'JinNanQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120113, 1201, '12,1201,120113', 3, '北辰区', 'BeiChenQu', 'BCQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120114, 1201, '12,1201,120114', 3, '武清区', 'WuQingQu', 'WQQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120115, 1201, '12,1201,120115', 3, '宝坻区', 'BaoDiQu', 'BDQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120116, 1201, '12,1201,120116', 3, '滨海新区', 'BinHaiXinQu', 'BHXQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120117, 1201, '12,1201,120117', 3, '宁河区', 'NingHeQu', 'NHQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120118, 1201, '12,1201,120118', 3, '静海区', 'JingHaiQu', 'JHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (120119, 1201, '12,1201,120119', 3, '蓟州区', 'JiZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130102, 1301, '13,1301,130102', 3, '长安区', 'ChangAnQu', 'CAQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130104, 1301, '13,1301,130104', 3, '桥西区', 'QiaoXiQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130105, 1301, '13,1301,130105', 3, '新华区', 'XinHuaQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130107, 1301, '13,1301,130107', 3, '井陉矿区', 'JingXingKuangQu', 'JXKQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130108, 1301, '13,1301,130108', 3, '裕华区', 'YuHuaQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130109, 1301, '13,1301,130109', 3, '藁城区', 'GaoChengQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130110, 1301, '13,1301,130110', 3, '鹿泉区', 'LuQuanQu', 'LQQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130111, 1301, '13,1301,130111', 3, '栾城区', 'LuanChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130121, 1301, '13,1301,130121', 3, '井陉县', 'JingXingXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130123, 1301, '13,1301,130123', 3, '正定县', 'ZhengDingXian', 'ZDX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130125, 1301, '13,1301,130125', 3, '行唐县', 'XingTangXian', 'XTX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130126, 1301, '13,1301,130126', 3, '灵寿县', 'LingShouXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130127, 1301, '13,1301,130127', 3, '高邑县', 'GaoYiXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130128, 1301, '13,1301,130128', 3, '深泽县', 'ShenZeXian', 'SZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130129, 1301, '13,1301,130129', 3, '赞皇县', 'ZanHuangXian', 'ZHX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130130, 1301, '13,1301,130130', 3, '无极县', 'WuJiXian', 'WJX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130131, 1301, '13,1301,130131', 3, '平山县', 'PingShanXian', 'PSX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130132, 1301, '13,1301,130132', 3, '元氏县', 'YuanShiXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130133, 1301, '13,1301,130133', 3, '赵县', 'ZhaoXian', 'ZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130171, 1301, '13,1301,130171', 3, '石家庄高新技术产业开发区', 'ShiJiaZhuangGaoXinJiShuChanYeKaiFaQu', 'SJZGXJSCYKFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130172, 1301, '13,1301,130172', 3, '石家庄循环化工园区', 'ShiJiaZhuangXunHuanHuaGongYuanQu', 'SJZXHHGYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130181, 1301, '13,1301,130181', 3, '辛集市', 'XinJiShi', 'XJS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130183, 1301, '13,1301,130183', 3, '晋州市', 'JinZhouShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130184, 1301, '13,1301,130184', 3, '新乐市', 'XinLeShi', 'XLS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130202, 1302, '13,1302,130202', 3, '路南区', 'LuNanQu', 'LNQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130203, 1302, '13,1302,130203', 3, '路北区', 'LuBeiQu', 'LBQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130204, 1302, '13,1302,130204', 3, '古冶区', 'GuYeQu', 'GYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130205, 1302, '13,1302,130205', 3, '开平区', 'KaiPingQu', 'KPQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130207, 1302, '13,1302,130207', 3, '丰南区', 'FengNanQu', 'FNQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130208, 1302, '13,1302,130208', 3, '丰润区', 'FengRunQu', 'FRQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130209, 1302, '13,1302,130209', 3, '曹妃甸区', 'CaoFeiDianQu', 'CFDQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130224, 1302, '13,1302,130224', 3, '滦南县', 'LuanNanXian', 'LNX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130225, 1302, '13,1302,130225', 3, '乐亭县', 'LeTingXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130227, 1302, '13,1302,130227', 3, '迁西县', 'QianXiXian', 'QXX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130229, 1302, '13,1302,130229', 3, '玉田县', 'YuTianXian', 'YTX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130271, 1302, '13,1302,130271', 3, '河北唐山芦台经济开发区', 'HeBeiTangShanLuTaiJingJiKaiFaQu', 'HBTSLTJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130272, 1302, '13,1302,130272', 3, '唐山市汉沽管理区', 'TangShanShiHanGuGuanLiQu', 'TSSHGGLQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130273, 1302, '13,1302,130273', 3, '唐山高新技术产业开发区', 'TangShanGaoXinJiShuChanYeKaiFaQu', 'TSGXJSCYKFQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130274, 1302, '13,1302,130274', 3, '河北唐山海港经济开发区', 'HeBeiTangShanHaiGangJingJiKaiFaQu', 'HBTSHGJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130281, 1302, '13,1302,130281', 3, '遵化市', 'ZunHuaShi', 'ZHS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130283, 1302, '13,1302,130283', 3, '迁安市', 'QianAnShi', 'QAS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130284, 1302, '13,1302,130284', 3, '滦州市', 'LuanZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130302, 1303, '13,1303,130302', 3, '海港区', 'HaiGangQu', 'HGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130303, 1303, '13,1303,130303', 3, '山海关区', 'ShanHaiGuanQu', 'SHGQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130304, 1303, '13,1303,130304', 3, '北戴河区', 'BeiDaiHeQu', 'BDHQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130306, 1303, '13,1303,130306', 3, '抚宁区', 'FuNingQu', 'FNQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130321, 1303, '13,1303,130321', 3, '青龙满族自治县', 'QingLongManZuZiZhiXian', 'QLMZZZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130322, 1303, '13,1303,130322', 3, '昌黎县', 'ChangLiXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130324, 1303, '13,1303,130324', 3, '卢龙县', 'LuLongXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130371, 1303, '13,1303,130371', 3, '秦皇岛市经济技术开发区', 'QinHuangDaoShiJingJiJiShuKaiFaQu', 'QHDSJJJSKFQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130372, 1303, '13,1303,130372', 3, '北戴河新区', 'BeiDaiHeXinQu', 'BDHXQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130402, 1304, '13,1304,130402', 3, '邯山区', 'HanShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130403, 1304, '13,1304,130403', 3, '丛台区', 'CongTaiQu', 'CTQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130404, 1304, '13,1304,130404', 3, '复兴区', 'FuXingQu', 'FXQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130406, 1304, '13,1304,130406', 3, '峰峰矿区', 'FengFengKuangQu', 'FFKQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130407, 1304, '13,1304,130407', 3, '肥乡区', 'FeiXiangQu', 'FXQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130408, 1304, '13,1304,130408', 3, '永年区', 'YongNianQu', 'YNQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130423, 1304, '13,1304,130423', 3, '临漳县', 'LinZhangXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130424, 1304, '13,1304,130424', 3, '成安县', 'ChengAnXian', 'CAX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130425, 1304, '13,1304,130425', 3, '大名县', 'DaMingXian', 'DMX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130426, 1304, '13,1304,130426', 3, '涉县', 'SheXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130427, 1304, '13,1304,130427', 3, '磁县', 'CiXian', 'CX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130430, 1304, '13,1304,130430', 3, '邱县', 'QiuXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130431, 1304, '13,1304,130431', 3, '鸡泽县', 'JiZeXian', 'JZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130432, 1304, '13,1304,130432', 3, '广平县', 'GuangPingXian', 'GPX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130433, 1304, '13,1304,130433', 3, '馆陶县', 'GuanTaoXian', 'GTX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130434, 1304, '13,1304,130434', 3, '魏县', 'WeiXian', 'WX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130435, 1304, '13,1304,130435', 3, '曲周县', 'QuZhouXian', 'QZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130471, 1304, '13,1304,130471', 3, '邯郸经济技术开发区', 'HanDanJingJiJiShuKaiFaQu', 'HDJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130473, 1304, '13,1304,130473', 3, '邯郸冀南新区', 'HanDanJiNanXinQu', 'HDJNXQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130481, 1304, '13,1304,130481', 3, '武安市', 'WuAnShi', 'WAS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130502, 1305, '13,1305,130502', 3, '桥东区', 'QiaoDongQu', 'QDQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130503, 1305, '13,1305,130503', 3, '桥西区', 'QiaoXiQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130521, 1305, '13,1305,130521', 3, '邢台县', 'XingTaiXian', 'XTX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130522, 1305, '13,1305,130522', 3, '临城县', 'LinChengXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130523, 1305, '13,1305,130523', 3, '内丘县', 'NeiQiuXian', 'NQX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130524, 1305, '13,1305,130524', 3, '柏乡县', 'BaiXiangXian', 'BXX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130525, 1305, '13,1305,130525', 3, '隆尧县', 'LongYaoXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130526, 1305, '13,1305,130526', 3, '任县', 'RenXian', 'RX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130527, 1305, '13,1305,130527', 3, '南和县', 'NanHeXian', 'NHX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130528, 1305, '13,1305,130528', 3, '宁晋县', 'NingJinXian', 'NJX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130529, 1305, '13,1305,130529', 3, '巨鹿县', 'JuLuXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130530, 1305, '13,1305,130530', 3, '新河县', 'XinHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130531, 1305, '13,1305,130531', 3, '广宗县', 'GuangZongXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130532, 1305, '13,1305,130532', 3, '平乡县', 'PingXiangXian', 'PXX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130533, 1305, '13,1305,130533', 3, '威县', 'WeiXian', 'WX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130534, 1305, '13,1305,130534', 3, '清河县', 'QingHeXian', 'QHX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130535, 1305, '13,1305,130535', 3, '临西县', 'LinXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130571, 1305, '13,1305,130571', 3, '河北邢台经济开发区', 'HeBeiXingTaiJingJiKaiFaQu', 'HBXTJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130581, 1305, '13,1305,130581', 3, '南宫市', 'NanGongShi', 'NGS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130582, 1305, '13,1305,130582', 3, '沙河市', 'ShaHeShi', 'SHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130602, 1306, '13,1306,130602', 3, '竞秀区', 'JingXiuQu', 'JXQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130606, 1306, '13,1306,130606', 3, '莲池区', 'LianChiQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130607, 1306, '13,1306,130607', 3, '满城区', 'ManChengQu', 'MCQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130608, 1306, '13,1306,130608', 3, '清苑区', 'QingYuanQu', 'QYQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130609, 1306, '13,1306,130609', 3, '徐水区', 'XuShuiQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130623, 1306, '13,1306,130623', 3, '涞水县', 'LaiShuiXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130624, 1306, '13,1306,130624', 3, '阜平县', 'FuPingXian', 'FPX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130626, 1306, '13,1306,130626', 3, '定兴县', 'DingXingXian', 'DXX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130627, 1306, '13,1306,130627', 3, '唐县', 'TangXian', 'TX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130628, 1306, '13,1306,130628', 3, '高阳县', 'GaoYangXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130629, 1306, '13,1306,130629', 3, '容城县', 'RongChengXian', 'RCX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130630, 1306, '13,1306,130630', 3, '涞源县', 'LaiYuanXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130631, 1306, '13,1306,130631', 3, '望都县', 'WangDuXian', 'WDX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130632, 1306, '13,1306,130632', 3, '安新县', 'AnXinXian', 'AXX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130633, 1306, '13,1306,130633', 3, '易县', 'YiXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130634, 1306, '13,1306,130634', 3, '曲阳县', 'QuYangXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130635, 1306, '13,1306,130635', 3, '蠡县', 'LiXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130636, 1306, '13,1306,130636', 3, '顺平县', 'ShunPingXian', 'SPX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130637, 1306, '13,1306,130637', 3, '博野县', 'BoYeXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130638, 1306, '13,1306,130638', 3, '雄县', 'XiongXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130671, 1306, '13,1306,130671', 3, '保定高新技术产业开发区', 'BaoDingGaoXinJiShuChanYeKaiFaQu', 'BDGXJSCYKFQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130672, 1306, '13,1306,130672', 3, '保定白沟新城', 'BaoDingBaiGouXinCheng', 'BDBGXC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130681, 1306, '13,1306,130681', 3, '涿州市', 'ZhuoZhouShi', 'ZZS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130682, 1306, '13,1306,130682', 3, '定州市', 'DingZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130683, 1306, '13,1306,130683', 3, '安国市', 'AnGuoShi', 'AGS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130684, 1306, '13,1306,130684', 3, '高碑店市', 'GaoBeiDianShi', 'GBDS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130702, 1307, '13,1307,130702', 3, '桥东区', 'QiaoDongQu', 'QDQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130703, 1307, '13,1307,130703', 3, '桥西区', 'QiaoXiQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130705, 1307, '13,1307,130705', 3, '宣化区', 'XuanHuaQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130706, 1307, '13,1307,130706', 3, '下花园区', 'XiaHuaYuanQu', 'XHYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130708, 1307, '13,1307,130708', 3, '万全区', 'WanQuanQu', 'WQQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130709, 1307, '13,1307,130709', 3, '崇礼区', 'ChongLiQu', 'CLQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130722, 1307, '13,1307,130722', 3, '张北县', 'ZhangBeiXian', 'ZBX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130723, 1307, '13,1307,130723', 3, '康保县', 'KangBaoXian', 'KBX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130724, 1307, '13,1307,130724', 3, '沽源县', 'GuYuanXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130725, 1307, '13,1307,130725', 3, '尚义县', 'ShangYiXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130726, 1307, '13,1307,130726', 3, '蔚县', 'YuXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130727, 1307, '13,1307,130727', 3, '阳原县', 'YangYuanXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130728, 1307, '13,1307,130728', 3, '怀安县', 'HuaiAnXian', 'HAX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130730, 1307, '13,1307,130730', 3, '怀来县', 'HuaiLaiXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130731, 1307, '13,1307,130731', 3, '涿鹿县', 'ZhuoLuXian', 'ZLX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130732, 1307, '13,1307,130732', 3, '赤城县', 'ChiChengXian', 'CCX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130771, 1307, '13,1307,130771', 3, '张家口经济开发区', 'ZhangJiaKouJingJiKaiFaQu', 'ZJKJJKFQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130772, 1307, '13,1307,130772', 3, '张家口市察北管理区', 'ZhangJiaKouShiChaBeiGuanLiQu', 'ZJKSCBGLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130773, 1307, '13,1307,130773', 3, '张家口市塞北管理区', 'ZhangJiaKouShiSaiBeiGuanLiQu', 'ZJKSSBGLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130802, 1308, '13,1308,130802', 3, '双桥区', 'ShuangQiaoQu', 'SQQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130803, 1308, '13,1308,130803', 3, '双滦区', 'ShuangLuanQu', 'SLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130804, 1308, '13,1308,130804', 3, '鹰手营子矿区', 'YingShouYingZiKuangQu', 'YSYZKQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130821, 1308, '13,1308,130821', 3, '承德县', 'ChengDeXian', 'CDX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130822, 1308, '13,1308,130822', 3, '兴隆县', 'XingLongXian', 'XLX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130824, 1308, '13,1308,130824', 3, '滦平县', 'LuanPingXian', 'LPX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130825, 1308, '13,1308,130825', 3, '隆化县', 'LongHuaXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130826, 1308, '13,1308,130826', 3, '丰宁满族自治县', 'FengNingManZuZiZhiXian', 'FNMZZZX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130827, 1308, '13,1308,130827', 3, '宽城满族自治县', 'KuanChengManZuZiZhiXian', 'KCMZZZX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130828, 1308, '13,1308,130828', 3, '围场满族蒙古族自治县', 'WeiChangManZuMengGuZuZiZhiXian', 'WCMZMGZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130871, 1308, '13,1308,130871', 3, '承德高新技术产业开发区', 'ChengDeGaoXinJiShuChanYeKaiFaQu', 'CDGXJSCYKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130881, 1308, '13,1308,130881', 3, '平泉市', 'PingQuanShi', 'PQS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130902, 1309, '13,1309,130902', 3, '新华区', 'XinHuaQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130903, 1309, '13,1309,130903', 3, '运河区', 'YunHeQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130921, 1309, '13,1309,130921', 3, '沧县', 'CangXian', 'CX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130922, 1309, '13,1309,130922', 3, '青县', 'QingXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130923, 1309, '13,1309,130923', 3, '东光县', 'DongGuangXian', 'DGX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130924, 1309, '13,1309,130924', 3, '海兴县', 'HaiXingXian', 'HXX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130925, 1309, '13,1309,130925', 3, '盐山县', 'YanShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130926, 1309, '13,1309,130926', 3, '肃宁县', 'SuNingXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130927, 1309, '13,1309,130927', 3, '南皮县', 'NanPiXian', 'NPX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130928, 1309, '13,1309,130928', 3, '吴桥县', 'WuQiaoXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130929, 1309, '13,1309,130929', 3, '献县', 'XianXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130930, 1309, '13,1309,130930', 3, '孟村回族自治县', 'MengCunHuiZuZiZhiXian', 'MCHZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130971, 1309, '13,1309,130971', 3, '河北沧州经济开发区', 'HeBeiCangZhouJingJiKaiFaQu', 'HBCZJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130972, 1309, '13,1309,130972', 3, '沧州高新技术产业开发区', 'CangZhouGaoXinJiShuChanYeKaiFaQu', 'CZGXJSCYKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130973, 1309, '13,1309,130973', 3, '沧州渤海新区', 'CangZhouBoHaiXinQu', 'CZBHXQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130981, 1309, '13,1309,130981', 3, '泊头市', 'BoTouShi', 'BTS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130982, 1309, '13,1309,130982', 3, '任丘市', 'RenQiuShi', 'RQS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130983, 1309, '13,1309,130983', 3, '黄骅市', 'HuangHuaShi', 'HHS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (130984, 1309, '13,1309,130984', 3, '河间市', 'HeJianShi', 'HJS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131002, 1310, '13,1310,131002', 3, '安次区', 'AnCiQu', 'ACQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131003, 1310, '13,1310,131003', 3, '广阳区', 'GuangYangQu', 'GYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131022, 1310, '13,1310,131022', 3, '固安县', 'GuAnXian', 'GAX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131023, 1310, '13,1310,131023', 3, '永清县', 'YongQingXian', 'YQX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131024, 1310, '13,1310,131024', 3, '香河县', 'XiangHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131025, 1310, '13,1310,131025', 3, '大城县', 'DaiChengXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131026, 1310, '13,1310,131026', 3, '文安县', 'WenAnXian', 'WAX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131028, 1310, '13,1310,131028', 3, '大厂回族自治县', 'DaChangHuiZuZiZhiXian', 'DCHZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131071, 1310, '13,1310,131071', 3, '廊坊经济技术开发区', 'LangFangJingJiJiShuKaiFaQu', 'LFJJJSKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131081, 1310, '13,1310,131081', 3, '霸州市', 'BaZhouShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131082, 1310, '13,1310,131082', 3, '三河市', 'SanHeShi', 'SHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131102, 1311, '13,1311,131102', 3, '桃城区', 'TaoChengQu', 'TCQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131103, 1311, '13,1311,131103', 3, '冀州区', 'JiZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131121, 1311, '13,1311,131121', 3, '枣强县', 'ZaoQiangXian', 'ZQX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131122, 1311, '13,1311,131122', 3, '武邑县', 'WuYiXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131123, 1311, '13,1311,131123', 3, '武强县', 'WuQiangXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131124, 1311, '13,1311,131124', 3, '饶阳县', 'RaoYangXian', 'RYX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131125, 1311, '13,1311,131125', 3, '安平县', 'AnPingXian', 'APX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131126, 1311, '13,1311,131126', 3, '故城县', 'GuChengXian', 'GCX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131127, 1311, '13,1311,131127', 3, '景县', 'JingXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131128, 1311, '13,1311,131128', 3, '阜城县', 'FuChengXian', 'FCX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131171, 1311, '13,1311,131171', 3, '河北衡水高新技术产业开发区', 'HeBeiHengShuiGaoXinJiShuChanYeKaiFaQu', 'HBHSGXJSCYKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131172, 1311, '13,1311,131172', 3, '衡水滨湖新区', 'HengShuiBinHuXinQu', 'HSBHXQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (131182, 1311, '13,1311,131182', 3, '深州市', 'ShenZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140105, 1401, '14,1401,140105', 3, '小店区', 'XiaoDianQu', 'XDQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140106, 1401, '14,1401,140106', 3, '迎泽区', 'YingZeQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140107, 1401, '14,1401,140107', 3, '杏花岭区', 'XingHuaLingQu', 'XHLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140108, 1401, '14,1401,140108', 3, '尖草坪区', 'JianCaoPingQu', 'JCPQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140109, 1401, '14,1401,140109', 3, '万柏林区', 'WanBoLinQu', 'WBLQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140110, 1401, '14,1401,140110', 3, '晋源区', 'JinYuanQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140121, 1401, '14,1401,140121', 3, '清徐县', 'QingXuXian', 'QXX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140122, 1401, '14,1401,140122', 3, '阳曲县', 'YangQuXian', 'YQX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140123, 1401, '14,1401,140123', 3, '娄烦县', 'LouFanXian', 'LFX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140171, 1401, '14,1401,140171', 3, '山西转型综合改革示范区', 'ShanXiZhuanXingZongHeGaiGeShiFanQu', 'SXZXZHGGSFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140181, 1401, '14,1401,140181', 3, '古交市', 'GuJiaoShi', 'GJS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140212, 1402, '14,1402,140212', 3, '新荣区', 'XinRongQu', 'XRQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140213, 1402, '14,1402,140213', 3, '平城区', 'PingChengQu', 'PCQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140214, 1402, '14,1402,140214', 3, '云冈区', 'YunGangQu', 'YGQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140215, 1402, '14,1402,140215', 3, '云州区', 'YunZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140221, 1402, '14,1402,140221', 3, '阳高县', 'YangGaoXian', 'YGX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140222, 1402, '14,1402,140222', 3, '天镇县', 'TianZhenXian', 'TZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140223, 1402, '14,1402,140223', 3, '广灵县', 'GuangLingXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140224, 1402, '14,1402,140224', 3, '灵丘县', 'LingQiuXian', 'LQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140225, 1402, '14,1402,140225', 3, '浑源县', 'HunYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140226, 1402, '14,1402,140226', 3, '左云县', 'ZuoYunXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140271, 1402, '14,1402,140271', 3, '山西大同经济开发区', 'ShanXiDaTongJingJiKaiFaQu', 'SXDTJJKFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140302, 1403, '14,1403,140302', 3, '城区', 'ChengQu', 'CQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140303, 1403, '14,1403,140303', 3, '矿区', 'KuangQu', 'KQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140311, 1403, '14,1403,140311', 3, '郊区', 'JiaoQu', 'JQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140321, 1403, '14,1403,140321', 3, '平定县', 'PingDingXian', 'PDX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140322, 1403, '14,1403,140322', 3, '盂县', 'YuXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140403, 1404, '14,1404,140403', 3, '潞州区', 'LuZhouQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140404, 1404, '14,1404,140404', 3, '上党区', 'ShangDangQu', 'SDQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140405, 1404, '14,1404,140405', 3, '屯留区', 'TunLiuQu', 'TLQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140406, 1404, '14,1404,140406', 3, '潞城区', 'LuChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140423, 1404, '14,1404,140423', 3, '襄垣县', 'XiangYuanXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140425, 1404, '14,1404,140425', 3, '平顺县', 'PingShunXian', 'PSX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140426, 1404, '14,1404,140426', 3, '黎城县', 'LiChengXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140427, 1404, '14,1404,140427', 3, '壶关县', 'HuGuanXian', 'HGX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140428, 1404, '14,1404,140428', 3, '长子县', 'ChangZiXian', 'CZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140429, 1404, '14,1404,140429', 3, '武乡县', 'WuXiangXian', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140430, 1404, '14,1404,140430', 3, '沁县', 'QinXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140431, 1404, '14,1404,140431', 3, '沁源县', 'QinYuanXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140471, 1404, '14,1404,140471', 3, '山西长治高新技术产业园区', 'ShanXiChangZhiGaoXinJiShuChanYeYuanQu', 'SXCZGXJSCYYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140502, 1405, '14,1405,140502', 3, '城区', 'ChengQu', 'CQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140521, 1405, '14,1405,140521', 3, '沁水县', 'QinShuiXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140522, 1405, '14,1405,140522', 3, '阳城县', 'YangChengXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140524, 1405, '14,1405,140524', 3, '陵川县', 'LingChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140525, 1405, '14,1405,140525', 3, '泽州县', 'ZeZhouXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140581, 1405, '14,1405,140581', 3, '高平市', 'GaoPingShi', 'GPS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140602, 1406, '14,1406,140602', 3, '朔城区', 'ShuoChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140603, 1406, '14,1406,140603', 3, '平鲁区', 'PingLuQu', 'PLQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140621, 1406, '14,1406,140621', 3, '山阴县', 'ShanYinXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140622, 1406, '14,1406,140622', 3, '应县', 'YingXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140623, 1406, '14,1406,140623', 3, '右玉县', 'YouYuXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140671, 1406, '14,1406,140671', 3, '山西朔州经济开发区', 'ShanXiShuoZhouJingJiKaiFaQu', 'SXSZJJKFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140681, 1406, '14,1406,140681', 3, '怀仁市', 'HuaiRenShi', 'HRS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140702, 1407, '14,1407,140702', 3, '榆次区', 'YuCiQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140721, 1407, '14,1407,140721', 3, '榆社县', 'YuSheXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140722, 1407, '14,1407,140722', 3, '左权县', 'ZuoQuanXian', 'ZQX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140723, 1407, '14,1407,140723', 3, '和顺县', 'HeShunXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140724, 1407, '14,1407,140724', 3, '昔阳县', 'XiYangXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140725, 1407, '14,1407,140725', 3, '寿阳县', 'ShouYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140726, 1407, '14,1407,140726', 3, '太谷县', 'TaiGuXian', 'TGX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140727, 1407, '14,1407,140727', 3, '祁县', 'QiXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140728, 1407, '14,1407,140728', 3, '平遥县', 'PingYaoXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140729, 1407, '14,1407,140729', 3, '灵石县', 'LingShiXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140781, 1407, '14,1407,140781', 3, '介休市', 'JieXiuShi', 'JXS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140802, 1408, '14,1408,140802', 3, '盐湖区', 'YanHuQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140821, 1408, '14,1408,140821', 3, '临猗县', 'LinYiXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140822, 1408, '14,1408,140822', 3, '万荣县', 'WanRongXian', 'WRX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140823, 1408, '14,1408,140823', 3, '闻喜县', 'WenXiXian', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140824, 1408, '14,1408,140824', 3, '稷山县', 'JiShanXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140825, 1408, '14,1408,140825', 3, '新绛县', 'XinJiangXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140826, 1408, '14,1408,140826', 3, '绛县', 'JiangXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140827, 1408, '14,1408,140827', 3, '垣曲县', 'YuanQuXian', 'YQX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140828, 1408, '14,1408,140828', 3, '夏县', 'XiaXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140829, 1408, '14,1408,140829', 3, '平陆县', 'PingLuXian', 'PLX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140830, 1408, '14,1408,140830', 3, '芮城县', 'RuiChengXian', 'RCX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140881, 1408, '14,1408,140881', 3, '永济市', 'YongJiShi', 'YJS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140882, 1408, '14,1408,140882', 3, '河津市', 'HeJinShi', 'HJS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140902, 1409, '14,1409,140902', 3, '忻府区', 'XinFuQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140921, 1409, '14,1409,140921', 3, '定襄县', 'DingXiangXian', 'DXX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140922, 1409, '14,1409,140922', 3, '五台县', 'WuTaiXian', 'WTX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140923, 1409, '14,1409,140923', 3, '代县', 'DaiXian', 'DX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140924, 1409, '14,1409,140924', 3, '繁峙县', 'FanShiXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140925, 1409, '14,1409,140925', 3, '宁武县', 'NingWuXian', 'NWX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140926, 1409, '14,1409,140926', 3, '静乐县', 'JingLeXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140927, 1409, '14,1409,140927', 3, '神池县', 'ShenChiXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140928, 1409, '14,1409,140928', 3, '五寨县', 'WuZhaiXian', 'WZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140929, 1409, '14,1409,140929', 3, '岢岚县', 'KeLanXian', 'KLX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140930, 1409, '14,1409,140930', 3, '河曲县', 'HeQuXian', 'HQX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140931, 1409, '14,1409,140931', 3, '保德县', 'BaoDeXian', 'BDX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140932, 1409, '14,1409,140932', 3, '偏关县', 'PianGuanXian', 'PGX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140971, 1409, '14,1409,140971', 3, '五台山风景名胜区', 'WuTaiShanFengJingMingShengQu', 'WTSFJMSQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (140981, 1409, '14,1409,140981', 3, '原平市', 'YuanPingShi', 'YPS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141002, 1410, '14,1410,141002', 3, '尧都区', 'YaoDuQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141021, 1410, '14,1410,141021', 3, '曲沃县', 'QuWoXian', 'QWX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141022, 1410, '14,1410,141022', 3, '翼城县', 'YiChengXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141023, 1410, '14,1410,141023', 3, '襄汾县', 'XiangFenXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141024, 1410, '14,1410,141024', 3, '洪洞县', 'HongTongXian', 'HTX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141025, 1410, '14,1410,141025', 3, '古县', 'GuXian', 'GX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141026, 1410, '14,1410,141026', 3, '安泽县', 'AnZeXian', 'AZX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141027, 1410, '14,1410,141027', 3, '浮山县', 'FuShanXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141028, 1410, '14,1410,141028', 3, '吉县', 'JiXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141029, 1410, '14,1410,141029', 3, '乡宁县', 'XiangNingXian', 'XNX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141030, 1410, '14,1410,141030', 3, '大宁县', 'DaNingXian', 'DNX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141031, 1410, '14,1410,141031', 3, '隰县', 'XiXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141032, 1410, '14,1410,141032', 3, '永和县', 'YongHeXian', 'YHX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141033, 1410, '14,1410,141033', 3, '蒲县', 'PuXian', 'PX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141034, 1410, '14,1410,141034', 3, '汾西县', 'FenXiXian', 'FXX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141081, 1410, '14,1410,141081', 3, '侯马市', 'HouMaShi', 'HMS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141082, 1410, '14,1410,141082', 3, '霍州市', 'HuoZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141102, 1411, '14,1411,141102', 3, '离石区', 'LiShiQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141121, 1411, '14,1411,141121', 3, '文水县', 'WenShuiXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141122, 1411, '14,1411,141122', 3, '交城县', 'JiaoChengXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141123, 1411, '14,1411,141123', 3, '兴县', 'XingXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141124, 1411, '14,1411,141124', 3, '临县', 'LinXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141125, 1411, '14,1411,141125', 3, '柳林县', 'LiuLinXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141126, 1411, '14,1411,141126', 3, '石楼县', 'ShiLouXian', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141127, 1411, '14,1411,141127', 3, '岚县', 'LanXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141128, 1411, '14,1411,141128', 3, '方山县', 'FangShanXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141129, 1411, '14,1411,141129', 3, '中阳县', 'ZhongYangXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141130, 1411, '14,1411,141130', 3, '交口县', 'JiaoKouXian', 'JKX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141181, 1411, '14,1411,141181', 3, '孝义市', 'XiaoYiShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (141182, 1411, '14,1411,141182', 3, '汾阳市', 'FenYangShi', 'FYS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150102, 1501, '15,1501,150102', 3, '新城区', 'XinChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150103, 1501, '15,1501,150103', 3, '回民区', 'HuiMinQu', 'HMQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150104, 1501, '15,1501,150104', 3, '玉泉区', 'YuQuanQu', 'YQQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150105, 1501, '15,1501,150105', 3, '赛罕区', 'SaiHanQu', 'SHQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150121, 1501, '15,1501,150121', 3, '土默特左旗', 'TuMoTeZuoQi', 'TMTZQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150122, 1501, '15,1501,150122', 3, '托克托县', 'TuoKeTuoXian', 'TKTX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150123, 1501, '15,1501,150123', 3, '和林格尔县', 'HeLinGeErXian', 'HLGEX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150124, 1501, '15,1501,150124', 3, '清水河县', 'QingShuiHeXian', 'QSHX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150125, 1501, '15,1501,150125', 3, '武川县', 'WuChuanXian', 'WCX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150171, 1501, '15,1501,150171', 3, '呼和浩特金海工业园区', 'HuHeHaoTeJinHaiGongYeYuanQu', 'HHHTJHGYYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150172, 1501, '15,1501,150172', 3, '呼和浩特经济技术开发区', 'HuHeHaoTeJingJiJiShuKaiFaQu', 'HHHTJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150202, 1502, '15,1502,150202', 3, '东河区', 'DongHeQu', 'DHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150203, 1502, '15,1502,150203', 3, '昆都仑区', 'KunDuLunQu', 'KDLQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150204, 1502, '15,1502,150204', 3, '青山区', 'QingShanQu', 'QSQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150205, 1502, '15,1502,150205', 3, '石拐区', 'ShiGuaiQu', 'SGQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150206, 1502, '15,1502,150206', 3, '白云鄂博矿区', 'BaiYunEBoKuangQu', 'BYEBKQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150207, 1502, '15,1502,150207', 3, '九原区', 'JiuYuanQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150221, 1502, '15,1502,150221', 3, '土默特右旗', 'TuMoTeYouQi', 'TMTYQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150222, 1502, '15,1502,150222', 3, '固阳县', 'GuYangXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150223, 1502, '15,1502,150223', 3, '达尔罕茂明安联合旗', 'DaErHanMaoMingAnLianHeQi', 'DEHMMALHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150271, 1502, '15,1502,150271', 3, '包头稀土高新技术产业开发区', 'BaoTouXiTuGaoXinJiShuChanYeKaiFaQu', 'BTXTGXJSCYKFQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150302, 1503, '15,1503,150302', 3, '海勃湾区', 'HaiBoWanQu', 'HBWQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150303, 1503, '15,1503,150303', 3, '海南区', 'HaiNanQu', 'HNQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150304, 1503, '15,1503,150304', 3, '乌达区', 'WuDaQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150402, 1504, '15,1504,150402', 3, '红山区', 'HongShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150403, 1504, '15,1504,150403', 3, '元宝山区', 'YuanBaoShanQu', 'YBSQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150404, 1504, '15,1504,150404', 3, '松山区', 'SongShanQu', 'SSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150421, 1504, '15,1504,150421', 3, '阿鲁科尔沁旗', 'ALuKeErQinQi', 'ALKEQQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150422, 1504, '15,1504,150422', 3, '巴林左旗', 'BaLinZuoQi', 'BLZQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150423, 1504, '15,1504,150423', 3, '巴林右旗', 'BaLinYouQi', 'BLYQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150424, 1504, '15,1504,150424', 3, '林西县', 'LinXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150425, 1504, '15,1504,150425', 3, '克什克腾旗', 'KeShiKeTengQi', 'KSKTQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150426, 1504, '15,1504,150426', 3, '翁牛特旗', 'WengNiuTeQi', 'WNTQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150428, 1504, '15,1504,150428', 3, '喀喇沁旗', 'KaLaQinQi', 'KLQQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150429, 1504, '15,1504,150429', 3, '宁城县', 'NingChengXian', 'NCX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150430, 1504, '15,1504,150430', 3, '敖汉旗', 'AoHanQi', 'AHQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150502, 1505, '15,1505,150502', 3, '科尔沁区', 'KeErQinQu', 'KEQQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150521, 1505, '15,1505,150521', 3, '科尔沁左翼中旗', 'KeErQinZuoYiZhongQi', 'KEQZYZQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150522, 1505, '15,1505,150522', 3, '科尔沁左翼后旗', 'KeErQinZuoYiHouQi', 'KEQZYHQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150523, 1505, '15,1505,150523', 3, '开鲁县', 'KaiLuXian', 'KLX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150524, 1505, '15,1505,150524', 3, '库伦旗', 'KuLunQi', 'KLQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150525, 1505, '15,1505,150525', 3, '奈曼旗', 'NaiManQi', 'NMQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150526, 1505, '15,1505,150526', 3, '扎鲁特旗', 'ZaLuTeQi', 'ZLTQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150571, 1505, '15,1505,150571', 3, '通辽经济技术开发区', 'TongLiaoJingJiJiShuKaiFaQu', 'TLJJJSKFQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150581, 1505, '15,1505,150581', 3, '霍林郭勒市', 'HuoLinGuoLeShi', 'HLGLS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150602, 1506, '15,1506,150602', 3, '东胜区', 'DongShengQu', 'DSQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150603, 1506, '15,1506,150603', 3, '康巴什区', 'KangBaShenQu', 'KBSQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150621, 1506, '15,1506,150621', 3, '达拉特旗', 'DaLaTeQi', 'DLTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150622, 1506, '15,1506,150622', 3, '准格尔旗', 'ZhunGeErQi', 'ZGEQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150623, 1506, '15,1506,150623', 3, '鄂托克前旗', 'ETuoKeQianQi', 'ETKQQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150624, 1506, '15,1506,150624', 3, '鄂托克旗', 'ETuoKeQi', 'ETKQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150625, 1506, '15,1506,150625', 3, '杭锦旗', 'HangJinQi', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150626, 1506, '15,1506,150626', 3, '乌审旗', 'WuShenQi', 'WSQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150627, 1506, '15,1506,150627', 3, '伊金霍洛旗', 'YiJinHuoLuoQi', 'YJHLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150702, 1507, '15,1507,150702', 3, '海拉尔区', 'HaiLaErQu', 'HLEQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150703, 1507, '15,1507,150703', 3, '扎赉诺尔区', 'ZhaLaiNuoErQu', 'ZLNEQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150721, 1507, '15,1507,150721', 3, '阿荣旗', 'ARongQi', 'ARQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150722, 1507, '15,1507,150722', 3, '莫力达瓦达斡尔族自治旗', 'MoLiDaWaDaWoErZuZiZhiQi', 'MLDWDWEZZZQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150723, 1507, '15,1507,150723', 3, '鄂伦春自治旗', 'ELunChunZiZhiQi', 'ELCZZQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150724, 1507, '15,1507,150724', 3, '鄂温克族自治旗', 'EWenKeZuZiZhiQi', 'EWKZZZQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150725, 1507, '15,1507,150725', 3, '陈巴尔虎旗', 'ChenBaErHuQi', 'CBEHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150726, 1507, '15,1507,150726', 3, '新巴尔虎左旗', 'XinBaErHuZuoQi', 'XBEHZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150727, 1507, '15,1507,150727', 3, '新巴尔虎右旗', 'XinBaErHuYouQi', 'XBEHYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150781, 1507, '15,1507,150781', 3, '满洲里市', 'ManZhouLiShi', 'MZLS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150782, 1507, '15,1507,150782', 3, '牙克石市', 'YaKeShiShi', 'YKSS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150783, 1507, '15,1507,150783', 3, '扎兰屯市', 'ZhaLanTunShi', 'ZLTS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150784, 1507, '15,1507,150784', 3, '额尔古纳市', 'EErGuNaShi', 'EEGNS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150785, 1507, '15,1507,150785', 3, '根河市', 'GenHeShi', 'GHS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150802, 1508, '15,1508,150802', 3, '临河区', 'LinHeQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150821, 1508, '15,1508,150821', 3, '五原县', 'WuYuanXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150822, 1508, '15,1508,150822', 3, '磴口县', 'DengKouXian', 'DKX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150823, 1508, '15,1508,150823', 3, '乌拉特前旗', 'WuLaTeQianQi', 'WLTQQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150824, 1508, '15,1508,150824', 3, '乌拉特中旗', 'WuLaTeZhongQi', 'WLTZQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150825, 1508, '15,1508,150825', 3, '乌拉特后旗', 'WuLaTeHouQi', 'WLTHQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150826, 1508, '15,1508,150826', 3, '杭锦后旗', 'HangJinHouQi', 'HJHQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150902, 1509, '15,1509,150902', 3, '集宁区', 'JiNingQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150921, 1509, '15,1509,150921', 3, '卓资县', 'ZhuoZiXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150922, 1509, '15,1509,150922', 3, '化德县', 'HuaDeXian', 'HDX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150923, 1509, '15,1509,150923', 3, '商都县', 'ShangDuXian', 'SDX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150924, 1509, '15,1509,150924', 3, '兴和县', 'XingHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150925, 1509, '15,1509,150925', 3, '凉城县', 'LiangChengXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150926, 1509, '15,1509,150926', 3, '察哈尔右翼前旗', 'ChaHaErYouYiQianQi', 'CHEYYQQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150927, 1509, '15,1509,150927', 3, '察哈尔右翼中旗', 'ChaHaErYouYiZhongQi', 'CHEYYZQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150928, 1509, '15,1509,150928', 3, '察哈尔右翼后旗', 'ChaHaErYouYiHouQi', 'CHEYYHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150929, 1509, '15,1509,150929', 3, '四子王旗', 'SiZiWangQi', 'SZWQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (150981, 1509, '15,1509,150981', 3, '丰镇市', 'FengZhenShi', 'FZS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152201, 1522, '15,1522,152201', 3, '乌兰浩特市', 'WuLanHaoTeShi', 'WLHTS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152202, 1522, '15,1522,152202', 3, '阿尔山市', 'AErShanShi', 'AESS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152221, 1522, '15,1522,152221', 3, '科尔沁右翼前旗', 'KeErQinYouYiQianQi', 'KEQYYQQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152222, 1522, '15,1522,152222', 3, '科尔沁右翼中旗', 'KeErQinYouYiZhongQi', 'KEQYYZQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152223, 1522, '15,1522,152223', 3, '扎赉特旗', 'ZhaLaiTeQi', 'ZLTQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152224, 1522, '15,1522,152224', 3, '突泉县', 'TuQuanXian', 'TQX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152501, 1525, '15,1525,152501', 3, '二连浩特市', 'ErLianHaoTeShi', 'ELHTS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152502, 1525, '15,1525,152502', 3, '锡林浩特市', 'XiLinHaoTeShi', 'XLHTS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152522, 1525, '15,1525,152522', 3, '阿巴嘎旗', 'ABaGaQi', 'ABGQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152523, 1525, '15,1525,152523', 3, '苏尼特左旗', 'SuNiTeZuoQi', 'SNTZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152524, 1525, '15,1525,152524', 3, '苏尼特右旗', 'SuNiTeYouQi', 'SNTYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152525, 1525, '15,1525,152525', 3, '东乌珠穆沁旗', 'DongWuZhuMuQinQi', 'DWZMQQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152526, 1525, '15,1525,152526', 3, '西乌珠穆沁旗', 'XiWuZhuMuQinQi', 'XWZMQQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152527, 1525, '15,1525,152527', 3, '太仆寺旗', 'TaiPuSiQi', 'TPSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152528, 1525, '15,1525,152528', 3, '镶黄旗', 'XiangHuangQi', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152529, 1525, '15,1525,152529', 3, '正镶白旗', 'ZhengXiangBaiQi', 'ZXBQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152530, 1525, '15,1525,152530', 3, '正蓝旗', 'ZhengLanQi', 'ZLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152531, 1525, '15,1525,152531', 3, '多伦县', 'DuoLunXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152571, 1525, '15,1525,152571', 3, '乌拉盖管委会', 'WuLaGaiGuanWeiHui', 'WLGGWH', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152921, 1529, '15,1529,152921', 3, '阿拉善左旗', 'ALaShanZuoQi', 'ALSZQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152922, 1529, '15,1529,152922', 3, '阿拉善右旗', 'ALaShanYouQi', 'ALSYQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152923, 1529, '15,1529,152923', 3, '额济纳旗', 'EJiNaQi', 'EJNQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (152971, 1529, '15,1529,152971', 3, '内蒙古阿拉善经济开发区', 'NeiMengGuALaShanJingJiKaiFaQu', 'NMGALSJJKFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210102, 2101, '21,2101,210102', 3, '和平区', 'HePingQu', 'HPQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210103, 2101, '21,2101,210103', 3, '沈河区', 'ShenHeQu', 'SHQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210104, 2101, '21,2101,210104', 3, '大东区', 'DaDongQu', 'DDQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210105, 2101, '21,2101,210105', 3, '皇姑区', 'HuangGuQu', 'HGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210106, 2101, '21,2101,210106', 3, '铁西区', 'TieXiQu', 'TXQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210111, 2101, '21,2101,210111', 3, '苏家屯区', 'SuJiaTunQu', 'SJTQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210112, 2101, '21,2101,210112', 3, '浑南区', 'HunNanQu', 'HNQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210113, 2101, '21,2101,210113', 3, '沈北新区', 'ShenBeiXinQu', 'SBXQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210114, 2101, '21,2101,210114', 3, '于洪区', 'YuHongQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210115, 2101, '21,2101,210115', 3, '辽中区', 'LiaoZhongQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210123, 2101, '21,2101,210123', 3, '康平县', 'KangPingXian', 'KPX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210124, 2101, '21,2101,210124', 3, '法库县', 'FaKuXian', 'FKX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210181, 2101, '21,2101,210181', 3, '新民市', 'XinMinShi', 'XMS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210202, 2102, '21,2102,210202', 3, '中山区', 'ZhongShanQu', 'ZSQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210203, 2102, '21,2102,210203', 3, '西岗区', 'XiGangQu', 'XGQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210204, 2102, '21,2102,210204', 3, '沙河口区', 'ShaHeKouQu', 'SHKQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210211, 2102, '21,2102,210211', 3, '甘井子区', 'GanJingZiQu', 'GJZQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210212, 2102, '21,2102,210212', 3, '旅顺口区', 'LyuShunKouQu', 'LSKQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210213, 2102, '21,2102,210213', 3, '金州区', 'JinZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210214, 2102, '21,2102,210214', 3, '普兰店区', 'PuLanDianQu', 'PLDQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210224, 2102, '21,2102,210224', 3, '长海县', 'ChangHaiXian', 'CHX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210281, 2102, '21,2102,210281', 3, '瓦房店市', 'WaFangDianShi', 'WFDS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210283, 2102, '21,2102,210283', 3, '庄河市', 'ZhuangHeShi', 'ZHS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210302, 2103, '21,2103,210302', 3, '铁东区', 'TieDongQu', 'TDQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210303, 2103, '21,2103,210303', 3, '铁西区', 'TieXiQu', 'TXQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210304, 2103, '21,2103,210304', 3, '立山区', 'LiShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210311, 2103, '21,2103,210311', 3, '千山区', 'QianShanQu', 'QSQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210321, 2103, '21,2103,210321', 3, '台安县', 'TaiAnXian', 'TAX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210323, 2103, '21,2103,210323', 3, '岫岩满族自治县', 'XiuYanManZuZiZhiXian', 'XYMZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210381, 2103, '21,2103,210381', 3, '海城市', 'HaiChengShi', 'HCS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210402, 2104, '21,2104,210402', 3, '新抚区', 'XinFuQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210403, 2104, '21,2104,210403', 3, '东洲区', 'DongZhouQu', 'DZQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210404, 2104, '21,2104,210404', 3, '望花区', 'WangHuaQu', 'WHQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210411, 2104, '21,2104,210411', 3, '顺城区', 'ShunChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210421, 2104, '21,2104,210421', 3, '抚顺县', 'FuShunXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210422, 2104, '21,2104,210422', 3, '新宾满族自治县', 'XinBinManZuZiZhiXian', 'XBMZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210423, 2104, '21,2104,210423', 3, '清原满族自治县', 'QingYuanManZuZiZhiXian', 'QYMZZZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210502, 2105, '21,2105,210502', 3, '平山区', 'PingShanQu', 'PSQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210503, 2105, '21,2105,210503', 3, '溪湖区', 'XiHuQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210504, 2105, '21,2105,210504', 3, '明山区', 'MingShanQu', 'MSQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210505, 2105, '21,2105,210505', 3, '南芬区', 'NanFenQu', 'NFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210521, 2105, '21,2105,210521', 3, '本溪满族自治县', 'BenXiManZuZiZhiXian', 'BXMZZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210522, 2105, '21,2105,210522', 3, '桓仁满族自治县', 'HuanRenManZuZiZhiXian', 'HRMZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210602, 2106, '21,2106,210602', 3, '元宝区', 'YuanBaoQu', 'YBQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210603, 2106, '21,2106,210603', 3, '振兴区', 'ZhenXingQu', 'ZXQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210604, 2106, '21,2106,210604', 3, '振安区', 'ZhenAnQu', 'ZAQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210624, 2106, '21,2106,210624', 3, '宽甸满族自治县', 'KuanDianManZuZiZhiXian', 'KDMZZZX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210681, 2106, '21,2106,210681', 3, '东港市', 'DongGangShi', 'DGS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210682, 2106, '21,2106,210682', 3, '凤城市', 'FengChengShi', 'FCS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210702, 2107, '21,2107,210702', 3, '古塔区', 'GuTaQu', 'GTQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210703, 2107, '21,2107,210703', 3, '凌河区', 'LingHeQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210711, 2107, '21,2107,210711', 3, '太和区', 'TaiHeQu', 'THQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210726, 2107, '21,2107,210726', 3, '黑山县', 'HeiShanXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210727, 2107, '21,2107,210727', 3, '义县', 'YiXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210781, 2107, '21,2107,210781', 3, '凌海市', 'LingHaiShi', 'LHS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210782, 2107, '21,2107,210782', 3, '北镇市', 'BeiZhenShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210802, 2108, '21,2108,210802', 3, '站前区', 'ZhanQianQu', 'ZQQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210803, 2108, '21,2108,210803', 3, '西市区', 'XiShiQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210804, 2108, '21,2108,210804', 3, '鲅鱼圈区', 'BaYuQuanQu', 'BYQQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210811, 2108, '21,2108,210811', 3, '老边区', 'LaoBianQu', 'LBQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210881, 2108, '21,2108,210881', 3, '盖州市', 'GaiZhouShi', 'GZS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210882, 2108, '21,2108,210882', 3, '大石桥市', 'DaShiQiaoShi', 'DSQS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210902, 2109, '21,2109,210902', 3, '海州区', 'HaiZhouQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210903, 2109, '21,2109,210903', 3, '新邱区', 'XinQiuQu', 'XQQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210904, 2109, '21,2109,210904', 3, '太平区', 'TaiPingQu', 'TPQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210905, 2109, '21,2109,210905', 3, '清河门区', 'QingHeMenQu', 'QHMQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210911, 2109, '21,2109,210911', 3, '细河区', 'XiHeQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210921, 2109, '21,2109,210921', 3, '阜新蒙古族自治县', 'FuXinMengGuZuZiZhiXian', 'FXMGZZZX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (210922, 2109, '21,2109,210922', 3, '彰武县', 'ZhangWuXian', 'ZWX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211002, 2110, '21,2110,211002', 3, '白塔区', 'BaiTaQu', 'BTQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211003, 2110, '21,2110,211003', 3, '文圣区', 'WenShengQu', 'WSQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211004, 2110, '21,2110,211004', 3, '宏伟区', 'HongWeiQu', 'HWQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211005, 2110, '21,2110,211005', 3, '弓长岭区', 'GongChangLingQu', 'GCLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211011, 2110, '21,2110,211011', 3, '太子河区', 'TaiZiHeQu', 'TZHQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211021, 2110, '21,2110,211021', 3, '辽阳县', 'LiaoYangXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211081, 2110, '21,2110,211081', 3, '灯塔市', 'DengTaShi', 'DTS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211102, 2111, '21,2111,211102', 3, '双台子区', 'ShuangTaiZiQu', 'STZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211103, 2111, '21,2111,211103', 3, '兴隆台区', 'XingLongTaiQu', 'XLTQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211104, 2111, '21,2111,211104', 3, '大洼区', 'DaWaQu', 'DWQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211122, 2111, '21,2111,211122', 3, '盘山县', 'PanShanXian', 'PSX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211202, 2112, '21,2112,211202', 3, '银州区', 'YinZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211204, 2112, '21,2112,211204', 3, '清河区', 'QingHeQu', 'QHQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211221, 2112, '21,2112,211221', 3, '铁岭县', 'TieLingXian', 'TLX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211223, 2112, '21,2112,211223', 3, '西丰县', 'XiFengXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211224, 2112, '21,2112,211224', 3, '昌图县', 'ChangTuXian', 'CTX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211281, 2112, '21,2112,211281', 3, '调兵山市', 'DiaoBingShanShi', 'DBSS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211282, 2112, '21,2112,211282', 3, '开原市', 'KaiYuanShi', 'KYS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211302, 2113, '21,2113,211302', 3, '双塔区', 'ShuangTaQu', 'STQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211303, 2113, '21,2113,211303', 3, '龙城区', 'LongChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211321, 2113, '21,2113,211321', 3, '朝阳县', 'ZhaoYangXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211322, 2113, '21,2113,211322', 3, '建平县', 'JianPingXian', 'JPX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211324, 2113, '21,2113,211324', 3, '喀喇沁左翼蒙古族自治县', 'KaLaQinZuoYiMengGuZuZiZhiXian', 'KLQZYMGZZZX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211381, 2113, '21,2113,211381', 3, '北票市', 'BeiPiaoShi', 'BPS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211382, 2113, '21,2113,211382', 3, '凌源市', 'LingYuanShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211402, 2114, '21,2114,211402', 3, '连山区', 'LianShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211403, 2114, '21,2114,211403', 3, '龙港区', 'LongGangQu', 'LGQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211404, 2114, '21,2114,211404', 3, '南票区', 'NanPiaoQu', 'NPQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211421, 2114, '21,2114,211421', 3, '绥中县', 'SuiZhongXian', 'SZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211422, 2114, '21,2114,211422', 3, '建昌县', 'JianChangXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (211481, 2114, '21,2114,211481', 3, '兴城市', 'XingChengShi', 'XCS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220102, 2201, '22,2201,220102', 3, '南关区', 'NanGuanQu', 'NGQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220103, 2201, '22,2201,220103', 3, '宽城区', 'KuanChengQu', 'KCQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220104, 2201, '22,2201,220104', 3, '朝阳区', 'ChaoYangQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220105, 2201, '22,2201,220105', 3, '二道区', 'ErDaoQu', 'EDQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220106, 2201, '22,2201,220106', 3, '绿园区', 'LyuYuanQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220112, 2201, '22,2201,220112', 3, '双阳区', 'ShuangYangQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220113, 2201, '22,2201,220113', 3, '九台区', 'JiuTaiQu', 'JTQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220122, 2201, '22,2201,220122', 3, '农安县', 'NongAnXian', 'NAX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220171, 2201, '22,2201,220171', 3, '长春经济技术开发区', 'ChangChunJingJiJiShuKaiFaQu', 'CCJJJSKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220172, 2201, '22,2201,220172', 3, '长春净月高新技术产业开发区', 'ChangChunJingYueGaoXinJiShuChanYeKaiFaQu', 'CCJYGXJSCYKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220173, 2201, '22,2201,220173', 3, '长春高新技术产业开发区', 'ChangChunGaoXinJiShuChanYeKaiFaQu', 'CCGXJSCYKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220174, 2201, '22,2201,220174', 3, '长春汽车经济技术开发区', 'ChangChunQiCheJingJiJiShuKaiFaQu', 'CCQCJJJSKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220182, 2201, '22,2201,220182', 3, '榆树市', 'YuShuShi', 'YSS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220183, 2201, '22,2201,220183', 3, '德惠市', 'DeHuiShi', 'DHS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220202, 2202, '22,2202,220202', 3, '昌邑区', 'ChangYiQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220203, 2202, '22,2202,220203', 3, '龙潭区', 'LongTanQu', 'LTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220204, 2202, '22,2202,220204', 3, '船营区', 'ChuanYingQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220211, 2202, '22,2202,220211', 3, '丰满区', 'FengManQu', 'FMQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220221, 2202, '22,2202,220221', 3, '永吉县', 'YongJiXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220271, 2202, '22,2202,220271', 3, '吉林经济开发区', 'JiLinJingJiKaiFaQu', 'JLJJKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220272, 2202, '22,2202,220272', 3, '吉林高新技术产业开发区', 'JiLinGaoXinJiShuChanYeKaiFaQu', 'JLGXJSCYKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220273, 2202, '22,2202,220273', 3, '吉林中国新加坡食品区', 'JiLinZhongGuoXinJiaPoShiPinQu', 'JLZGXJPSPQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220281, 2202, '22,2202,220281', 3, '蛟河市', 'JiaoHeShi', 'JHS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220282, 2202, '22,2202,220282', 3, '桦甸市', 'HuaDianShi', 'HDS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220283, 2202, '22,2202,220283', 3, '舒兰市', 'ShuLanShi', 'SLS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220284, 2202, '22,2202,220284', 3, '磐石市', 'PanShiShi', 'PSS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220302, 2203, '22,2203,220302', 3, '铁西区', 'TieXiQu', 'TXQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220303, 2203, '22,2203,220303', 3, '铁东区', 'TieDongQu', 'TDQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220322, 2203, '22,2203,220322', 3, '梨树县', 'LiShuXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220323, 2203, '22,2203,220323', 3, '伊通满族自治县', 'YiTongManZuZiZhiXian', 'YTMZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220381, 2203, '22,2203,220381', 3, '公主岭市', 'GongZhuLingShi', 'GZLS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220382, 2203, '22,2203,220382', 3, '双辽市', 'ShuangLiaoShi', 'SLS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220402, 2204, '22,2204,220402', 3, '龙山区', 'LongShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220403, 2204, '22,2204,220403', 3, '西安区', 'XiAnQu', 'XAQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220421, 2204, '22,2204,220421', 3, '东丰县', 'DongFengXian', 'DFX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220422, 2204, '22,2204,220422', 3, '东辽县', 'DongLiaoXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220502, 2205, '22,2205,220502', 3, '东昌区', 'DongChangQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220503, 2205, '22,2205,220503', 3, '二道江区', 'ErDaoJiangQu', 'EDJQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220521, 2205, '22,2205,220521', 3, '通化县', 'TongHuaXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220523, 2205, '22,2205,220523', 3, '辉南县', 'HuiNanXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220524, 2205, '22,2205,220524', 3, '柳河县', 'LiuHeXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220581, 2205, '22,2205,220581', 3, '梅河口市', 'MeiHeKouShi', 'MHKS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220582, 2205, '22,2205,220582', 3, '集安市', 'JiAnShi', 'JAS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220602, 2206, '22,2206,220602', 3, '浑江区', 'HunJiangQu', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220605, 2206, '22,2206,220605', 3, '江源区', 'JiangYuanQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220621, 2206, '22,2206,220621', 3, '抚松县', 'FuSongXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220622, 2206, '22,2206,220622', 3, '靖宇县', 'JingYuXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220623, 2206, '22,2206,220623', 3, '长白朝鲜族自治县', 'ChangBaiChaoXianZuZiZhiXian', 'CBCXZZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220681, 2206, '22,2206,220681', 3, '临江市', 'LinJiangShi', 'LJS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220702, 2207, '22,2207,220702', 3, '宁江区', 'NingJiangQu', 'NJQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220721, 2207, '22,2207,220721', 3, '前郭尔罗斯蒙古族自治县', 'QianGuoErLuoSiMengGuZuZiZhiXian', 'QGELSMGZZZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220722, 2207, '22,2207,220722', 3, '长岭县', 'ChangLingXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220723, 2207, '22,2207,220723', 3, '乾安县', 'QianAnXian', 'QAX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220771, 2207, '22,2207,220771', 3, '吉林松原经济开发区', 'JiLinSongYuanJingJiKaiFaQu', 'JLSYJJKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220781, 2207, '22,2207,220781', 3, '扶余市', 'FuYuShi', 'FYS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220802, 2208, '22,2208,220802', 3, '洮北区', 'TaoBeiQu', 'TBQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220821, 2208, '22,2208,220821', 3, '镇赉县', 'ZhenLaiXian', 'ZLX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220822, 2208, '22,2208,220822', 3, '通榆县', 'TongYuXian', 'TYX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220871, 2208, '22,2208,220871', 3, '吉林白城经济开发区', 'JiLinBaiChengJingJiKaiFaQu', 'JLBCJJKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220881, 2208, '22,2208,220881', 3, '洮南市', 'TaoNanShi', 'TNS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (220882, 2208, '22,2208,220882', 3, '大安市', 'DaAnShi', 'DAS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222401, 2224, '22,2224,222401', 3, '延吉市', 'YanJiShi', 'YJS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222402, 2224, '22,2224,222402', 3, '图们市', 'TuMenShi', 'TMS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222403, 2224, '22,2224,222403', 3, '敦化市', 'DunHuaShi', 'DHS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222404, 2224, '22,2224,222404', 3, '珲春市', 'HunChunShi', 'HCS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222405, 2224, '22,2224,222405', 3, '龙井市', 'LongJingShi', 'LJS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222406, 2224, '22,2224,222406', 3, '和龙市', 'HeLongShi', 'HLS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222424, 2224, '22,2224,222424', 3, '汪清县', 'WangQingXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (222426, 2224, '22,2224,222426', 3, '安图县', 'AnTuXian', 'ATX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230102, 2301, '23,2301,230102', 3, '道里区', 'DaoLiQu', 'DLQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230103, 2301, '23,2301,230103', 3, '南岗区', 'NanGangQu', 'NGQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230104, 2301, '23,2301,230104', 3, '道外区', 'DaoWaiQu', 'DWQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230108, 2301, '23,2301,230108', 3, '平房区', 'PingFangQu', 'PFQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230109, 2301, '23,2301,230109', 3, '松北区', 'SongBeiQu', 'SBQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230110, 2301, '23,2301,230110', 3, '香坊区', 'XiangFangQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230111, 2301, '23,2301,230111', 3, '呼兰区', 'HuLanQu', 'HLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230112, 2301, '23,2301,230112', 3, '阿城区', 'AChengQu', 'ACQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230113, 2301, '23,2301,230113', 3, '双城区', 'ShuangChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230123, 2301, '23,2301,230123', 3, '依兰县', 'YiLanXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230124, 2301, '23,2301,230124', 3, '方正县', 'FangZhengXian', 'FZX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230125, 2301, '23,2301,230125', 3, '宾县', 'BinXian', 'BX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230126, 2301, '23,2301,230126', 3, '巴彦县', 'BaYanXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230127, 2301, '23,2301,230127', 3, '木兰县', 'MuLanXian', 'MLX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230128, 2301, '23,2301,230128', 3, '通河县', 'TongHeXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230129, 2301, '23,2301,230129', 3, '延寿县', 'YanShouXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230183, 2301, '23,2301,230183', 3, '尚志市', 'ShangZhiShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230184, 2301, '23,2301,230184', 3, '五常市', 'WuChangShi', 'WCS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230202, 2302, '23,2302,230202', 3, '龙沙区', 'LongShaQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230203, 2302, '23,2302,230203', 3, '建华区', 'JianHuaQu', 'JHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230204, 2302, '23,2302,230204', 3, '铁锋区', 'TieFengQu', 'TFQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230205, 2302, '23,2302,230205', 3, '昂昂溪区', 'AngAngXiQu', 'AAXQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230206, 2302, '23,2302,230206', 3, '富拉尔基区', 'FuLaErJiQu', 'FLEJQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230207, 2302, '23,2302,230207', 3, '碾子山区', 'NianZiShanQu', 'NZSQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230208, 2302, '23,2302,230208', 3, '梅里斯达斡尔族区', 'MeiLiSiDaWoErZuQu', 'MLSDWEZQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230221, 2302, '23,2302,230221', 3, '龙江县', 'LongJiangXian', 'LJX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230223, 2302, '23,2302,230223', 3, '依安县', 'YiAnXian', 'YAX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230224, 2302, '23,2302,230224', 3, '泰来县', 'TaiLaiXian', 'TLX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230225, 2302, '23,2302,230225', 3, '甘南县', 'GanNanXian', 'GNX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230227, 2302, '23,2302,230227', 3, '富裕县', 'FuYuXian', 'FYX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230229, 2302, '23,2302,230229', 3, '克山县', 'KeShanXian', 'KSX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230230, 2302, '23,2302,230230', 3, '克东县', 'KeDongXian', 'KDX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230231, 2302, '23,2302,230231', 3, '拜泉县', 'BaiQuanXian', 'BQX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230281, 2302, '23,2302,230281', 3, '讷河市', 'NeHeShi', 'NHS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230302, 2303, '23,2303,230302', 3, '鸡冠区', 'JiGuanQu', 'JGQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230303, 2303, '23,2303,230303', 3, '恒山区', 'HengShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230304, 2303, '23,2303,230304', 3, '滴道区', 'DiDaoQu', 'DDQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230305, 2303, '23,2303,230305', 3, '梨树区', 'LiShuQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230306, 2303, '23,2303,230306', 3, '城子河区', 'ChengZiHeQu', 'CZHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230307, 2303, '23,2303,230307', 3, '麻山区', 'MaShanQu', 'MSQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230321, 2303, '23,2303,230321', 3, '鸡东县', 'JiDongXian', 'JDX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230381, 2303, '23,2303,230381', 3, '虎林市', 'HuLinShi', 'HLS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230382, 2303, '23,2303,230382', 3, '密山市', 'MiShanShi', 'MSS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230402, 2304, '23,2304,230402', 3, '向阳区', 'XiangYangQu', 'XYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230403, 2304, '23,2304,230403', 3, '工农区', 'GongNongQu', 'GNQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230404, 2304, '23,2304,230404', 3, '南山区', 'NanShanQu', 'NSQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230405, 2304, '23,2304,230405', 3, '兴安区', 'XingAnQu', 'XAQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230406, 2304, '23,2304,230406', 3, '东山区', 'DongShanQu', 'DSQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230407, 2304, '23,2304,230407', 3, '兴山区', 'XingShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230421, 2304, '23,2304,230421', 3, '萝北县', 'LuoBeiXian', 'LBX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230422, 2304, '23,2304,230422', 3, '绥滨县', 'SuiBinXian', 'SBX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230502, 2305, '23,2305,230502', 3, '尖山区', 'JianShanQu', 'JSQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230503, 2305, '23,2305,230503', 3, '岭东区', 'LingDongQu', 'LDQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230505, 2305, '23,2305,230505', 3, '四方台区', 'SiFangTaiQu', 'SFTQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230506, 2305, '23,2305,230506', 3, '宝山区', 'BaoShanQu', 'BSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230521, 2305, '23,2305,230521', 3, '集贤县', 'JiXianXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230522, 2305, '23,2305,230522', 3, '友谊县', 'YouYiXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230523, 2305, '23,2305,230523', 3, '宝清县', 'BaoQingXian', 'BQX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230524, 2305, '23,2305,230524', 3, '饶河县', 'RaoHeXian', 'RHX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230602, 2306, '23,2306,230602', 3, '萨尔图区', 'SaErTuQu', 'SETQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230603, 2306, '23,2306,230603', 3, '龙凤区', 'LongFengQu', 'LFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230604, 2306, '23,2306,230604', 3, '让胡路区', 'RangHuLuQu', 'RHLQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230605, 2306, '23,2306,230605', 3, '红岗区', 'HongGangQu', 'HGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230606, 2306, '23,2306,230606', 3, '大同区', 'DaTongQu', 'DTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230621, 2306, '23,2306,230621', 3, '肇州县', 'ZhaoZhouXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230622, 2306, '23,2306,230622', 3, '肇源县', 'ZhaoYuanXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230623, 2306, '23,2306,230623', 3, '林甸县', 'LinDianXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230624, 2306, '23,2306,230624', 3, '杜尔伯特蒙古族自治县', 'DuErBoTeMengGuZuZiZhiXian', 'DEBTMGZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230671, 2306, '23,2306,230671', 3, '大庆高新技术产业开发区', 'DaQingGaoXinJiShuChanYeKaiFaQu', 'DQGXJSCYKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230717, 2307, '23,2307,230717', 3, '伊美区', 'YiMeiQu', 'YMQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230718, 2307, '23,2307,230718', 3, '乌翠区', 'WuCuiQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230719, 2307, '23,2307,230719', 3, '友好区', 'YouHaoQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230722, 2307, '23,2307,230722', 3, '嘉荫县', 'JiaYinXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230723, 2307, '23,2307,230723', 3, '汤旺县', 'TangWangXian', 'TWX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230724, 2307, '23,2307,230724', 3, '丰林县', 'FengLinXian', 'FLX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230725, 2307, '23,2307,230725', 3, '大箐山县', 'DaQingShanXian', 'DQSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230726, 2307, '23,2307,230726', 3, '南岔县', 'NanChaXian', 'NCX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230751, 2307, '23,2307,230751', 3, '金林区', 'JinLinQu', 'JLQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230781, 2307, '23,2307,230781', 3, '铁力市', 'TieLiShi', 'TLS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230803, 2308, '23,2308,230803', 3, '向阳区', 'XiangYangQu', 'XYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230804, 2308, '23,2308,230804', 3, '前进区', 'QianJinQu', 'QJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230805, 2308, '23,2308,230805', 3, '东风区', 'DongFengQu', 'DFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230811, 2308, '23,2308,230811', 3, '郊区', 'JiaoQu', 'JQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230822, 2308, '23,2308,230822', 3, '桦南县', 'HuaNanXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230826, 2308, '23,2308,230826', 3, '桦川县', 'HuaChuanXian', 'HCX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230828, 2308, '23,2308,230828', 3, '汤原县', 'TangYuanXian', 'TYX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230881, 2308, '23,2308,230881', 3, '同江市', 'TongJiangShi', 'TJS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230882, 2308, '23,2308,230882', 3, '富锦市', 'FuJinShi', 'FJS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230883, 2308, '23,2308,230883', 3, '抚远市', 'FuYuanShi', 'FYS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230902, 2309, '23,2309,230902', 3, '新兴区', 'XinXingQu', 'XXQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230903, 2309, '23,2309,230903', 3, '桃山区', 'TaoShanQu', 'TSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230904, 2309, '23,2309,230904', 3, '茄子河区', 'QieZiHeQu', 'QZHQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (230921, 2309, '23,2309,230921', 3, '勃利县', 'BoLiXian', 'BLX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231002, 2310, '23,2310,231002', 3, '东安区', 'DongAnQu', 'DAQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231003, 2310, '23,2310,231003', 3, '阳明区', 'YangMingQu', 'YMQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231004, 2310, '23,2310,231004', 3, '爱民区', 'AiMinQu', 'AMQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231005, 2310, '23,2310,231005', 3, '西安区', 'XiAnQu', 'XAQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231025, 2310, '23,2310,231025', 3, '林口县', 'LinKouXian', 'LKX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231071, 2310, '23,2310,231071', 3, '牡丹江经济技术开发区', 'MuDanJiangJingJiJiShuKaiFaQu', 'MDJJJJSKFQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231081, 2310, '23,2310,231081', 3, '绥芬河市', 'SuiFenHeShi', 'SFHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231083, 2310, '23,2310,231083', 3, '海林市', 'HaiLinShi', 'HLS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231084, 2310, '23,2310,231084', 3, '宁安市', 'NingAnShi', 'NAS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231085, 2310, '23,2310,231085', 3, '穆棱市', 'MuLingShi', 'MLS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231086, 2310, '23,2310,231086', 3, '东宁市', 'DongNingShi', 'DNS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231102, 2311, '23,2311,231102', 3, '爱辉区', 'AiHuiQu', 'AHQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231123, 2311, '23,2311,231123', 3, '逊克县', 'XunKeXian', 'XKX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231124, 2311, '23,2311,231124', 3, '孙吴县', 'SunWuXian', 'SWX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231181, 2311, '23,2311,231181', 3, '北安市', 'BeiAnShi', 'BAS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231182, 2311, '23,2311,231182', 3, '五大连池市', 'WuDaLianChiShi', 'WDLCS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231183, 2311, '23,2311,231183', 3, '嫩江市', 'NenJiangShi', 'NJS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231202, 2312, '23,2312,231202', 3, '北林区', 'BeiLinQu', 'BLQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231221, 2312, '23,2312,231221', 3, '望奎县', 'WangKuiXian', 'WKX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231222, 2312, '23,2312,231222', 3, '兰西县', 'LanXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231223, 2312, '23,2312,231223', 3, '青冈县', 'QingGangXian', 'QGX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231224, 2312, '23,2312,231224', 3, '庆安县', 'QingAnXian', 'QAX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231225, 2312, '23,2312,231225', 3, '明水县', 'MingShuiXian', 'MSX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231226, 2312, '23,2312,231226', 3, '绥棱县', 'SuiLengXian', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231281, 2312, '23,2312,231281', 3, '安达市', 'AnDaShi', 'ADS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231282, 2312, '23,2312,231282', 3, '肇东市', 'ZhaoDongShi', 'ZDS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (231283, 2312, '23,2312,231283', 3, '海伦市', 'HaiLunShi', 'HLS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232701, 2327, '23,2327,232701', 3, '漠河市', 'MoHeShi', 'MHS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232721, 2327, '23,2327,232721', 3, '呼玛县', 'HuMaXian', 'HMX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232722, 2327, '23,2327,232722', 3, '塔河县', 'TaHeXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232761, 2327, '23,2327,232761', 3, '加格达奇区', 'JiaGeDaQiQu', 'JGDQQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232762, 2327, '23,2327,232762', 3, '松岭区', 'SongLingQu', 'SLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232763, 2327, '23,2327,232763', 3, '新林区', 'XinLinQu', 'XLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (232764, 2327, '23,2327,232764', 3, '呼中区', 'HuZhongQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310101, 3101, '31,3101,310101', 3, '黄浦区', 'HuangPuQu', 'HPQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310104, 3101, '31,3101,310104', 3, '徐汇区', 'XuHuiQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310105, 3101, '31,3101,310105', 3, '长宁区', 'ChangNingQu', 'CNQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310106, 3101, '31,3101,310106', 3, '静安区', 'JingAnQu', 'JAQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310107, 3101, '31,3101,310107', 3, '普陀区', 'PuTuoQu', 'PTQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310109, 3101, '31,3101,310109', 3, '虹口区', 'HongKouQu', 'HKQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310110, 3101, '31,3101,310110', 3, '杨浦区', 'YangPuQu', 'YPQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310112, 3101, '31,3101,310112', 3, '闵行区', 'MinHangQu', 'MHQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310113, 3101, '31,3101,310113', 3, '宝山区', 'BaoShanQu', 'BSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310114, 3101, '31,3101,310114', 3, '嘉定区', 'JiaDingQu', 'JDQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310115, 3101, '31,3101,310115', 3, '浦东新区', 'PuDongXinQu', 'PDXQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310116, 3101, '31,3101,310116', 3, '金山区', 'JinShanQu', 'JSQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310117, 3101, '31,3101,310117', 3, '松江区', 'SongJiangQu', 'SJQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310118, 3101, '31,3101,310118', 3, '青浦区', 'QingPuQu', 'QPQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310120, 3101, '31,3101,310120', 3, '奉贤区', 'FengXianQu', 'FXQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (310151, 3101, '31,3101,310151', 3, '崇明区', 'ChongMingQu', 'CMQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320102, 3201, '32,3201,320102', 3, '玄武区', 'XuanWuQu', 'XWQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320104, 3201, '32,3201,320104', 3, '秦淮区', 'QinHuaiQu', 'QHQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320105, 3201, '32,3201,320105', 3, '建邺区', 'JianYeQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320106, 3201, '32,3201,320106', 3, '鼓楼区', 'GuLouQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320111, 3201, '32,3201,320111', 3, '浦口区', 'PuKouQu', 'PKQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320113, 3201, '32,3201,320113', 3, '栖霞区', 'QiXiaQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320114, 3201, '32,3201,320114', 3, '雨花台区', 'YuHuaTaiQu', 'YHTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320115, 3201, '32,3201,320115', 3, '江宁区', 'JiangNingQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320116, 3201, '32,3201,320116', 3, '六合区', 'LuHeQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320117, 3201, '32,3201,320117', 3, '溧水区', 'LiShuiQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320118, 3201, '32,3201,320118', 3, '高淳区', 'GaoChunQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320205, 3202, '32,3202,320205', 3, '锡山区', 'XiShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320206, 3202, '32,3202,320206', 3, '惠山区', 'HuiShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320211, 3202, '32,3202,320211', 3, '滨湖区', 'BinHuQu', 'BHQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320213, 3202, '32,3202,320213', 3, '梁溪区', 'LiangXiQu', 'LXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320214, 3202, '32,3202,320214', 3, '新吴区', 'XinWuQu', 'XWQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320281, 3202, '32,3202,320281', 3, '江阴市', 'JiangYinShi', 'JYS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320282, 3202, '32,3202,320282', 3, '宜兴市', 'YiXingShi', 'YXS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320302, 3203, '32,3203,320302', 3, '鼓楼区', 'GuLouQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320303, 3203, '32,3203,320303', 3, '云龙区', 'YunLongQu', 'YLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320305, 3203, '32,3203,320305', 3, '贾汪区', 'JiaWangQu', 'JWQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320311, 3203, '32,3203,320311', 3, '泉山区', 'QuanShanQu', 'QSQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320312, 3203, '32,3203,320312', 3, '铜山区', 'TongShanQu', 'TSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320321, 3203, '32,3203,320321', 3, '丰县', 'FengXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320322, 3203, '32,3203,320322', 3, '沛县', 'PeiXian', 'PX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320324, 3203, '32,3203,320324', 3, '睢宁县', 'SuiNingXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320371, 3203, '32,3203,320371', 3, '徐州经济技术开发区', 'XuZhouJingJiJiShuKaiFaQu', 'XZJJJSKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320381, 3203, '32,3203,320381', 3, '新沂市', 'XinYiShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320382, 3203, '32,3203,320382', 3, '邳州市', 'PiZhouShi', 'PZS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320402, 3204, '32,3204,320402', 3, '天宁区', 'TianNingQu', 'TNQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320404, 3204, '32,3204,320404', 3, '钟楼区', 'ZhongLouQu', 'ZLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320411, 3204, '32,3204,320411', 3, '新北区', 'XinBeiQu', 'XBQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320412, 3204, '32,3204,320412', 3, '武进区', 'WuJinQu', 'WJQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320413, 3204, '32,3204,320413', 3, '金坛区', 'JinTanQu', 'JTQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320481, 3204, '32,3204,320481', 3, '溧阳市', 'LiYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320505, 3205, '32,3205,320505', 3, '虎丘区', 'HuQiuQu', 'HQQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320506, 3205, '32,3205,320506', 3, '吴中区', 'WuZhongQu', 'WZQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320507, 3205, '32,3205,320507', 3, '相城区', 'XiangChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320508, 3205, '32,3205,320508', 3, '姑苏区', 'GuSuQu', 'GSQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320509, 3205, '32,3205,320509', 3, '吴江区', 'WuJiangQu', 'WJQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320571, 3205, '32,3205,320571', 3, '苏州工业园区', 'SuZhouGongYeYuanQu', 'SZGYYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320581, 3205, '32,3205,320581', 3, '常熟市', 'ChangShuShi', 'CSS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320582, 3205, '32,3205,320582', 3, '张家港市', 'ZhangJiaGangShi', 'ZJGS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320583, 3205, '32,3205,320583', 3, '昆山市', 'KunShanShi', 'KSS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320585, 3205, '32,3205,320585', 3, '太仓市', 'TaiCangShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320602, 3206, '32,3206,320602', 3, '崇川区', 'ChongChuanQu', 'CCQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320611, 3206, '32,3206,320611', 3, '港闸区', 'GangZhaQu', 'GZQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320612, 3206, '32,3206,320612', 3, '通州区', 'TongZhouQu', 'TZQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320623, 3206, '32,3206,320623', 3, '如东县', 'RuDongXian', 'RDX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320671, 3206, '32,3206,320671', 3, '南通经济技术开发区', 'NanTongJingJiJiShuKaiFaQu', 'NTJJJSKFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320681, 3206, '32,3206,320681', 3, '启东市', 'QiDongShi', 'QDS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320682, 3206, '32,3206,320682', 3, '如皋市', 'RuGaoShi', 'RGS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320684, 3206, '32,3206,320684', 3, '海门市', 'HaiMenShi', 'HMS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320685, 3206, '32,3206,320685', 3, '海安市', 'HaiAnShi', 'HAS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320703, 3207, '32,3207,320703', 3, '连云区', 'LianYunQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320706, 3207, '32,3207,320706', 3, '海州区', 'HaiZhouQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320707, 3207, '32,3207,320707', 3, '赣榆区', 'GanYuQu', 'GYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320722, 3207, '32,3207,320722', 3, '东海县', 'DongHaiXian', 'DHX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320723, 3207, '32,3207,320723', 3, '灌云县', 'GuanYunXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320724, 3207, '32,3207,320724', 3, '灌南县', 'GuanNanXian', 'GNX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320771, 3207, '32,3207,320771', 3, '连云港经济技术开发区', 'LianYunGangJingJiJiShuKaiFaQu', 'LYGJJJSKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320772, 3207, '32,3207,320772', 3, '连云港高新技术产业开发区', 'LianYunGangGaoXinJiShuChanYeKaiFaQu', 'LYGGXJSCYKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320803, 3208, '32,3208,320803', 3, '淮安区', 'HuaiAnQu', 'HAQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320804, 3208, '32,3208,320804', 3, '淮阴区', 'HuaiYinQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320812, 3208, '32,3208,320812', 3, '清江浦区', 'QingJiangPuQu', 'QJPQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320813, 3208, '32,3208,320813', 3, '洪泽区', 'HongZeQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320826, 3208, '32,3208,320826', 3, '涟水县', 'LianShuiXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320830, 3208, '32,3208,320830', 3, '盱眙县', 'XuYiXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320831, 3208, '32,3208,320831', 3, '金湖县', 'JinHuXian', 'JHX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320871, 3208, '32,3208,320871', 3, '淮安经济技术开发区', 'HuaiAnJingJiJiShuKaiFaQu', 'HAJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320902, 3209, '32,3209,320902', 3, '亭湖区', 'TingHuQu', 'THQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320903, 3209, '32,3209,320903', 3, '盐都区', 'YanDuQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320904, 3209, '32,3209,320904', 3, '大丰区', 'DaFengQu', 'DFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320921, 3209, '32,3209,320921', 3, '响水县', 'XiangShuiXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320922, 3209, '32,3209,320922', 3, '滨海县', 'BinHaiXian', 'BHX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320923, 3209, '32,3209,320923', 3, '阜宁县', 'FuNingXian', 'FNX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320924, 3209, '32,3209,320924', 3, '射阳县', 'SheYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320925, 3209, '32,3209,320925', 3, '建湖县', 'JianHuXian', 'JHX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320971, 3209, '32,3209,320971', 3, '盐城经济技术开发区', 'YanChengJingJiJiShuKaiFaQu', 'YCJJJSKFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (320981, 3209, '32,3209,320981', 3, '东台市', 'DongTaiShi', 'DTS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321002, 3210, '32,3210,321002', 3, '广陵区', 'GuangLingQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321003, 3210, '32,3210,321003', 3, '邗江区', 'HanJiangQu', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321012, 3210, '32,3210,321012', 3, '江都区', 'JiangDuQu', 'JDQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321023, 3210, '32,3210,321023', 3, '宝应县', 'BaoYingXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321071, 3210, '32,3210,321071', 3, '扬州经济技术开发区', 'YangZhouJingJiJiShuKaiFaQu', 'YZJJJSKFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321081, 3210, '32,3210,321081', 3, '仪征市', 'YiZhengShi', 'YZS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321084, 3210, '32,3210,321084', 3, '高邮市', 'GaoYouShi', 'GYS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321102, 3211, '32,3211,321102', 3, '京口区', 'JingKouQu', 'JKQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321111, 3211, '32,3211,321111', 3, '润州区', 'RunZhouQu', 'RZQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321112, 3211, '32,3211,321112', 3, '丹徒区', 'DanTuQu', 'DTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321171, 3211, '32,3211,321171', 3, '镇江新区', 'ZhenJiangXinQu', 'ZJXQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321181, 3211, '32,3211,321181', 3, '丹阳市', 'DanYangShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321182, 3211, '32,3211,321182', 3, '扬中市', 'YangZhongShi', 'YZS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321183, 3211, '32,3211,321183', 3, '句容市', 'JuRongShi', 'JRS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321202, 3212, '32,3212,321202', 3, '海陵区', 'HaiLingQu', 'HLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321203, 3212, '32,3212,321203', 3, '高港区', 'GaoGangQu', 'GGQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321204, 3212, '32,3212,321204', 3, '姜堰区', 'JiangYanQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321271, 3212, '32,3212,321271', 3, '泰州医药高新技术产业开发区', 'TaiZhouYiYaoGaoXinJiShuChanYeKaiFaQu', 'TZYYGXJSCYKFQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321281, 3212, '32,3212,321281', 3, '兴化市', 'XingHuaShi', 'XHS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321282, 3212, '32,3212,321282', 3, '靖江市', 'JingJiangShi', 'JJS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321283, 3212, '32,3212,321283', 3, '泰兴市', 'TaiXingShi', 'TXS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321302, 3213, '32,3213,321302', 3, '宿城区', 'SuChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321311, 3213, '32,3213,321311', 3, '宿豫区', 'SuYuQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321322, 3213, '32,3213,321322', 3, '沭阳县', 'ShuYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321323, 3213, '32,3213,321323', 3, '泗阳县', 'SiYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321324, 3213, '32,3213,321324', 3, '泗洪县', 'SiHongXian', 'SHX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (321371, 3213, '32,3213,321371', 3, '宿迁经济技术开发区', 'SuQianJingJiJiShuKaiFaQu', 'SQJJJSKFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330102, 3301, '33,3301,330102', 3, '上城区', 'ShangChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330103, 3301, '33,3301,330103', 3, '下城区', 'XiaChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330104, 3301, '33,3301,330104', 3, '江干区', 'JiangGanQu', 'JGQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330105, 3301, '33,3301,330105', 3, '拱墅区', 'GongShuQu', 'GSQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330106, 3301, '33,3301,330106', 3, '西湖区', 'XiHuQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330108, 3301, '33,3301,330108', 3, '滨江区', 'BinJiangQu', 'BJQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330109, 3301, '33,3301,330109', 3, '萧山区', 'XiaoShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330110, 3301, '33,3301,330110', 3, '余杭区', 'YuHangQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330111, 3301, '33,3301,330111', 3, '富阳区', 'FuYangQu', 'FYQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330112, 3301, '33,3301,330112', 3, '临安区', 'LinAnQu', 'LAQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330122, 3301, '33,3301,330122', 3, '桐庐县', 'TongLuXian', 'TLX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330127, 3301, '33,3301,330127', 3, '淳安县', 'ChunAnXian', 'CAX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330182, 3301, '33,3301,330182', 3, '建德市', 'JianDeShi', 'JDS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330203, 3302, '33,3302,330203', 3, '海曙区', 'HaiShuQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330205, 3302, '33,3302,330205', 3, '江北区', 'JiangBeiQu', 'JBQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330206, 3302, '33,3302,330206', 3, '北仑区', 'BeiLunQu', 'BLQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330211, 3302, '33,3302,330211', 3, '镇海区', 'ZhenHaiQu', 'ZHQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330212, 3302, '33,3302,330212', 3, '鄞州区', 'YinZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330213, 3302, '33,3302,330213', 3, '奉化区', 'FengHuaQu', 'FHQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330225, 3302, '33,3302,330225', 3, '象山县', 'XiangShanXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330226, 3302, '33,3302,330226', 3, '宁海县', 'NingHaiXian', 'NHX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330281, 3302, '33,3302,330281', 3, '余姚市', 'YuYaoShi', 'YYS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330282, 3302, '33,3302,330282', 3, '慈溪市', 'CiXiShi', 'CXS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330302, 3303, '33,3303,330302', 3, '鹿城区', 'LuChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330303, 3303, '33,3303,330303', 3, '龙湾区', 'LongWanQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330304, 3303, '33,3303,330304', 3, '瓯海区', 'OuHaiQu', 'OHQ', 'O', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330305, 3303, '33,3303,330305', 3, '洞头区', 'DongTouQu', 'DTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330324, 3303, '33,3303,330324', 3, '永嘉县', 'YongJiaXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330326, 3303, '33,3303,330326', 3, '平阳县', 'PingYangXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330327, 3303, '33,3303,330327', 3, '苍南县', 'CangNanXian', 'CNX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330328, 3303, '33,3303,330328', 3, '文成县', 'WenChengXian', 'WCX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330329, 3303, '33,3303,330329', 3, '泰顺县', 'TaiShunXian', 'TSX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330371, 3303, '33,3303,330371', 3, '温州经济技术开发区', 'WenZhouJingJiJiShuKaiFaQu', 'WZJJJSKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330381, 3303, '33,3303,330381', 3, '瑞安市', 'RuiAnShi', 'RAS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330382, 3303, '33,3303,330382', 3, '乐清市', 'YueQingShi', 'YQS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330383, 3303, '33,3303,330383', 3, '龙港市', 'LongGangShi', 'LGS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330402, 3304, '33,3304,330402', 3, '南湖区', 'NanHuQu', 'NHQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330411, 3304, '33,3304,330411', 3, '秀洲区', 'XiuZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330421, 3304, '33,3304,330421', 3, '嘉善县', 'JiaShanXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330424, 3304, '33,3304,330424', 3, '海盐县', 'HaiYanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330481, 3304, '33,3304,330481', 3, '海宁市', 'HaiNingShi', 'HNS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330482, 3304, '33,3304,330482', 3, '平湖市', 'PingHuShi', 'PHS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330483, 3304, '33,3304,330483', 3, '桐乡市', 'TongXiangShi', 'TXS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330502, 3305, '33,3305,330502', 3, '吴兴区', 'WuXingQu', 'WXQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330503, 3305, '33,3305,330503', 3, '南浔区', 'NanXunQu', 'NXQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330521, 3305, '33,3305,330521', 3, '德清县', 'DeQingXian', 'DQX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330522, 3305, '33,3305,330522', 3, '长兴县', 'ChangXingXian', 'CXX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330523, 3305, '33,3305,330523', 3, '安吉县', 'AnJiXian', 'AJX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330602, 3306, '33,3306,330602', 3, '越城区', 'YueChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330603, 3306, '33,3306,330603', 3, '柯桥区', 'KeQiaoQu', 'KQQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330604, 3306, '33,3306,330604', 3, '上虞区', 'ShangYuQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330624, 3306, '33,3306,330624', 3, '新昌县', 'XinChangXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330681, 3306, '33,3306,330681', 3, '诸暨市', 'ZhuJiShi', 'ZJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330683, 3306, '33,3306,330683', 3, '嵊州市', 'ShengZhouShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330702, 3307, '33,3307,330702', 3, '婺城区', 'WuChengQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330703, 3307, '33,3307,330703', 3, '金东区', 'JinDongQu', 'JDQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330723, 3307, '33,3307,330723', 3, '武义县', 'WuYiXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330726, 3307, '33,3307,330726', 3, '浦江县', 'PuJiangXian', 'PJX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330727, 3307, '33,3307,330727', 3, '磐安县', 'PanAnXian', 'PAX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330781, 3307, '33,3307,330781', 3, '兰溪市', 'LanXiShi', 'LXS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330782, 3307, '33,3307,330782', 3, '义乌市', 'YiWuShi', 'YWS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330783, 3307, '33,3307,330783', 3, '东阳市', 'DongYangShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330784, 3307, '33,3307,330784', 3, '永康市', 'YongKangShi', 'YKS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330802, 3308, '33,3308,330802', 3, '柯城区', 'KeChengQu', 'KCQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330803, 3308, '33,3308,330803', 3, '衢江区', 'QuJiangQu', 'QJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330822, 3308, '33,3308,330822', 3, '常山县', 'ChangShanXian', 'CSX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330824, 3308, '33,3308,330824', 3, '开化县', 'KaiHuaXian', 'KHX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330825, 3308, '33,3308,330825', 3, '龙游县', 'LongYouXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330881, 3308, '33,3308,330881', 3, '江山市', 'JiangShanShi', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330902, 3309, '33,3309,330902', 3, '定海区', 'DingHaiQu', 'DHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330903, 3309, '33,3309,330903', 3, '普陀区', 'PuTuoQu', 'PTQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330921, 3309, '33,3309,330921', 3, '岱山县', 'DaiShanXian', 'DSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (330922, 3309, '33,3309,330922', 3, '嵊泗县', 'ShengSiXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331002, 3310, '33,3310,331002', 3, '椒江区', 'JiaoJiangQu', 'JJQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331003, 3310, '33,3310,331003', 3, '黄岩区', 'HuangYanQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331004, 3310, '33,3310,331004', 3, '路桥区', 'LuQiaoQu', 'LQQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331022, 3310, '33,3310,331022', 3, '三门县', 'SanMenXian', 'SMX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331023, 3310, '33,3310,331023', 3, '天台县', 'TianTaiXian', 'TTX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331024, 3310, '33,3310,331024', 3, '仙居县', 'XianJuXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331081, 3310, '33,3310,331081', 3, '温岭市', 'WenLingShi', 'WLS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331082, 3310, '33,3310,331082', 3, '临海市', 'LinHaiShi', 'LHS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331083, 3310, '33,3310,331083', 3, '玉环市', 'YuHuanShi', 'YHS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331102, 3311, '33,3311,331102', 3, '莲都区', 'LianDuQu', 'LDQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331121, 3311, '33,3311,331121', 3, '青田县', 'QingTianXian', 'QTX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331122, 3311, '33,3311,331122', 3, '缙云县', 'JinYunXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331123, 3311, '33,3311,331123', 3, '遂昌县', 'SuiChangXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331124, 3311, '33,3311,331124', 3, '松阳县', 'SongYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331125, 3311, '33,3311,331125', 3, '云和县', 'YunHeXian', 'YHX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331126, 3311, '33,3311,331126', 3, '庆元县', 'QingYuanXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331127, 3311, '33,3311,331127', 3, '景宁畲族自治县', 'JingNingSheZuZiZhiXian', 'JNSZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (331181, 3311, '33,3311,331181', 3, '龙泉市', 'LongQuanShi', 'LQS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340102, 3401, '34,3401,340102', 3, '瑶海区', 'YaoHaiQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340103, 3401, '34,3401,340103', 3, '庐阳区', 'LuYangQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340104, 3401, '34,3401,340104', 3, '蜀山区', 'ShuShanQu', 'SSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340111, 3401, '34,3401,340111', 3, '包河区', 'BaoHeQu', 'BHQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340121, 3401, '34,3401,340121', 3, '长丰县', 'ChangFengXian', 'CFX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340122, 3401, '34,3401,340122', 3, '肥东县', 'FeiDongXian', 'FDX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340123, 3401, '34,3401,340123', 3, '肥西县', 'FeiXiXian', 'FXX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340124, 3401, '34,3401,340124', 3, '庐江县', 'LuJiangXian', 'LJX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340171, 3401, '34,3401,340171', 3, '合肥高新技术产业开发区', 'HeFeiGaoXinJiShuChanYeKaiFaQu', 'HFGXJSCYKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340172, 3401, '34,3401,340172', 3, '合肥经济技术开发区', 'HeFeiJingJiJiShuKaiFaQu', 'HFJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340173, 3401, '34,3401,340173', 3, '合肥新站高新技术产业开发区', 'HeFeiXinZhanGaoXinJiShuChanYeKaiFaQu', 'HFXZGXJSCYKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340181, 3401, '34,3401,340181', 3, '巢湖市', 'ChaoHuShi', 'CHS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340202, 3402, '34,3402,340202', 3, '镜湖区', 'JingHuQu', 'JHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340203, 3402, '34,3402,340203', 3, '弋江区', 'YiJiangQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340207, 3402, '34,3402,340207', 3, '鸠江区', 'JiuJiangQu', 'JJQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340208, 3402, '34,3402,340208', 3, '三山区', 'SanShanQu', 'SSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340221, 3402, '34,3402,340221', 3, '芜湖县', 'WuHuXian', 'WHX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340222, 3402, '34,3402,340222', 3, '繁昌县', 'FanChangXian', 'FCX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340223, 3402, '34,3402,340223', 3, '南陵县', 'NanLingXian', 'NLX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340225, 3402, '34,3402,340225', 3, '无为县', 'WuWeiXian', 'WWX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340271, 3402, '34,3402,340271', 3, '芜湖经济技术开发区', 'WuHuJingJiJiShuKaiFaQu', 'WHJJJSKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340272, 3402, '34,3402,340272', 3, '安徽芜湖长江大桥经济开发区', 'AnHuiWuHuChangJiangDaQiaoJingJiKaiFaQu', 'AHWHCJDQJJKFQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340302, 3403, '34,3403,340302', 3, '龙子湖区', 'LongZiHuQu', 'LZHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340303, 3403, '34,3403,340303', 3, '蚌山区', 'BengShanQu', 'BSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340304, 3403, '34,3403,340304', 3, '禹会区', 'YuHuiQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340311, 3403, '34,3403,340311', 3, '淮上区', 'HuaiShangQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340321, 3403, '34,3403,340321', 3, '怀远县', 'HuaiYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340322, 3403, '34,3403,340322', 3, '五河县', 'WuHeXian', 'WHX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340323, 3403, '34,3403,340323', 3, '固镇县', 'GuZhenXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340371, 3403, '34,3403,340371', 3, '蚌埠市高新技术开发区', 'BengBuShiGaoXinJiShuKaiFaQu', 'BBSGXJSKFQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340372, 3403, '34,3403,340372', 3, '蚌埠市经济开发区', 'BengBuShiJingJiKaiFaQu', 'BBSJJKFQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340402, 3404, '34,3404,340402', 3, '大通区', 'DaTongQu', 'DTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340403, 3404, '34,3404,340403', 3, '田家庵区', 'TianJiaAnQu', 'TJAQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340404, 3404, '34,3404,340404', 3, '谢家集区', 'XieJiaJiQu', 'XJJQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340405, 3404, '34,3404,340405', 3, '八公山区', 'BaGongShanQu', 'BGSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340406, 3404, '34,3404,340406', 3, '潘集区', 'PanJiQu', 'PJQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340421, 3404, '34,3404,340421', 3, '凤台县', 'FengTaiXian', 'FTX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340422, 3404, '34,3404,340422', 3, '寿县', 'ShouXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340503, 3405, '34,3405,340503', 3, '花山区', 'HuaShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340504, 3405, '34,3405,340504', 3, '雨山区', 'YuShanQu', 'YSQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340506, 3405, '34,3405,340506', 3, '博望区', 'BoWangQu', 'BWQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340521, 3405, '34,3405,340521', 3, '当涂县', 'DangTuXian', 'DTX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340522, 3405, '34,3405,340522', 3, '含山县', 'HanShanXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340523, 3405, '34,3405,340523', 3, '和县', 'HeXian', 'HX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340602, 3406, '34,3406,340602', 3, '杜集区', 'DuJiQu', 'DJQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340603, 3406, '34,3406,340603', 3, '相山区', 'XiangShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340604, 3406, '34,3406,340604', 3, '烈山区', 'LieShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340621, 3406, '34,3406,340621', 3, '濉溪县', 'SuiXiXian', 'SXX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340705, 3407, '34,3407,340705', 3, '铜官区', 'TongGuanQu', 'TGQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340706, 3407, '34,3407,340706', 3, '义安区', 'YiAnQu', 'YAQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340711, 3407, '34,3407,340711', 3, '郊区', 'JiaoQu', 'JQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340722, 3407, '34,3407,340722', 3, '枞阳县', 'ZongYangXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340802, 3408, '34,3408,340802', 3, '迎江区', 'YingJiangQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340803, 3408, '34,3408,340803', 3, '大观区', 'DaGuanQu', 'DGQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340811, 3408, '34,3408,340811', 3, '宜秀区', 'YiXiuQu', 'YXQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340822, 3408, '34,3408,340822', 3, '怀宁县', 'HuaiNingXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340825, 3408, '34,3408,340825', 3, '太湖县', 'TaiHuXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340826, 3408, '34,3408,340826', 3, '宿松县', 'SuSongXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340827, 3408, '34,3408,340827', 3, '望江县', 'WangJiangXian', 'WJX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340828, 3408, '34,3408,340828', 3, '岳西县', 'YueXiXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340871, 3408, '34,3408,340871', 3, '安徽安庆经济开发区', 'AnHuiAnQingJingJiKaiFaQu', 'AHAQJJKFQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340881, 3408, '34,3408,340881', 3, '桐城市', 'TongChengShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (340882, 3408, '34,3408,340882', 3, '潜山市', 'QianShanShi', 'QSS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341002, 3410, '34,3410,341002', 3, '屯溪区', 'TunXiQu', 'TXQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341003, 3410, '34,3410,341003', 3, '黄山区', 'HuangShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341004, 3410, '34,3410,341004', 3, '徽州区', 'HuiZhouQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341021, 3410, '34,3410,341021', 3, '歙县', 'SheXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341022, 3410, '34,3410,341022', 3, '休宁县', 'XiuNingXian', 'XNX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341023, 3410, '34,3410,341023', 3, '黟县', 'YiXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341024, 3410, '34,3410,341024', 3, '祁门县', 'QiMenXian', 'QMX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341102, 3411, '34,3411,341102', 3, '琅琊区', 'LangYaQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341103, 3411, '34,3411,341103', 3, '南谯区', 'NanQiaoQu', 'NQQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341122, 3411, '34,3411,341122', 3, '来安县', 'LaiAnXian', 'LAX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341124, 3411, '34,3411,341124', 3, '全椒县', 'QuanJiaoXian', 'QJX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341125, 3411, '34,3411,341125', 3, '定远县', 'DingYuanXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341126, 3411, '34,3411,341126', 3, '凤阳县', 'FengYangXian', 'FYX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341171, 3411, '34,3411,341171', 3, '苏滁现代产业园', 'SuChuXianDaiChanYeYuan', 'SCXDCYY', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341172, 3411, '34,3411,341172', 3, '滁州经济技术开发区', 'ChuZhouJingJiJiShuKaiFaQu', 'CZJJJSKFQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341181, 3411, '34,3411,341181', 3, '天长市', 'TianChangShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341182, 3411, '34,3411,341182', 3, '明光市', 'MingGuangShi', 'MGS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341202, 3412, '34,3412,341202', 3, '颍州区', 'YingZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341203, 3412, '34,3412,341203', 3, '颍东区', 'YingDongQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341204, 3412, '34,3412,341204', 3, '颍泉区', 'YingQuanQu', 'YQQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341221, 3412, '34,3412,341221', 3, '临泉县', 'LinQuanXian', 'LQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341222, 3412, '34,3412,341222', 3, '太和县', 'TaiHeXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341225, 3412, '34,3412,341225', 3, '阜南县', 'FuNanXian', 'FNX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341226, 3412, '34,3412,341226', 3, '颍上县', 'YingShangXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341271, 3412, '34,3412,341271', 3, '阜阳合肥现代产业园区', 'FuYangHeFeiXianDaiChanYeYuanQu', 'FYHFXDCYYQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341272, 3412, '34,3412,341272', 3, '阜阳经济技术开发区', 'FuYangJingJiJiShuKaiFaQu', 'FYJJJSKFQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341282, 3412, '34,3412,341282', 3, '界首市', 'JieShouShi', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341302, 3413, '34,3413,341302', 3, '埇桥区', 'YongQiaoQu', 'YQQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341321, 3413, '34,3413,341321', 3, '砀山县', 'DangShanXian', 'DSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341322, 3413, '34,3413,341322', 3, '萧县', 'XiaoXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341323, 3413, '34,3413,341323', 3, '灵璧县', 'LingBiXian', 'LBX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341324, 3413, '34,3413,341324', 3, '泗县', 'SiXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341371, 3413, '34,3413,341371', 3, '宿州马鞍山现代产业园区', 'SuZhouMaAnShanXianDaiChanYeYuanQu', 'SZMASXDCYYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341372, 3413, '34,3413,341372', 3, '宿州经济技术开发区', 'SuZhouJingJiJiShuKaiFaQu', 'SZJJJSKFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341502, 3415, '34,3415,341502', 3, '金安区', 'JinAnQu', 'JAQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341503, 3415, '34,3415,341503', 3, '裕安区', 'YuAnQu', 'YAQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341504, 3415, '34,3415,341504', 3, '叶集区', 'YeJiQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341522, 3415, '34,3415,341522', 3, '霍邱县', 'HuoQiuXian', 'HQX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341523, 3415, '34,3415,341523', 3, '舒城县', 'ShuChengXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341524, 3415, '34,3415,341524', 3, '金寨县', 'JinZhaiXian', 'JZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341525, 3415, '34,3415,341525', 3, '霍山县', 'HuoShanXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341602, 3416, '34,3416,341602', 3, '谯城区', 'QiaoChengQu', 'QCQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341621, 3416, '34,3416,341621', 3, '涡阳县', 'GuoYangXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341622, 3416, '34,3416,341622', 3, '蒙城县', 'MengChengXian', 'MCX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341623, 3416, '34,3416,341623', 3, '利辛县', 'LiXinXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341702, 3417, '34,3417,341702', 3, '贵池区', 'GuiChiQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341721, 3417, '34,3417,341721', 3, '东至县', 'DongZhiXian', 'DZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341722, 3417, '34,3417,341722', 3, '石台县', 'ShiTaiXian', 'STX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341723, 3417, '34,3417,341723', 3, '青阳县', 'QingYangXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341802, 3418, '34,3418,341802', 3, '宣州区', 'XuanZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341821, 3418, '34,3418,341821', 3, '郎溪县', 'LangXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341823, 3418, '34,3418,341823', 3, '泾县', 'JingXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341824, 3418, '34,3418,341824', 3, '绩溪县', 'JiXiXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341825, 3418, '34,3418,341825', 3, '旌德县', 'JingDeXian', 'JDX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341871, 3418, '34,3418,341871', 3, '宣城市经济开发区', 'XuanChengShiJingJiKaiFaQu', 'XCSJJKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341881, 3418, '34,3418,341881', 3, '宁国市', 'NingGuoShi', 'NGS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (341882, 3418, '34,3418,341882', 3, '广德市', 'GuangDeShi', 'GDS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350102, 3501, '35,3501,350102', 3, '鼓楼区', 'GuLouQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350103, 3501, '35,3501,350103', 3, '台江区', 'TaiJiangQu', 'TJQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350104, 3501, '35,3501,350104', 3, '仓山区', 'CangShanQu', 'CSQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350105, 3501, '35,3501,350105', 3, '马尾区', 'MaWeiQu', 'MWQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350111, 3501, '35,3501,350111', 3, '晋安区', 'JinAnQu', 'JAQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350112, 3501, '35,3501,350112', 3, '长乐区', 'ChangLeQu', 'CLQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350121, 3501, '35,3501,350121', 3, '闽侯县', 'MinHouXian', 'MHX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350122, 3501, '35,3501,350122', 3, '连江县', 'LianJiangXian', 'LJX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350123, 3501, '35,3501,350123', 3, '罗源县', 'LuoYuanXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350124, 3501, '35,3501,350124', 3, '闽清县', 'MinQingXian', 'MQX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350125, 3501, '35,3501,350125', 3, '永泰县', 'YongTaiXian', 'YTX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350128, 3501, '35,3501,350128', 3, '平潭县', 'PingTanXian', 'PTX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350181, 3501, '35,3501,350181', 3, '福清市', 'FuQingShi', 'FQS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350203, 3502, '35,3502,350203', 3, '思明区', 'SiMingQu', 'SMQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350205, 3502, '35,3502,350205', 3, '海沧区', 'HaiCangQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350206, 3502, '35,3502,350206', 3, '湖里区', 'HuLiQu', 'HLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350211, 3502, '35,3502,350211', 3, '集美区', 'JiMeiQu', 'JMQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350212, 3502, '35,3502,350212', 3, '同安区', 'TongAnQu', 'TAQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350213, 3502, '35,3502,350213', 3, '翔安区', 'XiangAnQu', 'XAQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350302, 3503, '35,3503,350302', 3, '城厢区', 'ChengXiangQu', 'CXQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350303, 3503, '35,3503,350303', 3, '涵江区', 'HanJiangQu', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350304, 3503, '35,3503,350304', 3, '荔城区', 'LiChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350305, 3503, '35,3503,350305', 3, '秀屿区', 'XiuYuQu', 'XYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350322, 3503, '35,3503,350322', 3, '仙游县', 'XianYouXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350402, 3504, '35,3504,350402', 3, '梅列区', 'MeiLieQu', 'MLQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350403, 3504, '35,3504,350403', 3, '三元区', 'SanYuanQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350421, 3504, '35,3504,350421', 3, '明溪县', 'MingXiXian', 'MXX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350423, 3504, '35,3504,350423', 3, '清流县', 'QingLiuXian', 'QLX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350424, 3504, '35,3504,350424', 3, '宁化县', 'NingHuaXian', 'NHX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350425, 3504, '35,3504,350425', 3, '大田县', 'DaTianXian', 'DTX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350426, 3504, '35,3504,350426', 3, '尤溪县', 'YouXiXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350427, 3504, '35,3504,350427', 3, '沙县', 'ShaXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350428, 3504, '35,3504,350428', 3, '将乐县', 'JiangLeXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350429, 3504, '35,3504,350429', 3, '泰宁县', 'TaiNingXian', 'TNX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350430, 3504, '35,3504,350430', 3, '建宁县', 'JianNingXian', 'JNX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350481, 3504, '35,3504,350481', 3, '永安市', 'YongAnShi', 'YAS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350502, 3505, '35,3505,350502', 3, '鲤城区', 'LiChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350503, 3505, '35,3505,350503', 3, '丰泽区', 'FengZeQu', 'FZQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350504, 3505, '35,3505,350504', 3, '洛江区', 'LuoJiangQu', 'LJQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350505, 3505, '35,3505,350505', 3, '泉港区', 'QuanGangQu', 'QGQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350521, 3505, '35,3505,350521', 3, '惠安县', 'HuiAnXian', 'HAX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350524, 3505, '35,3505,350524', 3, '安溪县', 'AnXiXian', 'AXX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350525, 3505, '35,3505,350525', 3, '永春县', 'YongChunXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350526, 3505, '35,3505,350526', 3, '德化县', 'DeHuaXian', 'DHX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350527, 3505, '35,3505,350527', 3, '金门县', 'JinMenXian', 'JMX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350581, 3505, '35,3505,350581', 3, '石狮市', 'ShiShiShi', 'SSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350582, 3505, '35,3505,350582', 3, '晋江市', 'JinJiangShi', 'JJS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350583, 3505, '35,3505,350583', 3, '南安市', 'NanAnShi', 'NAS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350602, 3506, '35,3506,350602', 3, '芗城区', 'XiangChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350603, 3506, '35,3506,350603', 3, '龙文区', 'LongWenQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350622, 3506, '35,3506,350622', 3, '云霄县', 'YunXiaoXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350623, 3506, '35,3506,350623', 3, '漳浦县', 'ZhangPuXian', 'ZPX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350624, 3506, '35,3506,350624', 3, '诏安县', 'ZhaoAnXian', 'ZAX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350625, 3506, '35,3506,350625', 3, '长泰县', 'ChangTaiXian', 'CTX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350626, 3506, '35,3506,350626', 3, '东山县', 'DongShanXian', 'DSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350627, 3506, '35,3506,350627', 3, '南靖县', 'NanJingXian', 'NJX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350628, 3506, '35,3506,350628', 3, '平和县', 'PingHeXian', 'PHX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350629, 3506, '35,3506,350629', 3, '华安县', 'HuaAnXian', 'HAX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350681, 3506, '35,3506,350681', 3, '龙海市', 'LongHaiShi', 'LHS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350702, 3507, '35,3507,350702', 3, '延平区', 'YanPingQu', 'YPQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350703, 3507, '35,3507,350703', 3, '建阳区', 'JianYangQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350721, 3507, '35,3507,350721', 3, '顺昌县', 'ShunChangXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350722, 3507, '35,3507,350722', 3, '浦城县', 'PuChengXian', 'PCX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350723, 3507, '35,3507,350723', 3, '光泽县', 'GuangZeXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350724, 3507, '35,3507,350724', 3, '松溪县', 'SongXiXian', 'SXX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350725, 3507, '35,3507,350725', 3, '政和县', 'ZhengHeXian', 'ZHX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350781, 3507, '35,3507,350781', 3, '邵武市', 'ShaoWuShi', 'SWS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350782, 3507, '35,3507,350782', 3, '武夷山市', 'WuYiShanShi', 'WYSS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350783, 3507, '35,3507,350783', 3, '建瓯市', 'JianOuShi', 'JOS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350802, 3508, '35,3508,350802', 3, '新罗区', 'XinLuoQu', 'XLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350803, 3508, '35,3508,350803', 3, '永定区', 'YongDingQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350821, 3508, '35,3508,350821', 3, '长汀县', 'ChangTingXian', 'CTX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350823, 3508, '35,3508,350823', 3, '上杭县', 'ShangHangXian', 'SHX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350824, 3508, '35,3508,350824', 3, '武平县', 'WuPingXian', 'WPX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350825, 3508, '35,3508,350825', 3, '连城县', 'LianChengXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350881, 3508, '35,3508,350881', 3, '漳平市', 'ZhangPingShi', 'ZPS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350902, 3509, '35,3509,350902', 3, '蕉城区', 'JiaoChengQu', 'JCQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350921, 3509, '35,3509,350921', 3, '霞浦县', 'XiaPuXian', 'XPX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350922, 3509, '35,3509,350922', 3, '古田县', 'GuTianXian', 'GTX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350923, 3509, '35,3509,350923', 3, '屏南县', 'PingNanXian', 'PNX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350924, 3509, '35,3509,350924', 3, '寿宁县', 'ShouNingXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350925, 3509, '35,3509,350925', 3, '周宁县', 'ZhouNingXian', 'ZNX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350926, 3509, '35,3509,350926', 3, '柘荣县', 'ZheRongXian', 'ZRX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350981, 3509, '35,3509,350981', 3, '福安市', 'FuAnShi', 'FAS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (350982, 3509, '35,3509,350982', 3, '福鼎市', 'FuDingShi', 'FDS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360102, 3601, '36,3601,360102', 3, '东湖区', 'DongHuQu', 'DHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360103, 3601, '36,3601,360103', 3, '西湖区', 'XiHuQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360104, 3601, '36,3601,360104', 3, '青云谱区', 'QingYunPuQu', 'QYPQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360105, 3601, '36,3601,360105', 3, '湾里区', 'WanLiQu', 'WLQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360111, 3601, '36,3601,360111', 3, '青山湖区', 'QingShanHuQu', 'QSHQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360112, 3601, '36,3601,360112', 3, '新建区', 'XinJianQu', 'XJQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360121, 3601, '36,3601,360121', 3, '南昌县', 'NanChangXian', 'NCX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360123, 3601, '36,3601,360123', 3, '安义县', 'AnYiXian', 'AYX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360124, 3601, '36,3601,360124', 3, '进贤县', 'JinXianXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360202, 3602, '36,3602,360202', 3, '昌江区', 'ChangJiangQu', 'CJQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360203, 3602, '36,3602,360203', 3, '珠山区', 'ZhuShanQu', 'ZSQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360222, 3602, '36,3602,360222', 3, '浮梁县', 'FuLiangXian', 'FLX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360281, 3602, '36,3602,360281', 3, '乐平市', 'LePingShi', 'LPS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360302, 3603, '36,3603,360302', 3, '安源区', 'AnYuanQu', 'AYQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360313, 3603, '36,3603,360313', 3, '湘东区', 'XiangDongQu', 'XDQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360321, 3603, '36,3603,360321', 3, '莲花县', 'LianHuaXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360322, 3603, '36,3603,360322', 3, '上栗县', 'ShangLiXian', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360323, 3603, '36,3603,360323', 3, '芦溪县', 'LuXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360402, 3604, '36,3604,360402', 3, '濂溪区', 'LianXiQu', 'LXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360403, 3604, '36,3604,360403', 3, '浔阳区', 'XunYangQu', 'XYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360404, 3604, '36,3604,360404', 3, '柴桑区', 'ChaiSangQu', 'CSQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360423, 3604, '36,3604,360423', 3, '武宁县', 'WuNingXian', 'WNX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360424, 3604, '36,3604,360424', 3, '修水县', 'XiuShuiXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360425, 3604, '36,3604,360425', 3, '永修县', 'YongXiuXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360426, 3604, '36,3604,360426', 3, '德安县', 'DeAnXian', 'DAX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360428, 3604, '36,3604,360428', 3, '都昌县', 'DuChangXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360429, 3604, '36,3604,360429', 3, '湖口县', 'HuKouXian', 'HKX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360430, 3604, '36,3604,360430', 3, '彭泽县', 'PengZeXian', 'PZX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360481, 3604, '36,3604,360481', 3, '瑞昌市', 'RuiChangShi', 'RCS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360482, 3604, '36,3604,360482', 3, '共青城市', 'GongQingChengShi', 'GQCS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360483, 3604, '36,3604,360483', 3, '庐山市', 'LuShanShi', 'LSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360502, 3605, '36,3605,360502', 3, '渝水区', 'YuShuiQu', 'YSQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360521, 3605, '36,3605,360521', 3, '分宜县', 'FenYiXian', 'FYX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360602, 3606, '36,3606,360602', 3, '月湖区', 'YueHuQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360603, 3606, '36,3606,360603', 3, '余江区', 'YuJiangQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360681, 3606, '36,3606,360681', 3, '贵溪市', 'GuiXiShi', 'GXS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360702, 3607, '36,3607,360702', 3, '章贡区', 'ZhangGongQu', 'ZGQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360703, 3607, '36,3607,360703', 3, '南康区', 'NanKangQu', 'NKQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360704, 3607, '36,3607,360704', 3, '赣县区', 'GanXianQu', 'GXQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360722, 3607, '36,3607,360722', 3, '信丰县', 'XinFengXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360723, 3607, '36,3607,360723', 3, '大余县', 'DaYuXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360724, 3607, '36,3607,360724', 3, '上犹县', 'ShangYouXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360725, 3607, '36,3607,360725', 3, '崇义县', 'ChongYiXian', 'CYX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360726, 3607, '36,3607,360726', 3, '安远县', 'AnYuanXian', 'AYX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360727, 3607, '36,3607,360727', 3, '龙南县', 'LongNanXian', 'LNX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360728, 3607, '36,3607,360728', 3, '定南县', 'DingNanXian', 'DNX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360729, 3607, '36,3607,360729', 3, '全南县', 'QuanNanXian', 'QNX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360730, 3607, '36,3607,360730', 3, '宁都县', 'NingDuXian', 'NDX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360731, 3607, '36,3607,360731', 3, '于都县', 'YuDuXian', 'YDX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360732, 3607, '36,3607,360732', 3, '兴国县', 'XingGuoXian', 'XGX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360733, 3607, '36,3607,360733', 3, '会昌县', 'HuiChangXian', 'HCX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360734, 3607, '36,3607,360734', 3, '寻乌县', 'XunWuXian', 'XWX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360735, 3607, '36,3607,360735', 3, '石城县', 'ShiChengXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360781, 3607, '36,3607,360781', 3, '瑞金市', 'RuiJinShi', 'RJS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360802, 3608, '36,3608,360802', 3, '吉州区', 'JiZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360803, 3608, '36,3608,360803', 3, '青原区', 'QingYuanQu', 'QYQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360821, 3608, '36,3608,360821', 3, '吉安县', 'JiAnXian', 'JAX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360822, 3608, '36,3608,360822', 3, '吉水县', 'JiShuiXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360823, 3608, '36,3608,360823', 3, '峡江县', 'XiaJiangXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360824, 3608, '36,3608,360824', 3, '新干县', 'XinGanXian', 'XGX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360825, 3608, '36,3608,360825', 3, '永丰县', 'YongFengXian', 'YFX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360826, 3608, '36,3608,360826', 3, '泰和县', 'TaiHeXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360827, 3608, '36,3608,360827', 3, '遂川县', 'SuiChuanXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360828, 3608, '36,3608,360828', 3, '万安县', 'WanAnXian', 'WAX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360829, 3608, '36,3608,360829', 3, '安福县', 'AnFuXian', 'AFX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360830, 3608, '36,3608,360830', 3, '永新县', 'YongXinXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360881, 3608, '36,3608,360881', 3, '井冈山市', 'JingGangShanShi', 'JGSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360902, 3609, '36,3609,360902', 3, '袁州区', 'YuanZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360921, 3609, '36,3609,360921', 3, '奉新县', 'FengXinXian', 'FXX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360922, 3609, '36,3609,360922', 3, '万载县', 'WanZaiXian', 'WZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360923, 3609, '36,3609,360923', 3, '上高县', 'ShangGaoXian', 'SGX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360924, 3609, '36,3609,360924', 3, '宜丰县', 'YiFengXian', 'YFX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360925, 3609, '36,3609,360925', 3, '靖安县', 'JingAnXian', 'JAX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360926, 3609, '36,3609,360926', 3, '铜鼓县', 'TongGuXian', 'TGX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360981, 3609, '36,3609,360981', 3, '丰城市', 'FengChengShi', 'FCS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360982, 3609, '36,3609,360982', 3, '樟树市', 'ZhangShuShi', 'ZSS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (360983, 3609, '36,3609,360983', 3, '高安市', 'GaoAnShi', 'GAS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361002, 3610, '36,3610,361002', 3, '临川区', 'LinChuanQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361003, 3610, '36,3610,361003', 3, '东乡区', 'DongXiangQu', 'DXQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361021, 3610, '36,3610,361021', 3, '南城县', 'NanChengXian', 'NCX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361022, 3610, '36,3610,361022', 3, '黎川县', 'LiChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361023, 3610, '36,3610,361023', 3, '南丰县', 'NanFengXian', 'NFX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361024, 3610, '36,3610,361024', 3, '崇仁县', 'ChongRenXian', 'CRX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361025, 3610, '36,3610,361025', 3, '乐安县', 'LeAnXian', 'LAX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361026, 3610, '36,3610,361026', 3, '宜黄县', 'YiHuangXian', 'YHX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361027, 3610, '36,3610,361027', 3, '金溪县', 'JinXiXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361028, 3610, '36,3610,361028', 3, '资溪县', 'ZiXiXian', 'ZXX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361030, 3610, '36,3610,361030', 3, '广昌县', 'GuangChangXian', 'GCX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361102, 3611, '36,3611,361102', 3, '信州区', 'XinZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361103, 3611, '36,3611,361103', 3, '广丰区', 'GuangFengQu', 'GFQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361104, 3611, '36,3611,361104', 3, '广信区', 'GuangXinQu', 'GXQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361123, 3611, '36,3611,361123', 3, '玉山县', 'YuShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361124, 3611, '36,3611,361124', 3, '铅山县', 'YanShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361125, 3611, '36,3611,361125', 3, '横峰县', 'HengFengXian', 'HFX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361126, 3611, '36,3611,361126', 3, '弋阳县', 'YiYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361127, 3611, '36,3611,361127', 3, '余干县', 'YuGanXian', 'YGX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361128, 3611, '36,3611,361128', 3, '鄱阳县', 'PoYangXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361129, 3611, '36,3611,361129', 3, '万年县', 'WanNianXian', 'WNX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361130, 3611, '36,3611,361130', 3, '婺源县', 'WuYuanXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (361181, 3611, '36,3611,361181', 3, '德兴市', 'DeXingShi', 'DXS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370102, 3701, '37,3701,370102', 3, '历下区', 'LiXiaQu', 'LXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370103, 3701, '37,3701,370103', 3, '市中区', 'ShiZhongQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370104, 3701, '37,3701,370104', 3, '槐荫区', 'HuaiYinQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370105, 3701, '37,3701,370105', 3, '天桥区', 'TianQiaoQu', 'TQQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370112, 3701, '37,3701,370112', 3, '历城区', 'LiChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370113, 3701, '37,3701,370113', 3, '长清区', 'ChangQingQu', 'CQQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370114, 3701, '37,3701,370114', 3, '章丘区', 'ZhangQiuQu', 'ZQQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370115, 3701, '37,3701,370115', 3, '济阳区', 'JiYangQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370116, 3701, '37,3701,370116', 3, '莱芜区', 'LaiWuQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370117, 3701, '37,3701,370117', 3, '钢城区', 'GangChengQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370124, 3701, '37,3701,370124', 3, '平阴县', 'PingYinXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370126, 3701, '37,3701,370126', 3, '商河县', 'ShangHeXian', 'SHX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370171, 3701, '37,3701,370171', 3, '济南高新技术产业开发区', 'JiNanGaoXinJiShuChanYeKaiFaQu', 'JNGXJSCYKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370202, 3702, '37,3702,370202', 3, '市南区', 'ShiNanQu', 'SNQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370203, 3702, '37,3702,370203', 3, '市北区', 'ShiBeiQu', 'SBQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370211, 3702, '37,3702,370211', 3, '黄岛区', 'HuangDaoQu', 'HDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370212, 3702, '37,3702,370212', 3, '崂山区', 'LaoShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370213, 3702, '37,3702,370213', 3, '李沧区', 'LiCangQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370214, 3702, '37,3702,370214', 3, '城阳区', 'ChengYangQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370215, 3702, '37,3702,370215', 3, '即墨区', 'JiMoQu', 'JMQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370271, 3702, '37,3702,370271', 3, '青岛高新技术产业开发区', 'QingDaoGaoXinJiShuChanYeKaiFaQu', 'QDGXJSCYKFQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370281, 3702, '37,3702,370281', 3, '胶州市', 'JiaoZhouShi', 'JZS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370283, 3702, '37,3702,370283', 3, '平度市', 'PingDuShi', 'PDS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370285, 3702, '37,3702,370285', 3, '莱西市', 'LaiXiShi', 'LXS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370302, 3703, '37,3703,370302', 3, '淄川区', 'ZiChuanQu', 'ZCQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370303, 3703, '37,3703,370303', 3, '张店区', 'ZhangDianQu', 'ZDQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370304, 3703, '37,3703,370304', 3, '博山区', 'BoShanQu', 'BSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370305, 3703, '37,3703,370305', 3, '临淄区', 'LinZiQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370306, 3703, '37,3703,370306', 3, '周村区', 'ZhouCunQu', 'ZCQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370321, 3703, '37,3703,370321', 3, '桓台县', 'HuanTaiXian', 'HTX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370322, 3703, '37,3703,370322', 3, '高青县', 'GaoQingXian', 'GQX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370323, 3703, '37,3703,370323', 3, '沂源县', 'YiYuanXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370402, 3704, '37,3704,370402', 3, '市中区', 'ShiZhongQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370403, 3704, '37,3704,370403', 3, '薛城区', 'XueChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370404, 3704, '37,3704,370404', 3, '峄城区', 'YiChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370405, 3704, '37,3704,370405', 3, '台儿庄区', 'TaiErZhuangQu', 'TEZQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370406, 3704, '37,3704,370406', 3, '山亭区', 'ShanTingQu', 'STQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370481, 3704, '37,3704,370481', 3, '滕州市', 'TengZhouShi', 'TZS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370502, 3705, '37,3705,370502', 3, '东营区', 'DongYingQu', 'DYQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370503, 3705, '37,3705,370503', 3, '河口区', 'HeKouQu', 'HKQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370505, 3705, '37,3705,370505', 3, '垦利区', 'KenLiQu', 'KLQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370522, 3705, '37,3705,370522', 3, '利津县', 'LiJinXian', 'LJX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370523, 3705, '37,3705,370523', 3, '广饶县', 'GuangRaoXian', 'GRX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370571, 3705, '37,3705,370571', 3, '东营经济技术开发区', 'DongYingJingJiJiShuKaiFaQu', 'DYJJJSKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370572, 3705, '37,3705,370572', 3, '东营港经济开发区', 'DongYingGangJingJiKaiFaQu', 'DYGJJKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370602, 3706, '37,3706,370602', 3, '芝罘区', 'ZhiFuQu', 'ZFQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370611, 3706, '37,3706,370611', 3, '福山区', 'FuShanQu', 'FSQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370612, 3706, '37,3706,370612', 3, '牟平区', 'MuPingQu', 'MPQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370613, 3706, '37,3706,370613', 3, '莱山区', 'LaiShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370634, 3706, '37,3706,370634', 3, '长岛县', 'ChangDaoXian', 'CDX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370671, 3706, '37,3706,370671', 3, '烟台高新技术产业开发区', 'YanTaiGaoXinJiShuChanYeKaiFaQu', 'YTGXJSCYKFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370672, 3706, '37,3706,370672', 3, '烟台经济技术开发区', 'YanTaiJingJiJiShuKaiFaQu', 'YTJJJSKFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370681, 3706, '37,3706,370681', 3, '龙口市', 'LongKouShi', 'LKS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370682, 3706, '37,3706,370682', 3, '莱阳市', 'LaiYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370683, 3706, '37,3706,370683', 3, '莱州市', 'LaiZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370684, 3706, '37,3706,370684', 3, '蓬莱市', 'PengLaiShi', 'PLS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370685, 3706, '37,3706,370685', 3, '招远市', 'ZhaoYuanShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370686, 3706, '37,3706,370686', 3, '栖霞市', 'XiXiaShi', 'XXS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370687, 3706, '37,3706,370687', 3, '海阳市', 'HaiYangShi', 'HYS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370702, 3707, '37,3707,370702', 3, '潍城区', 'WeiChengQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370703, 3707, '37,3707,370703', 3, '寒亭区', 'HanTingQu', 'HTQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370704, 3707, '37,3707,370704', 3, '坊子区', 'FangZiQu', 'FZQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370705, 3707, '37,3707,370705', 3, '奎文区', 'KuiWenQu', 'KWQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370724, 3707, '37,3707,370724', 3, '临朐县', 'LinQuXian', 'LQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370725, 3707, '37,3707,370725', 3, '昌乐县', 'ChangLeXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370772, 3707, '37,3707,370772', 3, '潍坊滨海经济技术开发区', 'WeiFangBinHaiJingJiJiShuKaiFaQu', 'WFBHJJJSKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370781, 3707, '37,3707,370781', 3, '青州市', 'QingZhouShi', 'QZS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370782, 3707, '37,3707,370782', 3, '诸城市', 'ZhuChengShi', 'ZCS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370783, 3707, '37,3707,370783', 3, '寿光市', 'ShouGuangShi', 'SGS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370784, 3707, '37,3707,370784', 3, '安丘市', 'AnQiuShi', 'AQS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370785, 3707, '37,3707,370785', 3, '高密市', 'GaoMiShi', 'GMS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370786, 3707, '37,3707,370786', 3, '昌邑市', 'ChangYiShi', 'CYS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370811, 3708, '37,3708,370811', 3, '任城区', 'RenChengQu', 'RCQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370812, 3708, '37,3708,370812', 3, '兖州区', 'YanZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370826, 3708, '37,3708,370826', 3, '微山县', 'WeiShanXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370827, 3708, '37,3708,370827', 3, '鱼台县', 'YuTaiXian', 'YTX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370828, 3708, '37,3708,370828', 3, '金乡县', 'JinXiangXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370829, 3708, '37,3708,370829', 3, '嘉祥县', 'JiaXiangXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370830, 3708, '37,3708,370830', 3, '汶上县', 'WenShangXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370831, 3708, '37,3708,370831', 3, '泗水县', 'SiShuiXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370832, 3708, '37,3708,370832', 3, '梁山县', 'LiangShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370871, 3708, '37,3708,370871', 3, '济宁高新技术产业开发区', 'JiNingGaoXinJiShuChanYeKaiFaQu', 'JNGXJSCYKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370881, 3708, '37,3708,370881', 3, '曲阜市', 'QuFuShi', 'QFS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370883, 3708, '37,3708,370883', 3, '邹城市', 'ZouChengShi', 'ZCS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370902, 3709, '37,3709,370902', 3, '泰山区', 'TaiShanQu', 'TSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370911, 3709, '37,3709,370911', 3, '岱岳区', 'DaiYueQu', 'DYQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370921, 3709, '37,3709,370921', 3, '宁阳县', 'NingYangXian', 'NYX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370923, 3709, '37,3709,370923', 3, '东平县', 'DongPingXian', 'DPX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370982, 3709, '37,3709,370982', 3, '新泰市', 'XinTaiShi', 'XTS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (370983, 3709, '37,3709,370983', 3, '肥城市', 'FeiChengShi', 'FCS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371002, 3710, '37,3710,371002', 3, '环翠区', 'HuanCuiQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371003, 3710, '37,3710,371003', 3, '文登区', 'WenDengQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371071, 3710, '37,3710,371071', 3, '威海火炬高技术产业开发区', 'WeiHaiHuoJuGaoJiShuChanYeKaiFaQu', 'WHHJGJSCYKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371072, 3710, '37,3710,371072', 3, '威海经济技术开发区', 'WeiHaiJingJiJiShuKaiFaQu', 'WHJJJSKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371073, 3710, '37,3710,371073', 3, '威海临港经济技术开发区', 'WeiHaiLinGangJingJiJiShuKaiFaQu', 'WHLGJJJSKFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371082, 3710, '37,3710,371082', 3, '荣成市', 'RongChengShi', 'RCS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371083, 3710, '37,3710,371083', 3, '乳山市', 'RuShanShi', 'RSS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371102, 3711, '37,3711,371102', 3, '东港区', 'DongGangQu', 'DGQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371103, 3711, '37,3711,371103', 3, '岚山区', 'LanShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371121, 3711, '37,3711,371121', 3, '五莲县', 'WuLianXian', 'WLX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371122, 3711, '37,3711,371122', 3, '莒县', 'JuXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371171, 3711, '37,3711,371171', 3, '日照经济技术开发区', 'RiZhaoJingJiJiShuKaiFaQu', 'RZJJJSKFQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371302, 3713, '37,3713,371302', 3, '兰山区', 'LanShanQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371311, 3713, '37,3713,371311', 3, '罗庄区', 'LuoZhuangQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371312, 3713, '37,3713,371312', 3, '河东区', 'HeDongQu', 'HDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371321, 3713, '37,3713,371321', 3, '沂南县', 'YiNanXian', 'YNX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371322, 3713, '37,3713,371322', 3, '郯城县', 'TanChengXian', 'TCX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371323, 3713, '37,3713,371323', 3, '沂水县', 'YiShuiXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371324, 3713, '37,3713,371324', 3, '兰陵县', 'LanLingXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371325, 3713, '37,3713,371325', 3, '费县', 'FeiXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371326, 3713, '37,3713,371326', 3, '平邑县', 'PingYiXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371327, 3713, '37,3713,371327', 3, '莒南县', 'JuNanXian', 'JNX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371328, 3713, '37,3713,371328', 3, '蒙阴县', 'MengYinXian', 'MYX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371329, 3713, '37,3713,371329', 3, '临沭县', 'LinShuXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371371, 3713, '37,3713,371371', 3, '临沂高新技术产业开发区', 'LinYiGaoXinJiShuChanYeKaiFaQu', 'LYGXJSCYKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371372, 3713, '37,3713,371372', 3, '临沂经济技术开发区', 'LinYiJingJiJiShuKaiFaQu', 'LYJJJSKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371373, 3713, '37,3713,371373', 3, '临沂临港经济开发区', 'LinYiLinGangJingJiKaiFaQu', 'LYLGJJKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371402, 3714, '37,3714,371402', 3, '德城区', 'DeChengQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371403, 3714, '37,3714,371403', 3, '陵城区', 'LingChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371422, 3714, '37,3714,371422', 3, '宁津县', 'NingJinXian', 'NJX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371423, 3714, '37,3714,371423', 3, '庆云县', 'QingYunXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371424, 3714, '37,3714,371424', 3, '临邑县', 'LinYiXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371425, 3714, '37,3714,371425', 3, '齐河县', 'QiHeXian', 'QHX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371426, 3714, '37,3714,371426', 3, '平原县', 'PingYuanXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371427, 3714, '37,3714,371427', 3, '夏津县', 'XiaJinXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371428, 3714, '37,3714,371428', 3, '武城县', 'WuChengXian', 'WCX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371471, 3714, '37,3714,371471', 3, '德州经济技术开发区', 'DeZhouJingJiJiShuKaiFaQu', 'DZJJJSKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371472, 3714, '37,3714,371472', 3, '德州运河经济开发区', 'DeZhouYunHeJingJiKaiFaQu', 'DZYHJJKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371481, 3714, '37,3714,371481', 3, '乐陵市', 'LeLingShi', 'LLS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371482, 3714, '37,3714,371482', 3, '禹城市', 'YuChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371502, 3715, '37,3715,371502', 3, '东昌府区', 'DongChangFuQu', 'DCFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371503, 3715, '37,3715,371503', 3, '茌平区', 'ChiPingQu', 'CPQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371521, 3715, '37,3715,371521', 3, '阳谷县', 'YangGuXian', 'YGX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371522, 3715, '37,3715,371522', 3, '莘县', 'ShenXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371524, 3715, '37,3715,371524', 3, '东阿县', 'DongEXian', 'DEX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371525, 3715, '37,3715,371525', 3, '冠县', 'GuanXian', 'GX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371526, 3715, '37,3715,371526', 3, '高唐县', 'GaoTangXian', 'GTX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371581, 3715, '37,3715,371581', 3, '临清市', 'LinQingShi', 'LQS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371602, 3716, '37,3716,371602', 3, '滨城区', 'BinChengQu', 'BCQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371603, 3716, '37,3716,371603', 3, '沾化区', 'ZhanHuaQu', 'ZHQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371621, 3716, '37,3716,371621', 3, '惠民县', 'HuiMinXian', 'HMX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371622, 3716, '37,3716,371622', 3, '阳信县', 'YangXinXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371623, 3716, '37,3716,371623', 3, '无棣县', 'WuDiXian', 'WDX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371625, 3716, '37,3716,371625', 3, '博兴县', 'BoXingXian', 'BXX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371681, 3716, '37,3716,371681', 3, '邹平市', 'ZouPingShi', 'ZPS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371702, 3717, '37,3717,371702', 3, '牡丹区', 'MuDanQu', 'MDQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371703, 3717, '37,3717,371703', 3, '定陶区', 'DingTaoQu', 'DTQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371721, 3717, '37,3717,371721', 3, '曹县', 'CaoXian', 'CX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371722, 3717, '37,3717,371722', 3, '单县', 'ShanXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371723, 3717, '37,3717,371723', 3, '成武县', 'ChengWuXian', 'CWX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371724, 3717, '37,3717,371724', 3, '巨野县', 'JuYeXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371725, 3717, '37,3717,371725', 3, '郓城县', 'YunChengXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371726, 3717, '37,3717,371726', 3, '鄄城县', 'JuanChengXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371728, 3717, '37,3717,371728', 3, '东明县', 'DongMingXian', 'DMX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371771, 3717, '37,3717,371771', 3, '菏泽经济技术开发区', 'HeZeJingJiJiShuKaiFaQu', 'HZJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (371772, 3717, '37,3717,371772', 3, '菏泽高新技术开发区', 'HeZeGaoXinJiShuKaiFaQu', 'HZGXJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410102, 4101, '41,4101,410102', 3, '中原区', 'ZhongYuanQu', 'ZYQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410103, 4101, '41,4101,410103', 3, '二七区', 'ErQiQu', 'EQQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410104, 4101, '41,4101,410104', 3, '管城回族区', 'GuanChengHuiZuQu', 'GCHZQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410105, 4101, '41,4101,410105', 3, '金水区', 'JinShuiQu', 'JSQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410106, 4101, '41,4101,410106', 3, '上街区', 'ShangJieQu', 'SJQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410108, 4101, '41,4101,410108', 3, '惠济区', 'HuiJiQu', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410122, 4101, '41,4101,410122', 3, '中牟县', 'ZhongMuXian', 'ZMX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410171, 4101, '41,4101,410171', 3, '郑州经济技术开发区', 'ZhengZhouJingJiJiShuKaiFaQu', 'ZZJJJSKFQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410172, 4101, '41,4101,410172', 3, '郑州高新技术产业开发区', 'ZhengZhouGaoXinJiShuChanYeKaiFaQu', 'ZZGXJSCYKFQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410173, 4101, '41,4101,410173', 3, '郑州航空港经济综合实验区', 'ZhengZhouHangKongGangJingJiZongHeShiYanQu', 'ZZHKGJJZHSYQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410181, 4101, '41,4101,410181', 3, '巩义市', 'GongYiShi', 'GYS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410182, 4101, '41,4101,410182', 3, '荥阳市', 'XingYangShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410183, 4101, '41,4101,410183', 3, '新密市', 'XinMiShi', 'XMS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410184, 4101, '41,4101,410184', 3, '新郑市', 'XinZhengShi', 'XZS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410185, 4101, '41,4101,410185', 3, '登封市', 'DengFengShi', 'DFS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410202, 4102, '41,4102,410202', 3, '龙亭区', 'LongTingQu', 'LTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410203, 4102, '41,4102,410203', 3, '顺河回族区', 'ShunHeHuiZuQu', 'SHHZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410204, 4102, '41,4102,410204', 3, '鼓楼区', 'GuLouQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410205, 4102, '41,4102,410205', 3, '禹王台区', 'YuWangTaiQu', 'YWTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410212, 4102, '41,4102,410212', 3, '祥符区', 'XiangFuQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410221, 4102, '41,4102,410221', 3, '杞县', 'QiXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410222, 4102, '41,4102,410222', 3, '通许县', 'TongXuXian', 'TXX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410223, 4102, '41,4102,410223', 3, '尉氏县', 'WeiShiXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410225, 4102, '41,4102,410225', 3, '兰考县', 'LanKaoXian', 'LKX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410302, 4103, '41,4103,410302', 3, '老城区', 'LaoChengQu', 'LCQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410303, 4103, '41,4103,410303', 3, '西工区', 'XiGongQu', 'XGQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410304, 4103, '41,4103,410304', 3, '瀍河回族区', 'ChanHeHuiZuQu', 'CHHZQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410305, 4103, '41,4103,410305', 3, '涧西区', 'JianXiQu', 'JXQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410306, 4103, '41,4103,410306', 3, '吉利区', 'JiLiQu', 'JLQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410311, 4103, '41,4103,410311', 3, '洛龙区', 'LuoLongQu', 'LLQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410322, 4103, '41,4103,410322', 3, '孟津县', 'MengJinXian', 'MJX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410323, 4103, '41,4103,410323', 3, '新安县', 'XinAnXian', 'XAX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410324, 4103, '41,4103,410324', 3, '栾川县', 'LuanChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410325, 4103, '41,4103,410325', 3, '嵩县', 'SongXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410326, 4103, '41,4103,410326', 3, '汝阳县', 'RuYangXian', 'RYX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410327, 4103, '41,4103,410327', 3, '宜阳县', 'YiYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410328, 4103, '41,4103,410328', 3, '洛宁县', 'LuoNingXian', 'LNX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410329, 4103, '41,4103,410329', 3, '伊川县', 'YiChuanXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410371, 4103, '41,4103,410371', 3, '洛阳高新技术产业开发区', 'LuoYangGaoXinJiShuChanYeKaiFaQu', 'LYGXJSCYKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410381, 4103, '41,4103,410381', 3, '偃师市', 'YanShiShi', 'YSS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410402, 4104, '41,4104,410402', 3, '新华区', 'XinHuaQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410403, 4104, '41,4104,410403', 3, '卫东区', 'WeiDongQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410404, 4104, '41,4104,410404', 3, '石龙区', 'ShiLongQu', 'SLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410411, 4104, '41,4104,410411', 3, '湛河区', 'ZhanHeQu', 'ZHQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410421, 4104, '41,4104,410421', 3, '宝丰县', 'BaoFengXian', 'BFX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410422, 4104, '41,4104,410422', 3, '叶县', 'YeXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410423, 4104, '41,4104,410423', 3, '鲁山县', 'LuShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410425, 4104, '41,4104,410425', 3, '郏县', 'JiaXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410471, 4104, '41,4104,410471', 3, '平顶山高新技术产业开发区', 'PingDingShanGaoXinJiShuChanYeKaiFaQu', 'PDSGXJSCYKFQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410472, 4104, '41,4104,410472', 3, '平顶山市城乡一体化示范区', 'PingDingShanShiChengXiangYiTiHuaShiFanQu', 'PDSSCXYTHSFQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410481, 4104, '41,4104,410481', 3, '舞钢市', 'WuGangShi', 'WGS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410482, 4104, '41,4104,410482', 3, '汝州市', 'RuZhouShi', 'RZS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410502, 4105, '41,4105,410502', 3, '文峰区', 'WenFengQu', 'WFQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410503, 4105, '41,4105,410503', 3, '北关区', 'BeiGuanQu', 'BGQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410505, 4105, '41,4105,410505', 3, '殷都区', 'YinDuQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410506, 4105, '41,4105,410506', 3, '龙安区', 'LongAnQu', 'LAQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410522, 4105, '41,4105,410522', 3, '安阳县', 'AnYangXian', 'AYX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410523, 4105, '41,4105,410523', 3, '汤阴县', 'TangYinXian', 'TYX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410526, 4105, '41,4105,410526', 3, '滑县', 'HuaXian', 'HX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410527, 4105, '41,4105,410527', 3, '内黄县', 'NeiHuangXian', 'NHX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410571, 4105, '41,4105,410571', 3, '安阳高新技术产业开发区', 'AnYangGaoXinJiShuChanYeKaiFaQu', 'AYGXJSCYKFQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410581, 4105, '41,4105,410581', 3, '林州市', 'LinZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410602, 4106, '41,4106,410602', 3, '鹤山区', 'HeShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410603, 4106, '41,4106,410603', 3, '山城区', 'ShanChengQu', 'SCQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410611, 4106, '41,4106,410611', 3, '淇滨区', 'QiBinQu', 'QBQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410621, 4106, '41,4106,410621', 3, '浚县', 'JunXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410622, 4106, '41,4106,410622', 3, '淇县', 'QiXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410671, 4106, '41,4106,410671', 3, '鹤壁经济技术开发区', 'HeBiJingJiJiShuKaiFaQu', 'HBJJJSKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410702, 4107, '41,4107,410702', 3, '红旗区', 'HongQiQu', 'HQQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410703, 4107, '41,4107,410703', 3, '卫滨区', 'WeiBinQu', 'WBQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410704, 4107, '41,4107,410704', 3, '凤泉区', 'FengQuanQu', 'FQQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410711, 4107, '41,4107,410711', 3, '牧野区', 'MuYeQu', 'MYQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410721, 4107, '41,4107,410721', 3, '新乡县', 'XinXiangXian', 'XXX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410724, 4107, '41,4107,410724', 3, '获嘉县', 'HuoJiaXian', 'HJX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410725, 4107, '41,4107,410725', 3, '原阳县', 'YuanYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410726, 4107, '41,4107,410726', 3, '延津县', 'YanJinXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410727, 4107, '41,4107,410727', 3, '封丘县', 'FengQiuXian', 'FQX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410771, 4107, '41,4107,410771', 3, '新乡高新技术产业开发区', 'XinXiangGaoXinJiShuChanYeKaiFaQu', 'XXGXJSCYKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410772, 4107, '41,4107,410772', 3, '新乡经济技术开发区', 'XinXiangJingJiJiShuKaiFaQu', 'XXJJJSKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410773, 4107, '41,4107,410773', 3, '新乡市平原城乡一体化示范区', 'XinXiangShiPingYuanChengXiangYiTiHuaShiFanQu', 'XXSPYCXYTHSFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410781, 4107, '41,4107,410781', 3, '卫辉市', 'WeiHuiShi', 'WHS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410782, 4107, '41,4107,410782', 3, '辉县市', 'HuiXianShi', 'HXS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410783, 4107, '41,4107,410783', 3, '长垣市', 'ChangYuanShi', 'CYS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410802, 4108, '41,4108,410802', 3, '解放区', 'JieFangQu', 'JFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410803, 4108, '41,4108,410803', 3, '中站区', 'ZhongZhanQu', 'ZZQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410804, 4108, '41,4108,410804', 3, '马村区', 'MaCunQu', 'MCQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410811, 4108, '41,4108,410811', 3, '山阳区', 'ShanYangQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410821, 4108, '41,4108,410821', 3, '修武县', 'XiuWuXian', 'XWX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410822, 4108, '41,4108,410822', 3, '博爱县', 'BoAiXian', 'BAX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410823, 4108, '41,4108,410823', 3, '武陟县', 'WuZhiXian', 'WZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410825, 4108, '41,4108,410825', 3, '温县', 'WenXian', 'WX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410871, 4108, '41,4108,410871', 3, '焦作城乡一体化示范区', 'JiaoZuoChengXiangYiTiHuaShiFanQu', 'JZCXYTHSFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410882, 4108, '41,4108,410882', 3, '沁阳市', 'QinYangShi', 'QYS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410883, 4108, '41,4108,410883', 3, '孟州市', 'MengZhouShi', 'MZS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410902, 4109, '41,4109,410902', 3, '华龙区', 'HuaLongQu', 'HLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410922, 4109, '41,4109,410922', 3, '清丰县', 'QingFengXian', 'QFX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410923, 4109, '41,4109,410923', 3, '南乐县', 'NanLeXian', 'NLX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410926, 4109, '41,4109,410926', 3, '范县', 'FanXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410927, 4109, '41,4109,410927', 3, '台前县', 'TaiQianXian', 'TQX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410928, 4109, '41,4109,410928', 3, '濮阳县', 'PuYangXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410971, 4109, '41,4109,410971', 3, '河南濮阳工业园区', 'HeNanPuYangGongYeYuanQu', 'HNPYGYYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (410972, 4109, '41,4109,410972', 3, '濮阳经济技术开发区', 'PuYangJingJiJiShuKaiFaQu', 'PYJJJSKFQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411002, 4110, '41,4110,411002', 3, '魏都区', 'WeiDuQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411003, 4110, '41,4110,411003', 3, '建安区', 'JianAnQu', 'JAQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411024, 4110, '41,4110,411024', 3, '鄢陵县', 'YanLingXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411025, 4110, '41,4110,411025', 3, '襄城县', 'XiangChengXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411071, 4110, '41,4110,411071', 3, '许昌经济技术开发区', 'XuChangJingJiJiShuKaiFaQu', 'XCJJJSKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411081, 4110, '41,4110,411081', 3, '禹州市', 'YuZhouShi', 'YZS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411082, 4110, '41,4110,411082', 3, '长葛市', 'ChangGeShi', 'CGS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411102, 4111, '41,4111,411102', 3, '源汇区', 'YuanHuiQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411103, 4111, '41,4111,411103', 3, '郾城区', 'YanChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411104, 4111, '41,4111,411104', 3, '召陵区', 'ShaoLingQu', 'SLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411121, 4111, '41,4111,411121', 3, '舞阳县', 'WuYangXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411122, 4111, '41,4111,411122', 3, '临颍县', 'LinYingXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411171, 4111, '41,4111,411171', 3, '漯河经济技术开发区', 'TaHeJingJiJiShuKaiFaQu', 'THJJJSKFQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411202, 4112, '41,4112,411202', 3, '湖滨区', 'HuBinQu', 'HBQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411203, 4112, '41,4112,411203', 3, '陕州区', 'ShanZhouQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411221, 4112, '41,4112,411221', 3, '渑池县', 'MianChiXian', 'MCX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411224, 4112, '41,4112,411224', 3, '卢氏县', 'LuShiXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411271, 4112, '41,4112,411271', 3, '河南三门峡经济开发区', 'HeNanSanMenXiaJingJiKaiFaQu', 'HNSMXJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411281, 4112, '41,4112,411281', 3, '义马市', 'YiMaShi', 'YMS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411282, 4112, '41,4112,411282', 3, '灵宝市', 'LingBaoShi', 'LBS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411302, 4113, '41,4113,411302', 3, '宛城区', 'WanChengQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411303, 4113, '41,4113,411303', 3, '卧龙区', 'WoLongQu', 'WLQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411321, 4113, '41,4113,411321', 3, '南召县', 'NanZhaoXian', 'NZX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411322, 4113, '41,4113,411322', 3, '方城县', 'FangChengXian', 'FCX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411323, 4113, '41,4113,411323', 3, '西峡县', 'XiXiaXian', 'XXX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411324, 4113, '41,4113,411324', 3, '镇平县', 'ZhenPingXian', 'ZPX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411325, 4113, '41,4113,411325', 3, '内乡县', 'NeiXiangXian', 'NXX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411326, 4113, '41,4113,411326', 3, '淅川县', 'XiChuanXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411327, 4113, '41,4113,411327', 3, '社旗县', 'SheQiXian', 'SQX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411328, 4113, '41,4113,411328', 3, '唐河县', 'TangHeXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411329, 4113, '41,4113,411329', 3, '新野县', 'XinYeXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411330, 4113, '41,4113,411330', 3, '桐柏县', 'TongBaiXian', 'TBX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411371, 4113, '41,4113,411371', 3, '南阳高新技术产业开发区', 'NanYangGaoXinJiShuChanYeKaiFaQu', 'NYGXJSCYKFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411372, 4113, '41,4113,411372', 3, '南阳市城乡一体化示范区', 'NanYangShiChengXiangYiTiHuaShiFanQu', 'NYSCXYTHSFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411381, 4113, '41,4113,411381', 3, '邓州市', 'DengZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411402, 4114, '41,4114,411402', 3, '梁园区', 'LiangYuanQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411403, 4114, '41,4114,411403', 3, '睢阳区', 'SuiYangQu', 'SYQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411421, 4114, '41,4114,411421', 3, '民权县', 'MinQuanXian', 'MQX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411422, 4114, '41,4114,411422', 3, '睢县', 'SuiXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411423, 4114, '41,4114,411423', 3, '宁陵县', 'NingLingXian', 'NLX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411424, 4114, '41,4114,411424', 3, '柘城县', 'ZheChengXian', 'ZCX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411425, 4114, '41,4114,411425', 3, '虞城县', 'YuChengXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411426, 4114, '41,4114,411426', 3, '夏邑县', 'XiaYiXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411471, 4114, '41,4114,411471', 3, '豫东综合物流产业聚集区', 'YuDongZongHeWuLiuChanYeJuJiQu', 'YDZHWLCYJJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411472, 4114, '41,4114,411472', 3, '河南商丘经济开发区', 'HeNanShangQiuJingJiKaiFaQu', 'HNSQJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411481, 4114, '41,4114,411481', 3, '永城市', 'YongChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411502, 4115, '41,4115,411502', 3, '浉河区', 'ShiHeQu', 'SHQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411503, 4115, '41,4115,411503', 3, '平桥区', 'PingQiaoQu', 'PQQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411521, 4115, '41,4115,411521', 3, '罗山县', 'LuoShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411522, 4115, '41,4115,411522', 3, '光山县', 'GuangShanXian', 'GSX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411523, 4115, '41,4115,411523', 3, '新县', 'XinXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411524, 4115, '41,4115,411524', 3, '商城县', 'ShangChengXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411525, 4115, '41,4115,411525', 3, '固始县', 'GuShiXian', 'GSX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411526, 4115, '41,4115,411526', 3, '潢川县', 'HuangChuanXian', 'HCX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411527, 4115, '41,4115,411527', 3, '淮滨县', 'HuaiBinXian', 'HBX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411528, 4115, '41,4115,411528', 3, '息县', 'XiXian', 'XX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411571, 4115, '41,4115,411571', 3, '信阳高新技术产业开发区', 'XinYangGaoXinJiShuChanYeKaiFaQu', 'XYGXJSCYKFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411602, 4116, '41,4116,411602', 3, '川汇区', 'ChuanHuiQu', 'CHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411603, 4116, '41,4116,411603', 3, '淮阳区', 'HuaiYangQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411621, 4116, '41,4116,411621', 3, '扶沟县', 'FuGouXian', 'FGX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411622, 4116, '41,4116,411622', 3, '西华县', 'XiHuaXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411623, 4116, '41,4116,411623', 3, '商水县', 'ShangShuiXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411624, 4116, '41,4116,411624', 3, '沈丘县', 'ShenQiuXian', 'SQX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411625, 4116, '41,4116,411625', 3, '郸城县', 'DanChengXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411627, 4116, '41,4116,411627', 3, '太康县', 'TaiKangXian', 'TKX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411628, 4116, '41,4116,411628', 3, '鹿邑县', 'LuYiXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411671, 4116, '41,4116,411671', 3, '河南周口经济开发区', 'HeNanZhouKouJingJiKaiFaQu', 'HNZKJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411681, 4116, '41,4116,411681', 3, '项城市', 'XiangChengShi', 'XCS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411702, 4117, '41,4117,411702', 3, '驿城区', 'YiChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411721, 4117, '41,4117,411721', 3, '西平县', 'XiPingXian', 'XPX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411722, 4117, '41,4117,411722', 3, '上蔡县', 'ShangCaiXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411723, 4117, '41,4117,411723', 3, '平舆县', 'PingYuXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411724, 4117, '41,4117,411724', 3, '正阳县', 'ZhengYangXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411725, 4117, '41,4117,411725', 3, '确山县', 'QueShanXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411726, 4117, '41,4117,411726', 3, '泌阳县', 'BiYangXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411727, 4117, '41,4117,411727', 3, '汝南县', 'RuNanXian', 'RNX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411728, 4117, '41,4117,411728', 3, '遂平县', 'SuiPingXian', 'SPX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411729, 4117, '41,4117,411729', 3, '新蔡县', 'XinCaiXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (411771, 4117, '41,4117,411771', 3, '河南驻马店经济开发区', 'HeNanZhuMaDianJingJiKaiFaQu', 'HNZMDJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (419001, 41, '41,419001', 2, '济源市', 'JiYuanShi', 'JYS', 'J', '0391', '454650', '112.635599', '35.081888', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420102, 4201, '42,4201,420102', 3, '江岸区', 'JiangAnQu', 'JAQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420103, 4201, '42,4201,420103', 3, '江汉区', 'JiangHanQu', 'JHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420104, 4201, '42,4201,420104', 3, '硚口区', 'QiaoKouQu', 'QKQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420105, 4201, '42,4201,420105', 3, '汉阳区', 'HanYangQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420106, 4201, '42,4201,420106', 3, '武昌区', 'WuChangQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420107, 4201, '42,4201,420107', 3, '青山区', 'QingShanQu', 'QSQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420111, 4201, '42,4201,420111', 3, '洪山区', 'HongShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420112, 4201, '42,4201,420112', 3, '东西湖区', 'DongXiHuQu', 'DXHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420113, 4201, '42,4201,420113', 3, '汉南区', 'HanNanQu', 'HNQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420114, 4201, '42,4201,420114', 3, '蔡甸区', 'CaiDianQu', 'CDQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420115, 4201, '42,4201,420115', 3, '江夏区', 'JiangXiaQu', 'JXQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420116, 4201, '42,4201,420116', 3, '黄陂区', 'HuangPiQu', 'HPQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420117, 4201, '42,4201,420117', 3, '新洲区', 'XinZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420202, 4202, '42,4202,420202', 3, '黄石港区', 'HuangShiGangQu', 'HSGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420203, 4202, '42,4202,420203', 3, '西塞山区', 'XiSaiShanQu', 'XSSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420204, 4202, '42,4202,420204', 3, '下陆区', 'XiaLuQu', 'XLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420205, 4202, '42,4202,420205', 3, '铁山区', 'TieShanQu', 'TSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420222, 4202, '42,4202,420222', 3, '阳新县', 'YangXinXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420281, 4202, '42,4202,420281', 3, '大冶市', 'DaYeShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420302, 4203, '42,4203,420302', 3, '茅箭区', 'MaoJianQu', 'MJQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420303, 4203, '42,4203,420303', 3, '张湾区', 'ZhangWanQu', 'ZWQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420304, 4203, '42,4203,420304', 3, '郧阳区', 'YunYangQu', 'YYQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420322, 4203, '42,4203,420322', 3, '郧西县', 'YunXiXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420323, 4203, '42,4203,420323', 3, '竹山县', 'ZhuShanXian', 'ZSX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420324, 4203, '42,4203,420324', 3, '竹溪县', 'ZhuXiXian', 'ZXX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420325, 4203, '42,4203,420325', 3, '房县', 'FangXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420381, 4203, '42,4203,420381', 3, '丹江口市', 'DanJiangKouShi', 'DJKS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420502, 4205, '42,4205,420502', 3, '西陵区', 'XiLingQu', 'XLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420503, 4205, '42,4205,420503', 3, '伍家岗区', 'WuJiaGangQu', 'WJGQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420504, 4205, '42,4205,420504', 3, '点军区', 'DianJunQu', 'DJQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420505, 4205, '42,4205,420505', 3, '猇亭区', 'XiaoTingQu', 'XTQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420506, 4205, '42,4205,420506', 3, '夷陵区', 'YiLingQu', 'YLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420525, 4205, '42,4205,420525', 3, '远安县', 'YuanAnXian', 'YAX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420526, 4205, '42,4205,420526', 3, '兴山县', 'XingShanXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420527, 4205, '42,4205,420527', 3, '秭归县', 'ZiGuiXian', 'ZGX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420528, 4205, '42,4205,420528', 3, '长阳土家族自治县', 'ChangYangTuJiaZuZiZhiXian', 'CYTJZZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420529, 4205, '42,4205,420529', 3, '五峰土家族自治县', 'WuFengTuJiaZuZiZhiXian', 'WFTJZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420581, 4205, '42,4205,420581', 3, '宜都市', 'YiDuShi', 'YDS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420582, 4205, '42,4205,420582', 3, '当阳市', 'DangYangShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420583, 4205, '42,4205,420583', 3, '枝江市', 'ZhiJiangShi', 'ZJS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420602, 4206, '42,4206,420602', 3, '襄城区', 'XiangChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420606, 4206, '42,4206,420606', 3, '樊城区', 'FanChengQu', 'FCQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420607, 4206, '42,4206,420607', 3, '襄州区', 'XiangZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420624, 4206, '42,4206,420624', 3, '南漳县', 'NanZhangXian', 'NZX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420625, 4206, '42,4206,420625', 3, '谷城县', 'GuChengXian', 'GCX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420626, 4206, '42,4206,420626', 3, '保康县', 'BaoKangXian', 'BKX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420682, 4206, '42,4206,420682', 3, '老河口市', 'LaoHeKouShi', 'LHKS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420683, 4206, '42,4206,420683', 3, '枣阳市', 'ZaoYangShi', 'ZYS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420684, 4206, '42,4206,420684', 3, '宜城市', 'YiChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420702, 4207, '42,4207,420702', 3, '梁子湖区', 'LiangZiHuQu', 'LZHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420703, 4207, '42,4207,420703', 3, '华容区', 'HuaRongQu', 'HRQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420704, 4207, '42,4207,420704', 3, '鄂城区', 'EChengQu', 'ECQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420802, 4208, '42,4208,420802', 3, '东宝区', 'DongBaoQu', 'DBQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420804, 4208, '42,4208,420804', 3, '掇刀区', 'DuoDaoQu', 'DDQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420822, 4208, '42,4208,420822', 3, '沙洋县', 'ShaYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420881, 4208, '42,4208,420881', 3, '钟祥市', 'ZhongXiangShi', 'ZXS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420882, 4208, '42,4208,420882', 3, '京山市', 'JingShanShi', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420902, 4209, '42,4209,420902', 3, '孝南区', 'XiaoNanQu', 'XNQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420921, 4209, '42,4209,420921', 3, '孝昌县', 'XiaoChangXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420922, 4209, '42,4209,420922', 3, '大悟县', 'DaWuXian', 'DWX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420923, 4209, '42,4209,420923', 3, '云梦县', 'YunMengXian', 'YMX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420981, 4209, '42,4209,420981', 3, '应城市', 'YingChengShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420982, 4209, '42,4209,420982', 3, '安陆市', 'AnLuShi', 'ALS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (420984, 4209, '42,4209,420984', 3, '汉川市', 'HanChuanShi', 'HCS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421002, 4210, '42,4210,421002', 3, '沙市区', 'ShaShiQu', 'SSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421003, 4210, '42,4210,421003', 3, '荆州区', 'JingZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421022, 4210, '42,4210,421022', 3, '公安县', 'GongAnXian', 'GAX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421023, 4210, '42,4210,421023', 3, '监利县', 'JianLiXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421024, 4210, '42,4210,421024', 3, '江陵县', 'JiangLingXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421071, 4210, '42,4210,421071', 3, '荆州经济技术开发区', 'JingZhouJingJiJiShuKaiFaQu', 'JZJJJSKFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421081, 4210, '42,4210,421081', 3, '石首市', 'ShiShouShi', 'SSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421083, 4210, '42,4210,421083', 3, '洪湖市', 'HongHuShi', 'HHS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421087, 4210, '42,4210,421087', 3, '松滋市', 'SongZiShi', 'SZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421102, 4211, '42,4211,421102', 3, '黄州区', 'HuangZhouQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421121, 4211, '42,4211,421121', 3, '团风县', 'TuanFengXian', 'TFX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421122, 4211, '42,4211,421122', 3, '红安县', 'HongAnXian', 'HAX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421123, 4211, '42,4211,421123', 3, '罗田县', 'LuoTianXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421124, 4211, '42,4211,421124', 3, '英山县', 'YingShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421125, 4211, '42,4211,421125', 3, '浠水县', 'XiShuiXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421126, 4211, '42,4211,421126', 3, '蕲春县', 'QiChunXian', 'QCX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421127, 4211, '42,4211,421127', 3, '黄梅县', 'HuangMeiXian', 'HMX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421171, 4211, '42,4211,421171', 3, '龙感湖管理区', 'LongGanHuGuanLiQu', 'LGHGLQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421181, 4211, '42,4211,421181', 3, '麻城市', 'MaChengShi', 'MCS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421182, 4211, '42,4211,421182', 3, '武穴市', 'WuXueShi', 'WXS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421202, 4212, '42,4212,421202', 3, '咸安区', 'XianAnQu', 'XAQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421221, 4212, '42,4212,421221', 3, '嘉鱼县', 'JiaYuXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421222, 4212, '42,4212,421222', 3, '通城县', 'TongChengXian', 'TCX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421223, 4212, '42,4212,421223', 3, '崇阳县', 'ChongYangXian', 'CYX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421224, 4212, '42,4212,421224', 3, '通山县', 'TongShanXian', 'TSX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421281, 4212, '42,4212,421281', 3, '赤壁市', 'ChiBiShi', 'CBS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421303, 4213, '42,4213,421303', 3, '曾都区', 'ZengDuQu', 'ZDQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421321, 4213, '42,4213,421321', 3, '随县', 'SuiXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (421381, 4213, '42,4213,421381', 3, '广水市', 'GuangShuiShi', 'GSS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422801, 4228, '42,4228,422801', 3, '恩施市', 'EnShiShi', 'ESS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422802, 4228, '42,4228,422802', 3, '利川市', 'LiChuanShi', 'LCS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422822, 4228, '42,4228,422822', 3, '建始县', 'JianShiXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422823, 4228, '42,4228,422823', 3, '巴东县', 'BaDongXian', 'BDX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422825, 4228, '42,4228,422825', 3, '宣恩县', 'XuanEnXian', 'XEX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422826, 4228, '42,4228,422826', 3, '咸丰县', 'XianFengXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422827, 4228, '42,4228,422827', 3, '来凤县', 'LaiFengXian', 'LFX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (422828, 4228, '42,4228,422828', 3, '鹤峰县', 'HeFengXian', 'HFX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004, 42, '42,429004', 2, '仙桃市', 'XianTaoShi', 'XTS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005, 42, '42,429005', 2, '潜江市', 'QianJiangShi', 'QJS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006, 42, '42,429006', 2, '天门市', 'TianMenShi', 'TMS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021, 42, '42,429021', 2, '神农架林区', 'ShenNongJiaLinQu', 'SNJLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430102, 4301, '43,4301,430102', 3, '芙蓉区', 'FuRongQu', 'FRQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430103, 4301, '43,4301,430103', 3, '天心区', 'TianXinQu', 'TXQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430104, 4301, '43,4301,430104', 3, '岳麓区', 'YueLuQu', 'YLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430105, 4301, '43,4301,430105', 3, '开福区', 'KaiFuQu', 'KFQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430111, 4301, '43,4301,430111', 3, '雨花区', 'YuHuaQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430112, 4301, '43,4301,430112', 3, '望城区', 'WangChengQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430121, 4301, '43,4301,430121', 3, '长沙县', 'ChangShaXian', 'CSX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430181, 4301, '43,4301,430181', 3, '浏阳市', 'LiuYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430182, 4301, '43,4301,430182', 3, '宁乡市', 'NingXiangShi', 'NXS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430202, 4302, '43,4302,430202', 3, '荷塘区', 'HeTangQu', 'HTQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430203, 4302, '43,4302,430203', 3, '芦淞区', 'LuSongQu', 'LSQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430204, 4302, '43,4302,430204', 3, '石峰区', 'ShiFengQu', 'SFQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430211, 4302, '43,4302,430211', 3, '天元区', 'TianYuanQu', 'TYQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430212, 4302, '43,4302,430212', 3, '渌口区', 'LuKouQu', 'LKQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430223, 4302, '43,4302,430223', 3, '攸县', 'YouXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430224, 4302, '43,4302,430224', 3, '茶陵县', 'ChaLingXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430225, 4302, '43,4302,430225', 3, '炎陵县', 'YanLingXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430271, 4302, '43,4302,430271', 3, '云龙示范区', 'YunLongShiFanQu', 'YLSFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430281, 4302, '43,4302,430281', 3, '醴陵市', 'LiLingShi', 'LLS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430302, 4303, '43,4303,430302', 3, '雨湖区', 'YuHuQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430304, 4303, '43,4303,430304', 3, '岳塘区', 'YueTangQu', 'YTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430321, 4303, '43,4303,430321', 3, '湘潭县', 'XiangTanXian', 'XTX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430371, 4303, '43,4303,430371', 3, '湖南湘潭高新技术产业园区', 'HuNanXiangTanGaoXinJiShuChanYeYuanQu', 'HNXTGXJSCYYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430372, 4303, '43,4303,430372', 3, '湘潭昭山示范区', 'XiangTanZhaoShanShiFanQu', 'XTZSSFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430373, 4303, '43,4303,430373', 3, '湘潭九华示范区', 'XiangTanJiuHuaShiFanQu', 'XTJHSFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430381, 4303, '43,4303,430381', 3, '湘乡市', 'XiangXiangShi', 'XXS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430382, 4303, '43,4303,430382', 3, '韶山市', 'ShaoShanShi', 'SSS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430405, 4304, '43,4304,430405', 3, '珠晖区', 'ZhuHuiQu', 'ZHQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430406, 4304, '43,4304,430406', 3, '雁峰区', 'YanFengQu', 'YFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430407, 4304, '43,4304,430407', 3, '石鼓区', 'DanGuQu', 'DGQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430408, 4304, '43,4304,430408', 3, '蒸湘区', 'ZhengXiangQu', 'ZXQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430412, 4304, '43,4304,430412', 3, '南岳区', 'NanYueQu', 'NYQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430421, 4304, '43,4304,430421', 3, '衡阳县', 'HengYangXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430422, 4304, '43,4304,430422', 3, '衡南县', 'HengNanXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430423, 4304, '43,4304,430423', 3, '衡山县', 'HengShanXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430424, 4304, '43,4304,430424', 3, '衡东县', 'HengDongXian', 'HDX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430426, 4304, '43,4304,430426', 3, '祁东县', 'QiDongXian', 'QDX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430471, 4304, '43,4304,430471', 3, '衡阳综合保税区', 'HengYangZongHeBaoShuiQu', 'HYZHBSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430472, 4304, '43,4304,430472', 3, '湖南衡阳高新技术产业园区', 'HuNanHengYangGaoXinJiShuChanYeYuanQu', 'HNHYGXJSCYYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430473, 4304, '43,4304,430473', 3, '湖南衡阳松木经济开发区', 'HuNanHengYangSongMuJingJiKaiFaQu', 'HNHYSMJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430481, 4304, '43,4304,430481', 3, '耒阳市', 'LeiYangShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430482, 4304, '43,4304,430482', 3, '常宁市', 'ChangNingShi', 'CNS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430502, 4305, '43,4305,430502', 3, '双清区', 'ShuangQingQu', 'SQQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430503, 4305, '43,4305,430503', 3, '大祥区', 'DaXiangQu', 'DXQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430511, 4305, '43,4305,430511', 3, '北塔区', 'BeiTaQu', 'BTQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430522, 4305, '43,4305,430522', 3, '新邵县', 'XinShaoXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430523, 4305, '43,4305,430523', 3, '邵阳县', 'ShaoYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430524, 4305, '43,4305,430524', 3, '隆回县', 'LongHuiXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430525, 4305, '43,4305,430525', 3, '洞口县', 'DongKouXian', 'DKX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430527, 4305, '43,4305,430527', 3, '绥宁县', 'SuiNingXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430528, 4305, '43,4305,430528', 3, '新宁县', 'XinNingXian', 'XNX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430529, 4305, '43,4305,430529', 3, '城步苗族自治县', 'ChengBuMiaoZuZiZhiXian', 'CBMZZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430581, 4305, '43,4305,430581', 3, '武冈市', 'WuGangShi', 'WGS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430582, 4305, '43,4305,430582', 3, '邵东市', 'ShaoDongShi', 'SDS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430602, 4306, '43,4306,430602', 3, '岳阳楼区', 'YueYangLouQu', 'YYLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430603, 4306, '43,4306,430603', 3, '云溪区', 'YunXiQu', 'YXQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430611, 4306, '43,4306,430611', 3, '君山区', 'JunShanQu', 'JSQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430621, 4306, '43,4306,430621', 3, '岳阳县', 'YueYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430623, 4306, '43,4306,430623', 3, '华容县', 'HuaRongXian', 'HRX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430624, 4306, '43,4306,430624', 3, '湘阴县', 'XiangYinXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430626, 4306, '43,4306,430626', 3, '平江县', 'PingJiangXian', 'PJX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430671, 4306, '43,4306,430671', 3, '岳阳市屈原管理区', 'YueYangShiQuYuanGuanLiQu', 'YYSQYGLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430681, 4306, '43,4306,430681', 3, '汨罗市', 'MiLuoShi', 'MLS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430682, 4306, '43,4306,430682', 3, '临湘市', 'LinXiangShi', 'LXS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430702, 4307, '43,4307,430702', 3, '武陵区', 'WuLingQu', 'WLQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430703, 4307, '43,4307,430703', 3, '鼎城区', 'DingChengQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430721, 4307, '43,4307,430721', 3, '安乡县', 'AnXiangXian', 'AXX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430722, 4307, '43,4307,430722', 3, '汉寿县', 'HanShouXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430723, 4307, '43,4307,430723', 3, '澧县', 'LiXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430724, 4307, '43,4307,430724', 3, '临澧县', 'LinLiXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430725, 4307, '43,4307,430725', 3, '桃源县', 'TaoYuanXian', 'TYX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430726, 4307, '43,4307,430726', 3, '石门县', 'ShiMenXian', 'SMX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430771, 4307, '43,4307,430771', 3, '常德市西洞庭管理区', 'ChangDeShiXiDongTingGuanLiQu', 'CDSXDTGLQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430781, 4307, '43,4307,430781', 3, '津市市', 'JinShiShi', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430802, 4308, '43,4308,430802', 3, '永定区', 'YongDingQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430811, 4308, '43,4308,430811', 3, '武陵源区', 'WuLingYuanQu', 'WLYQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430821, 4308, '43,4308,430821', 3, '慈利县', 'CiLiXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430822, 4308, '43,4308,430822', 3, '桑植县', 'SangZhiXian', 'SZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430902, 4309, '43,4309,430902', 3, '资阳区', 'ZiYangQu', 'ZYQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430903, 4309, '43,4309,430903', 3, '赫山区', 'HeShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430921, 4309, '43,4309,430921', 3, '南县', 'NanXian', 'NX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430922, 4309, '43,4309,430922', 3, '桃江县', 'TaoJiangXian', 'TJX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430923, 4309, '43,4309,430923', 3, '安化县', 'AnHuaXian', 'AHX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430971, 4309, '43,4309,430971', 3, '益阳市大通湖管理区', 'YiYangShiDaTongHuGuanLiQu', 'YYSDTHGLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430972, 4309, '43,4309,430972', 3, '湖南益阳高新技术产业园区', 'HuNanYiYangGaoXinJiShuChanYeYuanQu', 'HNYYGXJSCYYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (430981, 4309, '43,4309,430981', 3, '沅江市', 'YuanJiangShi', 'YJS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431002, 4310, '43,4310,431002', 3, '北湖区', 'BeiHuQu', 'BHQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431003, 4310, '43,4310,431003', 3, '苏仙区', 'SuXianQu', 'SXQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431021, 4310, '43,4310,431021', 3, '桂阳县', 'GuiYangXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431022, 4310, '43,4310,431022', 3, '宜章县', 'YiZhangXian', 'YZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431023, 4310, '43,4310,431023', 3, '永兴县', 'YongXingXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431024, 4310, '43,4310,431024', 3, '嘉禾县', 'JiaHeXian', 'JHX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431025, 4310, '43,4310,431025', 3, '临武县', 'LinWuXian', 'LWX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431026, 4310, '43,4310,431026', 3, '汝城县', 'RuChengXian', 'RCX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431027, 4310, '43,4310,431027', 3, '桂东县', 'GuiDongXian', 'GDX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431028, 4310, '43,4310,431028', 3, '安仁县', 'AnRenXian', 'ARX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431081, 4310, '43,4310,431081', 3, '资兴市', 'ZiXingShi', 'ZXS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431102, 4311, '43,4311,431102', 3, '零陵区', 'LingLingQu', 'LLQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431103, 4311, '43,4311,431103', 3, '冷水滩区', 'LengShuiTanQu', 'LSTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431121, 4311, '43,4311,431121', 3, '祁阳县', 'QiYangXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431122, 4311, '43,4311,431122', 3, '东安县', 'DongAnXian', 'DAX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431123, 4311, '43,4311,431123', 3, '双牌县', 'ShuangPaiXian', 'SPX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431124, 4311, '43,4311,431124', 3, '道县', 'DaoXian', 'DX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431125, 4311, '43,4311,431125', 3, '江永县', 'JiangYongXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431126, 4311, '43,4311,431126', 3, '宁远县', 'NingYuanXian', 'NYX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431127, 4311, '43,4311,431127', 3, '蓝山县', 'LanShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431128, 4311, '43,4311,431128', 3, '新田县', 'XinTianXian', 'XTX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431129, 4311, '43,4311,431129', 3, '江华瑶族自治县', 'JiangHuaYaoZuZiZhiXian', 'JHYZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431171, 4311, '43,4311,431171', 3, '永州经济技术开发区', 'YongZhouJingJiJiShuKaiFaQu', 'YZJJJSKFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431172, 4311, '43,4311,431172', 3, '永州市金洞管理区', 'YongZhouShiJinDongGuanLiQu', 'YZSJDGLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431173, 4311, '43,4311,431173', 3, '永州市回龙圩管理区', 'YongZhouShiHuiLongWeiGuanLiQu', 'YZSHLWGLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431202, 4312, '43,4312,431202', 3, '鹤城区', 'HeChengQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431221, 4312, '43,4312,431221', 3, '中方县', 'ZhongFangXian', 'ZFX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431222, 4312, '43,4312,431222', 3, '沅陵县', 'YuanLingXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431223, 4312, '43,4312,431223', 3, '辰溪县', 'ChenXiXian', 'CXX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431224, 4312, '43,4312,431224', 3, '溆浦县', 'XuPuXian', 'XPX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431225, 4312, '43,4312,431225', 3, '会同县', 'HuiTongXian', 'HTX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431226, 4312, '43,4312,431226', 3, '麻阳苗族自治县', 'MaYangMiaoZuZiZhiXian', 'MYMZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431227, 4312, '43,4312,431227', 3, '新晃侗族自治县', 'XinHuangDongZuZiZhiXian', 'XHDZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431228, 4312, '43,4312,431228', 3, '芷江侗族自治县', 'ZhiJiangDongZuZiZhiXian', 'ZJDZZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431229, 4312, '43,4312,431229', 3, '靖州苗族侗族自治县', 'JingZhouMiaoZuDongZuZiZhiXian', 'JZMZDZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431230, 4312, '43,4312,431230', 3, '通道侗族自治县', 'TongDaoDongZuZiZhiXian', 'TDDZZZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431271, 4312, '43,4312,431271', 3, '怀化市洪江管理区', 'HuaiHuaShiHongJiangGuanLiQu', 'HHSHJGLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431281, 4312, '43,4312,431281', 3, '洪江市', 'HongJiangShi', 'HJS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431302, 4313, '43,4313,431302', 3, '娄星区', 'LouXingQu', 'LXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431321, 4313, '43,4313,431321', 3, '双峰县', 'ShuangFengXian', 'SFX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431322, 4313, '43,4313,431322', 3, '新化县', 'XinHuaXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431381, 4313, '43,4313,431381', 3, '冷水江市', 'LengShuiJiangShi', 'LSJS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (431382, 4313, '43,4313,431382', 3, '涟源市', 'LianYuanShi', 'LYS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433101, 4331, '43,4331,433101', 3, '吉首市', 'JiShouShi', 'JSS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433122, 4331, '43,4331,433122', 3, '泸溪县', 'LuXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433123, 4331, '43,4331,433123', 3, '凤凰县', 'FengHuangXian', 'FHX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433124, 4331, '43,4331,433124', 3, '花垣县', 'HuaYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433125, 4331, '43,4331,433125', 3, '保靖县', 'BaoJingXian', 'BJX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433126, 4331, '43,4331,433126', 3, '古丈县', 'GuZhangXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433127, 4331, '43,4331,433127', 3, '永顺县', 'YongShunXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433130, 4331, '43,4331,433130', 3, '龙山县', 'LongShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (433173, 4331, '43,4331,433173', 3, '湖南永顺经济开发区', 'HuNanYongShunJingJiKaiFaQu', 'HNYSJJKFQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440103, 4401, '44,4401,440103', 3, '荔湾区', 'LiWanQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440104, 4401, '44,4401,440104', 3, '越秀区', 'YueXiuQu', 'YXQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440105, 4401, '44,4401,440105', 3, '海珠区', 'HaiZhuQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440106, 4401, '44,4401,440106', 3, '天河区', 'TianHeQu', 'THQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440111, 4401, '44,4401,440111', 3, '白云区', 'BaiYunQu', 'BYQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440112, 4401, '44,4401,440112', 3, '黄埔区', 'HuangPuQu', 'HPQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440113, 4401, '44,4401,440113', 3, '番禺区', 'PanYuQu', 'PYQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440114, 4401, '44,4401,440114', 3, '花都区', 'HuaDuQu', 'HDQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440115, 4401, '44,4401,440115', 3, '南沙区', 'NanShaQu', 'NSQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440117, 4401, '44,4401,440117', 3, '从化区', 'CongHuaQu', 'CHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440118, 4401, '44,4401,440118', 3, '增城区', 'ZengChengQu', 'ZCQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440203, 4402, '44,4402,440203', 3, '武江区', 'WuJiangQu', 'WJQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440204, 4402, '44,4402,440204', 3, '浈江区', 'ZhenJiangQu', 'ZJQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440205, 4402, '44,4402,440205', 3, '曲江区', 'QuJiangQu', 'QJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440222, 4402, '44,4402,440222', 3, '始兴县', 'ShiXingXian', 'SXX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440224, 4402, '44,4402,440224', 3, '仁化县', 'RenHuaXian', 'RHX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440229, 4402, '44,4402,440229', 3, '翁源县', 'WengYuanXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440232, 4402, '44,4402,440232', 3, '乳源瑶族自治县', 'RuYuanYaoZuZiZhiXian', 'RYYZZZX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440233, 4402, '44,4402,440233', 3, '新丰县', 'XinFengXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440281, 4402, '44,4402,440281', 3, '乐昌市', 'LeChangShi', 'LCS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440282, 4402, '44,4402,440282', 3, '南雄市', 'NanXiongShi', 'NXS', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440303, 4403, '44,4403,440303', 3, '罗湖区', 'LuoHuQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440304, 4403, '44,4403,440304', 3, '福田区', 'FuTianQu', 'FTQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440305, 4403, '44,4403,440305', 3, '南山区', 'NanShanQu', 'NSQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440306, 4403, '44,4403,440306', 3, '宝安区', 'BaoAnQu', 'BAQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440307, 4403, '44,4403,440307', 3, '龙岗区', 'LongGangQu', 'LGQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440308, 4403, '44,4403,440308', 3, '盐田区', 'YanTianQu', 'YTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440309, 4403, '44,4403,440309', 3, '龙华区', 'LongHuaQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440310, 4403, '44,4403,440310', 3, '坪山区', 'PingShanQu', 'PSQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440311, 4403, '44,4403,440311', 3, '光明区', 'GuangMingQu', 'GMQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440402, 4404, '44,4404,440402', 3, '香洲区', 'XiangZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440403, 4404, '44,4404,440403', 3, '斗门区', 'DouMenQu', 'DMQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440404, 4404, '44,4404,440404', 3, '金湾区', 'JinWanQu', 'JWQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440507, 4405, '44,4405,440507', 3, '龙湖区', 'LongHuQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440511, 4405, '44,4405,440511', 3, '金平区', 'JinPingQu', 'JPQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440512, 4405, '44,4405,440512', 3, '濠江区', 'HaoJiangQu', 'HJQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440513, 4405, '44,4405,440513', 3, '潮阳区', 'ChaoYangQu', 'CYQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440514, 4405, '44,4405,440514', 3, '潮南区', 'ChaoNanQu', 'CNQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440515, 4405, '44,4405,440515', 3, '澄海区', 'ChengHaiQu', 'CHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440523, 4405, '44,4405,440523', 3, '南澳县', 'NanAoXian', 'NAX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440604, 4406, '44,4406,440604', 3, '禅城区', 'ChanChengQu', 'CCQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440605, 4406, '44,4406,440605', 3, '南海区', 'NanHaiQu', 'NHQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440606, 4406, '44,4406,440606', 3, '顺德区', 'ShunDeQu', 'SDQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440607, 4406, '44,4406,440607', 3, '三水区', 'SanShuiQu', 'SSQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440608, 4406, '44,4406,440608', 3, '高明区', 'GaoMingQu', 'GMQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440703, 4407, '44,4407,440703', 3, '蓬江区', 'PengJiangQu', 'PJQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440704, 4407, '44,4407,440704', 3, '江海区', 'JiangHaiQu', 'JHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440705, 4407, '44,4407,440705', 3, '新会区', 'XinHuiQu', 'XHQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440781, 4407, '44,4407,440781', 3, '台山市', 'TaiShanShi', 'TSS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440783, 4407, '44,4407,440783', 3, '开平市', 'KaiPingShi', 'KPS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440784, 4407, '44,4407,440784', 3, '鹤山市', 'HeShanShi', 'HSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440785, 4407, '44,4407,440785', 3, '恩平市', 'EnPingShi', 'EPS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440802, 4408, '44,4408,440802', 3, '赤坎区', 'ChiKanQu', 'CKQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440803, 4408, '44,4408,440803', 3, '霞山区', 'XiaShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440804, 4408, '44,4408,440804', 3, '坡头区', 'PoTouQu', 'PTQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440811, 4408, '44,4408,440811', 3, '麻章区', 'MaZhangQu', 'MZQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440823, 4408, '44,4408,440823', 3, '遂溪县', 'SuiXiXian', 'SXX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440825, 4408, '44,4408,440825', 3, '徐闻县', 'XuWenXian', 'XWX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440881, 4408, '44,4408,440881', 3, '廉江市', 'LianJiangShi', 'LJS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440882, 4408, '44,4408,440882', 3, '雷州市', 'LeiZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440883, 4408, '44,4408,440883', 3, '吴川市', 'WuChuanShi', 'WCS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440902, 4409, '44,4409,440902', 3, '茂南区', 'MaoNanQu', 'MNQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440904, 4409, '44,4409,440904', 3, '电白区', 'DianBaiQu', 'DBQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440981, 4409, '44,4409,440981', 3, '高州市', 'GaoZhouShi', 'GZS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440982, 4409, '44,4409,440982', 3, '化州市', 'HuaZhouShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (440983, 4409, '44,4409,440983', 3, '信宜市', 'XinYiShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441202, 4412, '44,4412,441202', 3, '端州区', 'DuanZhouQu', 'DZQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441203, 4412, '44,4412,441203', 3, '鼎湖区', 'DingHuQu', 'DHQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441204, 4412, '44,4412,441204', 3, '高要区', 'GaoYaoQu', 'GYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441223, 4412, '44,4412,441223', 3, '广宁县', 'GuangNingXian', 'GNX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441224, 4412, '44,4412,441224', 3, '怀集县', 'HuaiJiXian', 'HJX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441225, 4412, '44,4412,441225', 3, '封开县', 'FengKaiXian', 'FKX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441226, 4412, '44,4412,441226', 3, '德庆县', 'DeQingXian', 'DQX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441284, 4412, '44,4412,441284', 3, '四会市', 'SiHuiShi', 'SHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441302, 4413, '44,4413,441302', 3, '惠城区', 'HuiChengQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441303, 4413, '44,4413,441303', 3, '惠阳区', 'HuiYangQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441322, 4413, '44,4413,441322', 3, '博罗县', 'BoLuoXian', 'BLX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441323, 4413, '44,4413,441323', 3, '惠东县', 'HuiDongXian', 'HDX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441324, 4413, '44,4413,441324', 3, '龙门县', 'LongMenXian', 'LMX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441402, 4414, '44,4414,441402', 3, '梅江区', 'MeiJiangQu', 'MJQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441403, 4414, '44,4414,441403', 3, '梅县区', 'MeiXianQu', 'MXQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441422, 4414, '44,4414,441422', 3, '大埔县', 'DaBuXian', 'DBX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441423, 4414, '44,4414,441423', 3, '丰顺县', 'FengShunXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441424, 4414, '44,4414,441424', 3, '五华县', 'WuHuaXian', 'WHX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441426, 4414, '44,4414,441426', 3, '平远县', 'PingYuanXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441427, 4414, '44,4414,441427', 3, '蕉岭县', 'JiaoLingXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441481, 4414, '44,4414,441481', 3, '兴宁市', 'XingNingShi', 'XNS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441502, 4415, '44,4415,441502', 3, '城区', 'ChengQu', 'CQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441521, 4415, '44,4415,441521', 3, '海丰县', 'HaiFengXian', 'HFX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441523, 4415, '44,4415,441523', 3, '陆河县', 'LuHeXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441581, 4415, '44,4415,441581', 3, '陆丰市', 'LuFengShi', 'LFS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441602, 4416, '44,4416,441602', 3, '源城区', 'YuanChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441621, 4416, '44,4416,441621', 3, '紫金县', 'ZiJinXian', 'ZJX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441622, 4416, '44,4416,441622', 3, '龙川县', 'LongChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441623, 4416, '44,4416,441623', 3, '连平县', 'LianPingXian', 'LPX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441624, 4416, '44,4416,441624', 3, '和平县', 'HePingXian', 'HPX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441625, 4416, '44,4416,441625', 3, '东源县', 'DongYuanXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441702, 4417, '44,4417,441702', 3, '江城区', 'JiangChengQu', 'JCQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441704, 4417, '44,4417,441704', 3, '阳东区', 'YangDongQu', 'YDQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441721, 4417, '44,4417,441721', 3, '阳西县', 'YangXiXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441781, 4417, '44,4417,441781', 3, '阳春市', 'YangChunShi', 'YCS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441802, 4418, '44,4418,441802', 3, '清城区', 'QingChengQu', 'QCQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441803, 4418, '44,4418,441803', 3, '清新区', 'QingXinQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441821, 4418, '44,4418,441821', 3, '佛冈县', 'FoGangXian', 'FGX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441823, 4418, '44,4418,441823', 3, '阳山县', 'YangShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441825, 4418, '44,4418,441825', 3, '连山壮族瑶族自治县', 'LianShanZhuangZuYaoZuZiZhiXian', 'LSZZYZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441826, 4418, '44,4418,441826', 3, '连南瑶族自治县', 'LianNanYaoZuZiZhiXian', 'LNYZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441881, 4418, '44,4418,441881', 3, '英德市', 'YingDeShi', 'YDS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441882, 4418, '44,4418,441882', 3, '连州市', 'LianZhouShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (441900, 4419, '44,4419,441900', 3, '东莞市', 'DongGuanShi', 'DGS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (442000, 4420, '44,4420,442000', 3, '中山市', 'ZhongShanShi', 'ZSS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445102, 4451, '44,4451,445102', 3, '湘桥区', 'XiangQiaoQu', 'XQQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445103, 4451, '44,4451,445103', 3, '潮安区', 'ChaoAnQu', 'CAQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445122, 4451, '44,4451,445122', 3, '饶平县', 'RaoPingXian', 'RPX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445202, 4452, '44,4452,445202', 3, '榕城区', 'RongChengQu', 'RCQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445203, 4452, '44,4452,445203', 3, '揭东区', 'JieDongQu', 'JDQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445222, 4452, '44,4452,445222', 3, '揭西县', 'JieXiXian', 'JXX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445224, 4452, '44,4452,445224', 3, '惠来县', 'HuiLaiXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445281, 4452, '44,4452,445281', 3, '普宁市', 'PuNingShi', 'PNS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445302, 4453, '44,4453,445302', 3, '云城区', 'YunChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445303, 4453, '44,4453,445303', 3, '云安区', 'YunAnQu', 'YAQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445321, 4453, '44,4453,445321', 3, '新兴县', 'XinXingXian', 'XXX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445322, 4453, '44,4453,445322', 3, '郁南县', 'YuNanXian', 'YNX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (445381, 4453, '44,4453,445381', 3, '罗定市', 'LuoDingShi', 'LDS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450102, 4501, '45,4501,450102', 3, '兴宁区', 'XingNingQu', 'XNQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450103, 4501, '45,4501,450103', 3, '青秀区', 'QingXiuQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450105, 4501, '45,4501,450105', 3, '江南区', 'JiangNanQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450107, 4501, '45,4501,450107', 3, '西乡塘区', 'XiXiangTangQu', 'XXTQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450108, 4501, '45,4501,450108', 3, '良庆区', 'LiangQingQu', 'LQQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450109, 4501, '45,4501,450109', 3, '邕宁区', 'YongNingQu', 'YNQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450110, 4501, '45,4501,450110', 3, '武鸣区', 'WuMingQu', 'WMQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450123, 4501, '45,4501,450123', 3, '隆安县', 'LongAnXian', 'LAX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450124, 4501, '45,4501,450124', 3, '马山县', 'MaShanXian', 'MSX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450125, 4501, '45,4501,450125', 3, '上林县', 'ShangLinXian', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450126, 4501, '45,4501,450126', 3, '宾阳县', 'BinYangXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450127, 4501, '45,4501,450127', 3, '横县', 'HengXian', 'HX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450202, 4502, '45,4502,450202', 3, '城中区', 'ChengZhongQu', 'CZQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450203, 4502, '45,4502,450203', 3, '鱼峰区', 'YuFengQu', 'YFQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450204, 4502, '45,4502,450204', 3, '柳南区', 'LiuNanQu', 'LNQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450205, 4502, '45,4502,450205', 3, '柳北区', 'LiuBeiQu', 'LBQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450206, 4502, '45,4502,450206', 3, '柳江区', 'LiuJiangQu', 'LJQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450222, 4502, '45,4502,450222', 3, '柳城县', 'LiuChengXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450223, 4502, '45,4502,450223', 3, '鹿寨县', 'LuZhaiXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450224, 4502, '45,4502,450224', 3, '融安县', 'RongAnXian', 'RAX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450225, 4502, '45,4502,450225', 3, '融水苗族自治县', 'RongShuiMiaoZuZiZhiXian', 'RSMZZZX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450226, 4502, '45,4502,450226', 3, '三江侗族自治县', 'SanJiangDongZuZiZhiXian', 'SJDZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450302, 4503, '45,4503,450302', 3, '秀峰区', 'XiuFengQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450303, 4503, '45,4503,450303', 3, '叠彩区', 'DieCaiQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450304, 4503, '45,4503,450304', 3, '象山区', 'XiangShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450305, 4503, '45,4503,450305', 3, '七星区', 'QiXingQu', 'QXQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450311, 4503, '45,4503,450311', 3, '雁山区', 'YanShanQu', 'YSQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450312, 4503, '45,4503,450312', 3, '临桂区', 'LinGuiQu', 'LGQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450321, 4503, '45,4503,450321', 3, '阳朔县', 'YangShuoXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450323, 4503, '45,4503,450323', 3, '灵川县', 'LingChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450324, 4503, '45,4503,450324', 3, '全州县', 'QuanZhouXian', 'QZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450325, 4503, '45,4503,450325', 3, '兴安县', 'XingAnXian', 'XAX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450326, 4503, '45,4503,450326', 3, '永福县', 'YongFuXian', 'YFX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450327, 4503, '45,4503,450327', 3, '灌阳县', 'GuanYangXian', 'GYX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450328, 4503, '45,4503,450328', 3, '龙胜各族自治县', 'LongShengGeZuZiZhiXian', 'LSGZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450329, 4503, '45,4503,450329', 3, '资源县', 'ZiYuanXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450330, 4503, '45,4503,450330', 3, '平乐县', 'PingLeXian', 'PLX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450332, 4503, '45,4503,450332', 3, '恭城瑶族自治县', 'GongChengYaoZuZiZhiXian', 'GCYZZZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450381, 4503, '45,4503,450381', 3, '荔浦市', 'LiPuShi', 'LPS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450403, 4504, '45,4504,450403', 3, '万秀区', 'WanXiuQu', 'WXQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450405, 4504, '45,4504,450405', 3, '长洲区', 'ChangZhouQu', 'CZQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450406, 4504, '45,4504,450406', 3, '龙圩区', 'LongWeiQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450421, 4504, '45,4504,450421', 3, '苍梧县', 'CangWuXian', 'CWX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450422, 4504, '45,4504,450422', 3, '藤县', 'TengXian', 'TX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450423, 4504, '45,4504,450423', 3, '蒙山县', 'MengShanXian', 'MSX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450481, 4504, '45,4504,450481', 3, '岑溪市', 'CenXiShi', 'CXS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450502, 4505, '45,4505,450502', 3, '海城区', 'HaiChengQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450503, 4505, '45,4505,450503', 3, '银海区', 'YinHaiQu', 'YHQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450512, 4505, '45,4505,450512', 3, '铁山港区', 'TieShanGangQu', 'TSGQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450521, 4505, '45,4505,450521', 3, '合浦县', 'HePuXian', 'HPX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450602, 4506, '45,4506,450602', 3, '港口区', 'GangKouQu', 'GKQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450603, 4506, '45,4506,450603', 3, '防城区', 'FangChengQu', 'FCQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450621, 4506, '45,4506,450621', 3, '上思县', 'ShangSiXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450681, 4506, '45,4506,450681', 3, '东兴市', 'DongXingShi', 'DXS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450702, 4507, '45,4507,450702', 3, '钦南区', 'QinNanQu', 'QNQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450703, 4507, '45,4507,450703', 3, '钦北区', 'QinBeiQu', 'QBQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450721, 4507, '45,4507,450721', 3, '灵山县', 'LingShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450722, 4507, '45,4507,450722', 3, '浦北县', 'PuBeiXian', 'PBX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450802, 4508, '45,4508,450802', 3, '港北区', 'GangBeiQu', 'GBQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450803, 4508, '45,4508,450803', 3, '港南区', 'GangNanQu', 'GNQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450804, 4508, '45,4508,450804', 3, '覃塘区', 'TanTangQu', 'TTQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450821, 4508, '45,4508,450821', 3, '平南县', 'PingNanXian', 'PNX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450881, 4508, '45,4508,450881', 3, '桂平市', 'GuiPingShi', 'GPS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450902, 4509, '45,4509,450902', 3, '玉州区', 'YuZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450903, 4509, '45,4509,450903', 3, '福绵区', 'FuMianQu', 'FMQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450921, 4509, '45,4509,450921', 3, '容县', 'RongXian', 'RX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450922, 4509, '45,4509,450922', 3, '陆川县', 'LuChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450923, 4509, '45,4509,450923', 3, '博白县', 'BoBaiXian', 'BBX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450924, 4509, '45,4509,450924', 3, '兴业县', 'XingYeXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (450981, 4509, '45,4509,450981', 3, '北流市', 'BeiLiuShi', 'BLS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451002, 4510, '45,4510,451002', 3, '右江区', 'YouJiangQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451003, 4510, '45,4510,451003', 3, '田阳区', 'TianYangQu', 'TYQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451022, 4510, '45,4510,451022', 3, '田东县', 'TianDongXian', 'TDX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451023, 4510, '45,4510,451023', 3, '平果县', 'PingGuoXian', 'PGX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451024, 4510, '45,4510,451024', 3, '德保县', 'DeBaoXian', 'DBX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451026, 4510, '45,4510,451026', 3, '那坡县', 'NaPoXian', 'NPX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451027, 4510, '45,4510,451027', 3, '凌云县', 'LingYunXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451028, 4510, '45,4510,451028', 3, '乐业县', 'LeYeXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451029, 4510, '45,4510,451029', 3, '田林县', 'TianLinXian', 'TLX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451030, 4510, '45,4510,451030', 3, '西林县', 'XiLinXian', 'XLX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451031, 4510, '45,4510,451031', 3, '隆林各族自治县', 'LongLinGeZuZiZhiXian', 'LLGZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451081, 4510, '45,4510,451081', 3, '靖西市', 'JingXiShi', 'JXS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451102, 4511, '45,4511,451102', 3, '八步区', 'BaBuQu', 'BBQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451103, 4511, '45,4511,451103', 3, '平桂区', 'PingGuiQu', 'PGQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451121, 4511, '45,4511,451121', 3, '昭平县', 'ZhaoPingXian', 'ZPX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451122, 4511, '45,4511,451122', 3, '钟山县', 'ZhongShanXian', 'ZSX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451123, 4511, '45,4511,451123', 3, '富川瑶族自治县', 'FuChuanYaoZuZiZhiXian', 'FCYZZZX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451202, 4512, '45,4512,451202', 3, '金城江区', 'JinChengJiangQu', 'JCJQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451203, 4512, '45,4512,451203', 3, '宜州区', 'YiZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451221, 4512, '45,4512,451221', 3, '南丹县', 'NanDanXian', 'NDX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451222, 4512, '45,4512,451222', 3, '天峨县', 'TianEXian', 'TEX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451223, 4512, '45,4512,451223', 3, '凤山县', 'FengShanXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451224, 4512, '45,4512,451224', 3, '东兰县', 'DongLanXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451225, 4512, '45,4512,451225', 3, '罗城仫佬族自治县', 'LuoChengMuLaoZuZiZhiXian', 'LCMLZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451226, 4512, '45,4512,451226', 3, '环江毛南族自治县', 'HuanJiangMaoNanZuZiZhiXian', 'HJMNZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451227, 4512, '45,4512,451227', 3, '巴马瑶族自治县', 'BaMaYaoZuZiZhiXian', 'BMYZZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451228, 4512, '45,4512,451228', 3, '都安瑶族自治县', 'DuAnYaoZuZiZhiXian', 'DAYZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451229, 4512, '45,4512,451229', 3, '大化瑶族自治县', 'DaHuaYaoZuZiZhiXian', 'DHYZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451302, 4513, '45,4513,451302', 3, '兴宾区', 'XingBinQu', 'XBQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451321, 4513, '45,4513,451321', 3, '忻城县', 'XinChengXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451322, 4513, '45,4513,451322', 3, '象州县', 'XiangZhouXian', 'XZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451323, 4513, '45,4513,451323', 3, '武宣县', 'WuXuanXian', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451324, 4513, '45,4513,451324', 3, '金秀瑶族自治县', 'JinXiuYaoZuZiZhiXian', 'JXYZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451381, 4513, '45,4513,451381', 3, '合山市', 'HeShanShi', 'HSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451402, 4514, '45,4514,451402', 3, '江州区', 'JiangZhouQu', 'JZQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451421, 4514, '45,4514,451421', 3, '扶绥县', 'FuSuiXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451422, 4514, '45,4514,451422', 3, '宁明县', 'NingMingXian', 'NMX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451423, 4514, '45,4514,451423', 3, '龙州县', 'LongZhouXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451424, 4514, '45,4514,451424', 3, '大新县', 'DaXinXian', 'DXX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451425, 4514, '45,4514,451425', 3, '天等县', 'TianDengXian', 'TDX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (451481, 4514, '45,4514,451481', 3, '凭祥市', 'PingXiangShi', 'PXS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460105, 4601, '46,4601,460105', 3, '秀英区', 'XiuYingQu', 'XYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460106, 4601, '46,4601,460106', 3, '龙华区', 'LongHuaQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460107, 4601, '46,4601,460107', 3, '琼山区', 'QiongShanQu', 'QSQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460108, 4601, '46,4601,460108', 3, '美兰区', 'MeiLanQu', 'MLQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460202, 4602, '46,4602,460202', 3, '海棠区', 'HaiTangQu', 'HTQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460203, 4602, '46,4602,460203', 3, '吉阳区', 'JiYangQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460204, 4602, '46,4602,460204', 3, '天涯区', 'TianYaQu', 'TYQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460205, 4602, '46,4602,460205', 3, '崖州区', 'YaZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460321, 4603, '46,4603,460321', 3, '西沙群岛', 'XiShaQunDao', 'XSQD', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460322, 4603, '46,4603,460322', 3, '南沙群岛', 'NanShaQunDao', 'NSQD', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460323, 4603, '46,4603,460323', 3, '中沙群岛的岛礁及其海域', 'ZhongShaQunDaoDeDaoJiaoJiQiHaiYu', 'ZSQDDDJJQHY', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (460400, 4604, '46,4604,460400', 3, '儋州市', 'DanZhouShi', 'DZS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001, 46, '46,469001', 2, '五指山市', 'WuZhiShanShi', 'WZSS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002, 46, '46,469002', 2, '琼海市', 'QiongHaiShi', 'QHS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005, 46, '46,469005', 2, '文昌市', 'WenChangShi', 'WCS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006, 46, '46,469006', 2, '万宁市', 'WanNingShi', 'WNS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007, 46, '46,469007', 2, '东方市', 'DongFangShi', 'DFS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021, 46, '46,469021', 2, '定安县', 'DingAnXian', 'DAX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022, 46, '46,469022', 2, '屯昌县', 'TunChangXian', 'TCX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023, 46, '46,469023', 2, '澄迈县', 'ChengMaiXian', 'CMX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024, 46, '46,469024', 2, '临高县', 'LinGaoXian', 'LGX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025, 46, '46469025', 2, '白沙黎族自治县', 'BaiShaLiZuZiZhiXian', 'BSLZZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026, 46, '46,469026', 2, '昌江黎族自治县', 'ChangJiangLiZuZiZhiXian', 'CJLZZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027, 46, '46,469027', 2, '乐东黎族自治县', 'LeDongLiZuZiZhiXian', 'LDLZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028, 46, '46,469028', 2, '陵水黎族自治县', 'LingShuiLiZuZiZhiXian', 'LSLZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029, 46, '46,469029', 2, '保亭黎族苗族自治县', 'BaoTingLiZuMiaoZuZiZhiXian', 'BTLZMZZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030, 46, '46,469030', 2, '琼中黎族苗族自治县', 'QiongZhongLiZuMiaoZuZiZhiXian', 'QZLZMZZZX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500101, 5001, '50,5001,500101', 3, '万州区', 'WanZhouQu', 'WZQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500102, 5001, '50,5001,500102', 3, '涪陵区', 'FuLingQu', 'FLQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500103, 5001, '50,5001,500103', 3, '渝中区', 'YuZhongQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500104, 5001, '50,5001,500104', 3, '大渡口区', 'DaDuKouQu', 'DDKQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500105, 5001, '50,5001,500105', 3, '江北区', 'JiangBeiQu', 'JBQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500106, 5001, '50,5001,500106', 3, '沙坪坝区', 'ShaPingBaQu', 'SPBQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500107, 5001, '50,5001,500107', 3, '九龙坡区', 'JiuLongPoQu', 'JLPQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500108, 5001, '50,5001,500108', 3, '南岸区', 'NanAnQu', 'NAQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500109, 5001, '50,5001,500109', 3, '北碚区', 'BeiBeiQu', 'BBQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500110, 5001, '50,5001,500110', 3, '綦江区', 'QiJiangQu', 'QJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500111, 5001, '50,5001,500111', 3, '大足区', 'DaZuQu', 'DZQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500112, 5001, '50,5001,500112', 3, '渝北区', 'YuBeiQu', 'YBQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500113, 5001, '50,5001,500113', 3, '巴南区', 'BaNanQu', 'BNQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500114, 5001, '50,5001,500114', 3, '黔江区', 'QianJiangQu', 'QJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500115, 5001, '50,5001,500115', 3, '长寿区', 'ChangShouQu', 'CSQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500116, 5001, '50,5001,500116', 3, '江津区', 'JiangJinQu', 'JJQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500117, 5001, '50,5001,500117', 3, '合川区', 'HeChuanQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500118, 5001, '50,5001,500118', 3, '永川区', 'YongChuanQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500119, 5001, '50,5001,500119', 3, '南川区', 'NanChuanQu', 'NCQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500120, 5001, '50,5001,500120', 3, '璧山区', 'BiShanQu', 'BSQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500151, 5001, '50,5001,500151', 3, '铜梁区', 'TongLiangQu', 'TLQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500152, 5001, '50,5001,500152', 3, '潼南区', 'TongNanQu', 'TNQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500153, 5001, '50,5001,500153', 3, '荣昌区', 'RongChangQu', 'RCQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500154, 5001, '50,5001,500154', 3, '开州区', 'KaiZhouQu', 'KZQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500155, 5001, '50,5001,500155', 3, '梁平区', 'LiangPingQu', 'LPQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500156, 5001, '50,5001,500156', 3, '武隆区', 'WuLongQu', 'WLQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500229, 5001, '50,5001,500229', 3, '城口县', 'ChengKouXian', 'CKX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500230, 5001, '50,5001,500230', 3, '丰都县', 'FengDuXian', 'FDX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500231, 5001, '50,5001,500231', 3, '垫江县', 'DianJiangXian', 'DJX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500233, 5001, '50,5001,500233', 3, '忠县', 'ZhongXian', 'ZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500235, 5001, '50,5001,500235', 3, '云阳县', 'YunYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500236, 5001, '50,5001,500236', 3, '奉节县', 'FengJieXian', 'FJX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500237, 5001, '50,5001,500237', 3, '巫山县', 'WuShanXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500238, 5001, '50,5001,500238', 3, '巫溪县', 'WuXiXian', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500240, 5001, '50,5001,500240', 3, '石柱土家族自治县', 'ShiZhuTuJiaZuZiZhiXian', 'SZTJZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500241, 5001, '50,5001,500241', 3, '秀山土家族苗族自治县', 'XiuShanTuJiaZuMiaoZuZiZhiXian', 'XSTJZMZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500242, 5001, '50,5001,500242', 3, '酉阳土家族苗族自治县', 'YouYangTuJiaZuMiaoZuZiZhiXian', 'YYTJZMZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (500243, 5001, '50,5001,500243', 3, '彭水苗族土家族自治县', 'PengShuiMiaoZuTuJiaZuZiZhiXian', 'PSMZTJZZZX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510104, 5101, '51,5101,510104', 3, '锦江区', 'JinJiangQu', 'JJQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510105, 5101, '51,5101,510105', 3, '青羊区', 'QingYangQu', 'QYQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510106, 5101, '51,5101,510106', 3, '金牛区', 'JinNiuQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510107, 5101, '51,5101,510107', 3, '武侯区', 'WuHouQu', 'WHQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510108, 5101, '51,5101,510108', 3, '成华区', 'ChengHuaQu', 'CHQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510112, 5101, '51,5101,510112', 3, '龙泉驿区', 'LongQuanYiQu', 'LQYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510113, 5101, '51,5101,510113', 3, '青白江区', 'QingBaiJiangQu', 'QBJQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510114, 5101, '51,5101,510114', 3, '新都区', 'XinDuQu', 'XDQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510115, 5101, '51,5101,510115', 3, '温江区', 'WenJiangQu', 'WJQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510116, 5101, '51,5101,510116', 3, '双流区', 'ShuangLiuQu', 'SLQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510117, 5101, '51,5101,510117', 3, '郫都区', 'PiDouQu', 'PDQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510121, 5101, '51,5101,510121', 3, '金堂县', 'JinTangXian', 'JTX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510129, 5101, '51,5101,510129', 3, '大邑县', 'DaYiXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510131, 5101, '51,5101,510131', 3, '蒲江县', 'PuJiangXian', 'PJX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510132, 5101, '51,5101,510132', 3, '新津县', 'XinJinXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510181, 5101, '51,5101,510181', 3, '都江堰市', 'DuJiangYanShi', 'DJYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510182, 5101, '51,5101,510182', 3, '彭州市', 'PengZhouShi', 'PZS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510183, 5101, '51,5101,510183', 3, '邛崃市', 'QiongLaiShi', 'QLS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510184, 5101, '51,5101,510184', 3, '崇州市', 'ChongZhouShi', 'CZS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510185, 5101, '51,5101,510185', 3, '简阳市', 'JianYangShi', 'JYS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510302, 5103, '51,5103,510302', 3, '自流井区', 'ZiLiuJingQu', 'ZLJQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510303, 5103, '51,5103,510303', 3, '贡井区', 'GongJingQu', 'GJQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510304, 5103, '51,5103,510304', 3, '大安区', 'DaAnQu', 'DAQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510311, 5103, '51,5103,510311', 3, '沿滩区', 'YanTanQu', 'YTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510321, 5103, '51,5103,510321', 3, '荣县', 'RongXian', 'RX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510322, 5103, '51,5103,510322', 3, '富顺县', 'FuShunXian', 'FSX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510402, 5104, '51,5104,510402', 3, '东区', 'DongQu', 'DQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510403, 5104, '51,5104,510403', 3, '西区', 'XiQu', 'XQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510411, 5104, '51,5104,510411', 3, '仁和区', 'RenHeQu', 'RHQ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510421, 5104, '51,5104,510421', 3, '米易县', 'MiYiXian', 'MYX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510422, 5104, '51,5104,510422', 3, '盐边县', 'YanBianXian', 'YBX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510502, 5105, '51,5105,510502', 3, '江阳区', 'JiangYangQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510503, 5105, '51,5105,510503', 3, '纳溪区', 'NaXiQu', 'NXQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510504, 5105, '51,5105,510504', 3, '龙马潭区', 'LongMaTanQu', 'LMTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510521, 5105, '51,5105,510521', 3, '泸县', 'LuXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510522, 5105, '51,5105,510522', 3, '合江县', 'HeJiangXian', 'HJX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510524, 5105, '51,5105,510524', 3, '叙永县', 'XuYongXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510525, 5105, '51,5105,510525', 3, '古蔺县', 'GuLinXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510603, 5106, '51,5106,510603', 3, '旌阳区', 'JingYangQu', 'JYQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510604, 5106, '51,5106,510604', 3, '罗江区', 'LuoJiangQu', 'LJQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510623, 5106, '51,5106,510623', 3, '中江县', 'ZhongJiangXian', 'ZJX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510681, 5106, '51,5106,510681', 3, '广汉市', 'GuangHanShi', 'GHS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510682, 5106, '51,5106,510682', 3, '什邡市', 'ShiFangShi', 'SFS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510683, 5106, '51,5106,510683', 3, '绵竹市', 'MianZhuShi', 'MZS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510703, 5107, '51,5107,510703', 3, '涪城区', 'FuChengQu', 'FCQ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510704, 5107, '51,5107,510704', 3, '游仙区', 'YouXianQu', 'YXQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510705, 5107, '51,5107,510705', 3, '安州区', 'AnZhouQu', 'AZQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510722, 5107, '51,5107,510722', 3, '三台县', 'SanTaiXian', 'STX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510723, 5107, '51,5107,510723', 3, '盐亭县', 'YanTingXian', 'YTX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510725, 5107, '51,5107,510725', 3, '梓潼县', 'ZiTongXian', 'ZTX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510726, 5107, '51,5107,510726', 3, '北川羌族自治县', 'BeiChuanQiangZuZiZhiXian', 'BCQZZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510727, 5107, '51,5107,510727', 3, '平武县', 'PingWuXian', 'PWX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510781, 5107, '51,5107,510781', 3, '江油市', 'JiangYouShi', 'JYS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510802, 5108, '51,5108,510802', 3, '利州区', 'LiZhouQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510811, 5108, '51,5108,510811', 3, '昭化区', 'ZhaoHuaQu', 'ZHQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510812, 5108, '51,5108,510812', 3, '朝天区', 'ChaoTianQu', 'CTQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510821, 5108, '51,5108,510821', 3, '旺苍县', 'WangCangXian', 'WCX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510822, 5108, '51,5108,510822', 3, '青川县', 'QingChuanXian', 'QCX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510823, 5108, '51,5108,510823', 3, '剑阁县', 'JianGeXian', 'JGX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510824, 5108, '51,5108,510824', 3, '苍溪县', 'CangXiXian', 'CXX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510903, 5109, '51,5109,510903', 3, '船山区', 'ChuanShanQu', 'CSQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510904, 5109, '51,5109,510904', 3, '安居区', 'AnJuQu', 'AJQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510921, 5109, '51,5109,510921', 3, '蓬溪县', 'PengXiXian', 'PXX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510923, 5109, '51,5109,510923', 3, '大英县', 'DaYingXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (510981, 5109, '51,5109,510981', 3, '射洪市', 'SheHongShi', 'SHS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511002, 5110, '51,5110,511002', 3, '市中区', 'ShiZhongQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511011, 5110, '51,5110,511011', 3, '东兴区', 'DongXingQu', 'DXQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511024, 5110, '51,5110,511024', 3, '威远县', 'WeiYuanXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511025, 5110, '51,5110,511025', 3, '资中县', 'ZiZhongXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511071, 5110, '51,5110,511071', 3, '内江经济开发区', 'NeiJiangJingJiKaiFaQu', 'NJJJKFQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511083, 5110, '51,5110,511083', 3, '隆昌市', 'LongChangShi', 'LCS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511102, 5111, '51,5111,511102', 3, '市中区', 'ShiZhongQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511111, 5111, '51,5111,511111', 3, '沙湾区', 'ShaWanQu', 'SWQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511112, 5111, '51,5111,511112', 3, '五通桥区', 'WuTongQiaoQu', 'WTQQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511113, 5111, '51,5111,511113', 3, '金口河区', 'JinKouHeQu', 'JKHQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511123, 5111, '51,5111,511123', 3, '犍为县', 'QianWeiXian', 'QWX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511124, 5111, '51,5111,511124', 3, '井研县', 'JingYanXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511126, 5111, '51,5111,511126', 3, '夹江县', 'JiaJiangXian', 'JJX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511129, 5111, '51,5111,511129', 3, '沐川县', 'MuChuanXian', 'MCX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511132, 5111, '51,5111,511132', 3, '峨边彝族自治县', 'EBianYiZuZiZhiXian', 'EBYZZZX', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511133, 5111, '51,5111,511133', 3, '马边彝族自治县', 'MaBianYiZuZiZhiXian', 'MBYZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511181, 5111, '51,5111,511181', 3, '峨眉山市', 'EMeiShanShi', 'EMSS', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511302, 5113, '51,5113,511302', 3, '顺庆区', 'ShunQingQu', 'SQQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511303, 5113, '51,5113,511303', 3, '高坪区', 'GaoPingQu', 'GPQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511304, 5113, '51,5113,511304', 3, '嘉陵区', 'JiaLingQu', 'JLQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511321, 5113, '51,5113,511321', 3, '南部县', 'NanBuXian', 'NBX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511322, 5113, '51,5113,511322', 3, '营山县', 'YingShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511323, 5113, '51,5113,511323', 3, '蓬安县', 'PengAnXian', 'PAX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511324, 5113, '51,5113,511324', 3, '仪陇县', 'YiLongXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511325, 5113, '51,5113,511325', 3, '西充县', 'XiChongXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511381, 5113, '51,5113,511381', 3, '阆中市', 'LangZhongShi', 'LZS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511402, 5114, '51,5114,511402', 3, '东坡区', 'DongPoQu', 'DPQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511403, 5114, '51,5114,511403', 3, '彭山区', 'PengShanQu', 'PSQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511421, 5114, '51,5114,511421', 3, '仁寿县', 'RenShouXian', 'RSX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511423, 5114, '51,5114,511423', 3, '洪雅县', 'HongYaXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511424, 5114, '51,5114,511424', 3, '丹棱县', 'DanLengXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511425, 5114, '51,5114,511425', 3, '青神县', 'QingShenXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511502, 5115, '51,5115,511502', 3, '翠屏区', 'CuiPingQu', 'CPQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511503, 5115, '51,5115,511503', 3, '南溪区', 'NanXiQu', 'NXQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511504, 5115, '51,5115,511504', 3, '叙州区', 'XuZhouQu', 'XZQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511523, 5115, '51,5115,511523', 3, '江安县', 'JiangAnXian', 'JAX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511524, 5115, '51,5115,511524', 3, '长宁县', 'ChangNingXian', 'CNX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511525, 5115, '51,5115,511525', 3, '高县', 'GaoXian', 'GX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511526, 5115, '51,5115,511526', 3, '珙县', 'GongXian', 'GX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511527, 5115, '51,5115,511527', 3, '筠连县', 'JunLianXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511528, 5115, '51,5115,511528', 3, '兴文县', 'XingWenXian', 'XWX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511529, 5115, '51,5115,511529', 3, '屏山县', 'PingShanXian', 'PSX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511602, 5116, '51,5116,511602', 3, '广安区', 'GuangAnQu', 'GAQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511603, 5116, '51,5116,511603', 3, '前锋区', 'QianFengQu', 'QFQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511621, 5116, '51,5116,511621', 3, '岳池县', 'YueChiXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511622, 5116, '51,5116,511622', 3, '武胜县', 'WuShengXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511623, 5116, '51,5116,511623', 3, '邻水县', 'LinShuiXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511681, 5116, '51,5116,511681', 3, '华蓥市', 'HuaYingShi', 'HYS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511702, 5117, '51,5117,511702', 3, '通川区', 'TongChuanQu', 'TCQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511703, 5117, '51,5117,511703', 3, '达川区', 'DaChuanQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511722, 5117, '51,5117,511722', 3, '宣汉县', 'XuanHanXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511723, 5117, '51,5117,511723', 3, '开江县', 'KaiJiangXian', 'KJX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511724, 5117, '51,5117,511724', 3, '大竹县', 'DaZhuXian', 'DZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511725, 5117, '51,5117,511725', 3, '渠县', 'QuXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511771, 5117, '51,5117,511771', 3, '达州经济开发区', 'DaZhouJingJiKaiFaQu', 'DZJJKFQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511781, 5117, '51,5117,511781', 3, '万源市', 'WanYuanShi', 'WYS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511802, 5118, '51,5118,511802', 3, '雨城区', 'YuChengQu', 'YCQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511803, 5118, '51,5118,511803', 3, '名山区', 'MingShanQu', 'MSQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511822, 5118, '51,5118,511822', 3, '荥经县', 'YingJingXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511823, 5118, '51,5118,511823', 3, '汉源县', 'HanYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511824, 5118, '51,5118,511824', 3, '石棉县', 'ShiMianXian', 'SMX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511825, 5118, '51,5118,511825', 3, '天全县', 'TianQuanXian', 'TQX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511826, 5118, '51,5118,511826', 3, '芦山县', 'LuShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511827, 5118, '51,5118,511827', 3, '宝兴县', 'BaoXingXian', 'BXX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511902, 5119, '51,5119,511902', 3, '巴州区', 'BaZhouQu', 'BZQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511903, 5119, '51,5119,511903', 3, '恩阳区', 'EnYangQu', 'EYQ', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511921, 5119, '51,5119,511921', 3, '通江县', 'TongJiangXian', 'TJX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511922, 5119, '51,5119,511922', 3, '南江县', 'NanJiangXian', 'NJX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511923, 5119, '51,5119,511923', 3, '平昌县', 'PingChangXian', 'PCX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (511971, 5119, '51,5119,511971', 3, '巴中经济开发区', 'BaZhongJingJiKaiFaQu', 'BZJJKFQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (512002, 5120, '51,5120,512002', 3, '雁江区', 'YanJiangQu', 'YJQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (512021, 5120, '51,5120,512021', 3, '安岳县', 'AnYueXian', 'AYX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (512022, 5120, '51,5120,512022', 3, '乐至县', 'LeZhiXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513201, 5132, '51,5132,513201', 3, '马尔康市', 'MaErKangShi', 'MEKS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513221, 5132, '51,5132,513221', 3, '汶川县', 'WenChuanXian', 'WCX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513222, 5132, '51,5132,513222', 3, '理县', 'LiXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513223, 5132, '51,5132,513223', 3, '茂县', 'MaoXian', 'MX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513224, 5132, '51,5132,513224', 3, '松潘县', 'SongPanXian', 'SPX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513225, 5132, '51,5132,513225', 3, '九寨沟县', 'JiuZhaiGouXian', 'JZGX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513226, 5132, '51,5132,513226', 3, '金川县', 'JinChuanXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513227, 5132, '51,5132,513227', 3, '小金县', 'XiaoJinXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513228, 5132, '51,5132,513228', 3, '黑水县', 'HeiShuiXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513230, 5132, '51,5132,513230', 3, '壤塘县', 'RangTangXian', 'RTX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513231, 5132, '51,5132,513231', 3, '阿坝县', 'ABaXian', 'ABX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513232, 5132, '51,5132,513232', 3, '若尔盖县', 'RuoErGaiXian', 'REGX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513233, 5132, '51,5132,513233', 3, '红原县', 'HongYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513301, 5133, '51,5133,513301', 3, '康定市', 'KangDingShi', 'KDS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513322, 5133, '51,5133,513322', 3, '泸定县', 'LuDingXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513323, 5133, '51,5133,513323', 3, '丹巴县', 'DanBaXian', 'DBX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513324, 5133, '51,5133,513324', 3, '九龙县', 'JiuLongXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513325, 5133, '51,5133,513325', 3, '雅江县', 'YaJiangXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513326, 5133, '51,5133,513326', 3, '道孚县', 'DaoFuXian', 'DFX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513327, 5133, '51,5133,513327', 3, '炉霍县', 'LuHuoXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513328, 5133, '51,5133,513328', 3, '甘孜县', 'GanZiXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513329, 5133, '51,5133,513329', 3, '新龙县', 'XinLongXian', 'XLX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513330, 5133, '51,5133,513330', 3, '德格县', 'DeGeXian', 'DGX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513331, 5133, '51,5133,513331', 3, '白玉县', 'BaiYuXian', 'BYX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513332, 5133, '51,5133,513332', 3, '石渠县', 'ShiQuXian', 'SQX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513333, 5133, '51,5133,513333', 3, '色达县', 'ShaiDaXian', 'SDX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513334, 5133, '51,5133,513334', 3, '理塘县', 'LiTangXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513335, 5133, '51,5133,513335', 3, '巴塘县', 'BaTangXian', 'BTX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513336, 5133, '51,5133,513336', 3, '乡城县', 'XiangChengXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513337, 5133, '51,5133,513337', 3, '稻城县', 'DaoChengXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513338, 5133, '51,5133,513338', 3, '得荣县', 'DeRongXian', 'DRX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513401, 5134, '51,5134,513401', 3, '西昌市', 'XiChangShi', 'XCS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513422, 5134, '51,5134,513422', 3, '木里藏族自治县', 'MuLiZangZuZiZhiXian', 'MLZZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513423, 5134, '51,5134,513423', 3, '盐源县', 'YanYuanXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513424, 5134, '51,5134,513424', 3, '德昌县', 'DeChangXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513425, 5134, '51,5134,513425', 3, '会理县', 'HuiLiXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513426, 5134, '51,5134,513426', 3, '会东县', 'HuiDongXian', 'HDX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513427, 5134, '51,5134,513427', 3, '宁南县', 'NingNanXian', 'NNX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513428, 5134, '51,5134,513428', 3, '普格县', 'PuGeXian', 'PGX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513429, 5134, '51,5134,513429', 3, '布拖县', 'BuTuoXian', 'BTX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513430, 5134, '51,5134,513430', 3, '金阳县', 'JinYangXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513431, 5134, '51,5134,513431', 3, '昭觉县', 'ZhaoJueXian', 'ZJX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513432, 5134, '51,5134,513432', 3, '喜德县', 'XiDeXian', 'XDX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513433, 5134, '51,5134,513433', 3, '冕宁县', 'MianNingXian', 'MNX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513434, 5134, '51,5134,513434', 3, '越西县', 'YueXiXian', 'YXX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513435, 5134, '51,5134,513435', 3, '甘洛县', 'GanLuoXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513436, 5134, '51,5134,513436', 3, '美姑县', 'MeiGuXian', 'MGX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (513437, 5134, '51,5134,513437', 3, '雷波县', 'LeiBoXian', 'LBX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520102, 5201, '52,5201,520102', 3, '南明区', 'NanMingQu', 'NMQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520103, 5201, '52,5201,520103', 3, '云岩区', 'YunYanQu', 'YYQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520111, 5201, '52,5201,520111', 3, '花溪区', 'HuaXiQu', 'HXQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520112, 5201, '52,5201,520112', 3, '乌当区', 'WuDangQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520113, 5201, '52,5201,520113', 3, '白云区', 'BaiYunQu', 'BYQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520115, 5201, '52,5201,520115', 3, '观山湖区', 'GuanShanHuQu', 'GSHQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520121, 5201, '52,5201,520121', 3, '开阳县', 'KaiYangXian', 'KYX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520122, 5201, '52,5201,520122', 3, '息烽县', 'XiFengXian', 'XFX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520123, 5201, '52,5201,520123', 3, '修文县', 'XiuWenXian', 'XWX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520181, 5201, '52,5201,520181', 3, '清镇市', 'QingZhenShi', 'QZS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520201, 5202, '52,5202,520201', 3, '钟山区', 'ZhongShanQu', 'ZSQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520203, 5202, '52,5202,520203', 3, '六枝特区', 'LuZhiTeQu', 'LZTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520221, 5202, '52,5202,520221', 3, '水城县', 'ShuiChengXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520281, 5202, '52,5202,520281', 3, '盘州市', 'PanZhouShi', 'PZS', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520302, 5203, '52,5203,520302', 3, '红花岗区', 'HongHuaGangQu', 'HHGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520303, 5203, '52,5203,520303', 3, '汇川区', 'HuiChuanQu', 'HCQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520304, 5203, '52,5203,520304', 3, '播州区', 'BoZhouQu', 'BZQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520322, 5203, '52,5203,520322', 3, '桐梓县', 'TongZiXian', 'TZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520323, 5203, '52,5203,520323', 3, '绥阳县', 'SuiYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520324, 5203, '52,5203,520324', 3, '正安县', 'ZhengAnXian', 'ZAX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520325, 5203, '52,5203,520325', 3, '道真仡佬族苗族自治县', 'DaoZhenGeLaoZuMiaoZuZiZhiXian', 'DZGLZMZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520326, 5203, '52,5203,520326', 3, '务川仡佬族苗族自治县', 'WuChuanGeLaoZuMiaoZuZiZhiXian', 'WCGLZMZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520327, 5203, '52,5203,520327', 3, '凤冈县', 'FengGangXian', 'FGX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520328, 5203, '52,5203,520328', 3, '湄潭县', 'MeiTanXian', 'MTX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520329, 5203, '52,5203,520329', 3, '余庆县', 'YuQingXian', 'YQX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520330, 5203, '52,5203,520330', 3, '习水县', 'XiShuiXian', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520381, 5203, '52,5203,520381', 3, '赤水市', 'ChiShuiShi', 'CSS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520382, 5203, '52,5203,520382', 3, '仁怀市', 'RenHuaiShi', 'RHS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520402, 5204, '52,5204,520402', 3, '西秀区', 'XiXiuQu', 'XXQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520403, 5204, '52,5204,520403', 3, '平坝区', 'PingBaQu', 'PBQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520422, 5204, '52,5204,520422', 3, '普定县', 'PuDingXian', 'PDX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520423, 5204, '52,5204,520423', 3, '镇宁布依族苗族自治县', 'ZhenNingBuYiZuMiaoZuZiZhiXian', 'ZNBYZMZZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520424, 5204, '52,5204,520424', 3, '关岭布依族苗族自治县', 'GuanLingBuYiZuMiaoZuZiZhiXian', 'GLBYZMZZZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520425, 5204, '52,5204,520425', 3, '紫云苗族布依族自治县', 'ZiYunMiaoZuBuYiZuZiZhiXian', 'ZYMZBYZZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520502, 5205, '52,5205,520502', 3, '七星关区', 'QiXingGuanQu', 'QXGQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520521, 5205, '52,5205,520521', 3, '大方县', 'DaFangXian', 'DFX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520522, 5205, '52,5205,520522', 3, '黔西县', 'QianXiXian', 'QXX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520523, 5205, '52,5205,520523', 3, '金沙县', 'JinShaXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520524, 5205, '52,5205,520524', 3, '织金县', 'ZhiJinXian', 'ZJX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520525, 5205, '52,5205,520525', 3, '纳雍县', 'NaYongXian', 'NYX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520526, 5205, '52,5205,520526', 3, '威宁彝族回族苗族自治县', 'WeiNingYiZuHuiZuMiaoZuZiZhiXian', 'WNYZHZMZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520527, 5205, '52,5205,520527', 3, '赫章县', 'HeZhangXian', 'HZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520602, 5206, '52,5206,520602', 3, '碧江区', 'BiJiangQu', 'BJQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520603, 5206, '52,5206,520603', 3, '万山区', 'WanShanQu', 'WSQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520621, 5206, '52,5206,520621', 3, '江口县', 'JiangKouXian', 'JKX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520622, 5206, '52,5206,520622', 3, '玉屏侗族自治县', 'YuPingDongZuZiZhiXian', 'YPDZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520623, 5206, '52,5206,520623', 3, '石阡县', 'ShiQianXian', 'SQX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520624, 5206, '52,5206,520624', 3, '思南县', 'SiNanXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520625, 5206, '52,5206,520625', 3, '印江土家族苗族自治县', 'YinJiangTuJiaZuMiaoZuZiZhiXian', 'YJTJZMZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520626, 5206, '52,5206,520626', 3, '德江县', 'DeJiangXian', 'DJX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520627, 5206, '52,5206,520627', 3, '沿河土家族自治县', 'YanHeTuJiaZuZiZhiXian', 'YHTJZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (520628, 5206, '52,5206,520628', 3, '松桃苗族自治县', 'SongTaoMiaoZuZiZhiXian', 'STMZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522301, 5223, '52,5223,522301', 3, '兴义市', 'XingYiShi', 'XYS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522302, 5223, '52,5223,522302', 3, '兴仁市', 'XingRenShi', 'XRS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522323, 5223, '52,5223,522323', 3, '普安县', 'PuAnXian', 'PAX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522324, 5223, '52,5223,522324', 3, '晴隆县', 'QingLongXian', 'QLX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522325, 5223, '52,5223,522325', 3, '贞丰县', 'ZhenFengXian', 'ZFX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522326, 5223, '52,5223,522326', 3, '望谟县', 'WangMoXian', 'WMX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522327, 5223, '52,5223,522327', 3, '册亨县', 'CeHengXian', 'CHX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522328, 5223, '52,5223,522328', 3, '安龙县', 'AnLongXian', 'ALX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522601, 5226, '52,5226,522601', 3, '凯里市', 'KaiLiShi', 'KLS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522622, 5226, '52,5226,522622', 3, '黄平县', 'HuangPingXian', 'HPX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522623, 5226, '52,5226,522623', 3, '施秉县', 'ShiBingXian', 'SBX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522624, 5226, '52,5226,522624', 3, '三穗县', 'SanSuiXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522625, 5226, '52,5226,522625', 3, '镇远县', 'ZhenYuanXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522626, 5226, '52,5226,522626', 3, '岑巩县', 'CenGongXian', 'CGX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522627, 5226, '52,5226,522627', 3, '天柱县', 'TianZhuXian', 'TZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522628, 5226, '52,5226,522628', 3, '锦屏县', 'JinPingXian', 'JPX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522629, 5226, '52,5226,522629', 3, '剑河县', 'JianHeXian', 'JHX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522630, 5226, '52,5226,522630', 3, '台江县', 'TaiJiangXian', 'TJX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522631, 5226, '52,5226,522631', 3, '黎平县', 'LiPingXian', 'LPX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522632, 5226, '52,5226,522632', 3, '榕江县', 'RongJiangXian', 'RJX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522633, 5226, '52,5226,522633', 3, '从江县', 'CongJiangXian', 'CJX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522634, 5226, '52,5226,522634', 3, '雷山县', 'LeiShanXian', 'LSX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522635, 5226, '52,5226,522635', 3, '麻江县', 'MaJiangXian', 'MJX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522636, 5226, '52,5226,522636', 3, '丹寨县', 'DanZhaiXian', 'DZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522701, 5227, '52,5227,522701', 3, '都匀市', 'DuYunShi', 'DYS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522702, 5227, '52,5227,522702', 3, '福泉市', 'FuQuanShi', 'FQS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522722, 5227, '52,5227,522722', 3, '荔波县', 'LiBoXian', 'LBX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522723, 5227, '52,5227,522723', 3, '贵定县', 'GuiDingXian', 'GDX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522725, 5227, '52,5227,522725', 3, '瓮安县', 'WengAnXian', 'WAX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522726, 5227, '52,5227,522726', 3, '独山县', 'DuShanXian', 'DSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522727, 5227, '52,5227,522727', 3, '平塘县', 'PingTangXian', 'PTX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522728, 5227, '52,5227,522728', 3, '罗甸县', 'LuoDianXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522729, 5227, '52,5227,522729', 3, '长顺县', 'ChangShunXian', 'CSX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522730, 5227, '52,5227,522730', 3, '龙里县', 'LongLiXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522731, 5227, '52,5227,522731', 3, '惠水县', 'HuiShuiXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (522732, 5227, '52,5227,522732', 3, '三都水族自治县', 'SanDuShuiZuZiZhiXian', 'SDSZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530102, 5301, '53,5301,530102', 3, '五华区', 'WuHuaQu', 'WHQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530103, 5301, '53,5301,530103', 3, '盘龙区', 'PanLongQu', 'PLQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530111, 5301, '53,5301,530111', 3, '官渡区', 'GuanDuQu', 'GDQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530112, 5301, '53,5301,530112', 3, '西山区', 'XiShanQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530113, 5301, '53,5301,530113', 3, '东川区', 'DongChuanQu', 'DCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530114, 5301, '53,5301,530114', 3, '呈贡区', 'ChengGongQu', 'CGQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530115, 5301, '53,5301,530115', 3, '晋宁区', 'JinNingQu', 'JNQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530124, 5301, '53,5301,530124', 3, '富民县', 'FuMinXian', 'FMX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530125, 5301, '53,5301,530125', 3, '宜良县', 'YiLiangXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530126, 5301, '53,5301,530126', 3, '石林彝族自治县', 'ShiLinYiZuZiZhiXian', 'SLYZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530127, 5301, '53,5301,530127', 3, '嵩明县', 'SongMingXian', 'SMX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530128, 5301, '53,5301,530128', 3, '禄劝彝族苗族自治县', 'LuQuanYiZuMiaoZuZiZhiXian', 'LQYZMZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530129, 5301, '53,5301,530129', 3, '寻甸回族彝族自治县', 'XunDianHuiZuYiZuZiZhiXian', 'XDHZYZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530181, 5301, '53,5301,530181', 3, '安宁市', 'AnNingShi', 'ANS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530302, 5303, '53,5303,530302', 3, '麒麟区', 'QiLinQu', 'QLQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530303, 5303, '53,5303,530303', 3, '沾益区', 'ZhanYiQu', 'ZYQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530304, 5303, '53,5303,530304', 3, '马龙区', 'MaLongQu', 'MLQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530322, 5303, '53,5303,530322', 3, '陆良县', 'LuLiangXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530323, 5303, '53,5303,530323', 3, '师宗县', 'ShiZongXian', 'SZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530324, 5303, '53,5303,530324', 3, '罗平县', 'LuoPingXian', 'LPX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530325, 5303, '53,5303,530325', 3, '富源县', 'FuYuanXian', 'FYX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530326, 5303, '53,5303,530326', 3, '会泽县', 'HuiZeXian', 'HZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530381, 5303, '53,5303,530381', 3, '宣威市', 'XuanWeiShi', 'XWS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530402, 5304, '53,5304,530402', 3, '红塔区', 'HongTaQu', 'HTQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530403, 5304, '53,5304,530403', 3, '江川区', 'JiangChuanQu', 'JCQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530422, 5304, '53,5304,530422', 3, '澄江县', 'ChengJiangXian', 'CJX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530423, 5304, '53,5304,530423', 3, '通海县', 'TongHaiXian', 'THX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530424, 5304, '53,5304,530424', 3, '华宁县', 'HuaNingXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530425, 5304, '53,5304,530425', 3, '易门县', 'YiMenXian', 'YMX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530426, 5304, '53,5304,530426', 3, '峨山彝族自治县', 'EShanYiZuZiZhiXian', 'ESYZZZX', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530427, 5304, '53,5304,530427', 3, '新平彝族傣族自治县', 'XinPingYiZuDaiZuZiZhiXian', 'XPYZDZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530428, 5304, '53,5304,530428', 3, '元江哈尼族彝族傣族自治县', 'YuanJiangHaNiZuYiZuDaiZuZiZhiXian', 'YJHNZYZDZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530502, 5305, '53,5305,530502', 3, '隆阳区', 'LongYangQu', 'LYQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530521, 5305, '53,5305,530521', 3, '施甸县', 'ShiDianXian', 'SDX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530523, 5305, '53,5305,530523', 3, '龙陵县', 'LongLingXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530524, 5305, '53,5305,530524', 3, '昌宁县', 'ChangNingXian', 'CNX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530581, 5305, '53,5305,530581', 3, '腾冲市', 'TengChongShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530602, 5306, '53,5306,530602', 3, '昭阳区', 'ZhaoYangQu', 'ZYQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530621, 5306, '53,5306,530621', 3, '鲁甸县', 'LuDianXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530622, 5306, '53,5306,530622', 3, '巧家县', 'QiaoJiaXian', 'QJX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530623, 5306, '53,5306,530623', 3, '盐津县', 'YanJinXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530624, 5306, '53,5306,530624', 3, '大关县', 'DaGuanXian', 'DGX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530625, 5306, '53,5306,530625', 3, '永善县', 'YongShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530626, 5306, '53,5306,530626', 3, '绥江县', 'SuiJiangXian', 'SJX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530627, 5306, '53,5306,530627', 3, '镇雄县', 'ZhenXiongXian', 'ZXX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530628, 5306, '53,5306,530628', 3, '彝良县', 'YiLiangXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530629, 5306, '53,5306,530629', 3, '威信县', 'WeiXinXian', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530681, 5306, '53,5306,530681', 3, '水富市', 'ShuiFuShi', 'SFS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530702, 5307, '53,5307,530702', 3, '古城区', 'GuChengQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530721, 5307, '53,5307,530721', 3, '玉龙纳西族自治县', 'YuLongNaXiZuZiZhiXian', 'YLNXZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530722, 5307, '53,5307,530722', 3, '永胜县', 'YongShengXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530723, 5307, '53,5307,530723', 3, '华坪县', 'HuaPingXian', 'HPX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530724, 5307, '53,5307,530724', 3, '宁蒗彝族自治县', 'NingLangYiZuZiZhiXian', 'NLYZZZX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530802, 5308, '53,5308,530802', 3, '思茅区', 'SiMaoQu', 'SMQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530821, 5308, '53,5308,530821', 3, '宁洱哈尼族彝族自治县', 'NingErHaNiZuYiZuZiZhiXian', 'NEHNZYZZZX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530822, 5308, '53,5308,530822', 3, '墨江哈尼族自治县', 'MoJiangHaNiZuZiZhiXian', 'MJHNZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530823, 5308, '53,5308,530823', 3, '景东彝族自治县', 'JingDongYiZuZiZhiXian', 'JDYZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530824, 5308, '53,5308,530824', 3, '景谷傣族彝族自治县', 'JingGuDaiZuYiZuZiZhiXian', 'JGDZYZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530825, 5308, '53,5308,530825', 3, '镇沅彝族哈尼族拉祜族自治县', 'ZhenYuanYiZuHaNiZuLaHuZuZiZhiXian', 'ZYYZHNZLHZZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530826, 5308, '53,5308,530826', 3, '江城哈尼族彝族自治县', 'JiangChengHaNiZuYiZuZiZhiXian', 'JCHNZYZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530827, 5308, '53,5308,530827', 3, '孟连傣族拉祜族佤族自治县', 'MengLianDaiZuLaHuZuWaZuZiZhiXian', 'MLDZLHZWZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530828, 5308, '53,5308,530828', 3, '澜沧拉祜族自治县', 'LanCangLaHuZuZiZhiXian', 'LCLHZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530829, 5308, '53,5308,530829', 3, '西盟佤族自治县', 'XiMengWaZuZiZhiXian', 'XMWZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530902, 5309, '53,5309,530902', 3, '临翔区', 'LinXiangQu', 'LXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530921, 5309, '53,5309,530921', 3, '凤庆县', 'FengQingXian', 'FQX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530922, 5309, '53,5309,530922', 3, '云县', 'YunXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530923, 5309, '53,5309,530923', 3, '永德县', 'YongDeXian', 'YDX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530924, 5309, '53,5309,530924', 3, '镇康县', 'ZhenKangXian', 'ZKX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530925, 5309, '53,5309,530925', 3, '双江拉祜族佤族布朗族傣族自治县', 'ShuangJiangLaHuZuWaZuBuLangZuDaiZuZiZhiXian', 'SJLHZWZBLZDZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530926, 5309, '53,5309,530926', 3, '耿马傣族佤族自治县', 'GengMaDaiZuWaZuZiZhiXian', 'GMDZWZZZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (530927, 5309, '53,5309,530927', 3, '沧源佤族自治县', 'CangYuanWaZuZiZhiXian', 'CYWZZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532301, 5323, '53,5323,532301', 3, '楚雄市', 'ChuXiongShi', 'CXS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532322, 5323, '53,5323,532322', 3, '双柏县', 'ShuangBaiXian', 'SBX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532323, 5323, '53,5323,532323', 3, '牟定县', 'MouDingXian', 'MDX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532324, 5323, '53,5323,532324', 3, '南华县', 'NanHuaXian', 'NHX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532325, 5323, '53,5323,532325', 3, '姚安县', 'YaoAnXian', 'YAX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532326, 5323, '53,5323,532326', 3, '大姚县', 'DaYaoXian', 'DYX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532327, 5323, '53,5323,532327', 3, '永仁县', 'YongRenXian', 'YRX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532328, 5323, '53,5323,532328', 3, '元谋县', 'YuanMouXian', 'YMX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532329, 5323, '53,5323,532329', 3, '武定县', 'WuDingXian', 'WDX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532331, 5323, '53,5323,532331', 3, '禄丰县', 'LuFengXian', 'LFX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532501, 5325, '53,5325,532501', 3, '个旧市', 'GeJiuShi', 'GJS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532502, 5325, '53,5325,532502', 3, '开远市', 'KaiYuanShi', 'KYS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532503, 5325, '53,5325,532503', 3, '蒙自市', 'MengZiShi', 'MZS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532504, 5325, '53,5325,532504', 3, '弥勒市', 'MiLeShi', 'MLS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532523, 5325, '53,5325,532523', 3, '屏边苗族自治县', 'PingBianMiaoZuZiZhiXian', 'PBMZZZX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532524, 5325, '53,5325,532524', 3, '建水县', 'JianShuiXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532525, 5325, '53,5325,532525', 3, '石屏县', 'ShiPingXian', 'SPX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532527, 5325, '53,5325,532527', 3, '泸西县', 'LuXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532528, 5325, '53,5325,532528', 3, '元阳县', 'YuanYangXian', 'YYX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532529, 5325, '53,5325,532529', 3, '红河县', 'HongHeXian', 'HHX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532530, 5325, '53,5325,532530', 3, '金平苗族瑶族傣族自治县', 'JinPingMiaoZuYaoZuDaiZuZiZhiXian', 'JPMZYZDZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532531, 5325, '53,5325,532531', 3, '绿春县', 'LyuChunXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532532, 5325, '53,5325,532532', 3, '河口瑶族自治县', 'HeKouYaoZuZiZhiXian', 'HKYZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532601, 5326, '53,5326,532601', 3, '文山市', 'WenShanShi', 'WSS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532622, 5326, '53,5326,532622', 3, '砚山县', 'YanShanXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532623, 5326, '53,5326,532623', 3, '西畴县', 'XiChouXian', 'XCX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532624, 5326, '53,5326,532624', 3, '麻栗坡县', 'MaLiPoXian', 'MLPX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532625, 5326, '53,5326,532625', 3, '马关县', 'MaGuanXian', 'MGX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532626, 5326, '53,5326,532626', 3, '丘北县', 'QiuBeiXian', 'QBX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532627, 5326, '53,5326,532627', 3, '广南县', 'GuangNanXian', 'GNX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532628, 5326, '53,5326,532628', 3, '富宁县', 'FuNingXian', 'FNX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532801, 5328, '53,5328,532801', 3, '景洪市', 'JingHongShi', 'JHS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532822, 5328, '53,5328,532822', 3, '勐海县', 'MengHaiXian', 'MHX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532823, 5328, '53,5328,532823', 3, '勐腊县', 'MengLaXian', 'MLX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532901, 5329, '53,5329,532901', 3, '大理市', 'DaLiShi', 'DLS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532922, 5329, '53,5329,532922', 3, '漾濞彝族自治县', 'YangBiYiZuZiZhiXian', 'YBYZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532923, 5329, '53,5329,532923', 3, '祥云县', 'XiangYunXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532924, 5329, '53,5329,532924', 3, '宾川县', 'BinChuanXian', 'BCX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532925, 5329, '53,5329,532925', 3, '弥渡县', 'MiDuXian', 'MDX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532926, 5329, '53,5329,532926', 3, '南涧彝族自治县', 'NanJianYiZuZiZhiXian', 'NJYZZZX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532927, 5329, '53,5329,532927', 3, '巍山彝族回族自治县', 'WeiShanYiZuHuiZuZiZhiXian', 'WSYZHZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532928, 5329, '53,5329,532928', 3, '永平县', 'YongPingXian', 'YPX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532929, 5329, '53,5329,532929', 3, '云龙县', 'YunLongXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532930, 5329, '53,5329,532930', 3, '洱源县', 'ErYuanXian', 'EYX', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532931, 5329, '53,5329,532931', 3, '剑川县', 'JianChuanXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (532932, 5329, '53,5329,532932', 3, '鹤庆县', 'HeQingXian', 'HQX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533102, 5331, '53,5331,533102', 3, '瑞丽市', 'RuiLiShi', 'RLS', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533103, 5331, '53,5331,533103', 3, '芒市', 'MangShi', 'MS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533122, 5331, '53,5331,533122', 3, '梁河县', 'LiangHeXian', 'LHX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533123, 5331, '53,5331,533123', 3, '盈江县', 'YingJiangXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533124, 5331, '53,5331,533124', 3, '陇川县', 'LongChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533301, 5333, '53,5333,533301', 3, '泸水市', 'LuShuiShi', 'LSS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533323, 5333, '53,5333,533323', 3, '福贡县', 'FuGongXian', 'FGX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533324, 5333, '53,5333,533324', 3, '贡山独龙族怒族自治县', 'GongShanDuLongZuNuZuZiZhiXian', 'GSDLZNZZZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533325, 5333, '53,5333,533325', 3, '兰坪白族普米族自治县', 'LanPingBaiZuPuMiZuZiZhiXian', 'LPBZPMZZZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533401, 5334, '53,5334,533401', 3, '香格里拉市', 'XiangGeLiLaShi', 'XGLLS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533422, 5334, '53,5334,533422', 3, '德钦县', 'DeQinXian', 'DQX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (533423, 5334, '53,5334,533423', 3, '维西傈僳族自治县', 'WeiXiLiSuZuZiZhiXian', 'WXLSZZZX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540102, 5401, '54,5401,540102', 3, '城关区', 'ChengGuanQu', 'CGQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540103, 5401, '54,5401,540103', 3, '堆龙德庆区', 'DuiLongDeQingQu', 'DLDQQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540104, 5401, '54,5401,540104', 3, '达孜区', 'DaZiQu', 'DZQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540121, 5401, '54,5401,540121', 3, '林周县', 'LinZhouXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540122, 5401, '54,5401,540122', 3, '当雄县', 'DangXiongXian', 'DXX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540123, 5401, '54,5401,540123', 3, '尼木县', 'NiMuXian', 'NMX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540124, 5401, '54,5401,540124', 3, '曲水县', 'QuShuiXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540127, 5401, '54,5401,540127', 3, '墨竹工卡县', 'MoZhuGongKaXian', 'MZGKX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540171, 5401, '54,5401,540171', 3, '格尔木藏青工业园区', 'GeErMuZangQingGongYeYuanQu', 'GEMZQGYYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540172, 5401, '54,5401,540172', 3, '拉萨经济技术开发区', 'LaSaJingJiJiShuKaiFaQu', 'LSJJJSKFQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540173, 5401, '54,5401,540173', 3, '西藏文化旅游创意园区', 'XiZangWenHuaLyuYouChuangYiYuanQu', 'XZWHLYCYYQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540174, 5401, '54,5401,540174', 3, '达孜工业园区', 'DaZiGongYeYuanQu', 'DZGYYQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540202, 5402, '54,5402,540202', 3, '桑珠孜区', 'SangZhuZiQu', 'SZZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540221, 5402, '54,5402,540221', 3, '南木林县', 'NanMuLinXian', 'NMLX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540222, 5402, '54,5402,540222', 3, '江孜县', 'JiangZiXian', 'JZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540223, 5402, '54,5402,540223', 3, '定日县', 'DingRiXian', 'DRX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540224, 5402, '54,5402,540224', 3, '萨迦县', 'SaJiaXian', 'SJX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540225, 5402, '54,5402,540225', 3, '拉孜县', 'LaZiXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540226, 5402, '54,5402,540226', 3, '昂仁县', 'AngRenXian', 'ARX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540227, 5402, '54,5402,540227', 3, '谢通门县', 'XieTongMenXian', 'XTMX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540228, 5402, '54,5402,540228', 3, '白朗县', 'BaiLangXian', 'BLX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540229, 5402, '54,5402,540229', 3, '仁布县', 'RenBuXian', 'RBX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540230, 5402, '54,5402,540230', 3, '康马县', 'KangMaXian', 'KMX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540231, 5402, '54,5402,540231', 3, '定结县', 'DingJieXian', 'DJX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540232, 5402, '54,5402,540232', 3, '仲巴县', 'ZhongBaXian', 'ZBX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540233, 5402, '54,5402,540233', 3, '亚东县', 'YaDongXian', 'YDX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540234, 5402, '54,5402,540234', 3, '吉隆县', 'JiLongXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540235, 5402, '54,5402,540235', 3, '聂拉木县', 'NieLaMuXian', 'NLMX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540236, 5402, '54,5402,540236', 3, '萨嘎县', 'SaGaXian', 'SGX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540237, 5402, '54,5402,540237', 3, '岗巴县', 'GangBaXian', 'GBX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540302, 5403, '54,5403,540302', 3, '卡若区', 'KaRuoQu', 'KRQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540321, 5403, '54,5403,540321', 3, '江达县', 'JiangDaXian', 'JDX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540322, 5403, '54,5403,540322', 3, '贡觉县', 'GongJueXian', 'GJX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540323, 5403, '54,5403,540323', 3, '类乌齐县', 'LeiWuQiXian', 'LWQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540324, 5403, '54,5403,540324', 3, '丁青县', 'DingQingXian', 'DQX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540325, 5403, '54,5403,540325', 3, '察雅县', 'ChaYaXian', 'CYX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540326, 5403, '54,5403,540326', 3, '八宿县', 'BaSuXian', 'BSX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540327, 5403, '54,5403,540327', 3, '左贡县', 'ZuoGongXian', 'ZGX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540328, 5403, '54,5403,540328', 3, '芒康县', 'MangKangXian', 'MKX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540329, 5403, '54,5403,540329', 3, '洛隆县', 'LuoLongXian', 'LLX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540330, 5403, '54,5403,540330', 3, '边坝县', 'BianBaXian', 'BBX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540402, 5404, '54,5404,540402', 3, '巴宜区', 'BaYiQu', 'BYQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540421, 5404, '54,5404,540421', 3, '工布江达县', 'GongBuJiangDaXian', 'GBJDX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540422, 5404, '54,5404,540422', 3, '米林县', 'MiLinXian', 'MLX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540423, 5404, '54,5404,540423', 3, '墨脱县', 'MoTuoXian', 'MTX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540424, 5404, '54,5404,540424', 3, '波密县', 'BoMiXian', 'BMX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540425, 5404, '54,5404,540425', 3, '察隅县', 'ChaYuXian', 'CYX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540426, 5404, '54,5404,540426', 3, '朗县', 'LangXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540502, 5405, '54,5405,540502', 3, '乃东区', 'NaiDongQu', 'NDQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540521, 5405, '54,5405,540521', 3, '扎囊县', 'ZaNangXian', 'ZNX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540522, 5405, '54,5405,540522', 3, '贡嘎县', 'GongGaXian', 'GGX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540523, 5405, '54,5405,540523', 3, '桑日县', 'SangRiXian', 'SRX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540524, 5405, '54,5405,540524', 3, '琼结县', 'QiongJieXian', 'QJX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540525, 5405, '54,5405,540525', 3, '曲松县', 'QuSongXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540526, 5405, '54,5405,540526', 3, '措美县', 'CuoMeiXian', 'CMX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540527, 5405, '54,5405,540527', 3, '洛扎县', 'LuoZhaXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540528, 5405, '54,5405,540528', 3, '加查县', 'JiaChaXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540529, 5405, '54,5405,540529', 3, '隆子县', 'LongZiXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540530, 5405, '54,5405,540530', 3, '错那县', 'CuoNaXian', 'CNX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540531, 5405, '54,5405,540531', 3, '浪卡子县', 'LangKaZiXian', 'LKZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540602, 5406, '54,5406,540602', 3, '色尼区', 'SeNiQu', 'SNQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540621, 5406, '54,5406,540621', 3, '嘉黎县', 'JiaLiXian', 'JLX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540622, 5406, '54,5406,540622', 3, '比如县', 'BiRuXian', 'BRX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540623, 5406, '54,5406,540623', 3, '聂荣县', 'NieRongXian', 'NRX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540624, 5406, '54,5406,540624', 3, '安多县', 'AnDuoXian', 'ADX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540625, 5406, '54,5406,540625', 3, '申扎县', 'ShenZhaXian', 'SZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540626, 5406, '54,5406,540626', 3, '索县', 'SuoXian', 'SX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540627, 5406, '54,5406,540627', 3, '班戈县', 'BanGeXian', 'BGX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540628, 5406, '54,5406,540628', 3, '巴青县', 'BaQingXian', 'BQX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540629, 5406, '54,5406,540629', 3, '尼玛县', 'NiMaXian', 'NMX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (540630, 5406, '54,5406,540630', 3, '双湖县', 'ShuangHuXian', 'SHX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542521, 5425, '54,5425,542521', 3, '普兰县', 'PuLanXian', 'PLX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542522, 5425, '54,5425,542522', 3, '札达县', 'ZhaDaXian', 'ZDX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542523, 5425, '54,5425,542523', 3, '噶尔县', 'GaErXian', 'GEX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542524, 5425, '54,5425,542524', 3, '日土县', 'RiTuXian', 'RTX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542525, 5425, '54,5425,542525', 3, '革吉县', 'GeJiXian', 'GJX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542526, 5425, '54,5425,542526', 3, '改则县', 'GaiZeXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (542527, 5425, '54,5425,542527', 3, '措勤县', 'CuoQinXian', 'CQX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610102, 6101, '61,6101,610102', 3, '新城区', 'XinChengQu', 'XCQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610103, 6101, '61,6101,610103', 3, '碑林区', 'BeiLinQu', 'BLQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610104, 6101, '61,6101,610104', 3, '莲湖区', 'LianHuQu', 'LHQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610111, 6101, '61,6101,610111', 3, '灞桥区', 'BaQiaoQu', 'BQQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610112, 6101, '61,6101,610112', 3, '未央区', 'WeiYangQu', 'WYQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610113, 6101, '61,6101,610113', 3, '雁塔区', 'YanTaQu', 'YTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610114, 6101, '61,6101,610114', 3, '阎良区', 'YanLiangQu', 'YLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610115, 6101, '61,6101,610115', 3, '临潼区', 'LinTongQu', 'LTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610116, 6101, '61,6101,610116', 3, '长安区', 'ChangAnQu', 'CAQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610117, 6101, '61,6101,610117', 3, '高陵区', 'GaoLingQu', 'GLQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610118, 6101, '61,6101,610118', 3, '鄠邑区', 'HuYiQu', 'HYQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610122, 6101, '61,6101,610122', 3, '蓝田县', 'LanTianXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610124, 6101, '61,6101,610124', 3, '周至县', 'ZhouZhiXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610202, 6102, '61,6102,610202', 3, '王益区', 'WangYiQu', 'WYQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610203, 6102, '61,6102,610203', 3, '印台区', 'YinTaiQu', 'YTQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610204, 6102, '61,6102,610204', 3, '耀州区', 'YaoZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610222, 6102, '61,6102,610222', 3, '宜君县', 'YiJunXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610302, 6103, '61,6103,610302', 3, '渭滨区', 'WeiBinQu', 'WBQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610303, 6103, '61,6103,610303', 3, '金台区', 'JinTaiQu', 'JTQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610304, 6103, '61,6103,610304', 3, '陈仓区', 'ChenCangQu', 'CCQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610322, 6103, '61,6103,610322', 3, '凤翔县', 'FengXiangXian', 'FXX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610323, 6103, '61,6103,610323', 3, '岐山县', 'QiShanXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610324, 6103, '61,6103,610324', 3, '扶风县', 'FuFengXian', 'FFX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610326, 6103, '61,6103,610326', 3, '眉县', 'MeiXian', 'MX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610327, 6103, '61,6103,610327', 3, '陇县', 'LongXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610328, 6103, '61,6103,610328', 3, '千阳县', 'QianYangXian', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610329, 6103, '61,6103,610329', 3, '麟游县', 'LinYouXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610330, 6103, '61,6103,610330', 3, '凤县', 'FengXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610331, 6103, '61,6103,610331', 3, '太白县', 'TaiBaiXian', 'TBX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610402, 6104, '61,6104,610402', 3, '秦都区', 'QinDuQu', 'QDQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610403, 6104, '61,6104,610403', 3, '杨陵区', 'YangLingQu', 'YLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610404, 6104, '61,6104,610404', 3, '渭城区', 'WeiChengQu', 'WCQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610422, 6104, '61,6104,610422', 3, '三原县', 'SanYuanXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610423, 6104, '61,6104,610423', 3, '泾阳县', 'JingYangXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610424, 6104, '61,6104,610424', 3, '乾县', 'QianXian', 'QX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610425, 6104, '61,6104,610425', 3, '礼泉县', 'LiQuanXian', 'LQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610426, 6104, '61,6104,610426', 3, '永寿县', 'YongShouXian', 'YSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610428, 6104, '61,6104,610428', 3, '长武县', 'ChangWuXian', 'CWX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610429, 6104, '61,6104,610429', 3, '旬邑县', 'XunYiXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610430, 6104, '61,6104,610430', 3, '淳化县', 'ChunHuaXian', 'CHX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610431, 6104, '61,6104,610431', 3, '武功县', 'WuGongXian', 'WGX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610481, 6104, '61,6104,610481', 3, '兴平市', 'XingPingShi', 'XPS', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610482, 6104, '61,6104,610482', 3, '彬州市', 'BinZhouShi', 'BZS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610502, 6105, '61,6105,610502', 3, '临渭区', 'LinWeiQu', 'LWQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610503, 6105, '61,6105,610503', 3, '华州区', 'HuaZhouQu', 'HZQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610522, 6105, '61,6105,610522', 3, '潼关县', 'TongGuanXian', 'TGX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610523, 6105, '61,6105,610523', 3, '大荔县', 'DaLiXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610524, 6105, '61,6105,610524', 3, '合阳县', 'HeYangXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610525, 6105, '61,6105,610525', 3, '澄城县', 'ChengChengXian', 'CCX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610526, 6105, '61,6105,610526', 3, '蒲城县', 'PuChengXian', 'PCX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610527, 6105, '61,6105,610527', 3, '白水县', 'BaiShuiXian', 'BSX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610528, 6105, '61,6105,610528', 3, '富平县', 'FuPingXian', 'FPX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610581, 6105, '61,6105,610581', 3, '韩城市', 'HanChengShi', 'HCS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610582, 6105, '61,6105,610582', 3, '华阴市', 'HuaYinShi', 'HYS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610602, 6106, '61,6106,610602', 3, '宝塔区', 'BaoTaQu', 'BTQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610603, 6106, '61,6106,610603', 3, '安塞区', 'AnSaiQu', 'ASQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610621, 6106, '61,6106,610621', 3, '延长县', 'YanChangXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610622, 6106, '61,6106,610622', 3, '延川县', 'YanChuanXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610625, 6106, '61,6106,610625', 3, '志丹县', 'ZhiDanXian', 'ZDX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610626, 6106, '61,6106,610626', 3, '吴起县', 'WuQiXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610627, 6106, '61,6106,610627', 3, '甘泉县', 'GanQuanXian', 'GQX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610628, 6106, '61,6106,610628', 3, '富县', 'FuXian', 'FX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610629, 6106, '61,6106,610629', 3, '洛川县', 'LuoChuanXian', 'LCX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610630, 6106, '61,6106,610630', 3, '宜川县', 'YiChuanXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610631, 6106, '61,6106,610631', 3, '黄龙县', 'HuangLongXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610632, 6106, '61,6106,610632', 3, '黄陵县', 'HuangLingXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610681, 6106, '61,6106,610681', 3, '子长市', 'ZiChangShi', 'ZCS', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610702, 6107, '61,6107,610702', 3, '汉台区', 'HanTaiQu', 'HTQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610703, 6107, '61,6107,610703', 3, '南郑区', 'NanZhengQu', 'NZQ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610722, 6107, '61,6107,610722', 3, '城固县', 'ChengGuXian', 'CGX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610723, 6107, '61,6107,610723', 3, '洋县', 'YangXian', 'YX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610724, 6107, '61,6107,610724', 3, '西乡县', 'XiXiangXian', 'XXX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610725, 6107, '61,6107,610725', 3, '勉县', 'MianXian', 'MX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610726, 6107, '61,6107,610726', 3, '宁强县', 'NingQiangXian', 'NQX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610727, 6107, '61,6107,610727', 3, '略阳县', 'LueYangXian', 'LYX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610728, 6107, '61,6107,610728', 3, '镇巴县', 'ZhenBaXian', 'ZBX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610729, 6107, '61,6107,610729', 3, '留坝县', 'LiuBaXian', 'LBX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610730, 6107, '61,6107,610730', 3, '佛坪县', 'FoPingXian', 'FPX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610802, 6108, '61,6108,610802', 3, '榆阳区', 'YuYangQu', 'YYQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610803, 6108, '61,6108,610803', 3, '横山区', 'HengShanQu', 'HSQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610822, 6108, '61,6108,610822', 3, '府谷县', 'FuGuXian', 'FGX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610824, 6108, '61,6108,610824', 3, '靖边县', 'JingBianXian', 'JBX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610825, 6108, '61,6108,610825', 3, '定边县', 'DingBianXian', 'DBX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610826, 6108, '61,6108,610826', 3, '绥德县', 'SuiDeXian', 'SDX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610827, 6108, '61,6108,610827', 3, '米脂县', 'MiZhiXian', 'MZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610828, 6108, '61,6108,610828', 3, '佳县', 'JiaXian', 'JX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610829, 6108, '61,6108,610829', 3, '吴堡县', 'WuBuXian', 'WBX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610830, 6108, '61,6108,610830', 3, '清涧县', 'QingJianXian', 'QJX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610831, 6108, '61,6108,610831', 3, '子洲县', 'ZiZhouXian', 'ZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610881, 6108, '61,6108,610881', 3, '神木市', 'ShenMuShi', 'SMS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610902, 6109, '61,6109,610902', 3, '汉滨区', 'HanBinQu', 'HBQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610921, 6109, '61,6109,610921', 3, '汉阴县', 'HanYinXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610922, 6109, '61,6109,610922', 3, '石泉县', 'ShiQuanXian', 'SQX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610923, 6109, '61,6109,610923', 3, '宁陕县', 'NingShanXian', 'NSX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610924, 6109, '61,6109,610924', 3, '紫阳县', 'ZiYangXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610925, 6109, '61,6109,610925', 3, '岚皋县', 'LanGaoXian', 'LGX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610926, 6109, '61,6109,610926', 3, '平利县', 'PingLiXian', 'PLX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610927, 6109, '61,6109,610927', 3, '镇坪县', 'ZhenPingXian', 'ZPX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610928, 6109, '61,6109,610928', 3, '旬阳县', 'XunYangXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (610929, 6109, '61,6109,610929', 3, '白河县', 'BaiHeXian', 'BHX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611002, 6110, '61,6110,611002', 3, '商州区', 'ShangZhouQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611021, 6110, '61,6110,611021', 3, '洛南县', 'LuoNanXian', 'LNX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611022, 6110, '61,6110,611022', 3, '丹凤县', 'DanFengXian', 'DFX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611023, 6110, '61,6110,611023', 3, '商南县', 'ShangNanXian', 'SNX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611024, 6110, '61,6110,611024', 3, '山阳县', 'ShanYangXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611025, 6110, '61,6110,611025', 3, '镇安县', 'ZhenAnXian', 'ZAX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (611026, 6110, '61,6110,611026', 3, '柞水县', 'ZhaShuiXian', 'ZSX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620102, 6201, '62,6201,620102', 3, '城关区', 'ChengGuanQu', 'CGQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620103, 6201, '62,6201,620103', 3, '七里河区', 'QiLiHeQu', 'QLHQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620104, 6201, '62,6201,620104', 3, '西固区', 'XiGuQu', 'XGQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620105, 6201, '62,6201,620105', 3, '安宁区', 'AnNingQu', 'ANQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620111, 6201, '62,6201,620111', 3, '红古区', 'HongGuQu', 'HGQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620121, 6201, '62,6201,620121', 3, '永登县', 'YongDengXian', 'YDX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620122, 6201, '62,6201,620122', 3, '皋兰县', 'GaoLanXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620123, 6201, '62,6201,620123', 3, '榆中县', 'YuZhongXian', 'YZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620171, 6201, '62,6201,620171', 3, '兰州新区', 'LanZhouXinQu', 'LZXQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620201, 6202, '62,6202,620201', 3, '嘉峪关市', 'JiaYuGuanShi', 'JYGS', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620302, 6203, '62,6203,620302', 3, '金川区', 'JinChuanQu', 'JCQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620321, 6203, '62,6203,620321', 3, '永昌县', 'YongChangXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620402, 6204, '62,6204,620402', 3, '白银区', 'BaiYinQu', 'BYQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620403, 6204, '62,6204,620403', 3, '平川区', 'PingChuanQu', 'PCQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620421, 6204, '62,6204,620421', 3, '靖远县', 'JingYuanXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620422, 6204, '62,6204,620422', 3, '会宁县', 'HuiNingXian', 'HNX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620423, 6204, '62,6204,620423', 3, '景泰县', 'JingTaiXian', 'JTX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620502, 6205, '62,6205,620502', 3, '秦州区', 'QinZhouQu', 'QZQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620503, 6205, '62,6205,620503', 3, '麦积区', 'MaiJiQu', 'MJQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620521, 6205, '62,6205,620521', 3, '清水县', 'QingShuiXian', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620522, 6205, '62,6205,620522', 3, '秦安县', 'QinAnXian', 'QAX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620523, 6205, '62,6205,620523', 3, '甘谷县', 'GanGuXian', 'GGX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620524, 6205, '62,6205,620524', 3, '武山县', 'WuShanXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620525, 6205, '62,6205,620525', 3, '张家川回族自治县', 'ZhangJiaChuanHuiZuZiZhiXian', 'ZJCHZZZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620602, 6206, '62,6206,620602', 3, '凉州区', 'LiangZhouQu', 'LZQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620621, 6206, '62,6206,620621', 3, '民勤县', 'MinQinXian', 'MQX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620622, 6206, '62,6206,620622', 3, '古浪县', 'GuLangXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620623, 6206, '62,6206,620623', 3, '天祝藏族自治县', 'TianZhuZangZuZiZhiXian', 'TZZZZZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620702, 6207, '62,6207,620702', 3, '甘州区', 'GanZhouQu', 'GZQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620721, 6207, '62,6207,620721', 3, '肃南裕固族自治县', 'SuNanYuGuZuZiZhiXian', 'SNYGZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620722, 6207, '62,6207,620722', 3, '民乐县', 'MinYueXian', 'MYX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620723, 6207, '62,6207,620723', 3, '临泽县', 'LinZeXian', 'LZX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620724, 6207, '62,6207,620724', 3, '高台县', 'GaoTaiXian', 'GTX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620725, 6207, '62,6207,620725', 3, '山丹县', 'ShanDanXian', 'SDX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620802, 6208, '62,6208,620802', 3, '崆峒区', 'KongTongQu', 'KTQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620821, 6208, '62,6208,620821', 3, '泾川县', 'JingChuanXian', 'JCX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620822, 6208, '62,6208,620822', 3, '灵台县', 'LingTaiXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620823, 6208, '62,6208,620823', 3, '崇信县', 'ChongXinXian', 'CXX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620825, 6208, '62,6208,620825', 3, '庄浪县', 'ZhuangLangXian', 'ZLX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620826, 6208, '62,6208,620826', 3, '静宁县', 'JingNingXian', 'JNX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620881, 6208, '62,6208,620881', 3, '华亭市', 'HuaTingShi', 'HTS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620902, 6209, '62,6209,620902', 3, '肃州区', 'SuZhouQu', 'SZQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620921, 6209, '62,6209,620921', 3, '金塔县', 'JinTaXian', 'JTX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620922, 6209, '62,6209,620922', 3, '瓜州县', 'GuaZhouXian', 'GZX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620923, 6209, '62,6209,620923', 3, '肃北蒙古族自治县', 'SuBeiMengGuZuZiZhiXian', 'SBMGZZZX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620924, 6209, '62,6209,620924', 3, '阿克塞哈萨克族自治县', 'AKeSaiHaSaKeZuZiZhiXian', 'AKSHSKZZZX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620981, 6209, '62,6209,620981', 3, '玉门市', 'YuMenShi', 'YMS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (620982, 6209, '62,6209,620982', 3, '敦煌市', 'DunHuangShi', 'DHS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621002, 6210, '62,6210,621002', 3, '西峰区', 'XiFengQu', 'XFQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621021, 6210, '62,6210,621021', 3, '庆城县', 'QingChengXian', 'QCX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621022, 6210, '62,6210,621022', 3, '环县', 'HuanXian', 'HX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621023, 6210, '62,6210,621023', 3, '华池县', 'HuaChiXian', 'HCX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621024, 6210, '62,6210,621024', 3, '合水县', 'HeShuiXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621025, 6210, '62,6210,621025', 3, '正宁县', 'ZhengNingXian', 'ZNX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621026, 6210, '62,6210,621026', 3, '宁县', 'NingXian', 'NX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621027, 6210, '62,6210,621027', 3, '镇原县', 'ZhenYuanXian', 'ZYX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621102, 6211, '62,6211,621102', 3, '安定区', 'AnDingQu', 'ADQ', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621121, 6211, '62,6211,621121', 3, '通渭县', 'TongWeiXian', 'TWX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621122, 6211, '62,6211,621122', 3, '陇西县', 'LongXiXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621123, 6211, '62,6211,621123', 3, '渭源县', 'WeiYuanXian', 'WYX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621124, 6211, '62,6211,621124', 3, '临洮县', 'LinTaoXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621125, 6211, '62,6211,621125', 3, '漳县', 'ZhangXian', 'ZX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621126, 6211, '62,6211,621126', 3, '岷县', 'MinXian', 'MX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621202, 6212, '62,6212,621202', 3, '武都区', 'WuDuQu', 'WDQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621221, 6212, '62,6212,621221', 3, '成县', 'ChengXian', 'CX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621222, 6212, '62,6212,621222', 3, '文县', 'WenXian', 'WX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621223, 6212, '62,6212,621223', 3, '宕昌县', 'DangChangXian', 'DCX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621224, 6212, '62,6212,621224', 3, '康县', 'KangXian', 'KX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621225, 6212, '62,6212,621225', 3, '西和县', 'XiHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621226, 6212, '62,6212,621226', 3, '礼县', 'LiXian', 'LX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621227, 6212, '62,6212,621227', 3, '徽县', 'HuiXian', 'HX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (621228, 6212, '62,6212,621228', 3, '两当县', 'LiangDangXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622901, 6229, '62,6229,622901', 3, '临夏市', 'LinXiaShi', 'LXS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622921, 6229, '62,6229,622921', 3, '临夏县', 'LinXiaXian', 'LXX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622922, 6229, '62,6229,622922', 3, '康乐县', 'KangLeXian', 'KLX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622923, 6229, '62,6229,622923', 3, '永靖县', 'YongJingXian', 'YJX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622924, 6229, '62,6229,622924', 3, '广河县', 'GuangHeXian', 'GHX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622925, 6229, '62,6229,622925', 3, '和政县', 'HeZhengXian', 'HZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622926, 6229, '62,6229,622926', 3, '东乡族自治县', 'DongXiangZuZiZhiXian', 'DXZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (622927, 6229, '62,6229,622927', 3, '积石山保安族东乡族撒拉族自治县', 'JiShiShanBaoAnZuDongXiangZuSaLaZuZiZhiXian', 'JSSBAZDXZSLZZZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623001, 6230, '62,6230,623001', 3, '合作市', 'HeZuoShi', 'HZS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623021, 6230, '62,6230,623021', 3, '临潭县', 'LinTanXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623022, 6230, '62,6230,623022', 3, '卓尼县', 'ZhuoNiXian', 'ZNX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623023, 6230, '62,6230,623023', 3, '舟曲县', 'ZhouQuXian', 'ZQX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623024, 6230, '62,6230,623024', 3, '迭部县', 'DieBuXian', 'DBX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623025, 6230, '62,6230,623025', 3, '玛曲县', 'MaQuXian', 'MQX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623026, 6230, '62,6230,623026', 3, '碌曲县', 'LuQuXian', 'LQX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (623027, 6230, '62,6230,623027', 3, '夏河县', 'XiaHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630102, 6301, '63,6301,630102', 3, '城东区', 'ChengDongQu', 'CDQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630103, 6301, '63,6301,630103', 3, '城中区', 'ChengZhongQu', 'CZQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630104, 6301, '63,6301,630104', 3, '城西区', 'ChengXiQu', 'CXQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630105, 6301, '63,6301,630105', 3, '城北区', 'ChengBeiQu', 'CBQ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630121, 6301, '63,6301,630121', 3, '大通回族土族自治县', 'DaTongHuiZuTuZuZiZhiXian', 'DTHZTZZZX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630122, 6301, '63,6301,630122', 3, '湟中县', 'HuangZhongXian', 'HZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630123, 6301, '63,6301,630123', 3, '湟源县', 'HuangYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630202, 6302, '63,6302,630202', 3, '乐都区', 'LeDouQu', 'LDQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630203, 6302, '63,6302,630203', 3, '平安区', 'PingAnQu', 'PAQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630222, 6302, '63,6302,630222', 3, '民和回族土族自治县', 'MinHeHuiZuTuZuZiZhiXian', 'MHHZTZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630223, 6302, '63,6302,630223', 3, '互助土族自治县', 'HuZhuTuZuZiZhiXian', 'HZTZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630224, 6302, '63,6302,630224', 3, '化隆回族自治县', 'HuaLongHuiZuZiZhiXian', 'HLHZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (630225, 6302, '63,6302,630225', 3, '循化撒拉族自治县', 'XunHuaSaLaZuZiZhiXian', 'XHSLZZZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632221, 6322, '63,6322,632221', 3, '门源回族自治县', 'MenYuanHuiZuZiZhiXian', 'MYHZZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632222, 6322, '63,6322,632222', 3, '祁连县', 'QiLianXian', 'QLX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632223, 6322, '63,6322,632223', 3, '海晏县', 'HaiYanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632224, 6322, '63,6322,632224', 3, '刚察县', 'GangChaXian', 'GCX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632321, 6323, '63,6323,632321', 3, '同仁县', 'TongRenXian', 'TRX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632322, 6323, '63,6323,632322', 3, '尖扎县', 'JianZhaXian', 'JZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632323, 6323, '63,6323,632323', 3, '泽库县', 'ZeKuXian', 'ZKX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632324, 6323, '63,6323,632324', 3, '河南蒙古族自治县', 'HeNanMengGuZuZiZhiXian', 'HNMGZZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632521, 6325, '63,6325,632521', 3, '共和县', 'GongHeXian', 'GHX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632522, 6325, '63,6325,632522', 3, '同德县', 'TongDeXian', 'TDX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632523, 6325, '63,6325,632523', 3, '贵德县', 'GuiDeXian', 'GDX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632524, 6325, '63,6325,632524', 3, '兴海县', 'XingHaiXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632525, 6325, '63,6325,632525', 3, '贵南县', 'GuiNanXian', 'GNX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632621, 6326, '63,6326,632621', 3, '玛沁县', 'MaQinXian', 'MQX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632622, 6326, '63,6326,632622', 3, '班玛县', 'BanMaXian', 'BMX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632623, 6326, '63,6326,632623', 3, '甘德县', 'GanDeXian', 'GDX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632624, 6326, '63,6326,632624', 3, '达日县', 'DaRiXian', 'DRX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632625, 6326, '63,6326,632625', 3, '久治县', 'JiuZhiXian', 'JZX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632626, 6326, '63,6326,632626', 3, '玛多县', 'MaDuoXian', 'MDX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632701, 6327, '63,6327,632701', 3, '玉树市', 'YuShuShi', 'YSS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632722, 6327, '63,6327,632722', 3, '杂多县', 'ZaDuoXian', 'ZDX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632723, 6327, '63,6327,632723', 3, '称多县', 'ChenDuoXian', 'CDX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632724, 6327, '63,6327,632724', 3, '治多县', 'ZhiDuoXian', 'ZDX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632725, 6327, '63,6327,632725', 3, '囊谦县', 'NangQianXian', 'NQX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632726, 6327, '63,6327,632726', 3, '曲麻莱县', 'QuMaLaiXian', 'QMLX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632801, 6328, '63,6328,632801', 3, '格尔木市', 'GeErMuShi', 'GEMS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632802, 6328, '63,6328,632802', 3, '德令哈市', 'DeLingHaShi', 'DLHS', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632803, 6328, '63,6328,632803', 3, '茫崖市', 'MangYaShi', 'MYS', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632821, 6328, '63,6328,632821', 3, '乌兰县', 'WuLanXian', 'WLX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632822, 6328, '63,6328,632822', 3, '都兰县', 'DuLanXian', 'DLX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632823, 6328, '63,6328,632823', 3, '天峻县', 'TianJunXian', 'TJX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (632857, 6328, '63,6328,632857', 3, '大柴旦行政委员会', 'DaChaiDanXingZhengWeiYuanHui', 'DCDXZWYH', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640104, 6401, '64,6401,640104', 3, '兴庆区', 'XingQingQu', 'XQQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640105, 6401, '64,6401,640105', 3, '西夏区', 'XiXiaQu', 'XXQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640106, 6401, '64,6401,640106', 3, '金凤区', 'JinFengQu', 'JFQ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640121, 6401, '64,6401,640121', 3, '永宁县', 'YongNingXian', 'YNX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640122, 6401, '64,6401,640122', 3, '贺兰县', 'HeLanXian', 'HLX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640181, 6401, '64,6401,640181', 3, '灵武市', 'LingWuShi', 'LWS', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640202, 6402, '64,6402,640202', 3, '大武口区', 'DaWuKouQu', 'DWKQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640205, 6402, '64,6402,640205', 3, '惠农区', 'HuiNongQu', 'HNQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640221, 6402, '64,6402,640221', 3, '平罗县', 'PingLuoXian', 'PLX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640302, 6403, '64,6403,640302', 3, '利通区', 'LiTongQu', 'LTQ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640303, 6403, '64,6403,640303', 3, '红寺堡区', 'HongSiBaoQu', 'HSBQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640323, 6403, '64,6403,640323', 3, '盐池县', 'YanChiXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640324, 6403, '64,6403,640324', 3, '同心县', 'TongXinXian', 'TXX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640381, 6403, '64,6403,640381', 3, '青铜峡市', 'QingTongXiaShi', 'QTXS', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640402, 6404, '64,6404,640402', 3, '原州区', 'YuanZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640422, 6404, '64,6404,640422', 3, '西吉县', 'XiJiXian', 'XJX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640423, 6404, '64,6404,640423', 3, '隆德县', 'LongDeXian', 'LDX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640424, 6404, '64,6404,640424', 3, '泾源县', 'JingYuanXian', 'JYX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640425, 6404, '64,6404,640425', 3, '彭阳县', 'PengYangXian', 'PYX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640502, 6405, '64,6405,640502', 3, '沙坡头区', 'ShaPoTouQu', 'SPTQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640521, 6405, '64,6405,640521', 3, '中宁县', 'ZhongNingXian', 'ZNX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (640522, 6405, '64,6405,640522', 3, '海原县', 'HaiYuanXian', 'HYX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650102, 6501, '65,6501,650102', 3, '天山区', 'TianShanQu', 'TSQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650103, 6501, '65,6501,650103', 3, '沙依巴克区', 'ShaYiBaKeQu', 'SYBKQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650104, 6501, '65,6501,650104', 3, '新市区', 'XinShiQu', 'XSQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650105, 6501, '65,6501,650105', 3, '水磨沟区', 'ShuiMoGouQu', 'SMGQ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650106, 6501, '65,6501,650106', 3, '头屯河区', 'TouTunHeQu', 'TTHQ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650107, 6501, '65,6501,650107', 3, '达坂城区', 'DaBanChengQu', 'DBCQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650109, 6501, '65,6501,650109', 3, '米东区', 'MiDongQu', 'MDQ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650121, 6501, '65,6501,650121', 3, '乌鲁木齐县', 'WuLuMuQiXian', 'WLMQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650202, 6502, '65,6502,650202', 3, '独山子区', 'DuShanZiQu', 'DSZQ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650203, 6502, '65,6502,650203', 3, '克拉玛依区', 'KeLaMaYiQu', 'KLMYQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650204, 6502, '65,6502,650204', 3, '白碱滩区', 'BaiJianTanQu', 'BJTQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650205, 6502, '65,6502,650205', 3, '乌尔禾区', 'WuErHeQu', 'WEHQ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650402, 6504, '65,6504,650402', 3, '高昌区', 'GaoChangQu', 'GCQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650421, 6504, '65,6504,650421', 3, '鄯善县', 'ShanShanXian', 'SSX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650422, 6504, '65,6504,650422', 3, '托克逊县', 'TuoKeXunXian', 'TKXX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650502, 6505, '65,6505,650502', 3, '伊州区', 'YiZhouQu', 'YZQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650521, 6505, '65,6505,650521', 3, '巴里坤哈萨克自治县', 'BaLiKunHaSaKeZiZhiXian', 'BLKHSKZZX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (650522, 6505, '65,6505,650522', 3, '伊吾县', 'YiWuXian', 'YWX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652301, 6523, '65,6523,652301', 3, '昌吉市', 'ChangJiShi', 'CJS', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652302, 6523, '65,6523,652302', 3, '阜康市', 'FuKangShi', 'FKS', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652323, 6523, '65,6523,652323', 3, '呼图壁县', 'HuTuBiXian', 'HTBX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652324, 6523, '65,6523,652324', 3, '玛纳斯县', 'MaNaSiXian', 'MNSX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652325, 6523, '65,6523,652325', 3, '奇台县', 'QiTaiXian', 'QTX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652327, 6523, '65,6523,652327', 3, '吉木萨尔县', 'JiMuSaErXian', 'JMSEX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652328, 6523, '65,6523,652328', 3, '木垒哈萨克自治县', 'MuLeiHaSaKeZiZhiXian', 'MLHSKZZX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652701, 6527, '65,6527,652701', 3, '博乐市', 'BoLeShi', 'BLS', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652702, 6527, '65,6527,652702', 3, '阿拉山口市', 'ALaShanKouShi', 'ALSKS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652722, 6527, '65,6527,652722', 3, '精河县', 'JingHeXian', 'JHX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652723, 6527, '65,6527,652723', 3, '温泉县', 'WenQuanXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652801, 6528, '65,6528,652801', 3, '库尔勒市', 'KuErLeShi', 'KELS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652822, 6528, '65,6528,652822', 3, '轮台县', 'LunTaiXian', 'LTX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652823, 6528, '65,6528,652823', 3, '尉犁县', 'YuLiXian', 'YLX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652824, 6528, '65,6528,652824', 3, '若羌县', 'RuoQiangXian', 'RQX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652825, 6528, '65,6528,652825', 3, '且末县', 'QieMoXian', 'QMX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652826, 6528, '65,6528,652826', 3, '焉耆回族自治县', 'YanQiHuiZuZiZhiXian', 'YQHZZZX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652827, 6528, '65,6528,652827', 3, '和静县', 'HeJingXian', 'HJX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652828, 6528, '65,6528,652828', 3, '和硕县', 'HeShuoXian', 'HSX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652829, 6528, '65,6528,652829', 3, '博湖县', 'BoHuXian', 'BHX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652871, 6528, '65,6528,652871', 3, '库尔勒经济技术开发区', 'KuErLeJingJiJiShuKaiFaQu', 'KELJJJSKFQ', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652901, 6529, '65,6529,652901', 3, '阿克苏市', 'AKeSuShi', 'AKSS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652922, 6529, '65,6529,652922', 3, '温宿县', 'WenSuXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652923, 6529, '65,6529,652923', 3, '库车县', 'KuCheXian', 'KCX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652924, 6529, '65,6529,652924', 3, '沙雅县', 'ShaYaXian', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652925, 6529, '65,6529,652925', 3, '新和县', 'XinHeXian', 'XHX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652926, 6529, '65,6529,652926', 3, '拜城县', 'BaiChengXian', 'BCX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652927, 6529, '65,6529,652927', 3, '乌什县', 'WuShiXian', 'WSX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652928, 6529, '65,6529,652928', 3, '阿瓦提县', 'AWaTiXian', 'AWTX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (652929, 6529, '65,6529,652929', 3, '柯坪县', 'KePingXian', 'KPX', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653001, 6530, '65,6530,653001', 3, '阿图什市', 'ATuShiShi', 'ATSS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653022, 6530, '65,6530,653022', 3, '阿克陶县', 'AKeTaoXian', 'AKTX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653023, 6530, '65,6530,653023', 3, '阿合奇县', 'AHeQiXian', 'AHQX', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653024, 6530, '65,6530,653024', 3, '乌恰县', 'WuQiaXian', 'WQX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653101, 6531, '65,6531,653101', 3, '喀什市', 'KaShiShi', 'KSS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653121, 6531, '65,6531,653121', 3, '疏附县', 'ShuFuXian', 'SFX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653122, 6531, '65,6531,653122', 3, '疏勒县', 'ShuLeXian', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653123, 6531, '65,6531,653123', 3, '英吉沙县', 'YingJiShaXian', 'YJSX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653124, 6531, '65,6531,653124', 3, '泽普县', 'ZePuXian', 'ZPX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653125, 6531, '65,6531,653125', 3, '莎车县', 'ShaCheXian', 'SCX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653126, 6531, '65,6531,653126', 3, '叶城县', 'YeChengXian', 'YCX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653127, 6531, '65,6531,653127', 3, '麦盖提县', 'MaiGeTiXian', 'MGTX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653128, 6531, '65,6531,653128', 3, '岳普湖县', 'YuePuHuXian', 'YPHX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653129, 6531, '65,6531,653129', 3, '伽师县', 'JiaShiXian', 'JSX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653130, 6531, '65,6531,653130', 3, '巴楚县', 'BaChuXian', 'BCX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653131, 6531, '65,6531,653131', 3, '塔什库尔干塔吉克自治县', 'TaShiKuErGanTaJiKeZiZhiXian', 'TSKEGTJKZZX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653201, 6532, '65,6532,653201', 3, '和田市', 'HeTianShi', 'HTS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653221, 6532, '65,6532,653221', 3, '和田县', 'HeTianXian', 'HTX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653222, 6532, '65,6532,653222', 3, '墨玉县', 'MoYuXian', 'MYX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653223, 6532, '65,6532,653223', 3, '皮山县', 'PiShanXian', 'PSX', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653224, 6532, '65,6532,653224', 3, '洛浦县', 'LuoPuXian', 'LPX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653225, 6532, '65,6532,653225', 3, '策勒县', 'CeLeXian', 'CLX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653226, 6532, '65,6532,653226', 3, '于田县', 'YuTianXian', 'YTX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (653227, 6532, '65,6532,653227', 3, '民丰县', 'MinFengXian', 'MFX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654002, 6540, '65,6540,654002', 3, '伊宁市', 'YiNingShi', 'YNS', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654003, 6540, '65,6540,654003', 3, '奎屯市', 'KuiTunShi', 'KTS', 'K', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654004, 6540, '65,6540,654004', 3, '霍尔果斯市', 'HuoErGuoSiShi', 'HEGSS', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654021, 6540, '65,6540,654021', 3, '伊宁县', 'YiNingXian', 'YNX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654022, 6540, '65,6540,654022', 3, '察布查尔锡伯自治县', 'ChaBuChaErXiBoZiZhiXian', 'CBCEXBZZX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654023, 6540, '65,6540,654023', 3, '霍城县', 'HuoChengXian', 'HCX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654024, 6540, '65,6540,654024', 3, '巩留县', 'GongLiuXian', 'GLX', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654025, 6540, '65,6540,654025', 3, '新源县', 'XinYuanXian', 'XYX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654026, 6540, '65,6540,654026', 3, '昭苏县', 'ZhaoSuXian', 'ZSX', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654027, 6540, '65,6540,654027', 3, '特克斯县', 'TeKeSiXian', 'TKSX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654028, 6540, '65,6540,654028', 3, '尼勒克县', 'NiLeKeXian', 'NLKX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654201, 6542, '65,6542,654201', 3, '塔城市', 'TaChengShi', 'TCS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654202, 6542, '65,6542,654202', 3, '乌苏市', 'WuSuShi', 'WSS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654221, 6542, '65,6542,654221', 3, '额敏县', 'EMinXian', 'EMX', 'E', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654223, 6542, '65,6542,654223', 3, '沙湾县', 'ShaWanXian', 'SWX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654224, 6542, '65,6542,654224', 3, '托里县', 'TuoLiXian', 'TLX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654225, 6542, '65,6542,654225', 3, '裕民县', 'YuMinXian', 'YMX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654226, 6542, '65,6542,654226', 3, '和布克赛尔蒙古自治县', 'HeBuKeSaiErMengGuZiZhiXian', 'HBKSEMGZZX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654301, 6543, '65,6543,654301', 3, '阿勒泰市', 'ALeTaiShi', 'ALTS', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654321, 6543, '65,6543,654321', 3, '布尔津县', 'BuErJinXian', 'BEJX', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654322, 6543, '65,6543,654322', 3, '富蕴县', 'FuYunXian', 'FYX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654323, 6543, '65,6543,654323', 3, '福海县', 'FuHaiXian', 'FHX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654324, 6543, '65,6543,654324', 3, '哈巴河县', 'HaBaHeXian', 'HBHX', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654325, 6543, '65,6543,654325', 3, '青河县', 'QingHeXian', 'QHX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (654326, 6543, '65,6543,654326', 3, '吉木乃县', 'JiMuNaiXian', 'JMNX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001, 65, '65,659001', 2, '石河子市', 'ShiHeZiShi', 'SHZS', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002, 65, '65,659002', 2, '阿拉尔市', 'ALaErShi', 'ALES', 'A', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003, 65, '65,659003', 2, '图木舒克市', 'TuMuShuKeShi', 'TMSKS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004, 65, '65,659004', 2, '五家渠市', 'WuJiaQuShi', 'WJQS', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659006, 65, '65,659006', 2, '铁门关市', 'TieMenGuanShi', 'TMGS', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004001, 429004, '42,429004,429004001', 3, '沙嘴街道', 'ShaZuiJieDao', 'SZJD', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004002, 429004, '42,429004,429004002', 3, '干河街道', 'GanHeJieDao', 'GHJD', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004003, 429004, '42,429004,429004003', 3, '龙华山街道', 'LongHuaShanJieDao', 'LHSJD', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004100, 429004, '42,429004,429004100', 3, '郑场镇', 'ZhengChangZhen', 'ZCZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004101, 429004, '42,429004,429004101', 3, '毛嘴镇', 'MaoZuiZhen', 'MZZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004102, 429004, '42,429004,429004102', 3, '豆河镇', 'DouHeZhen', 'DHZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004103, 429004, '42,429004,429004103', 3, '三伏潭镇', 'SanFuTanZhen', 'SFTZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004104, 429004, '42,429004,429004104', 3, '胡场镇', 'HuChangZhen', 'HCZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004105, 429004, '42,429004,429004105', 3, '长倘口镇', 'ZhangTangKouZhen', 'ZTKZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004106, 429004, '42,429004,429004106', 3, '西流河镇', 'XiLiuHeZhen', 'XLHZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004107, 429004, '42,429004,429004107', 3, '沙湖镇', 'ShaHuZhen', 'SHZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004108, 429004, '42,429004,429004108', 3, '杨林尾镇', 'YangLinWeiZhen', 'YLWZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004109, 429004, '42,429004,429004109', 3, '彭场镇', 'PengChangZhen', 'PCZ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004110, 429004, '42,429004,429004110', 3, '张沟镇', 'ZhangGouZhen', 'ZGZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004111, 429004, '42,429004,429004111', 3, '郭河镇', 'GuoHeZhen', 'GHZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004112, 429004, '42,429004,429004112', 3, '沔城回族镇', 'MianChengHuiZuZhen', 'MCHZZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004113, 429004, '42,429004,429004113', 3, '通海口镇', 'TongHaiKouZhen', 'THKZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004114, 429004, '42,429004,429004114', 3, '陈场镇', 'ChenChangZhen', 'CCZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004400, 429004, '42,429004,429004400', 3, '工业园区', 'GongYeYuanQu', 'GYYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004401, 429004, '42,429004,429004401', 3, '九合垸原种场', 'JiuHeYuanYuanZhongChang', 'JHYYZC', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004402, 429004, '42,429004,429004402', 3, '沙湖原种场', 'ShaHuYuanZhongChang', 'SHYZC', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004404, 429004, '42,429004,429004404', 3, '五湖渔场', 'WuHuYuChang', 'WHYC', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004405, 429004, '42,429004,429004405', 3, '赵西垸林场', 'ZhaoXiYuanLinChang', 'ZXYLC', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004407, 429004, '42,429004,429004407', 3, '畜禽良种场', 'ChuQinLiangZhongChang', 'CQLZC', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429004408, 429004, '42,429004,429004408', 3, '排湖风景区', 'PaiHuFengJingQu', 'PHFJQ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005001, 429005, '42,429005,429005001', 3, '园林街道', 'YuanLinJieDao', 'YLJD', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005002, 429005, '42,429005,429005002', 3, '杨市街道', 'YangShiJieDao', 'YSJD', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005003, 429005, '42,429005,429005003', 3, '周矶街道', 'ZhouJiJieDao', 'ZJJD', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005004, 429005, '42,429005,429005004', 3, '广华街道', 'GuangHuaJieDao', 'GHJD', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005005, 429005, '42,429005,429005005', 3, '泰丰街道', 'TaiFengJieDao', 'TFJD', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005006, 429005, '42,429005,429005006', 3, '高场街道', 'GaoChangJieDao', 'GCJD', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005100, 429005, '42,429005,429005100', 3, '竹根滩镇', 'ZhuGenTanZhen', 'ZGTZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005101, 429005, '42,429005,429005101', 3, '渔洋镇', 'YuYangZhen', 'YYZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005102, 429005, '42,429005,429005102', 3, '王场镇', 'WangChangZhen', 'WCZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005103, 429005, '42,429005,429005103', 3, '高石碑镇', 'GaoShiBeiZhen', 'GSBZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005104, 429005, '42,429005,429005104', 3, '熊口镇', 'XiongKouZhen', 'XKZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005105, 429005, '42,429005,429005105', 3, '老新镇', 'LaoXinZhen', 'LXZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005106, 429005, '42,429005,429005106', 3, '浩口镇', 'HaoKouZhen', 'HKZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005107, 429005, '42,429005,429005107', 3, '积玉口镇', 'JiYuKouZhen', 'JYKZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005108, 429005, '42,429005,429005108', 3, '张金镇', 'ZhangJinZhen', 'ZJZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005109, 429005, '42,429005,429005109', 3, '龙湾镇', 'LongWanZhen', 'LWZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005400, 429005, '42,429005,429005400', 3, '江汉石油管理局', 'JiangHanShiYouGuanLiJu', 'JHSYGLJ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005401, 429005, '42,429005,429005401', 3, '潜江经济开发区', 'QianJiangJingJiKaiFaQu', 'QJJJKFQ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005450, 429005, '42,429005,429005450', 3, '周矶管理区', 'ZhouJiGuanLiQu', 'ZJGLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005451, 429005, '42,429005,429005451', 3, '后湖管理区', 'HouHuGuanLiQu', 'HHGLQ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005452, 429005, '42,429005,429005452', 3, '熊口管理区', 'XiongKouGuanLiQu', 'XKGLQ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005453, 429005, '42,429005,429005453', 3, '总口管理区', 'ZongKouGuanLiQu', 'ZKGLQ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005454, 429005, '42,429005,429005454', 3, '白鹭湖管理区', 'BaiLuHuGuanLiQu', 'BLHGLQ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005455, 429005, '42,429005,429005455', 3, '运粮湖管理区', 'YunLiangHuGuanLiQu', 'YLHGLQ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429005457, 429005, '42,429005,429005457', 3, '浩口原种场', 'HaoKouYuanZhongChang', 'HKYZC', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006001, 429006, '42,429006,429006001', 3, '竟陵街道', 'JingLingJieDao', 'JLJD', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006002, 429006, '42,429006,429006002', 3, '候口街道', 'HouKouJieDao', 'HKJD', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006003, 429006, '42,429006,429006003', 3, '杨林街道', 'YangLinJieDao', 'YLJD', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006100, 429006, '42,429006,429006100', 3, '多宝镇', 'DuoBaoZhen', 'DBZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006101, 429006, '42,429006,429006101', 3, '拖市镇', 'TuoShiZhen', 'TSZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006102, 429006, '42,429006,429006102', 3, '张港镇', 'ZhangGangZhen', 'ZGZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006103, 429006, '42,429006,429006103', 3, '蒋场镇', 'JiangChangZhen', 'JCZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006104, 429006, '42,429006,429006104', 3, '汪场镇', 'WangChangZhen', 'WCZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006105, 429006, '42,429006,429006105', 3, '渔薪镇', 'YuXinZhen', 'YXZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006106, 429006, '42,429006,429006106', 3, '黄潭镇', 'HuangTanZhen', 'HTZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006107, 429006, '42,429006,429006107', 3, '岳口镇', 'YueKouZhen', 'YKZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006108, 429006, '42,429006,429006108', 3, '横林镇', 'HengLinZhen', 'HLZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006109, 429006, '42,429006,429006109', 3, '彭市镇', 'PengShiZhen', 'PSZ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006110, 429006, '42,429006,429006110', 3, '麻洋镇', 'MaYangZhen', 'MYZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006111, 429006, '42,429006,429006111', 3, '多祥镇', 'DuoXiangZhen', 'DXZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006112, 429006, '42,429006,429006112', 3, '干驿镇', 'GanYiZhen', 'GYZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006113, 429006, '42,429006,429006113', 3, '马湾镇', 'MaWanZhen', 'MWZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006114, 429006, '42,429006,429006114', 3, '卢市镇', 'LuShiZhen', 'LSZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006115, 429006, '42,429006,429006115', 3, '小板镇', 'XiaoBanZhen', 'XBZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006116, 429006, '42,429006,429006116', 3, '九真镇', 'JiuZhenZhen', 'JZZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006118, 429006, '42,429006,429006118', 3, '皂市镇', 'ZaoShiZhen', 'ZSZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006119, 429006, '42,429006,429006119', 3, '胡市镇', 'HuShiZhen', 'HSZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006120, 429006, '42,429006,429006120', 3, '石家河镇', 'ShiJiaHeZhen', 'SJHZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006121, 429006, '42,429006,429006121', 3, '佛子山镇', 'FoZiShanZhen', 'FZSZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006201, 429006, '42,429006,429006201', 3, '净潭乡', 'JingTanXiang', 'JTX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006450, 429006, '42,429006,429006450', 3, '蒋湖农场', 'JiangHuNongChang', 'JHNC', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006451, 429006, '42,429006,429006451', 3, '白茅湖农场', 'BaiMaoHuNongChang', 'BMHNC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429006452, 429006, '42,429006,429006452', 3, '沉湖管委会', 'ChenHuGuanWeiHui', 'CHGWH', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021100, 429021, '42,429021,429021100', 3, '松柏镇', 'SongBaiZhen', 'SBZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021101, 429021, '42,429021,429021101', 3, '阳日镇', 'YangRiZhen', 'YRZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021102, 429021, '42,429021,429021102', 3, '木鱼镇', 'MuYuZhen', 'MYZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021103, 429021, '42,429021,429021103', 3, '红坪镇', 'HongPingZhen', 'HPZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021104, 429021, '42,429021,429021104', 3, '新华镇', 'XinHuaZhen', 'XHZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021105, 429021, '42,429021,429021105', 3, '九湖镇', 'JiuHuZhen', 'JHZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021200, 429021, '42,429021,429021200', 3, '宋洛乡', 'SongLuoXiang', 'SLX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (429021202, 429021, '42,429021,429021202', 3, '下谷坪土家族乡', 'XiaGuPingTuJiaZuXiang', 'XGPTJZX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001100, 469001, '46,469001,469001100', 3, '通什镇', 'TongShiZhen', 'TSZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001101, 469001, '46,469001,469001101', 3, '南圣镇', 'NanShengZhen', 'NSZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001102, 469001, '46,469001,469001102', 3, '毛阳镇', 'MaoYangZhen', 'MYZ', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001103, 469001, '46,469001,469001103', 3, '番阳镇', 'FanYangZhen', 'FYZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001198, 469001, '46,469001,469001198', 3, '县直辖村级区划', 'XianZhiXiaCunJiQuHua', 'XZXCJQH', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001200, 469001, '46,469001,469001200', 3, '畅好乡', 'ChangHaoXiang', 'CHX', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001201, 469001, '46,469001,469001201', 3, '毛道乡', 'MaoDaoXiang', 'MDX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469001202, 469001, '46,469001,469001202', 3, '水满乡', 'ShuiManXiang', 'SMX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002100, 469002, '46,469002,469002100', 3, '嘉积镇', 'JiaJiZhen', 'JJZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002101, 469002, '46,469002,469002101', 3, '万泉镇', 'WanQuanZhen', 'WQZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002102, 469002, '46,469002,469002102', 3, '石壁镇', 'ShiBiZhen', 'SBZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002103, 469002, '46,469002,469002103', 3, '中原镇', 'ZhongYuanZhen', 'ZYZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002104, 469002, '46,469002,469002104', 3, '博鳌镇', 'BoAoZhen', 'BAZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002105, 469002, '46,469002,469002105', 3, '阳江镇', 'YangJiangZhen', 'YJZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002106, 469002, '46,469002,469002106', 3, '龙江镇', 'LongJiangZhen', 'LJZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002107, 469002, '46,469002,469002107', 3, '潭门镇', 'TanMenZhen', 'TMZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002108, 469002, '46469002,469002108', 3, '塔洋镇', 'TaYangZhen', 'TYZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002109, 469002, '46,469002,469002109', 3, '长坡镇', 'ZhangPoZhen', 'ZPZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002110, 469002, '46,469002,469002110', 3, '大路镇', 'DaLuZhen', 'DLZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002111, 469002, '46,469002,469002111', 3, '会山镇', 'HuiShanZhen', 'HSZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469002500, 469002, '46,469002,469002500', 3, '彬村山华侨农场', 'BinCunShanHuaQiaoNongChang', 'BCSHQNC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005100, 469005, '46,469005,469005100', 3, '文城镇', 'WenChengZhen', 'WCZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005101, 469005, '46,469005,469005101', 3, '重兴镇', 'ZhongXingZhen', 'ZXZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005102, 469005, '46,469005,469005102', 3, '蓬莱镇', 'PengLaiZhen', 'PLZ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005103, 469005, '46,469005,469005103', 3, '会文镇', 'HuiWenZhen', 'HWZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005104, 469005, '46,469005,469005104', 3, '东路镇', 'DongLuZhen', 'DLZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005105, 469005, '46,469005,469005105', 3, '潭牛镇', 'TanNiuZhen', 'TNZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005106, 469005, '46,469005,469005106', 3, '东阁镇', 'DongGeZhen', 'DGZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005107, 469005, '46,469005,469005107', 3, '文教镇', 'WenJiaoZhen', 'WJZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005108, 469005, '46,469005,469005108', 3, '东郊镇', 'DongJiaoZhen', 'DJZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005109, 469005, '46,469005,469005109', 3, '龙楼镇', 'LongLouZhen', 'LLZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005110, 469005, '46,469005,469005110', 3, '昌洒镇', 'ChangSaZhen', 'CSZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005111, 469005, '46,,469005,469005111', 3, '翁田镇', 'WengTianZhen', 'WTZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005112, 469005, '46,469005,469005112', 3, '抱罗镇', 'BaoLuoZhen', 'BLZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005113, 469005, '46,469005,469005113', 3, '冯坡镇', 'FengPoZhen', 'FPZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005114, 469005, '46,469005,469005114', 3, '锦山镇', 'JinShanZhen', 'JSZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005115, 469005, '46,469005,469005115', 3, '铺前镇', 'PuQianZhen', 'PQZ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469005116, 469005, '46,469005,469005116', 3, '公坡镇', 'GongPoZhen', 'GPZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006100, 469006, '46,469006,469006100', 3, '万城镇', 'WanChengZhen', 'WCZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006101, 469006, '46,469006,469006101', 3, '龙滚镇', 'LongGunZhen', 'LGZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006102, 469006, '46,469006,469006102', 3, '和乐镇', 'HeLeZhen', 'HLZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006103, 469006, '46,469006,469006103', 3, '后安镇', 'HouAnZhen', 'HAZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006104, 469006, '46,469006,469006104', 3, '大茂镇', 'DaMaoZhen', 'DMZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006105, 469006, '46,469006,469006105', 3, '东澳镇', 'DongAoZhen', 'DAZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006106, 469006, '46,469006,469006106', 3, '礼纪镇', 'LiJiZhen', 'LJZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006107, 469006, '46,469006,469006107', 3, '长丰镇', 'ChangFengZhen', 'CFZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006108, 469006, '46,469006,469006108', 3, '山根镇', 'ShanGenZhen', 'SGZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006109, 469006, '46,469006,469006109', 3, '北大镇', 'BeiDaZhen', 'BDZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006110, 469006, '46,469006,469006110', 3, '南桥镇', 'NanQiaoZhen', 'NQZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006111, 469006, '46,469006,469006111', 3, '三更罗镇', 'SanGengLuoZhen', 'SGLZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006500, 469006, '46,469006,469006500', 3, '兴隆华侨农场', 'XingLongHuaQiaoNongChang', 'XLHQNC', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469006501, 469006, '46,469006,469006501', 3, '地方国营六连林场', 'DiFangGuoYingLiuLianLinChang', 'DFGYLLLC', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007100, 469007, '46,469007,469007100', 3, '八所镇', 'BaSuoZhen', 'BSZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007101, 469007, '46,469007,469007101', 3, '东河镇', 'DongHeZhen', 'DHZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007102, 469007, '46,469007,469007102', 3, '大田镇', 'DaTianZhen', 'DTZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007103, 469007, '46,469007,469007103', 3, '感城镇', 'GanChengZhen', 'GCZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007104, 469007, '46,469007,469007104', 3, '板桥镇', 'BanQiaoZhen', 'BQZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007105, 469007, '46,469007,469007105', 3, '三家镇', 'SanJiaZhen', 'SJZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007106, 469007, '46,469007,469007106', 3, '四更镇', 'SiGengZhen', 'SGZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007107, 469007, '46,469007,469007107', 3, '新龙镇', 'XinLongZhen', 'XLZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007200, 469007, '46,469007,469007200', 3, '天安乡', 'TianAnXiang', 'TAX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007201, 469007, '46,469007,469007201', 3, '江边乡', 'JiangBianXiang', 'JBX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469007500, 469007, '46,469007,469007500', 3, '东方华侨农场', 'DongFangHuaQiaoNongChang', 'DFHQNC', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021100, 469021, '46,469021,469021100', 3, '定城镇', 'DingChengZhen', 'DCZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021101, 469021, '46,469021,469021101', 3, '新竹镇', 'XinZhuZhen', 'XZZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021102, 469021, '46,469021,469021102', 3, '龙湖镇', 'LongHuZhen', 'LHZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021103, 469021, '46,469021,469021103', 3, '黄竹镇', 'HuangZhuZhen', 'HZZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021104, 469021, '46,469021,469021104', 3, '雷鸣镇', 'LeiMingZhen', 'LMZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021105, 469021, '46,469021,469021105', 3, '龙门镇', 'LongMenZhen', 'LMZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021106, 469021, '46,469021,469021106', 3, '龙河镇', 'LongHeZhen', 'LHZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021107, 469021, '46,469021,469021107', 3, '岭口镇', 'LingKouZhen', 'LKZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021108, 469021, '46,469021,469021108', 3, '翰林镇', 'HanLinZhen', 'HLZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469021109, 469021, '46,469021,469021109', 3, '富文镇', 'FuWenZhen', 'FWZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022100, 469022, '46,469022,469022100', 3, '屯城镇', 'TunChengZhen', 'TCZ', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022101, 469022, '46,469022,469022101', 3, '新兴镇', 'XinXingZhen', 'XXZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022102, 469022, '46,469022,469022102', 3, '枫木镇', 'FengMuZhen', 'FMZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022103, 469022, '46,469022,469022103', 3, '乌坡镇', 'WuPoZhen', 'WPZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022104, 469022, '46,469022,469022104', 3, '南吕镇', 'NanLyuZhen', 'NLZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022105, 469022, '46,469022,469022105', 3, '南坤镇', 'NanKunZhen', 'NKZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022106, 469022, '46,469022,469022106', 3, '坡心镇', 'PoXinZhen', 'PXZ', 'P', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469022107, 469022, '46,469022,469022107', 3, '西昌镇', 'XiChangZhen', 'XCZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023100, 469023, '46,469023,469023100', 3, '金江镇', 'JinJiangZhen', 'JJZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023101, 469023, '46,469023,469023101', 3, '老城镇', 'LaoChengZhen', 'LCZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023102, 469023, '46,469023,469023102', 3, '瑞溪镇', 'RuiXiZhen', 'RXZ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023103, 469023, '46,469023,469023103', 3, '永发镇', 'YongFaZhen', 'YFZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023104, 469023, '46,469023,469023104', 3, '加乐镇', 'JiaLeZhen', 'JLZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023105, 469023, '46,469023,469023105', 3, '文儒镇', 'WenRuZhen', 'WRZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023106, 469023, '46,469023,469023106', 3, '中兴镇', 'ZhongXingZhen', 'ZXZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023107, 469023, '46,469023,469023107', 3, '仁兴镇', 'RenXingZhen', 'RXZ', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023108, 469023, '46,469023,469023108', 3, '福山镇', 'FuShanZhen', 'FSZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023109, 469023, '46,469023,469023109', 3, '桥头镇', 'QiaoTouZhen', 'QTZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023110, 469023, '46,469023,469023110', 3, '大丰镇', 'DaFengZhen', 'DFZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469023405, 469023, '46,469023,469023405', 3, '国营金安农场', 'GuoYingJinAnNongChang', 'GYJANC', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024100, 469024, '46,469024,469024100', 3, '临城镇', 'LinChengZhen', 'LCZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024101, 469024, '46,469024,469024101', 3, '波莲镇', 'BoLianZhen', 'BLZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024102, 469024, '46,469024,469024102', 3, '东英镇', 'DongYingZhen', 'DYZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024103, 469024, '46,469024,469024103', 3, '博厚镇', 'BoHouZhen', 'BHZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024104, 469024, '46,469024,469024104', 3, '皇桐镇', 'HuangTongZhen', 'HTZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024105, 469024, '46,469024,469024105', 3, '多文镇', 'DuoWenZhen', 'DWZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024106, 469024, '46,469024,469024106', 3, '和舍镇', 'HeSheZhen', 'HSZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024107, 469024, '46,469024,469024107', 3, '南宝镇', 'NanBaoZhen', 'NBZ', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024108, 469024, '46,469024,469024108', 3, '新盈镇', 'XinYingZhen', 'XYZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024109, 469024, '46,469024,469024109', 3, '调楼镇', 'DiaoLouZhen', 'DLZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469024401, 469024, '46,469024,469024401', 3, '国营加来农场', 'GuoYingJiaLaiNongChang', 'GYJLNC', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025100, 469025, '46,469025,469025100', 3, '牙叉镇', 'YaChaZhen', 'YCZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025101, 469025, '46,469025,469025101', 3, '七坊镇', 'QiFangZhen', 'QFZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025102, 469025, '46,469025,469025102', 3, '邦溪镇', 'BangXiZhen', 'BXZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025103, 469025, '46,469025,469025103', 3, '打安镇', 'DaAnZhen', 'DAZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025200, 469025, '46,469025,469025200', 3, '细水乡', 'XiShuiXiang', 'XSX', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025201, 469025, '46,469025,469025201', 3, '元门乡', 'YuanMenXiang', 'YMX', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025202, 469025, '46,469025,469025202', 3, '南开乡', 'NanKaiXiang', 'NKX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025203, 469025, '46,469025,469025203', 3, '阜龙乡', 'FuLongXiang', 'FLX', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025204, 469025, '46,469025,469025204', 3, '青松乡', 'QingSongXiang', 'QSX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025205, 469025, '46,469025,469025205', 3, '金波乡', 'JinBoXiang', 'JBX', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469025206, 469025, '46,469025,469025206', 3, '荣邦乡', 'RongBangXiang', 'RBX', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026100, 469026, '46,469026,469026100', 3, '石碌镇', 'ShiLuZhen', 'SLZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026101, 469026, '46,469026,469026101', 3, '叉河镇', 'ChaHeZhen', 'CHZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026102, 469026, '46,469026,469026102', 3, '十月田镇', 'ShiYueTianZhen', 'SYTZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026103, 469026, '46,469026,469026103', 3, '乌烈镇', 'WuLieZhen', 'WLZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026104, 469026, '46,469026,469026104', 3, '昌化镇', 'ChangHuaZhen', 'CHZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026105, 469026, '46,469026,469026105', 3, '海尾镇', 'HaiWeiZhen', 'HWZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026106, 469026, '46,469026,469026106', 3, '七叉镇', 'QiChaZhen', 'QCZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026200, 469026, '46,469026,469026200', 3, '王下乡', 'WangXiaXiang', 'WXX', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469026500, 469026, '46,469026,469026500', 3, '国营霸王岭林场', 'GuoYingBaWangLingLinChang', 'GYBWLLC', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027100, 469027, '46,469027,469027100', 3, '抱由镇', 'BaoYouZhen', 'BYZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027101, 469027, '46,469027,469027101', 3, '万冲镇', 'WanChongZhen', 'WCZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027102, 469027, '46,469027,469027102', 3, '大安镇', 'DaAnZhen', 'DAZ', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027103, 469027, '46,469027,469027103', 3, '志仲镇', 'ZhiZhongZhen', 'ZZZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027104, 469027, '46,469027,469027104', 3, '千家镇', 'QianJiaZhen', 'QJZ', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027105, 469027, '46,469027,469027105', 3, '九所镇', 'JiuSuoZhen', 'JSZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027106, 469027, '46,469027,469027106', 3, '利国镇', 'LiGuoZhen', 'LGZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027107, 469027, '46,469027,469027107', 3, '黄流镇', 'HuangLiuZhen', 'HLZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027108, 469027, '46,469027,469027108', 3, '佛罗镇', 'FuLuoZhen', 'FLZ', 'F', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027109, 469027, '46,469027,469027109', 3, '尖峰镇', 'JianFengZhen', 'JFZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027110, 469027, '46,469027,469027110', 3, '莺歌海镇', 'YingGeHaiZhen', 'YGHZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027500, 469027, '46,469027,469027500', 3, '国营尖峰岭林业公司', 'GuoYingJianFengLingLinYeGongSi', 'GYJFLLYGS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469027501, 469027, '46,469027,469027501', 3, '国营莺歌海盐场', 'GuoYingYingGeHaiYanChang', 'GYYGHYC', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028100, 469028, '46,469028,469028100', 3, '椰林镇', 'YeLinZhen', 'YLZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028101, 469028, '46,469028,469028101', 3, '光坡镇', 'GuangPoZhen', 'GPZ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028102, 469028, '46,469028,469028102', 3, '三才镇', 'SanCaiZhen', 'SCZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028103, 469028, '46,469028,469028103', 3, '英州镇', 'YingZhouZhen', 'YZZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028104, 469028, '46,469028,469028104', 3, '隆广镇', 'LongGuangZhen', 'LGZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028105, 469028, '46,469028,469028105', 3, '文罗镇', 'WenLuoZhen', 'WLZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028106, 469028, '46,469028,469028106', 3, '本号镇', 'BenHaoZhen', 'BHZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028107, 469028, '46,469028,469028107', 3, '新村镇', 'XinCunZhen', 'XCZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028108, 469028, '46,469028,469028108', 3, '黎安镇', 'LiAnZhen', 'LAZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028200, 469028, '46,469028,469028200', 3, '提蒙乡', 'TiMengXiang', 'TMX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028201, 469028, '46,469028,469028201', 3, '群英乡', 'QunYingXiang', 'QYX', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469028500, 469028, '46,469028,469028500', 3, '国营吊罗山林业公司', 'GuoYingDiaoLuoShanLinYeGongSi', 'GYDLSLYGS', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029100, 469029, '46,469029,469029100', 3, '保城镇', 'BaoChengZhen', 'BCZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029101, 469029, '46,469029,469029101', 3, '什玲镇', 'ShenLingZhen', 'SLZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029102, 469029, '46,469029,469029102', 3, '加茂镇', 'JiaMaoZhen', 'JMZ', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029103, 469029, '46,469029,469029103', 3, '响水镇', 'XiangShuiZhen', 'XSZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029104, 469029, '46,469029,469029104', 3, '新政镇', 'XinZhengZhen', 'XZZ', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029105, 469029, '46,469029,469029105', 3, '三道镇', 'SanDaoZhen', 'SDZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029198, 469029, '46,469029,469029198', 3, '县直辖村级区划', 'XianZhiXiaCunJiQuHua', 'XZXCJQH', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029200, 469029, '46,469029,469029200', 3, '六弓乡', 'LiuGongXiang', 'LGX', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029201, 469029, '46,469029,469029201', 3, '南林乡', 'NanLinXiang', 'NLX', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469029202, 469029, '46,469029,469029202', 3, '毛感乡', 'MaoGanXiang', 'MGX', 'M', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030100, 469030, '46,469030,469030100', 3, '营根镇', 'YingGenZhen', 'YGZ', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030101, 469030, '46,469030,469030101', 3, '湾岭镇', 'WanLingZhen', 'WLZ', 'W', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030102, 469030, '46,469030,469030102', 3, '黎母山镇', 'LiMuShanZhen', 'LMSZ', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030103, 469030, '46,469030,469030103', 3, '和平镇', 'HePingZhen', 'HPZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030104, 469030, '46,469030,469030104', 3, '长征镇', 'ChangZhengZhen', 'CZZ', 'C', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030105, 469030, '46,469030,469030105', 3, '红毛镇', 'HongMaoZhen', 'HMZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030106, 469030, '46,469030,469030106', 3, '中平镇', 'ZhongPingZhen', 'ZPZ', 'Z', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030200, 469030, '46,469030,469030200', 3, '吊罗山乡', 'DiaoLuoShanXiang', 'DLSX', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030201, 469030, '46,469030,469030201', 3, '上安乡', 'ShangAnXiang', 'SAX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030202, 469030, '46,469030,469030202', 3, '什运乡', 'ShenYunXiang', 'SYX', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (469030500, 469030, '46,469030,469030500', 3, '海南省黎母山林场（海南黎母山省级自然保护区管理站）', 'HaiNanShengLiMuShanLinChangHaiNanLiMuShanShengJiZiRanBaoHuQuGuanLiZhan', 'HNSLMSLCHNLMSSJZRBHQGLZ', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001001, 659001, '65,659001,659001001', 3, '新城街道', 'XinChengJieDao', 'XCJD', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001002, 659001, '65,659001,659001002', 3, '向阳街道', 'XiangYangJieDao', 'XYJD', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001003, 659001, '65,659001,659001003', 3, '红山街道', 'HongShanJieDao', 'HSJD', 'H', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001004, 659001, '65,659001,659001004', 3, '老街街道', 'LaoJieJieDao', 'LJJD', 'L', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001005, 659001, '65,659001,659001005', 3, '东城街道', 'DongChengJieDao', 'DCJD', 'D', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001100, 659001, '65,659001,659001100', 3, '北泉镇', 'BeiQuanZhen', 'BQZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001101, 659001, '65,659001,659001101', 3, '石河子镇', 'ShiHeZiZhen', 'SHZZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659001500, 659001, '65,659001,659001500', 3, '兵团一五二团', 'BingTuanYiWuErTuan', 'BTYWET', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002001, 659002, '65,659002,659002001', 3, '金银川路街道', 'JinYinChuanLuJieDao', 'JYCLJD', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002002, 659002, '65,659002,659002002', 3, '幸福路街道', 'XingFuLuJieDao', 'XFLJD', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002003, 659002, '65,659002,659002003', 3, '青松路街道', 'QingSongLuJieDao', 'QSLJD', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002004, 659002, '65,659002,659002004', 3, '南口街道', 'NanKouJieDao', 'NKJD', 'N', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002200, 659002, '65,659002,659002200', 3, '托喀依乡', 'TuoKaYiXiang', 'TKYX', 'T', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002402, 659002, '65,659002,659002402', 3, '工业园区', 'GongYeYuanQu', 'GYYQ', 'G', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002500, 659002, '65,659002,659002500', 3, '兵团七团', 'BingTuanQiTuan', 'BTQT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002501, 659002, '65,659002,659002501', 3, '兵团八团', 'BingTuanBaTuan', 'BTBT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002503, 659002, '65,659002,659002503', 3, '兵团十团', 'BingTuanShiTuan', 'BTST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002504, 659002, '65,659002,659002504', 3, '兵团十一团', 'BingTuanShiYiTuan', 'BTSYT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002505, 659002, '65,659002,659002505', 3, '兵团十二团', 'BingTuanShiErTuan', 'BTSET', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002506, 659002, '65,659002,659002506', 3, '兵团十三团', 'BingTuanShiSanTuan', 'BTSST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002507, 659002, '65,659002,659002507', 3, '兵团十四团', 'BingTuanShiSiTuan', 'BTSST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002509, 659002, '65,659002,659002509', 3, '兵团十六团', 'BingTuanShiLiuTuan', 'BTSLT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002510, 659002, '65,659002,659002510', 3, '兵团九团', 'BingTuanJiuTuan', 'BTJT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002511, 659002, '65,659002,659002511', 3, '兵团第一师水利水电工程处', 'BingTuanDiYiShiShuiLiShuiDianGongChengChu', 'BTDYSSLSDGCC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002512, 659002, '65,659002,659002512', 3, '兵团第一师塔里木灌区水利管理处', 'BingTuanDiYiShiTaLiMuGuanQuShuiLiGuanLiChu', 'BTDYSTLMGQSLGLC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002516, 659002, '65,659002,659002516', 3, '兵团一团', 'BingTuanYiTuan', 'BTYT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002517, 659002, '65,659002,659002517', 3, '兵团农一师沙井子水利管理处', 'BingTuanNongYiShiShaJingZiShuiLiGuanLiChu', 'BTNYSSJZSLGLC', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002518, 659002, '65,659002,659002518', 3, '西工业园区管理委员会', 'XiGongYeYuanQuGuanLiWeiYuanHui', 'XGYYQGLWYH', 'X', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002519, 659002, '65,659002,659002519', 3, '兵团二团', 'BingTuanErTuan', 'BTET', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002520, 659002, '65,659002,659002520', 3, '兵团三团', 'BingTuanSanTuan', 'BTST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002521, 659002, '65,659002,659002521', 3, '兵团五团', 'BingTuanWuTuan', 'BTWT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002522, 659002, '65,659002,659002522', 3, '兵团六团', 'BingTuanLiuTuan', 'BTLT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659002523, 659002, '65,659002,659002523', 3, '兵团四团', 'BingTuanSiTuan', 'BTST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003001, 659003, '65,659003,659003001', 3, '齐干却勒街道', 'QiGanQueLeiJieDao', 'QGQLJD', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003002, 659003, '65,659003,659003002', 3, '前海街道', 'QianHaiJieDao', 'QHJD', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003003, 659003, '65,659003,659003003', 3, '永安坝街道', 'YongAnBaJieDao', 'YABJD', 'Y', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003501, 659003, '65,659003,659003501', 3, '兵团四十一团', 'BingTuanSiShiYiTuan', 'BTSSYT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003504, 659003, '65,659003,659003504', 3, '兵团四十四团', 'BingTuanSiShiSiTuan', 'BTSSST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003509, 659003, '65,659003,659003509', 3, '兵团四十九团', 'BingTuanSiShiJiuTuan', 'BTSSJT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003510, 659003, '65,659003,659003510', 3, '兵团五十团', 'BingTuanWuShiTuan', 'BTWST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003511, 659003, '65,659003,659003511', 3, '兵团五十一团', 'BingTuanWuShiYiTuan', 'BTWSYT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003513, 659003, '65,659003,659003513', 3, '兵团五十三团', 'BingTuanWuShiSanTuan', 'BTWSST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659003514, 659003, '65,659003,659003514', 3, '兵团图木舒克市喀拉拜勒镇', 'BingTuanTuMuShuKeShiKaLaBaiLeiZhen', 'BTTMSKSKLBLZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004001, 659004, '65,659004,659004001', 3, '军垦路街道', 'JunKenLuJieDao', 'JKLJD', 'J', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004002, 659004, '65,659004,659004002', 3, '青湖路街道', 'QingHuLuJieDao', 'QHLJD', 'Q', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004003, 659004, '65,659004,659004003', 3, '人民路街道', 'RenMinLuJieDao', 'RMLJD', 'R', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004500, 659004, '65,659004,659004500', 3, '兵团一零一团', 'BingTuanYiLingYiTuan', 'BTYLYT', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004501, 659004, '65,659004,659004501', 3, '兵团一零二团', 'BingTuanYiLingErTuan', 'BTYLET', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659004502, 659004, '65,659004,659004502', 3, '兵团一零三团', 'BingTuanYiLingSanTuan', 'BTYLST', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659006100, 659006, '65,659006,659006100', 3, '博古其镇', 'BoGuQiZhen', 'BGQZ', 'B', '', '', '', '', 2250, 0, NULL, NULL, NULL);
INSERT INTO `yyl_region` VALUES (659006101, 659006, '65,659006,659006101', 3, '双丰镇', 'ShuangFengZhen', 'SFZ', 'S', '', '', '', '', 2250, 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_setting
-- ----------------------------
DROP TABLE IF EXISTS `yyl_setting`;
CREATE TABLE `yyl_setting`  (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设置id',
  `token_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'Token名称',
  `token_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'Token密钥',
  `token_exp` int(5) NULL DEFAULT 720 COMMENT 'Token有效时间（小时）',
  `captcha_register` tinyint(1) NULL DEFAULT 1 COMMENT '注册验证码1开启0关闭',
  `captcha_login` tinyint(1) NULL DEFAULT 0 COMMENT '登录验证码1开启0关闭',
  `log_switch` tinyint(1) NULL DEFAULT 1 COMMENT '日志记录开关：1开启0关闭',
  `log_save_time` int(11) NULL DEFAULT 0 COMMENT '日志保留时间，0永久保留',
  `api_rate_num` int(5) NULL DEFAULT 3 COMMENT '接口请求速率（次数）',
  `api_rate_time` int(5) NULL DEFAULT 1 COMMENT '接口请求速率（时间）',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`setting_id`) USING BTREE,
  INDEX `setting_id`(`setting_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_setting
-- ----------------------------

-- ----------------------------
-- Table structure for yyl_setting_wechat
-- ----------------------------
DROP TABLE IF EXISTS `yyl_setting_wechat`;
CREATE TABLE `yyl_setting_wechat`  (
  `setting_wechat_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '微信设置id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '名称',
  `origin_id` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '原始id',
  `qrcode_id` int(11) NULL DEFAULT 0 COMMENT '二维码id',
  `appid` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '开发者ID',
  `appsecret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '开发者密钥',
  `server_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '服务器地址(URL)',
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '令牌(Token)',
  `encoding_aes_key` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '消息加密密钥',
  `encoding_aes_type` tinyint(1) NULL DEFAULT 1 COMMENT '消息加解密方式：1明文2兼容3安全',
  `create_time` datetime NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`setting_wechat_id`) USING BTREE,
  INDEX `setting_wechat_id`(`setting_wechat_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信设置' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of yyl_setting_wechat
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
