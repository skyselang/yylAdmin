<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 首页控制器
namespace app\index\controller;

use app\index\service\IndexService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("首页")
 * @Apidoc\Sort("1")
 */
class Index
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = '后端安装成功，感谢使用，如有帮助，欢迎Star！';

        return success($data, $msg);
    }
}
