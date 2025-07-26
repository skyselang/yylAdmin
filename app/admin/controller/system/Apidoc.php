<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use hg\apidoc\annotation as Apidocs;
use app\common\controller\BaseController;
use app\common\service\system\ApidocService;

/**
 * @Apidocs\Title("接口文档")
 * @Apidocs\Group("system")
 * @Apidocs\Sort("550")
 */
class Apidoc extends BaseController
{
    /**
     * @Apidocs\Title("接口文档")
     * @Apidocs\Returned(ref={ApidocService::class,"apidoc"})
     */
    public function apidoc()
    {
        $data = ApidocService::apidoc();

        return success($data);
    }
}
