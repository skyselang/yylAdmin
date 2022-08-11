<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\BaseController;
use app\api\service\IndexService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("首页")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("110")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("首页")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = '后端安装成功，欢迎使用，如有帮助，敬请Star！';

        return success($data, $msg);
    }
}
