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
    'title' => 'yylAdmin-接口文档与调试',
    // 文档说明
    'desc' => 'yylAdmin ApiDoc',
    // 默认请求类型
    'default_method' => 'GET',
    // 默认作者名称
    'default_author' => 'skyselang',
    // 多应用/多版本管理配置
    'apps' => [
        [
            'title' => 'index前台',
            'path' => 'app\index\controller',
            'folder' => 'index',
            'groups' => [
                ['title' => '首页', 'name' => 'index'],
                ['title' => '登录注册', 'name' => 'login'],
                ['title' => '会员中心', 'name' => 'member'],
                ['title' => '微信', 'name' => 'wechat'],
                ['title' => '地区', 'name' => 'region'],
                ['title' => '内容管理', 'name' => 'cms']
            ],
            'headers' => [
                ['name' => 'MemberToken', 'type' => 'string', 'require' => true, 'desc' => 'member_token']
            ]
        ],
        [
            'title' => 'admin后台',
            'path' => 'app\admin\controller',
            'folder' => 'admin',
            'groups' => [
                ['title' => '控制台', 'name' => 'adminConsole'],
                ['title' => '会员管理', 'name' => 'adminMember'],
                ['title' => '内容管理', 'name' => 'adminCms'],
                ['title' => '文件管理', 'name' => 'adminFile'],
                ['title' => '设置管理', 'name' => 'adminSetting'],
                ['title' => '权限管理', 'name' => 'adminAuthority'],
                ['title' => '系统管理', 'name' => 'adminSystem']
            ],
            'headers' => [
                ['name' => 'AdminToken', 'type' => 'string', 'require' => true, 'desc' => 'admin_token']
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
        // 是否启用权限认证，启用则需登录
        'enable' => true,
        // 进入接口文档页面的登录密码
        'password' => "WYUtY8UiGNYM",
        // 密码加密的盐，请务必更改
        'secret_key' => "yyladmin",
        // 密码访问有效期，超过本时间需重新输入访问密码
        'expire' => 30 * 24 * 60 * 60
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
    // 快速生成CRUD
    'crud' => [
        // 生成控制器配置
        'controller' => [
            'path' => 'app\${app[0].folder}\controller',
            'template' => '../private/apidoc/controller',
            'file_name' => '${controller.class_name}',
        ],

        // 生成验证器配置
        'validate' => [
            'path' => 'app\common\validate',
            'template' => '../private/apidoc/validate',
            'file_name' => '${validate.class_name}Validate',
            'rules' => [
                ['name' => '必填', 'rule' => 'require', 'message' => '${field} must'],
                ['name' => '1是0否', 'rule' => 'require|in:0,1', 'message' => ['${field}.require' => '${field} must', '${field}.in' => '${field} 1是0否']]
            ]
        ],

        // 生成业务逻辑配置
        'service' => [
            'path' => 'app\common\service',
            'template' => '../private/apidoc/service',
            'file_name' => '${service.class_name}Service',
        ],

        // 生成缓存配置
        'cache' => [
            'path' => 'app\common\cache',
            'template' => '../private/apidoc/cache',
            'file_name' => '${cache.class_name}Cache',
        ],

        // 生成模型配置
        'model' => [
            'path' => 'app\common\model',
            'template' => '../private/apidoc/model',
            'file_name' => '${model.class_name}Model',
            'default_fields' => [
                [
                    'field' => 'id',
                    'desc' => 'id',
                    'type' => 'int',
                    'length' => 11,
                    'default' => '',
                    'not_null' => true,
                    'main_key' => true,
                    'incremental' => true,
                    'validate' => '',
                    'query' => false,
                    'list' => true,
                    'detail' => true,
                    'add' => false,
                    'edit' => true
                ],
                [
                    'field' => 'is_delete',
                    'desc' => '是否删除1是0否',
                    'type' => 'tinyint',
                    'length' => 1,
                    'default' => 0,
                    'not_null' => false,
                    'main_key' => false,
                    'incremental' => false,
                    'validate' => '',
                    'query' => false,
                    'list' => false,
                    'detail' => false,
                    'add' => false,
                    'edit' => false
                ],
                [
                    'field' => 'create_time',
                    'desc' => '添加时间',
                    'type' => 'datetime',
                    'length' => 0,
                    'default' => NULL,
                    'not_null' => false,
                    'main_key' => false,
                    'incremental' => false,
                    'validate' => '',
                    'query' => false,
                    'list' => true,
                    'detail' => true,
                    'add' => false,
                    'edit' => true
                ],
                [
                    'field' => 'update_time',
                    'desc' => '修改时间',
                    'type' => 'datetime',
                    'length' => 0,
                    'default' => NULL,
                    'not_null' => false,
                    'main_key' => false,
                    'incremental' => false,
                    'validate' => '',
                    'query' => false,
                    'list' => true,
                    'detail' => true,
                    'add' => false,
                    'edit' => true
                ],
                [
                    'field' => 'delete_time',
                    'desc' => '删除时间',
                    'type' => 'datetime',
                    'length' => 0,
                    'default' => NULL,
                    'not_null' => false,
                    'main_key' => false,
                    'incremental' => false,
                    'validate' => '',
                    'query' => false,
                    'list' => false,
                    'detail' => false,
                    'add' => false,
                    'edit' => false
                ],
            ],
            'fields_types' => [
                'int',
                'varchar',
                'datetime',
                'tinyint',
                'char',
                'text',
                'float',
                'decimal',
            ]
        ]
    ]
];
