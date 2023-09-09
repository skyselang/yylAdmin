<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\controller\BaseController;
use app\common\validate\setting\SettingValidate;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("700")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel")
     * @Apidoc\Returned(ref="app\common\service\setting\SettingService\info")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel", withoutField="setting_id,create_uid,update_uid,create_time,update_time")
     */
    public function edit()
    {
        $param = $this->params([
            'favicon_id/d'  => 0,
            'logo_id/d'     => 0,
            'name/s'        => '',
            'title/s'       => '',
            'keywords/s'    => '',
            'description/s' => '',
            'icp/s'         => '',
            'copyright/s'   => '',
            'offi_id/d'     => 0,
            'mini_id/d'     => 0,
            'address/s'     => '',
            'tel/s'         => '',
            'fax/s'         => '',
            'mobile/s'      => '',
            'email/s'       => '',
            'qq/s'          => '',
            'wechat/s'      => '',
        ]);

        validate(SettingValidate::class)->scene('edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
