<?php
// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'file',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
            // 缓存前缀
            'prefix'     => Env::get('cache.prefix', 'yyl'),
            // 缓存保存目录
            'path'       => '',
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
        ],
        'redis' => [
            // 驱动方式
            'type'       => 'redis',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
            // 缓存前缀
            'prefix'     => Env::get('cache.prefix', 'yyl:'),
            // 主机
            'host'       => Env::get('cache.host', '127.0.0.1'),
            // 端口
            'port'       => Env::get('cache.port', 6379),
            // 密码
            'password'   => Env::get('cache.password', ''),
        ],
        'memcache' => [
            // 驱动方式
            'type'       => 'memcache',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
            // 缓存前缀
            'prefix'     => Env::get('cache.prefix', 'yyl:'),
            // 主机
            'host'       => Env::get('cache.host', '127.0.0.1'),
            // 端口
            'port'       => Env::get('cache.port', 11211),
            // 密码
            'password'   => Env::get('cache.password', ''),
        ],
        'wincache' => [
            // 驱动方式
            'type'       => 'wincache',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
            // 缓存前缀
            'prefix'     => Env::get('cache.prefix', 'yyl'),
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
        ],
        // 更多的缓存连接
    ],
];
