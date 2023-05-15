<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\controller\BaseController;
use app\common\validate\member\SettingValidate;
use app\common\service\member\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员设置")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("600")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="captcha_register,captcha_login")
     */
    public function captchaInfo()
    {
        $data = SettingService::info('captcha_register,captcha_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="captcha_register,captcha_login")
     */
    public function captchaEdit()
    {
        $param = $this->params(['captcha_register/d' => 0, 'captcha_login/d' => 0]);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $data = SettingService::info('token_key,token_exp,is_multi_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param = $this->params(['token_key/s' => '', 'token_exp/d' => 720, 'is_multi_login/d' => 0]);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logInfo()
    {
        $data = SettingService::info('log_switch,log_save_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logEdit()
    {
        $param = $this->params(['log_switch/d' => 0, 'log_save_time/d' => 0]);

        validate(SettingValidate::class)->scene('log_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
        $data = SettingService::info('is_member_api,api_rate_num,api_rate_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param = $this->params(['is_member_api/d' => 0, 'api_rate_num/d' => 3, 'api_rate_time/d' => 1]);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="is_register,is_login,is_offi_register,is_offi_login,is_mini_register,is_mini_login")
     */
    public function logregInfo()
    {
        $data = SettingService::info('is_register,is_login,is_offi_register,is_offi_login,is_mini_register,is_mini_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_register,is_login,is_offi_register,is_offi_login,is_mini_register,is_mini_login")
     */
    public function logregEdit()
    {
        $param = $this->params([
            'is_register/d'      => 1,
            'is_login/d'         => 1,
            'is_offi_register/d' => 1,
            'is_offi_login/d'    => 1,
            'is_mini_register/d' => 1,
            'is_mini_login/d'    => 1
        ]);

        validate(SettingValidate::class)->scene('logreg_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("自定义设置信息")
     * @Apidoc\Returned(ref="diyConReturn")
     */
    public function diyInfo()
    {
        $data = SettingService::info('diy_config');

        return success($data);
    }

    /**
     * @Apidoc\Title("自定义设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="diyConParam")
     */
    public function diyEdit()
    {
        $param = $this->params(['diy_config/a' => []]);

        validate(SettingValidate::class)->scene('diy_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
