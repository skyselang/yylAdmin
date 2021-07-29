<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 容器Provider定义文件
use app\ExceptionHandle;
use app\Request;

return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
