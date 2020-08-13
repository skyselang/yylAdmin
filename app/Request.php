<?php
/*
 * @Description  : 应用请求对象类
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2020-08-13
 */

namespace app;

class Request extends \think\Request
{
    protected $filter = ['trim'];
}
