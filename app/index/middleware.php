<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-12-01
 */

return [
    // token
    \app\index\middleware\TokenMiddleware::class,
    // api
    \app\index\middleware\ApiMiddleware::class,
    // log
    \app\index\middleware\LogMiddleware::class,
];
