<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\service\admin\ApidocService;
use hg\apidoc\annotation as Apidocs;

/**
 * @Apidocs\Title("接口文档")
 * @Apidocs\Group("adminSystem")
 * @Apidocs\Sort("720")
 */
class Apidoc extends BaseController
{
    /**
     * @Apidocs\Title("接口文档")
     * @Apidocs\Returned("apidoc_url", type="string", desc="接口文档链接")
     * @Apidocs\Returned("apidoc_pwd", type="string", desc="接口文档密码")
     * @Apidocs\Returned("admin_user_id", type="string", desc="用户id")
     * @Apidocs\Returned("admin_token", type="string", desc="admin_token")
     * @Apidocs\Returned("admin_token_sub", type="string", desc="admin_token（部分）")
     */
    public function apidoc()
    {
        $data = ApidocService::apidoc();

        return success($data);
    }
}
