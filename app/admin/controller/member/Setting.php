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
     * 信息
     *
     * @return array
     */
    public function info()
    {
        return SettingService::info(['create_uid' => user_id()]);
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $setting = $this->info();

        $data['token_key']      = $setting['token_key'];
        $data['token_exp']      = $setting['token_exp'];
        $data['is_multi_login'] = $setting['is_multi_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param['token_key']      = $this->request->param('token_key/s', '');
        $param['token_exp']      = $this->request->param('token_exp/d', 720);
        $param['is_multi_login'] = $this->request->param('is_multi_login/d', 0);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="captcha_register,captcha_login")
     */
    public function captchaInfo()
    {
        $setting = $this->info();

        $data['captcha_register'] = $setting['captcha_register'];
        $data['captcha_login']    = $setting['captcha_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="captcha_register,captcha_login")
     */
    public function captchaEdit()
    {
        $param['captcha_register'] = $this->request->param('captcha_register/d', 0);
        $param['captcha_login']    = $this->request->param('captcha_login/d', 0);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logInfo()
    {
        $setting = $this->info();

        $data['log_switch']    = $setting['log_switch'];
        $data['log_save_time'] = $setting['log_save_time'];

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logEdit()
    {
        $param['log_switch']    = $this->request->param('log_switch/d', 0);
        $param['log_save_time'] = $this->request->param('log_save_time/d', 0);

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
        $setting = $this->info();

        $data['is_member_api'] = $setting['is_member_api'];
        $data['api_rate_num']  = $setting['api_rate_num'];
        $data['api_rate_time'] = $setting['api_rate_time'];

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param['is_member_api'] = $this->request->param('is_member_api/d', 0);
        $param['api_rate_num']  = $this->request->param('api_rate_num/d', 3);
        $param['api_rate_time'] = $this->request->param('api_rate_time/d', 1);

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
        $setting = $this->info();

        $data['is_register']      = $setting['is_register'];
        $data['is_login']         = $setting['is_login'];
        $data['is_offi_register'] = $setting['is_offi_register'];
        $data['is_offi_login']    = $setting['is_offi_login'];
        $data['is_mini_register'] = $setting['is_mini_register'];
        $data['is_mini_login']    = $setting['is_mini_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_register,is_login,is_offi_register,is_offi_login,is_mini_register,is_mini_login")
     */
    public function logregEdit()
    {
        $param['is_register']      = $this->request->param('is_register/d', 1);
        $param['is_login']         = $this->request->param('is_login/d', 1);
        $param['is_offi_register'] = $this->request->param('is_offi_register/d', 1);
        $param['is_offi_login']    = $this->request->param('is_offi_login/d', 1);
        $param['is_mini_register'] = $this->request->param('is_mini_register/d', 1);
        $param['is_mini_login']    = $this->request->param('is_mini_login/d', 1);

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
        $setting = $this->info();

        $data['diy_config'] = $setting['diy_config'];

        return success($data);
    }

    /**
     * @Apidoc\Title("自定义设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="diyConParam")
     */
    public function diyEdit()
    {
        $param['diy_config'] = $this->request->param('diy_config/a', []);

        validate(SettingValidate::class)->scene('diy_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }
}
