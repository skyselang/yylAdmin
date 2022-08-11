<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\setting;

use app\common\BaseController;
use app\common\validate\setting\SettingValidate;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("540")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\tokenInfoParam")
     */
    public function tokenInfo()
    {
        $setting = SettingService::info();

        $data['token_key'] = $setting['token_key'];
        $data['token_exp'] = $setting['token_exp'];

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel\tokenInfoParam")
     */
    public function tokenEdit()
    {
        $param['token_key'] = $this->param('token_key/s', '');
        $param['token_exp'] = $this->param('token_exp/d', 720);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\captchaInfoParam")
     */
    public function captchaInfo()
    {
        $setting = SettingService::info();

        $data['captcha_register'] = $setting['captcha_register'];
        $data['captcha_login']    = $setting['captcha_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel\captchaInfoParam")
     */
    public function captchaEdit()
    {
        $param['captcha_register'] = $this->param('captcha_register/d', 0);
        $param['captcha_login']    = $this->param('captcha_login/d', 0);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\logInfoParam")
     */
    public function logInfo()
    {
        $setting = SettingService::info();

        $data['log_switch']    = $setting['log_switch'];
        $data['log_save_time'] = $setting['log_save_time'];

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel\logInfoParam")
     */
    public function logEdit()
    {
        $param['log_switch']    = $this->param('log_switch/d', 0);
        $param['log_save_time'] = $this->param('log_save_time/d', 0);

        validate(SettingValidate::class)->scene('log_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\apiInfoParam")
     */
    public function apiInfo()
    {
        $setting = SettingService::info();

        $data['api_manage']    = $setting['api_manage'];
        $data['api_rate_num']  = $setting['api_rate_num'];
        $data['api_rate_time'] = $setting['api_rate_time'];

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel\apiInfoParam")
     */
    public function apiEdit()
    {
        $param['api_manage']    = $this->param('api_manage/d', 1);
        $param['api_rate_num']  = $this->param('api_rate_num/d', 3);
        $param['api_rate_time'] = $this->param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置信息")
     * @Apidoc\Returned(ref="app\common\model\setting\SettingModel\logregInfoParam")
     */
    public function logregInfo()
    {
        $setting = SettingService::info();

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
     * @Apidoc\Param(ref="app\common\model\setting\SettingModel\logregInfoParam")
     */
    public function logregEdit()
    {
        $param['is_register']      = $this->param('is_register/d', 1);
        $param['is_login']         = $this->param('is_login/d', 1);
        $param['is_offi_register'] = $this->param('is_offi_register/d', 1);
        $param['is_offi_login']    = $this->param('is_offi_login/d', 1);
        $param['is_mini_register'] = $this->param('is_mini_register/d', 1);
        $param['is_mini_login']    = $this->param('is_mini_login/d', 1);

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
        $setting = SettingService::info();

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
        $param['diy_config'] = $this->param('diy_config/a', []);

        validate(SettingValidate::class)->scene('diy_edit')->check($param);

        $param['diy_config'] = serialize($param['diy_config']);

        $data = SettingService::edit($param);

        return success($data);
    }
}
