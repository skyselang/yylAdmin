<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 控制台配置
return [
    // 指令定义
    'commands' => [
        // crontab
        'crontab' => 'app\command\Crontab',
        // lang 
        'lang' => 'app\command\Lang',
        // timer
        'timer' => 'app\command\Timer',
    ],
];
