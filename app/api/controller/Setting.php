<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 首页控制器
namespace app\api\controller;

use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("100")
 * @Apidoc\Group("setting")
 */
class Setting
{
    /**
     * @Apidoc\Title("自定义设置")
     */
    public function diy()
    {
        $data = SettingService::diy();

        return success($data);
    }
}
