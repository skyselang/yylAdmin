<?php
/*
 * @Description  : index配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 */

return [
    // token 
    'token' => [
        // 密钥
        'key' => '2V81aWjC9k8f',
        // 签发者
        'iss' => 'index',
        // 签发时间
        'iat' => time(),
        // 过期时间
        'exp' => time() + 7 * 24 * 60 * 60,
    ],
    // token key
    'token_key' => 'Token',
    // admin_user_id key
    'user_id_key' => 'UserId',
    // 登录地址
    'login_url' => 'index/Login/login',
    // 白名单
    'white_list' => [
        'index/Login/login',
    ],
    // 是否记录日志
    'is_log' => false,
    // 接口访问频率限制（次数/时间）
    'api_limit' => [
        'number' => 2, //次数,0不限制
        'expire' => 1, //时间,单位秒
    ],
];
