<?php
/*
 * @Description  : index配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-13
 */

return [
    // 是否记录日志
    'is_log' => true,
    // token配置
    'token' => [
        // 密钥
        'key' => '2V81aWjC9k8f',
    ],
    // 请求头部token键名
    'token_key' => 'MemberToken',
    // 接口白名单
    'whitelist' => [
        'index/',
        'index/Register/verify',
        'index/Register/register',
        'index/Login/verify',
        'index/Login/login',
        'index/Login/offi',
        'index/Login/officallback',
        'index/Login/mini',
    ],
    // 请求频率限制（次数/时间）
    'throttle' => [
        'number' => 3,   //次数,0不限制
        'expire' => 1,   //时间,单位秒
    ]
];
