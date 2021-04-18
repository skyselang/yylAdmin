<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-17
 * @LastEditTime : 2021-04-17
 */

namespace app\index\controller;

use app\index\service\IndexService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("首页")
 */
class Index
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\Returned(ref="return")
     */
    public function index()
    {
        $data = IndexService::index();

        return success($data);
    }
}
