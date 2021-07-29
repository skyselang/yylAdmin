<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理控制器
namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\SettingValidate;
use app\common\service\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("基础设置")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("80")
 */
class Setting
{
    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingModel\tokenInfo")
     * )
     */
    public function tokenInfo()
    {
        $data = SettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingModel\tokenInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function tokenEdit()
    {
        $param['token_name'] = Request::param('token_name/s', '');
        $param['token_key']  = Request::param('token_key/s', '');
        $param['token_exp']  = Request::param('token_exp/d', 720);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::tokenEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingModel\captchaInfo")
     * )
     */
    public function captchaInfo()
    {
        $data = SettingService::captchaInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingModel\captchaInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function captchaEdit()
    {
        $param['captcha_register'] = Request::param('captcha_register/d', 0);
        $param['captcha_login']    = Request::param('captcha_login/d', 0);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::captchaEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingModel\logInfo")
     * )
     */
    public function logInfo()
    {
        $data = SettingService::logInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingModel\logInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function logEdit()
    {
        $param['log_switch'] = Request::param('log_switch/d', 0);

        validate(SettingValidate::class)->scene('log_edit')->check($param);

        $data = SettingService::logEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("API设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\SettingModel\apiInfo")
     * )
     */
    public function apiInfo()
    {
        $data = SettingService::apiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("API设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\SettingModel\apiInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function apiEdit()
    {
        $param['api_rate_num']  = Request::param('api_rate_num/d', 3);
        $param['api_rate_time'] = Request::param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::apiEdit($param);

        return success($data);
    }
}
