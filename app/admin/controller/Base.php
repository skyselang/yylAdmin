<?php

declare(strict_types=1);

namespace app\admin\controller;

/**
 * 控制器基础类
 */
class Base
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin:*"); //跨域
        header('Content-type:text/html;charset=utf-8'); //编码

    }
}
