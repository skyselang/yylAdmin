<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-17
 * @LastEditTime : 2020-09-27
 */

namespace app\index\controller;

use app\index\service\IndexService;

class Index
{
    /**
     * 首页
     *
     * @method GET
     *
     * @return json
     */
    public function index()
    {
        $data = IndexService::index();

        return success($data);
    }
}
