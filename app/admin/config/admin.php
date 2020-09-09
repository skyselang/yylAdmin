<?php
/*
 * @Description  : 后台配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-09
 */

return [
    // 超级管理员
    'super_admin' => [1],
    // 是否日志管理
    'is_admin_log' => true,
    // token密钥
    'token_key' => 'c8d16adf54b1a66fbf3f16147e4f159e',
    // 请求头部token键名
    'admin_token_key' => 'AdminToken',
    // 请求头部用户id键名
    'admin_user_id_key' => 'AdminUserId',
    // 接口白名单
    'api_white_list' => [
        'admin/AdminLogin/verify',
        'admin/AdminLogin/login',
    ],
    // 权限白名单
    'rule_white_list' => [
        'admin/AdminUsers/usersInfo',
        'admin/AdminUsers/usersLog',
        'admin/AdminIndex/index',
        'admin/AdminLogin/logout',
    ],
    // 接口访问频率限制（次数/时间）
    'api_limit' => [
        'number' => 3, //次数,0不限制
        'expire' => 1, //时间,单位秒
    ],
];
