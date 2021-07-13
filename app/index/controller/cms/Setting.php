<?php
/*
 * @Description  : 设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-04
 * @LastEditTime : 2021-07-13
 */

namespace app\index\controller\cms;

use app\common\service\cms\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("69")
 * @Apidoc\Group("indexCms")
 */
class Setting
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\cms\SettingModel\Info")
     * )
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }
}
