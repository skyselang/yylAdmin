<?php
/*
 * @Description  : 设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-04
 * @LastEditTime : 2021-07-03
 */

namespace app\index\controller;

use app\common\service\SettingCmsService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置")
 * @Apidoc\Sort("69")
 * @Apidoc\Group("indexCms")
 */
class Setting
{
    /**
     * @Apidoc\Title("内容设置信息")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingCmsModel\Info")
     * )
     */
    public function cmsInfo()
    {
        $data = SettingCmsService::cmsInfo();

        return success($data);
    }
}
