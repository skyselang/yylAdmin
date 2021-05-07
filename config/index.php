<?php
/*
 * @Description  : index配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-06
 */

return [
    // 是否记录日志
    'is_log' => true,
    // token 
    'token' => [
        // 密钥
        'key' => '2V81aWjC9k8f',
    ],
    // token key
    'token_key' => 'MemberToken',
    // 接口白名单
    'whitelist' => [
        'index/',
        'index/Register/register',
        'index/Register/verify',
        'index/Login/verify',
        'index/Login/login',
        'index/Login/offi',
        'index/Login/officallback',
    ],
    // 请求频率限制（次数/时间）
    'throttle' => [
        'number' => 3,   //次数,0不限制
        'expire' => 1,   //时间,单位秒
    ]
];
