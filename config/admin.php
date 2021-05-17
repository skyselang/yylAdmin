<?php
/*
 * @Description  : admin配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-17
 */

return [
    // 超管admin_user_id（所有权限）
    'super_ids' => [1],
    // 是否记录日志
    'is_log' => true,
    // token 
    'token' => [
        // 密钥
        'key' => '2V81aWjC9k8f',
    ],
    // admin_user_id key
    'user_id_key' => 'AdminUserId',
    // admin_token key
    'token_key' => 'AdminToken',
    // 接口白名单
    'api_whitelist' => [
        'admin/AdminLogin/verify',
        'admin/AdminLogin/login',
    ],
    // 权限白名单
    'rule_whitelist' => [
        'admin/AdminIndex/index',
        'admin/AdminLogin/logout',
        'admin/AdminUserCenter/info',
    ],
    // 请求频率限制（次数/时间）
    'throttle' => [
        'number' => 3, //次数,0不限制
        'expire' => 1, //时间,单位秒
    ],
];
