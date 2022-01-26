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
    // 无需登录接口url
    'api_is_unlogin' => [
        'api/',
        'api/Index/index',
        'api/Register/captcha',
        'api/Register/register',
        'api/Login/captcha',
        'api/Login/login'
    ],
    // 无需限率接口url
    'api_is_unrate' => [],
    // token名称，必须与前端设置一致
    'token_name' => env('token.api_token_name', 'ApiToken')
];
