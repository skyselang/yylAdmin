<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理控制器
namespace app\admin\controller\setting;

use think\facade\Request;
use app\common\validate\setting\SettingValidate;
use app\common\service\setting\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("adminSetting")
 * @Apidoc\Sort("540")
 */
class Setting
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
        $param['token_key'] = Request::param('token_key/s', '');
        $param['token_exp'] = Request::param('token_exp/d', 720);

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
        $param['captcha_register'] = Request::param('captcha_register/d', 0);
        $param['captcha_login']    = Request::param('captcha_login/d', 0);

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
        $param['log_switch']    = Request::param('log_switch/d', 0);
        $param['log_save_time'] = Request::param('log_save_time/d', 0);

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
        $param['api_rate_num']  = Request::param('api_rate_num/d', 3);
        $param['api_rate_time'] = Request::param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("自定义设置信息")
     * @Apidoc\Returned("diy_config", type="array", default="", desc="自定义设置",
     *     @Apidoc\Returned("config_key", type="string", require=true, default="", desc="键名"),
     *     @Apidoc\Returned("config_val", type="string", require=false, default="", desc="键值"),
     *     @Apidoc\Returned("config_desc", type="string", require=false, default="", desc="说明")
     * )
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
     * @Apidoc\Param("diy_config", type="array", default="", desc="自定义设置",
     *     @Apidoc\Param("config_key", type="string", require=true, default="", desc="键名"),
     *     @Apidoc\Param("config_val", type="string", require=false, default="", desc="键值"),
     *     @Apidoc\Param("config_desc", type="string", require=false, default="", desc="说明")
     * )
     */
    public function diyEdit()
    {
        $param['diy_config'] = Request::param('diy_config/a', []);

        validate(SettingValidate::class)->scene('diy_edit')->check($param);

        $param['diy_config'] = serialize($param['diy_config']);

        $data = SettingService::edit($param);

        return success($data);
    }
}