<?php
/*
 * @Description  : 后台配置
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-30 16:12:34
 */

return [
    // 超级管理员
    'super_admin' => [1],
    // token 
    'token' => [
        // 密钥
        'key' => 'AQEFeAOCuQ8AMIIB',
        // 签发者
        'iss' => 'yylAdmin',
        // 签发时间
        'iat' => time(),
        // 过期时间
        'exp' => time() + 1 * 24 * 60 * 60,
    ],
    // 登录地址
    'login_url' => 'admin/AdminLogin/login',
    // 权限白名单
    'rule_white_list' => [
        'admin/AdminLogin/login',
        'admin/AdminLogin/logout',
        'admin/AdminUser/userInfo',
    ],
];
