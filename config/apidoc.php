<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口文档配置 https://gitee.com/hg-code/apidoc-php
return [
    // （选配）文档标题，显示在左上角与首页
    'title' => env('apidoc.title', 'yylAdmin-接口文档'),
    // （选配）文档描述，显示在首页
    'desc' => env('apidoc.desc', 'yylAdmin 接口文档与调试'),
    // （选配）是否启用Apidoc，默认true
    'enable' => env('apidoc.enable', false),
    // （必须）设置文档的应用/版本
    'apps' => [
        [
            // （必须）标题
            'title' => 'admin',
            // （必须）控制器目录地址
            'path' => 'app\admin\controller',
            // （必须）唯一的key
            'key' => 'admin',
            // （选配）当前应用全局参数
            'params' => [
                // （选配）当前应用全局的请求Header
                'header' => [['name' => env('admin.token_name', 'AdminToken'), 'type' => 'string', 'require' => true, 'desc' => 'admin token']],
                // （选配）当前应用全局的请求Query
                'query' => [['name' => env('admin.token_name', 'AdminToken'), 'type' => 'string', 'require' => true, 'desc' => 'admin token']],
                // （选配）当前应用全局的请求Body
                'body' => [['name' => env('admin.token_name', 'AdminToken'), 'type' => 'string', 'require' => true, 'desc' => 'admin token']],
            ],
            // （选配）该用于的控制器分组
            'groups' => [
                ['title' => '登录', 'name' => 'logout'],
                ['title' => '控制台', 'name' => 'console'],
                ['title' => '会员管理', 'name' => 'member'],
                ['title' => '内容管理', 'name' => 'content'],
                ['title' => '文件管理', 'name' => 'file'],
                ['title' => '设置管理', 'name' => 'setting'],
                ['title' => '系统管理', 'name' => 'system'],
            ],
        ],
        [
            'title' => 'api',
            'path' => 'app\api\controller',
            'key' => 'api',
            'params' => [
                'header' => [['name' => env('api.token_name', 'ApiToken'), 'type' => 'string', 'require' => true, 'desc' => 'api token']],
                'query' => [['name' => env('api.token_name', 'ApiToken'), 'type' => 'string', 'require' => true, 'desc' => 'api token']],
                'body' => [['name' => env('api.token_name', 'ApiToken'), 'type' => 'string', 'require' => true, 'desc' => 'api token']],
            ],
            'groups' => [
                ['title' => '首页', 'name' => 'index'],
                ['title' => '设置', 'name' => 'setting'],
                ['title' => '会员', 'name' => 'member'],
                ['title' => '内容', 'name' => 'content']
            ],
        ]
    ],
    // （必须）指定通用注释定义的文件地址
    'definitions' => 'app\common\controller\ApidocDefinitions',
    // （必须）自动生成url规则，当接口不添加@Apidoc\Url("xxx")注解时，使用以下规则自动生成
    'auto_url' => [
        // 字母规则，lcfirst=首字母小写；ucfirst=首字母大写；
        'letter_rule' => 'lcfirst',
        // url前缀
        'prefix' => '',
        // 自定义url生成方法
        'custom' => function ($path, $method, $url) {
            $urlArr = explode('/', $url);
            $classPathArr = [];
            for ($i = 2; $i < count($urlArr) - 1; $i++) {
                if ($i == count($urlArr) - 2) {
                    $urlArr[$i] = ucfirst($urlArr[$i]);
                }
                $classPathArr[] = $urlArr[$i];
            }
            $classPath = implode('.', $classPathArr);
            return '/' . $urlArr[1] . '/' . $classPath . '/' . $method;
        },
    ],
    // （必须）缓存配置
    'cache' => [
        // 是否开启缓存
        'enable' => env('apidoc.cache_enable', !env('app.debug', false)),
    ],
    // （必须）权限认证配置
    'auth' => [
        // 是否启用密码验证
        'enable'     => env('apidoc.auth_enable', false),
        // 全局访问密码
        'password'   => env('apidoc.auth_password', 'yyladmin'),
        // 密码加密盐
        'secret_key' => env('apidoc.auth_secret_key', 'yyladmin-key'),
        // 授权访问后的有效期
        'expire'     => env('apidoc.auth_expire', 86400)
    ],
    // 全局参数
    'params' => [
        // （选配）全局的请求Header
        'header' => [
            // name=字段名，type=字段类型，require=是否必须，default=默认值，desc=字段描述
        ],
        // （选配）全局的请求Query
        'query' => [
            // 同上 header
        ],
        // （选配）全局的请求Body
        'body' => [
            // 同上 header
        ],
    ],
    // 全局响应体
    'responses' => [
        // 成功响应体
        'success' => [
            ['name' => 'code', 'desc' => '返回码', 'type' => 'int', 'require' => 1, 'default' => 200],
            ['name' => 'msg', 'desc' => '返回描述', 'type' => 'string', 'require' => 1],
            //参数同上 headers；main=true来指定接口Returned参数挂载节点
            ['name' => 'data', 'desc' => '返回数据', 'main' => true, 'type' => 'object', 'require' => 1],
        ],
        // 异常响应体
        'error' => [
            ['name' => 'code', 'desc' => '返回码', 'type' => 'int', 'require' => 1, 'md' => ''],
            ['name' => 'msg', 'desc' => '返回描述', 'type' => 'string', 'require' => 1],
            ['name' => 'data', 'desc' => '返回数据', 'main' => true, 'type' => 'object', 'require' => 1],
        ]
    ],
    // （选配）默认作者
    'default_author' => 'yyladmin',
    // （选配）默认请求类型
    'default_method' => 'GET',
    // （选配）允许跨域访问
    'allowCrossDomain' => false,
    /**
     * （选配）解析时忽略带@注解的关键词，当注解中存在带@字符并且非Apidoc注解，如 @key test，此时Apidoc页面报类似以下错误时:
     * [Semantical Error] The annotation "@key" in method xxx() was never imported. Did you maybe forget to add a "use" statement for this annotation?
     */
    'ignored_annitation' => [],
    // （选配）数据库配置
    'database' => [],
    // （选配）Markdown文档
    'docs' => [
        // title=文档标题，path=.md文件地址，appKey=指定应用/版本，多级分组使用children嵌套
        ['title' => '接口文档说明', 'path' => './private/apidoc/apidocs'],
    ],
    // （选配）代码生成器配置 注意：是一个二维数组
    'generator' => []
];
