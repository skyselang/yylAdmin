<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | 监听任务并执行：php think queue:listen
// | 查看可选参数：php think queue:listen --help
// +----------------------------------------------------------------------

// 队列设置：https://github.com/top-think/think-queue
use think\facade\Env;

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
            'table'      => 'jobs',
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
            'persistent' => false,
            'expire'     => 0,
        ],
    ],
    'failed'      => [
        'type'  => 'none',
        'table' => 'jobs_failed',
    ],
];
