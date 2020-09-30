<?php
/*
 * @Description  : 控制台
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-30
 */

namespace app\admin\controller;

use app\admin\service\AdminIndexService;

class AdminIndex
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
        $data = AdminIndexService::index();

        return success($data);
    }
}
