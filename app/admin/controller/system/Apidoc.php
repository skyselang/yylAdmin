<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\service\system\ApidocService;
use hg\apidoc\annotation as Apidocs;

/**
 * @Apidocs\Title("接口文档")
 * @Apidocs\Group("system")
 * @Apidocs\Sort("900")
 */
class Apidoc extends BaseController
{
    /**
     * @Apidocs\Title("接口文档")
     * @Apidocs\Returned("apidoc_url", type="string", desc="接口文档链接")
     * @Apidocs\Returned("apidoc_pwd", type="string", desc="接口文档密码")
     * @Apidocs\Returned(ref="app\common\model\system\UserModel", field="user_id")
     * @Apidocs\Returned("token", type="string", desc="token")
     * @Apidocs\Returned("token_sub", type="string", desc="token（省略）")
     */
    public function apidoc()
    {
        $data = ApidocService::apidoc();

        return success($data);
    }
}
