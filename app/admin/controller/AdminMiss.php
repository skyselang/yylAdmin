<?php
/*
 * @Description  : 后台miss
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-27
 */

namespace app\admin\controller;

class AdminMiss
{
    public function miss()
    {
        return error('接口地址不存在', 404);
    }
}
