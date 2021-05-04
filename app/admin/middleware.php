<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-04-07
 */

return [
    // 用户日志中间件
    \app\admin\middleware\AdminUserLogMiddleware::class,
    // Token验证中间件
    \app\admin\middleware\AdminTokenVerifyMiddleware::class,
    // 权限验证中间件
    \app\admin\middleware\AdminRuleVerifyMiddleware::class,
    // 请求频率限制中间件
    \app\admin\middleware\AdminThrottleMiddleware::class,
];
