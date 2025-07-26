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
    // 多语言加载
    \think\middleware\LoadLangPack::class,
    // 用户日志中间件
    \app\admin\middleware\UserLogMiddleware::class,
    // 用户Token中间件
    \app\admin\middleware\UserTokenMiddleware::class,
    // 用户菜单中间件
    \app\admin\middleware\UserMenuMiddleware::class,
    // 接口速率中间件
    \think\middleware\Throttle::class,
];
