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
    // 会员日志中间件
    \app\api\middleware\MemberLogMiddleware::class,
    // 会员Token中间件
    \app\api\middleware\MemberTokenMiddleware::class,
    // 会员接口中间件
    \app\api\middleware\MemberApiMiddleware::class,
    // 接口速率中间件
    \think\middleware\Throttle::class,
];
