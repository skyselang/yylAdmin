<?php
/*
 * @Description  : 设置管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-05
 * @LastEditTime : 2021-07-01
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminSettingValidate;
use app\common\service\AdminSettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("55")
 */
class AdminSetting
{
    /**
     * @Apidoc\Title("缓存设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned("type", type="string", default="", desc="缓存类型"),
     * )
     */
    public function cacheInfo()
    {
        $data = AdminSettingService::cacheInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存设置清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function cacheClear()
    {
        $data = AdminSettingService::cacheClear();

        return success($data, '缓存已清除');
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\tokenInfo")
     * )
     */
    public function tokenInfo()
    {
        $data = AdminSettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\tokenInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function tokenEdit()
    {
        $param['token_name'] = Request::param('token_name/s', '');
        $param['token_key']  = Request::param('token_key/s', '');
        $param['token_exp']  = Request::param('token_exp/d', 12);

        validate(AdminSettingValidate::class)->scene('token_edit')->check($param);

        $data = AdminSettingService::tokenEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\captchaInfo"),
     * )
     */
    public function captchaInfo()
    {
        $data = AdminSettingService::captchaInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\captchaInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function captchaEdit()
    {
        $param['captcha_switch'] = Request::param('captcha_switch/d', 0);

        validate(AdminSettingValidate::class)->scene('captcha_edit')->check($param);

        $data = AdminSettingService::captchaEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\logInfo")
     * )
     */
    public function logInfo()
    {
        $data = AdminSettingService::logInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\logInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function logEdit()
    {
        $param['log_switch'] = Request::param('log_switch/d', 0);

        validate(AdminSettingValidate::class)->scene('log_edit')->check($param);

        $data = AdminSettingService::logEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("API设置信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *    @Apidoc\Returned(ref="app\common\model\AdminSettingModel\apiInfo")
     * )
     */
    public function apiInfo()
    {
        $data = AdminSettingService::apiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("API设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminSettingModel\apiInfo")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function apiEdit()
    {
        $param['api_rate_num']  = Request::param('api_rate_num/d', 3);
        $param['api_rate_time'] = Request::param('api_rate_time/d', 1);

        validate(AdminSettingValidate::class)->scene('api_edit')->check($param);

        $data = AdminSettingService::apiEdit($param);

        return success($data);
    }
}
