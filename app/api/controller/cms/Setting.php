<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置控制器
namespace app\api\controller\cms;

use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("630")
 * @Apidoc\Group("cms")
 */
class Setting
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="app\common\model\cms\SettingModel\infoReturn")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }
}
