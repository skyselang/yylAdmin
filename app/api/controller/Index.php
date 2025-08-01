<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\api\service\IndexService;

/**
 * @Apidoc\Title("lang(首页)")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("100")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("lang(首页)")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = lang('后端安装成功，欢迎使用，如有帮助，敬请Star！');

        if (config('app.app_debug')) {
            return success($data, $msg);
        } else {
            if (request()->isAjax()) {
                return success($data, $msg);
            } else {
                return '<!doctype html><html lang="en"><head><meta charset="UTF-8"><title>yyladmin</title></head><body><div style="display: flex;justify-content: center;"><a style="text-decoration: none;color: #909399;" href="https://gitee.com/skyselang/yylAdmin" target="_blank">yyladmin</a></div></body></html>';
            }
        }
    }
}
