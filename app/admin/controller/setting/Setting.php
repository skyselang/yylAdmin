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
     * @Apidoc\Title("设置管理信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel")
     * @Apidoc\Returned(ref="app\common\service\setting\SettingService\info")
     */
    public function info()
    {
        $data = SettingService::info();

        return success($data);
    }

    /**
     * @Apidoc\Title("设置管理修改")
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


    /**
     * @Apidoc\Title("邮件设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel", field="email_host,email_port,email_secure,email_username,email_password,email_setfrom")
     */
    public function emailInfo()
    {
        $data = SettingService::info('email_host,email_port,email_secure,email_username,email_password,email_setfrom');

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel", field="email_host,email_secure,email_port,email_username,email_password,email_setfrom")
     */
    public function emailEdit()
    {
        $param = $this->params([
            'email_host/s'     => '',
            'email_secure/s'   => 'ssl',
            'email_port/s'     => '',
            'email_username/s' => '',
            'email_password/s' => '',
            'email_setfrom/s'  => '',
        ]);

        validate(SettingValidate::class)->scene('email_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置测试")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email_recipient", type="string", require=true, desc="收件人")
     * @Apidoc\Param("email_theme", type="string", require=true, desc="主题")
     * @Apidoc\Param("email_content", type="string", require=true, desc="内容")
     */
    public function emailTest()
    {
        $param = $this->params(['email_recipient/s' => '', 'email_theme/s' => '', 'email_content/s' => '']);

        validate(SettingValidate::class)->scene('email_test')->check($param);

        $data = SettingService::emailTest($param);

        return success($data, '发送成功');
    }
}
