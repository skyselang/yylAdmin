<?php
/*
 * @Description  : 接口文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use app\common\service\AdminApidocService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("接口文档")
 * @Apidoc\Group("admin")
 */
class AdminApidoc
{
    /**
     * @Apidoc\Title("接口文档")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     */
    public function apidoc()
    {
        $data = AdminApidocService::apidoc();

        return success($data);
    }
}
