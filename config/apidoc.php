<?php
return [
    // 文档标题
    'title' => '接口文档与调试',
    // 文档描述
    'desc' => 'yyladmin',
    // 版权申明
    'copyright' => '',
    // 默认作者
    'default_author' => '',
    // 默认请求类型
    'default_method' => 'GET',
    // 设置应用/版本（必须设置）
    'apps' => [
        ['title' => 'index前台', 'path' => 'app\index\controller', 'folder' => 'index'],
        ['title' => 'admin后台', 'path' => 'app\admin\controller', 'folder' => 'admin'],
    ],
    // 控制器分组
    'groups' => [],
    // 指定公共注释定义的文件地址
    'definitions' => 'app\common\controller\Definitions',
    //指定生成文档的控制器
    'controllers' => [],
    // 过滤，不解析的控制器
    'filter_controllers' => [],
    // 缓存配置
    'cache' => [
        // 是否开启缓存
        'enable' => true,
        // 缓存文件路径
        'path' => '../runtime/apidoc/',
        // 是否显示更新缓存按钮
        'reload' => true,
        // 最大缓存文件数
        'max' => 5,   //最大缓存数量
    ],
    // 权限认证配置
    'auth' => [
        // 是否启用密码验证
        'enable' => true,
        // 验证密码
        'password' => "XFWWDiIB5LlV",
        // 密码加密盐
        'secret_key' => "6L6eGcNmD0C8",
    ],
    // 统一的请求Header
    'headers' => [],
    // 统一的请求参数Parameters
    'parameters' => [],
    // 统一的请求响应体，仅显示在文档提示中
    'responses' => [],
    // md文档
    'docs' => [
        'menu_title' => '开发文档',
        'menus'      => [
            ['title' => '接口说明', 'path' => 'public/apidocs/apidocs'],
        ]
    ],
];
