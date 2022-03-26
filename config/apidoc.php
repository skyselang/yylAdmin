<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口文档配置 https://gitee.com/hg-code/thinkphp-apidoc
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
                ['title' => '设置', 'name' => 'setting'],
                ['title' => '首页', 'name' => 'index'],
                ['title' => '注册', 'name' => 'register'],
                ['title' => '登录退出', 'name' => 'login'],
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
        'enable' => env('apidoc.cache_enable', !env('app.debug', false))
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
    ],
    // 代码生成器配置 注意：是一个二维数组
    'generator' => [
        [
            // 标题
            'title' => '创建CRUD',
            // 是否启用
            'enable' => env('apidoc.generator_enable', false),
            // 执行中间件，具体请查看下方中间件介绍
            'middleware' => [
                // \app\middleware\CreateCrudMiddleware::class
            ],
            // 生成器窗口的表单配置
            'form' => [
                // 表单显示列数
                'colspan' => 3,
                // 表单项字段配置
                'items' => [
                    [
                        // 表单项标题
                        'title' => '控制器标题',
                        // 字段名
                        'field' => 'controller_title',
                        // 输入类型，支持：input、select
                        'type' => 'input',
                    ],
                ]
            ],
            // 文件生成配置，注意：是一个二维数组
            'files' => [
                [
                    // 生成文件的文件夹地址，或php文件地址
                    'path' => 'app\${app[0].folder}\controller',
                    // 生成文件的命名空间
                    'namespace' => 'app\${app[0].folder}\controller',
                    // 模板文件地址
                    'template' => 'private/apidoc/controller.tpl',
                    // 名称
                    'name' => 'controller',
                    // 验证规则
                    'rules' => [
                        ['required' => true, 'message' => '请输入控制器文件名'],
                        ['pattern' => '^[A-Z]{1}([a-zA-Z0-9]|[._]){2,19}$', 'message' => '请输入正确的目录名'],
                    ]
                ],
                [
                    'name' => 'service',
                    'path' => 'app\common\service',
                    'template' => 'private/apidoc/service.tpl',
                ],
                [
                    'name' => 'validate',
                    'path' => 'app\common\validate',
                    'template' => 'private/apidoc/validate.tpl',
                ],
                [
                    'name' => 'cache',
                    'path' => 'app\common\cache',
                    'template' => 'private/apidoc/cache.tpl',
                ],
            ],
            // 数据表配置
            'table' => [
                // 可选的字段类型
                'field_types' => [
                    "int",
                    "tinyint",
                    "integer",
                    "float",
                    "decimal",
                    "char",
                    "varchar",
                    "blob",
                    "text",
                    "point",
                ],
                // 数据表配置，注意：是一个二维数组，可定义多个数据表
                'items' => [
                    [
                        // 表标题
                        'title' => '主表',
                        // 模型名验证规则
                        'model_rules' => [
                            ['pattern' => '^[A-Z]{1}([a-zA-Z0-9]|[._]){2,19}$', 'message' => '模型文件名错误，请输入大写字母开头的字母+数字，长度2-19的组合']
                        ],
                        // 表名验证规则
                        'table_rules' => [
                            ['pattern' => '^[a-z]{1}([a-z0-9]|[_]){2,19}$', 'message' => '表名错误，请输入小写字母开头的字母+数字+下划线，长度2-19的组合']
                        ],
                        // 显示的提示文本
                        'desc' => '提示说明文本',
                        // 生成模型的命名空间
                        'namespace' => 'app\common\model',
                        // 生成模型的文件夹地址
                        'path' => "app\common\model",
                        // 模板文件地址
                        'template' => "private/apidoc/model.tpl",
                        // 自定义配置列
                        'columns' => [
                            [
                                'title' => '验证',
                                'field' => 'check',
                                'type' => 'select',
                                'width' => 180,
                                'props' => [
                                    'placeholder' => '请输入',
                                    'mode' => 'multiple',
                                    'maxTagCount' => 1,
                                    'options' => [
                                        ['label' => '必填', 'value' => 'require', 'message' => '缺少必要参数{$item.field}'],
                                        ['label' => '数字', 'value' => 'number', 'message' => '{$item.field}字段类型为数字'],
                                        ['label' => '整数', 'value' => 'integer', 'message' => '{$item.field}为整数'],
                                        ['label' => '布尔', 'value' => 'boolean', 'message' => '{$item.field}为布尔值'],
                                    ],
                                ],
                            ],
                            [
                                'title' => '查询',
                                'field' => 'query',
                                'type' => 'checkbox',
                                'width' => 60
                            ],
                            [
                                'title' => '列表',
                                'field' => 'list',
                                'type' => 'checkbox',
                                'width' => 60
                            ],
                            [
                                'title' => '信息',
                                'field' => 'info',
                                'type' => 'checkbox',
                                'width' => 60
                            ],
                            [
                                'title' => '添加',
                                'field' => 'add',
                                'type' => 'checkbox',
                                'width' => 60
                            ],
                            [
                                'title' => '修改',
                                'field' => 'edit',
                                'type' => 'checkbox',
                                'width' => 60
                            ]
                        ],
                        // 默认字段
                        'default_fields' => [
                            [
                                // 字段名
                                'field' => 'id',
                                // 字段注释
                                'desc' => '主键id',
                                // 字段类型
                                'type' => 'int',
                                // 字段长度
                                'length' => 11,
                                // 默认值
                                'default' => '',
                                // 非Null
                                'not_null' => true,
                                // 主键
                                'main_key' => true,
                                // 自增
                                'incremental' => true,
                                //也可以添加自定义列的值
                                'query' => true,
                            ],
                            [
                                // 字段名
                                'field' => 'is_delete',
                                // 字段注释
                                'desc' => '是否删除1是0否',
                                // 字段类型
                                'type' => 'tinyint',
                                // 字段长度
                                'length' => 1,
                                // 默认值
                                'default' => 0,
                                // 非Null
                                'not_null' => false,
                                // 主键
                                'main_key' => false,
                                // 自增
                                'incremental' => false,
                                //也可以添加自定义列的值
                                'query' => false,
                            ],
                            [
                                'field' => 'create_time',
                                'desc' => '创建时间',
                                'type' => 'datetime',
                                'length' => null,
                                'default' => '',
                                'not_null' => false,
                                'main_key' => false,
                                'incremental' => false,
                            ],
                            [
                                'field' => 'update_time',
                                'desc' => '更新时间',
                                'type' => 'datetime',
                                'length' => null,
                                'default' => '',
                                'not_null' => false,
                                'main_key' => false,
                                'incremental' => false,
                            ],
                            [
                                'field' => 'delete_time',
                                'desc' => '删除时间',
                                'type' => 'datetime',
                                'length' => null,
                                'default' => '',
                                'not_null' => false,
                                'main_key' => false,
                                'incremental' => false,
                            ]
                        ],
                        // 添加一行字段时，默认的值
                        'default_values' => [
                            //这里就是对应每列字段名=>值
                            'type' => 'varchar',
                            'length' => 255,
                            //...
                        ],
                    ],
                ]
            ]
        ],
    ]
];
