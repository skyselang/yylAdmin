<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\cms;

use app\common\BaseController;
use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Group("cms")
 * @Apidoc\Sort("630")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="app\common\model\cms\SettingModel\infoReturn")
     * @Apidoc\Returned("diy_config", type="object", desc="自定义信息")
     */
    public function info()
    {
        $data = SettingService::info();

        $diy_config = $data['diy_config'];
        unset($data['diy_config']);
        foreach ($diy_config as $v) {
            $data['diy_config'][$v['config_key']] = $v['config_val'];
        }

        return success($data);
    }
}
