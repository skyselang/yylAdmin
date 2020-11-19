<?php
/*
 * @Description  : 应用请求对象类
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-16
 * @LastEditTime : 2020-11-19
 */

namespace app;

class Request extends \think\Request
{
    // 全局过滤规则
    protected $filter = ['trim'];
}
