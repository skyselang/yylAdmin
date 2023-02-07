<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\validate\system\SettingValidate;
use app\common\service\system\SettingService;
use app\common\service\utils\ServerUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("系统设置")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("1000")
 */
class Setting extends BaseController
{
    /**
     * 设置信息
     *
     * @return array
     */
    public function info()
    {
        return SettingService::info();
    }

    /**
     * @Apidoc\Title("缓存设置信息")
     * @Apidoc\Returned("cache_type", type="string", default="", desc="缓存类型")
     */
    public function cacheInfo()
    {
        $setting = $this->info();

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
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="token_key,token_exp,is_multi_login")
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
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param['token_key']      = $this->request->param('token_key/s', '');
        $param['token_exp']      = $this->request->param('token_exp/d', 12);
        $param['is_multi_login'] = $this->request->param('is_multi_login/d', 0);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="captcha_switch,captcha_mode,captcha_type")
     */
    public function captchaInfo()
    {
        $setting = $this->info();

        $data['captcha_switch'] = $setting['captcha_switch'];
        $data['captcha_mode']   = $setting['captcha_mode'];
        $data['captcha_type']   = $setting['captcha_type'];

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="captcha_switch,captcha_mode,captcha_type")
     */
    public function captchaEdit()
    {
        $param['captcha_switch'] = $this->request->param('captcha_switch/d', 0);
        $param['captcha_mode']   = $this->request->param('captcha_mode/d', 1);
        $param['captcha_type']   = $this->request->param('captcha_type/d', 1);

        validate(SettingValidate::class)->scene('captcha_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="log_switch,log_save_time")
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
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="log_switch,log_save_time")
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
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
        $setting = $this->info();

        $data['api_rate_num']  = $setting['api_rate_num'];
        $data['api_rate_time'] = $setting['api_rate_time'];

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param['api_rate_num']  = $this->request->param('api_rate_num/d', 3);
        $param['api_rate_time'] = $this->request->param('api_rate_time/d', 1);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="logo_id,favicon_id,login_bg_id,system_name,page_title")
     * @Apidoc\Returned("logo_url", type="string", desc="logo链接")
     * @Apidoc\Returned("favicon_url", type="string", desc="favicon链接")
     * @Apidoc\Returned("login_bg_url", type="string", desc="登录背景链接")
     */
    public function systemInfo()
    {
        $setting = $this->info();

        $fields = 'logo_id,logo_url,favicon_id,favicon_url,login_bg_id,login_bg_url,system_name,page_title';
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            $data[$field] = $setting[$field] ?? '';
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="logo_id,logo_url,favicon_id,favicon_url,login_bg_id,login_bg_url,system_name,page_title")
     */
    public function systemEdit()
    {
        $param['system_name'] = $this->request->param('system_name/s', '');
        $param['page_title']  = $this->request->param('page_title/s', '');
        $param['logo_id']     = $this->request->param('logo_id/d', 0);
        $param['favicon_id']  = $this->request->param('favicon_id/d', 0);
        $param['login_bg_id'] = $this->request->param('login_bg_id/d', 0);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test")
     */
    public function emailInfo()
    {
        $setting = $this->info();

        $fields = 'email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test';
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            $data[$field] = $setting[$field] ?? '';
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test")
     */
    public function emailEdit()
    {
        $param['email_host']     = $this->request->param('email_host/s', '');
        $param['email_port']     = $this->request->param('email_port/s', '');
        $param['email_secure']   = $this->request->param('email_secure/s', 'ssl');
        $param['email_username'] = $this->request->param('email_username/s', '');
        $param['email_password'] = $this->request->param('email_password/s', '');
        $param['email_setfrom']  = $this->request->param('email_setfrom/s', '');
        $param['email_test']     = $this->request->param('email_test/s', '');

        validate(SettingValidate::class)->scene('email_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置测试")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="email_test")
     */
    public function emailTest()
    {
        $param['email_test'] = $this->request->param('email_test/s', '');

        validate(SettingValidate::class)->scene('email_test')->check($param);

        $data = SettingService::emailTest($param);

        return success($data, '发送成功');
    }

    /**
     * @Apidoc\Title("服务器信息")
     */
    public function serverInfo()
    {
        $data = ServerUtils::server();

        return success($data);
    }
}
