<?php
/*
 * @Description  : index配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-04-07
 */

return [
    // 是否记录日志
    'is_log' => true,
    // 接口白名单
    'whitelist' => [
        'index/',
        'index/Login/verify',
        'index/Login/login',
        'index/Register/register',
    ],
    // token 
    'token' => [
        // 密钥
        'key' => '2V81aWjC9k8f',
        // 签发者
        'iss' => 'yylAdminIndex',
        // 有效时间(小时)
        'exp' => 7200,
    ],
    // token key
    'token_key' => 'MemberToken',
    // 请求频率限制（次数/时间）
    'throttle' => [
        'number' => 3,   //次数,0不限制
        'expire' => 1,   //时间,单位秒
    ]
];
