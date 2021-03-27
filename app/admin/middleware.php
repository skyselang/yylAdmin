<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-03-27
 */

return [
    // 日志记录
    \app\admin\middleware\AdminLogMiddleware::class,
    // token验证
    \app\admin\middleware\AdminTokenVerifyMiddleware::class,
    // 权限验证
    \app\admin\middleware\AdminRuleVerifyMiddleware::class,
    // 请求频率限制
    \app\admin\middleware\AdminThrottleMiddleware::class,
];
