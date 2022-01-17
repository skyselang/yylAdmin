<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// index配置
return [
    // 无需登录接口url
    'api_is_unlogin' => [
        'index/',
        'index/Index/index',
        'index/Register/captcha',
        'index/Register/register',
        'index/Login/captcha',
        'index/Login/login'
    ],
    // 无需限率接口url
    'api_is_unrate' => [],
    // token名称，必须与前端设置一致
    'token_name' => env('token.index_token_name', 'MemberToken')
];
