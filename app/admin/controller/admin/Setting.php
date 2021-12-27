<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\SettingValidate;
use app\common\service\admin\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("设置管理")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("710")
 */
class Setting
{
    /**
     * @Apidoc\Title("缓存设置信息")
     * @Apidoc\Returned("type", type="string", default=" ", desc="缓存类型")
     */
    public function cacheInfo()
    {
        $data = SettingService::cacheInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存设置清除")
     * @Apidoc\Method("POST")
     */
    public function cacheClear()
    {
        $data = SettingService::cacheClear();

        return success($data, '缓存已清除');
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\tokenInfoParam")
     */
    public function tokenInfo()
    {
        $data = SettingService::tokenInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\tokenInfoParam")
     */
    public function tokenEdit()
    {
        $param['token_name'] = Request::param('token_name/s', '');
        $param['token_key']  = Request::param('token_key/s', '');
        $param['token_exp']  = Request::param('token_exp/d', 12);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::tokenEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\captchaInfoParam")
     */
    public function captchaInfo()
    {
        $data = SettingService::captchaInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\captchaInfoParam")
     */
    public function captchaEdit()
    {
        $param['captcha_switch'] = Request::param('captcha_switch/d', 0);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::captchaEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\logInfoParam")
     */
    public function logInfo()
    {
        $data = SettingService::logInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\logInfoParam")
     */
    public function logEdit()
    {
        $param['log_switch']    = Request::param('log_switch/d', 0);
        $param['log_save_time'] = Request::param('log_save_time/d', 0);

        validate(SettingValidate::class)->scene('log_edit')->check($param);

        $data = SettingService::logEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\apiInfoParam")
     */
    public function apiInfo()
    {
        $data = SettingService::apiInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\apiInfoParam")
     */
    public function apiEdit()
    {
        $param['api_rate_num']  = Request::param('api_rate_num/d', 3);
        $param['api_rate_time'] = Request::param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::apiEdit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\systemInfoParam")
     */
    public function systemInfo()
    {
        $data = SettingService::systemInfo();

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\systemInfoParam")
     */
    public function systemEdit()
    {
        $param['logo_id']     = Request::param('logo_id/d', 0);
        $param['favicon_id']  = Request::param('favicon_id/d', 0);
        $param['login_bg_id'] = Request::param('login_bg_id/d', 0);
        $param['system_name'] = Request::param('system_name/s', '');
        $param['page_title']  = Request::param('page_title/s', '');

        $data = SettingService::systemEdit($param);

        return success($data);
    }
}
