<?php
/*
 * @Description  : 接口文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-07-14
 */

namespace app\admin\controller\admin;

use app\common\service\admin\ApidocService;
use hg\apidoc\annotation as Apidocs;

/**
 * @Apidocs\Title("接口文档")
 * @Apidocs\Group("admin")
 * @Apidocs\Sort("60")
 */
class Apidoc
{
    /**
     * @Apidocs\Title("接口文档")
     * @Apidocs\Header(ref="headerAdmin")
     * @Apidocs\Returned(ref="returnCode")
     * @Apidocs\Returned(ref="returnData")
     */
    public function apidoc()
    {
        $data = ApidocService::apidoc();

        return success($data);
    }
}
