<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

use think\facade\Env;

// 队列设置：https://github.com/top-think/think-queue
return [
    // 驱动类型：sync、database、redis
    'default'     => 'database',
    'connections' => [
        // 同步执行
        'sync'     => [
            'type' => 'sync',
        ],
        // 数据库驱动
        'database' => [
            'type'       => 'database',
            'queue'      => 'default',
            'table'      => 'queue',
            'connection' => null,
        ],
        // Redis驱动
        'redis'    => [
            'type'       => 'redis',
            'queue'      => 'default',
            'host'       => Env::get('cache.host', '127.0.0.1'),
            'port'       => Env::get('cache.port', 6379),
            'password'   => Env::get('cache.password', ''),
            'select'     => 1,
            'timeout'    => 0,
            'persistent' => Env::get('cache.persistent', false),
            'expire'     => 0,
        ],
    ],
    'failed'      => [
        'type'  => 'none',
        'table' => 'queue_failed',
    ],
];
