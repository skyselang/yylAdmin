<?php
/*
 * @Description  : 内容设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-04
 * @LastEditTime : 2021-07-10
 */

namespace app\index\controller;

use app\common\service\CmsSettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("69")
 * @Apidoc\Group("indexCms")
 */
class CmsSetting
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\CmsSettingModel\Info")
     * )
     */
    public function info()
    {
        $data = CmsSettingService::info();

        return success($data);
    }
}
