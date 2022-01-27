<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口文档配置
return [
    // 页面标题
    'title' => env('apidoc.title', 'yylAdmin-接口文档与调试'),
    // 文档说明
    'desc' => env('apidoc.desc', 'yylAdmin Apidoc'),
    // 默认请求类型
    'default_method' => env('apidoc.default_method', 'GET'),
    // 默认作者名称
    'default_author' => env('apidoc.default_author', 'skyselang'),
    // 多应用/多版本管理配置
    'apps' => [
        [
            'title' => 'admin',
            'path' => 'app\admin\controller',
            'folder' => 'admin',
            'groups' => [
                ['title' => '控制台', 'name' => 'adminConsole'],
                ['title' => '会员管理', 'name' => 'adminMember'],
                ['title' => '内容管理', 'name' => 'adminCms'],
                ['title' => '文件管理', 'name' => 'adminFile'],
                ['title' => '设置管理', 'name' => 'adminSetting'],
                ['title' => '权限管理', 'name' => 'adminAuth'],
                ['title' => '系统管理', 'name' => 'adminSystem']
            ],
            'headers' => [
                ['name' => 'AdminToken', 'type' => 'string', 'require' => true, 'desc' => 'admin_token']
            ]
        ],
        [
            'title' => 'api',
            'path' => 'app\api\controller',
            'folder' => 'api',
            'groups' => [
                ['title' => '首页', 'name' => 'index'],
                ['title' => '登录注册', 'name' => 'login'],
                ['title' => '会员中心', 'name' => 'member'],
                ['title' => '微信', 'name' => 'wechat'],
                ['title' => '地区', 'name' => 'region'],
                ['title' => '内容', 'name' => 'cms']
            ],
            'headers' => [
                ['name' => 'ApiToken', 'type' => 'string', 'require' => true, 'desc' => 'api_token']
            ]
        ]
    ],
    // 指定公共注释定义的控制器地址
    'definitions' => 'app\common\controller\ApidocDefinitions',
    // 缓存配置
    'cache' => [
        // 是否开启缓存
        'enable' => !env('app.debug', false)
    ],
    // 进入接口问页面的权限认证配置
    'auth' => [
        // 是否密码登录
        'enable' => env('apidoc.auth_enable', false),
        // 登录密码
        'password' => env('apidoc.auth_password', 'yyladmin'),
        // 密码加密的盐
        'secret_key' => env('apidoc.auth_secret_key', 'yyladmin'),
        // 密码访问有效期
        'expire' => env('apidoc.auth_expire', 86400)
    ],
    // 全局请求头参数
    'headers' => [],
    // 全局请求参数
    'parameters' => [],
    // 统一的请求响应体
    'responses' => [
        ['name' => 'code', 'type' => 'int', 'desc' => '返回码'],
        ['name' => 'msg', 'type' => 'string', 'desc' => '返回描述'],
        ['name' => 'data', 'type' => 'object', 'desc' => '返回数据', 'main' => true],
    ],
    // Markdown文档配置
    'docs' => [
        ['title' => '接口说明', 'path' => './private/apidoc/apidocs']
    ]
];
