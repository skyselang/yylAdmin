<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\SettingValidate;
use app\common\service\admin\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("系统设置")
 * @Apidoc\Group("adminSystem")
 * @Apidoc\Sort("750")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("缓存设置信息")
     * @Apidoc\Returned("cache_type", type="string", default="", desc="缓存类型")
     */
    public function cacheInfo()
    {
        $setting = SettingService::info();

        $data['cache_type'] = $setting['cache_type'];

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
        $setting = SettingService::info();

        $data['token_key']      = $setting['token_key'];
        $data['token_exp']      = $setting['token_exp'];
        $data['is_multi_login'] = $setting['is_multi_login'];

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\tokenInfoParam")
     */
    public function tokenEdit()
    {
        $param['token_key']      = $this->param('token_key/s', '');
        $param['token_exp']      = $this->param('token_exp/d', 12);
        $param['is_multi_login'] = $this->param('is_multi_login/d', 0);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\captchaInfoParam")
     */
    public function captchaInfo()
    {
        $setting = SettingService::info();

        $data['captcha_switch'] = $setting['captcha_switch'];
        $data['captcha_mode']   = $setting['captcha_mode'];
        $data['captcha_type']   = $setting['captcha_type'];

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\captchaInfoParam")
     */
    public function captchaEdit()
    {
        $param['captcha_switch'] = $this->param('captcha_switch/d', 0);
        $param['captcha_mode']   = $this->param('captcha_mode/d', 1);
        $param['captcha_type']   = $this->param('captcha_type/d', 1);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\logInfoParam")
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
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\logInfoParam")
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
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\apiInfoParam")
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
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\apiInfoParam")
     */
    public function apiEdit()
    {
        $param['api_rate_num']  = $this->param('api_rate_num/d', 3);
        $param['api_rate_time'] = $this->param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\systemInfoParam")
     */
    public function systemInfo()
    {
        $setting = SettingService::info();

        $field = ['logo_id', 'logo_url', 'favicon_id', 'favicon_url', 'login_bg_id', 'login_bg_url', 'system_name', 'page_title'];
        foreach ($field as $v) {
            $data[$v] = $setting[$v] ?? '';
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\systemInfoParam")
     */
    public function systemEdit()
    {
        $param['system_name'] = $this->param('system_name/s', '');
        $param['page_title']  = $this->param('page_title/s', '');
        $param['logo_id']     = $this->param('logo_id/d', 0);
        $param['favicon_id']  = $this->param('favicon_id/d', 0);
        $param['login_bg_id'] = $this->param('login_bg_id/d', 0);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置信息")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\emailInfoParam")
     */
    public function emailInfo()
    {
        $setting = SettingService::info();

        $field = ['email_host', 'email_port', 'email_secure', 'email_username', 'email_password', 'email_setfrom', 'email_test'];
        foreach ($field as $v) {
            $data[$v] = $setting[$v];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\emailInfoParam")
     */
    public function emailEdit()
    {
        $param['email_host']     = $this->param('email_host/s', '');
        $param['email_port']     = $this->param('email_port/s', '');
        $param['email_secure']   = $this->param('email_secure/s', 'ssl');
        $param['email_username'] = $this->param('email_username/s', '');
        $param['email_password'] = $this->param('email_password/s', '');
        $param['email_setfrom']  = $this->param('email_setfrom/s', '');
        $param['email_test']     = $this->param('email_test/s', '');

        validate(SettingValidate::class)->scene('email_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置测试")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\SettingModel\emailTestParam")
     */
    public function emailTest()
    {
        $param['email_test'] = $this->param('email_test/s', '');

        validate(SettingValidate::class)->scene('email_test')->check($param);

        $data = SettingService::emailTest($param);

        return success($data, '发送成功');
    }
}
