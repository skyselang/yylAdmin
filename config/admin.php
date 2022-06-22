<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// admin配置
return [
    // 系统超管用户ID（所有权限）
    'super_ids' => [1],

    // 系统超管是否隐藏
    'super_is_hide' => true,

    // 菜单免登url（无需登录）
    'menu_is_unlogin' => [
        'admin/admin.Login/setting',
        'admin/admin.Login/captcha',
        'admin/admin.Login/login'
    ],

    // 菜单免权url（无需权限）
    'menu_is_unauth' => [
        'admin/Index/index',
        'admin/Index/notice',
        'admin/admin.Notice/info',
        'admin/admin.Login/logout',
        'admin/admin.UserCenter/info'
    ],

    // 菜单无需限率url
    'menu_is_unrate' => [
        'admin/file.File/add',
        'admin/file.File/list'
    ],

    // token名称，必须与前端设置一致
    'token_name' => env('token.admin_token_name', 'AdminToken'),

    // 日志记录请求参数排除字段（敏感、内容多等信息）
    'log_param_without' => [
        'password',
        'password_old',
        'password_new',
        'password_confirm',
        'content'
    ]
];
