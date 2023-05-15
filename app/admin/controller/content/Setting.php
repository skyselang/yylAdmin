<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\content;

use app\common\controller\BaseController;
use app\common\validate\content\SettingValidate;
use app\common\service\content\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("内容设置")
 * @Apidoc\Group("content")
 * @Apidoc\Sort("500")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("内容设置信息")
     * @Apidoc\Returned(ref="app\common\model\content\SettingModel")
     * @Apidoc\Returned(ref="diyConObjReturn")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("内容设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\content\SettingModel")
     * @Apidoc\Param(ref="diyConParam")
     */
    public function edit()
    {
        $param = $this->params(['is_content/d' => 1, 'diy_config/a' => []]);

        validate(SettingValidate::class)->scene('edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
