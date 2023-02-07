<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | Workerman设置 仅对 php think worker:gateway 指令有效
// +----------------------------------------------------------------------
return [
    // 扩展自身需要的配置
    'protocol'              => 'websocket', // 协议 支持 tcp udp unix http websocket text
    'host'                  => '0.0.0.0', // 监听地址
    'port'                  => 2348, // 监听端口
    'socket'                => '', // 完整监听地址
    'context'               => [], // socket 上下文选项
    'register_deploy'       => true, // 是否需要部署register
    'businessWorker_deploy' => true, // 是否需要部署businessWorker
    'gateway_deploy'        => true, // 是否需要部署gateway

    // Register配置
    'registerAddress'       => '127.0.0.1:1236',

    // Gateway配置
    'name'                  => 'thinkphp',
    'count'                 => 1,
    'lanIp'                 => '127.0.0.1',
    'startPort'             => 2000,
    'daemonize'             => false,
    'pingInterval'          => 30,
    'pingNotResponseLimit'  => 0,
    'pingData'              => '{"type":"ping"}',

    // BusinsessWorker配置
    'businessWorker'        => [
        'name'         => 'BusinessWorker',
        'count'        => 1,
        'eventHandler' => '\think\worker\Events',
    ],

];
