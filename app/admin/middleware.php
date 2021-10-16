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
    // 日志记录中间件
    \app\admin\middleware\UserLogMiddleware::class,
    // Token验证中间件
    \app\admin\middleware\TokenVerifyMiddleware::class,
    // 权限验证中间件
    \app\admin\middleware\RuleVerifyMiddleware::class,
    // 接口速率中间件
    \app\admin\middleware\ApiRateMiddleware::class,
];
