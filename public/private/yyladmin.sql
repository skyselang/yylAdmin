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

 Date: 18/09/2020 18:21:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for yyl_admin_apidoc
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_apidoc`;
CREATE TABLE `yyl_admin_apidoc`  (
  `admin_apidoc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '接口文档id',
  `apidoc_pid` int(11) NOT NULL DEFAULT 0 COMMENT '接口父级id',
  `apidoc_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '接口名称',
  `apidoc_path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '接口路径',
  `apidoc_sort` int(10) NOT NULL DEFAULT 200 COMMENT '接口排序',
  `apidoc_method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'GET' COMMENT '接口请求方式',
  `apidoc_request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '接口请求参数',
  `apidoc_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '接口返回参数',
  `apidoc_example` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '接口返回示例',
  `apidoc_explain` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '接口额外说明',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_apidoc_id`) USING BTREE,
  INDEX `admin_apidoc_id`(`admin_apidoc_id`) USING BTREE,
  INDEX `apidoc_pid`(`apidoc_pid`, `apidoc_name`) USING BTREE,
  INDEX `apidoc_path`(`apidoc_path`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '接口文档' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_apidoc
-- ----------------------------
INSERT INTO `yyl_admin_apidoc` VALUES (1, 0, '后台接口文档', '', 200, '', '', '', '', '', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_apidoc` VALUES (2, 0, '前台接口文档', '', 200, 'GET', '', '', '', '', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_apidoc` VALUES (3, 1, '验证码', 'admin/AdminLogin/login', 200, 'GET', 'user_id:用户id\nbegin_id:起始城市id\nend_id:目的城市id\nweight:重量\nvolume:体积\nbegin_type:port\nend_type:port', '{\n    \"code\": \"\",\n    \"msg\": \"\",\n    \"time\": \"\",\n    \"data\": {\n        \"list\": [{\n            \"supplier_icon\": \"航空公司的logo\",\n            \"supplier_name\": \"航空公司的名称\",\n            \"supplier_code\": \"航空公司的代码\",\n            \"from_port_name\": \"出发港口名称\",\n            \"from_port_code\": \"出发港口代码\",\n            \"from_city_id\": \"出发港口所属城市id\",\n            \"from_location_full_name\": \"出发港口所属城市全路径名称\",\n            \"to_port_name\": \"目的港口名称\",\n            \"to_port_code\": \"目的港口代码\",\n            \"to_city_id\": \"目的港口所属城市id\",\n            \"to_location_full_name\": \"目的港口所属城市全路径名称\",\n            \"list\": [{\n                \"transport_id\": \"\",\n                \"transport_supplier_id\": \"\",\n                \"itinerary_id_path\": \"\",\n                \"port_to_port_id_path\": \"\",\n                \"from_port_id\": \"始发港口id\",\n                \"from_port_code\": \"始发港口代码\",\n                \"to_port_id\": \"目的港口id\",\n                \"to_port_code\": \"目的港口代码\",\n                \"several_date\": \"日期\",\n                \"several_week\": \"周几1-7\",\n                \"several_weeks\": \"周几飞\",\n                \"etd\": \"起飞时间\",\n                \"eta\": \"到达时间\",\n                \"material_arrival_time\": \"\",\n                \"capacity_cm\": \"容量\",\n                \"flight_number\": \"航班号\",\n                \"via\": \"经停1\",\n                \"dest1\": \"经停2\",\n                \"dest2\": \"经停3\",\n                \"dest3\": \"经停4\",\n                \"dest4\": \"经停5\",\n                \"via_total\": \"总经停\",\n                \"price\": \"价格\",\n                \"time_consuming\": \"服务时效\",\n                \"time_transport\": \"飞行运输时间\",\n                \"airplane_type\": \"\",\n                \"airplane_model\": \"\",\n                \"enabled\": \"\",\n                \"is_del\": \"\",\n                \"create_time\": \"\",\n                \"update_time\": \"\",\n                \"supplier_icon\": \"\",\n                \"supplier_name\": \"\",\n                \"supplier_code\": \"\",\n                \"journey\": [{\n                    \"port_to_port_id\": \"\",\n                    \"from_port_id\": \"\",\n                    \"from_port_code\": \"\",\n                    \"to_port_id\": \"\",\n                    \"to_port_code\": \"\",\n                    \"transfer_type\": \"\",\n                    \"enabled\": \"\",\n                    \"flight_number\": \"\",\n                    \"time_consuming\": \"\",\n                    \"via\": \"\",\n                    \"dest1\": \"\",\n                    \"dest2\": \"\",\n                    \"dest3\": \"\",\n                    \"dest4\": \"\",\n                    \"via_total\": \"\",\n                    \"dest\": \"\",\n                    \"price_id\": \"\",\n                    \"level1_weight\": \"\",\n                    \"level2_weight\": \"\",\n                    \"level3_weight\": \"\",\n                    \"level4_weight\": \"\",\n                    \"level5_weight\": \"\",\n                    \"level6_weight\": \"\",\n                    \"level7_weight\": \"\",\n                    \"level8_weight\": \"\",\n                    \"level9_weight\": \"\",\n                    \"level10_weight\": \"\",\n                    \"level1_price\": \"\",\n                    \"level2_price\": \"\",\n                    \"level3_price\": \"\",\n                    \"level4_price\": \"\",\n                    \"level5_price\": \"\",\n                    \"level6_price\": \"\",\n                    \"level7_price\": \"\",\n                    \"level8_price\": \"\",\n                    \"level9_price\": \"\",\n                    \"level10_price\": \"\",\n                    \"minimum_rate\": \"\",\n                    \"normal_flat_rate\": \"\",\n                    \"effective_from\": \"\",\n                    \"effective_to\": \"\"\n                }]\n            }],\n            \"price\": \"价格\",\n            \"Mon\": \"周一\",\n            \"Tues\": \"周二\",\n            \"Wed\": \"周三\",\n            \"Thur\": \"周四\",\n            \"Fri\": \"周五\",\n            \"Sat\": \"周六\",\n            \"Sun\": \"周天\",\n            \"via\": \"经停1\",\n            \"dest1\": \"经停2\",\n            \"dest2\": \"经停3\",\n            \"dest3\": \"经停4\",\n            \"dest4\": \"经停5\"\n        }],\n        \"celerity\": {\n            \"from_port_id\": \"始发港口id\",\n            \"to_port_id\": \"目标港口id\",\n            \"transport_id\": \"线路id\",\n            \"total_price\": \"价格\",\n            \"list\": [{\n                \"supplier_icon\": \"\",\n                \"supplier_name\": \"\",\n                \"supplier_code\": \"\",\n                \"from_port_name\": \"\",\n                \"from_port_code\": \"\",\n                \"from_city_id\": \"\",\n                \"from_location_full_name\": \"\",\n                \"to_port_name\": \"\",\n                \"to_port_code\": \"\",\n                \"to_city_id\": \"\",\n                \"to_location_full_name\": \"\",\n                \"price\": \"\",\n                \"Mon\": \"\",\n                \"Tues\": \"\",\n                \"Wed\": \"\",\n                \"Thur\": \"\",\n                \"Fri\": \"\",\n                \"Sat\": \"\",\n                \"Sun\": \"\",\n                \"via\": \"\",\n                \"dest1\": \"\",\n                \"dest2\": \"\",\n                \"dest3\": \"\",\n                \"dest4\": \"\",\n                \"transport_id\": \"\",\n                \"transport_supplier_id\": \"\",\n                \"itinerary_id_path\": \"\",\n                \"port_to_port_id_path\": \"\",\n                \"from_port_id\": \"\",\n                \"to_port_id\": \"\",\n                \"several_weeks\": \"\",\n                \"etd\": \"\",\n                \"eta\": \"\",\n                \"material_arrival_time\": \"\",\n                \"capacity_cm\": \"\",\n                \"time_consuming\": \"\",\n                \"flight_number\": \"\",\n                \"via_total\": \"\",\n                \"airplane_type\": \"\",\n                \"airplane_model\": \"\",\n                \"enabled\": \"\",\n                \"is_del\": \"\",\n                \"create_time\": \"\",\n                \"update_time\": \"\"\n            }],\n            \"supplier_icon\": \"\",\n            \"supplier_name\": \"\",\n            \"supplier_code\": \"\",\n            \"from_port_name\": \"\",\n            \"from_port_code\": \"\",\n            \"from_city_id\": \"\",\n            \"from_location_full_name\": \"\",\n            \"to_port_name\": \"\",\n            \"to_port_code\": \"\",\n            \"to_city_id\": \"\",\n            \"to_location_full_name\": \"\",\n            \"price\": \"\",\n            \"Mon\": \"\",\n            \"Tues\": \"\",\n            \"Wed\": \"\",\n            \"Thur\": \"\",\n            \"Fri\": \"\",\n            \"Sat\": \"\",\n            \"Sun\": \"\",\n            \"transport_supplier_id\": \"\",\n            \"itinerary_id_path\": \"\",\n            \"port_to_port_id_path\": \"\",\n            \"several_weeks\": \"\",\n            \"etd\": \"\",\n            \"eta\": \"\",\n            \"airplane_type\": \"\",\n            \"airplane_model\": \"\",\n            \"material_arrival_time\": \"\",\n            \"capacity_cm\": \"\",\n            \"enabled\": \"\",\n            \"is_del\": \"\",\n            \"create_time\": \"\",\n            \"update_time\": \"\",\n            \"time_consuming\": \"\"\n        }\n    }\n}', '', '', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_apidoc` VALUES (4, 2, '注册', 'index/Register/register', 200, 'POST', 'username:用户名\npasssword:密码', '', '', '', 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for yyl_admin_devdoc
-- ----------------------------
DROP TABLE IF EXISTS `yyl_admin_devdoc`;
CREATE TABLE `yyl_admin_devdoc`  (
  `admin_devdoc_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文档id',
  `devdoc_pid` int(11) NOT NULL DEFAULT 0 COMMENT '父级id',
  `devdoc_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `devdoc_sort` int(10) NOT NULL DEFAULT 200 COMMENT '排序',
  `devdoc_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `is_delete` tinyint(1) NULL DEFAULT 0 COMMENT '是否删除1是0否',
  `create_time` datetime(0) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`admin_devdoc_id`) USING BTREE,
  INDEX `admin_devdoc_id`(`admin_devdoc_id`) USING BTREE,
  INDEX `devdoc_pid`(`devdoc_pid`, `devdoc_name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '开发文档' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_devdoc
-- ----------------------------
INSERT INTO `yyl_admin_devdoc` VALUES (1, 0, '介绍', 200, 'yylAdmin是基于ThinkPHP6和Element2的极简后台管理系统，只有登录注销、权限管理等基本功能，方便扩展；前后端分离。', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_devdoc` VALUES (2, 0, '基础', 200, '', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_devdoc` VALUES (3, 2, '要求', 200, 'PHP >= 7.1\nMySQL >= 5.6\nRedis', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_devdoc` VALUES (4, 2, '准备', 200, 'Git\nNode\nComposer\nThinkPHP\nElement\nPhpStudy', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_devdoc` VALUES (5, 2, '安装', 200, 'PHP部分\n\n# 克隆项目\ngit clone https://gitee.com/skyselang/yylAdmin.git\n\n# 进入项目目录\ncd yylAdmin\n\n# 安装依赖\ncomposer install\n\n# 可以通过composer镜像解决速度慢的问题\ncomposer config -g repo.packagist composer https://mirrors.aliyun.com/composer/\n\n# 配置环境（PhpStudy）\n\n# 导入数据库\n数据库文件：public/private/yyladmin.sql\n\nWEB部分\n\n# 克隆项目\ngit clone https://gitee.com/skyselang/yylAdminWeb.git\n\n# 进入项目目录\ncd yylAdminWeb\n\n# 安装依赖\nnpm install\n\n# 可以通过npm镜像解决速度慢的问题\nnpm install --registry=https://registry.npm.taobao.org\n\n# 启动服务\nnpm run dev', 0, NULL, NULL, NULL);

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
) ENGINE = MyISAM AUTO_INCREMENT = 105 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_menu
-- ----------------------------
INSERT INTO `yyl_admin_menu` VALUES (1, 0, '控制台', '', 200, '0', '0', 0, NULL, NULL, NULL);
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
INSERT INTO `yyl_admin_menu` VALUES (50, 36, '地图坐标拾取', 'admin/AdminTool/mapPoint', 150, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (51, 12, '登录', 'admin/AdminLogin/login', 160, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (52, 12, '退出', 'admin/AdminLogin/logout', 150, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (53, 2, '系统设置', '', 110, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (54, 12, '日志记录', 'admin/AdminUsers/usersLog', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (58, 36, '实用工具合集', 'admin/AdminTool/tools', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (56, 2, '访问统计', '', 120, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (64, 56, '日期统计', 'admin/AdminVisit/visitDate', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (60, 4, '用户权限明细', 'admin/AdminUser/userRuleInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (62, 53, '基础设置', 'admin/AdminSetting/settingBase', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (63, 58, '字符串转换', 'admin/AdminTool/strTran', 210, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (65, 56, '访问统计', 'admin/AdminVisit/visitStats', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (67, 56, '数量统计', 'admin/AdminVisit/visitCount', 220, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (71, 53, '缓存设置', 'admin/AdminSetting/settingCache', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (85, 53, 'Token设置', 'admin/AdminSetting/settingToken', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (73, 53, '验证码设置', 'admin/AdminSetting/settingVerify', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (75, 12, '验证码', 'admin/AdminLogin/verify', 170, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (88, 2, '权限管理', '', 210, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (87, 58, 'IP查询', 'admin/AdminTool/ipQuery', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (86, 58, '字节转换', 'admin/AdminTool/byteTran', 200, '0', '1', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (89, 0, '文档管理', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (90, 89, '接口文档', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (91, 90, '接口文档列表', 'admin/AdminApidoc/apidocList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (92, 90, '接口文档详情', 'admin/AdminApidoc/apidocInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (93, 90, '接口文档添加', 'admin/AdminApidoc/apidocAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (98, 89, '开发文档', '', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (95, 90, '接口文档修改', 'admin/AdminApidoc/apidocEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (96, 90, '接口文档删除', 'admin/AdminApidoc/apidocDele', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (100, 98, '开发文档详情', 'admin/AdminDevdoc/devdocInfo', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (99, 98, '开发文档列表', 'admin/AdminDevdoc/devdocList', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (101, 98, '开发文档添加', 'admin/AdminDevdoc/devdocAdd', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (102, 98, '开发文档修改', 'admin/AdminDevdoc/devdocEdit', 200, '0', '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_menu` VALUES (103, 98, '开发文档删除', 'admin/AdminDevdoc/devdocDele', 200, '0', '0', 0, NULL, NULL, NULL);

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
) ENGINE = MyISAM AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of yyl_admin_role
-- ----------------------------
INSERT INTO `yyl_admin_role` VALUES (1, '1,2,3,4,5,12,13,14,15,16,17,18,19,20,22,23,24,25,27,28,29,30,31,32,33,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,71,73,75,85,86,87', '管理员', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (2, '1,2,3,4,5,12,13,14,15,17,18,19,22,23,24,29,31,36,37,38,40,41,42,43,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,75,86,87', '技术', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (3, '1,2,3,4,5,12,13,15,17,18,19,22,23,29,36,37,38,40,41,42,43,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,75,86,87', '产品', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (4, '1,2,3,4,5,12,13,14,17,18,22,23,29,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,56,58,60,62,63,64,65,67,75,86,87', '操作', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (5, '1,2,3,4,5,12,13,17,22,36,37,38,40,41,42,45,46,47,48,49,50,51,52,54,56,58,62,63,64,65,67,75,86,87', '客服', '', 200, '0', 0, NULL, NULL, NULL);
INSERT INTO `yyl_admin_role` VALUES (6, '1,13,17,22,29,36,37,38,40,42,45,49,50,51,52,54,56,58,62,63,64,65,67,75,86,87,91,99', '演示', '', 200, '0', 0, NULL, NULL, NULL);

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
INSERT INTO `yyl_admin_user` VALUES (1, '', '', 'skyselang', 'skyselang', 'e10adc3949ba59abbe56e057f20f883e', '', 'storage/admin/user/1/avatar.png?t=20200916212502', '超级管理员', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (2, '6', '', 'yyladmin', 'yyladmin', 'e10adc3949ba59abbe56e057f20f883e', '', 'storage/admin/user/2/avatar.png?t=20200805092019', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (6, '6', '', 'admin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (7, '6', '', '12345', '12345', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `yyl_admin_user` VALUES (9, '6', '', '123456', '123456', 'e10adc3949ba59abbe56e057f20f883e', '', 'static/img/favicon.ico?t=20200612222621', '', 200, '0', '0', 0, 0, '', '', NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
