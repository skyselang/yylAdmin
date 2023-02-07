<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 事件定义文件
return [
    // 事件绑定
    'bind'      => [],

    // 事件监听
    'listen'    => [
        'AppInit'   => [],
        'HttpRun'   => [],
        'HttpEnd'   => [],
        'LogLevel'  => [],
        'LogWrite'  => [],
        // 用户日志事件
        'UserLog'   => ['app\listener\admin\UserLogListener'],
        // 会员日志事件
        'MemberLog' => ['app\listener\api\MemberLogListener'],
    ],

    // 事件订阅
    'subscribe' => [],
];
