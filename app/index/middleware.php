<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 应用中间件定义文件
return [
    // 接口中间件
    \app\index\middleware\ApiMiddleware::class,
    // 会员Token中间件
    \app\index\middleware\MemberTokenMiddleware::class,
    // 会员日志中间件
    \app\index\middleware\MemberLogMiddleware::class,
    // 接口速率中间件
    \app\index\middleware\ApiRateMiddleware::class,
];
