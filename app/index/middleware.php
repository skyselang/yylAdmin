<?php
/*
 * @Description  : 应用中间件定义文件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-04-07
 */

return [
    // 会员Token中间件
    \app\index\middleware\MemberTokenMiddleware::class,
    // 接口中间件
    \app\index\middleware\ApiMiddleware::class,
    // 会员日志中间件
    \app\index\middleware\MemberLogMiddleware::class,
];
