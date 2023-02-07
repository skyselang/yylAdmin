<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// api配置 http://skyselang.gitee.io/yyladmindoc/
return [
    // token方式：header、param；token名称；前后端必须一致
    'token_type' => env('api.token_type', 'header'),
    'token_name' => env('api.token_name', 'ApiToken'),

    // 超级会员ID（所有权限）
    'super_ids' => env('api.super_ids', []),

    // 接口免登url（无需登录）
    'api_is_unlogin' => [
        'api/',
        'api/Index/index',
        'api/Register/captcha',
        'api/Register/register',
        'api/Login/captcha',
        'api/Login/login'
    ],

    // 接口免权url（无需权限）
    'api_is_unauth' => [
        'api/Member/info',
        'api/Login/logout',
    ],

    // 接口免限url（不限速率）
    'api_is_unrate' => [],

    // 日志记录请求参数排除字段（敏感、内容多等信息）
    'log_param_without' => [
        'password',
        'password_new',
        'password_old',
        'content',
        'images',
        'videos',
        'audios',
        'words',
        'annexs',
        'others',
    ]
];
