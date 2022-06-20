<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// api配置
return [
    // 接口免登url（无需登录）
    'api_is_unlogin' => [
        'api/',
        'api/Index/index',
        'api/Register/captcha',
        'api/Register/register',
        'api/Login/captcha',
        'api/Login/login'
    ],

    // 接口无需限率url
    'api_is_unrate' => [],

    // token名称，必须与前端设置一致
    'token_name' => env('token.api_token_name', 'ApiToken'),

    // 日志记录请求参数排除字段（敏感、内容多等信息）
    'log_param_without' => [
        'password',
        'password_new',
        'password_old',
        'content'
    ]
];
