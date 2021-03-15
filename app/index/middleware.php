<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-12-24
 */

return [
    // token
    \app\index\middleware\TokenMiddleware::class,
    // api
    \app\index\middleware\ApiMiddleware::class,
    // user log
    \app\index\middleware\UserLogMiddleware::class,
];
