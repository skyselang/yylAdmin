<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-05-27
 */

return [
    // 日志记录中间件
    \app\admin\middleware\AdminUserLogMiddleware::class,
    // Token验证中间件
    \app\admin\middleware\AdminTokenVerifyMiddleware::class,
    // 权限验证中间件
    \app\admin\middleware\AdminRuleVerifyMiddleware::class,
    // 接口速率中间件
    \app\admin\middleware\AdminApiRateMiddleware::class,
];
