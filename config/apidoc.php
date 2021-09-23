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
    // 文档标题
    'title' => '接口文档与调试',
    // 文档描述
    'desc' => 'yylAdmin',
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
    'groups' => [
        ['title' => 'index', 'name' => 'index'],
        ['title' => 'admin', 'name' => 'admin'],
        ['title' => '文件管理', 'name' => 'adminFile'],
        ['title' => '内容管理', 'name' => 'adminCms'],
        ['title' => '内容管理', 'name' => 'indexCms'],
    ],
    // 指定公共注释定义的文件地址
    'definitions' => 'app\common\controller\ApidocDefinitions',
    // 指定生成文档的控制器
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
        'max' => 5,
    ],
    // 权限认证配置
    'auth' => [
        // 是否启用密码验证
        'enable' => true,
        // 验证密码
        'password' => "WYUtY8UiGNYM",
        // 密码加密盐
        'secret_key' => "yyladmin",
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
            ['title' => '接口说明', 'path' => './private/apidoc/apidocs'],
        ]
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
