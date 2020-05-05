<?php
/*
 * @Description  : Miss路由
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-08
 */

namespace app\common\controller;

class Miss
{
    public function miss()
    {
        error('接口地址不存在', 404);
    }
}
