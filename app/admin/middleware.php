<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-07-14
 */

return [
    // 日志记录中间件
    \app\admin\middleware\admin\UserLogMiddleware::class,
    // Token验证中间件
    \app\admin\middleware\admin\TokenVerifyMiddleware::class,
    // 权限验证中间件
    \app\admin\middleware\admin\RuleVerifyMiddleware::class,
    // 接口速率中间件
    \app\admin\middleware\admin\ApiRateMiddleware::class,
];
