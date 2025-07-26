<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\system\SettingValidate as Validate;
use app\common\service\system\SettingService as Service;

/**
 * @Apidoc\Title("lang(系统设置)")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("50")
 */
class Setting extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * @Apidoc\Title("lang(系统设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="system_name,page_title,favicon_id,logo_id,login_bg_id,login_bg_color,is_watermark,favicon_url,logo_url,login_bg_url")
     */
    public function systemInfo()
    {
        $data = $this->service::info('system_name,page_title,favicon_id,logo_id,login_bg_id,login_bg_color,is_watermark,favicon_url,logo_url,login_bg_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(系统设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="system_name,page_title,favicon_id,logo_id,login_bg_id,login_bg_color,is_watermark")
     */
    public function systemEdit()
    {
        $param = $this->params([
            'system_name/s'    => '',
            'page_title/s'     => '',
            'favicon_id/d'     => 0,
            'logo_id/d'        => 0,
            'login_bg_id/d'    => 0,
            'login_bg_color/s' => '',
            'is_watermark/d'   => 0,
        ]);

        validate($this->validate)->scene('system_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(验证码设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="captcha_switch,captcha_mode,captcha_type")
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function captchaInfo()
    {
        $data = $this->service::info('captcha_switch,captcha_mode,captcha_type');
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(验证码设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="captcha_switch,captcha_mode,captcha_type")
     */
    public function captchaEdit()
    {
        $param = $this->params(['captcha_switch/d' => 0, 'captcha_mode/d' => 1, 'captcha_type/d' => 1]);

        validate($this->validate)->scene('captcha_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(邮件设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="email_host,email_port,email_secure,email_username,email_password,email_setfrom")
     */
    public function emailInfo()
    {
        $data = $this->service::info('email_host,email_port,email_secure,email_username,email_password,email_setfrom');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(邮件设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="email_host,email_secure,email_port,email_username,email_password,email_setfrom")
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

        validate($this->validate)->scene('email_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(日志设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="log_switch,log_save_time,log_unlogin,log_param_without,email_log_switch,email_log_save_time,sms_log_switch,sms_log_save_time")
     */
    public function logInfo()
    {
        $data = $this->service::info('log_switch,log_save_time,log_unlogin,log_param_without,email_log_switch,email_log_save_time,sms_log_switch,sms_log_save_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(日志设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="log_switch,log_save_time,log_unlogin,log_param_without,email_log_switch,email_log_save_time,sms_log_switch,sms_log_save_time")
     */
    public function logEdit()
    {
        $param = $this->params([
            'log_switch/d'          => 0,
            'log_save_time/d'       => 0,
            'log_unlogin/d'         => 30,
            'log_param_without/s'   => '',
            'email_log_switch/d'    => 0,
            'email_log_save_time/d' => 30,
            'sms_log_switch/d'      => 0,
            'sms_log_save_time/d'   => 30,
        ]);

        validate($this->validate)->scene('log_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(缓存设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="cache_type")
     */
    public function cacheInfo()
    {
        $data = $this->service::info('cache_type');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(缓存设置清除)")
     * @Apidoc\Method("POST")
     */
    public function cacheClear()
    {
        $data = $this->service::cacheClear();

        return success($data, lang('清除缓存成功'));
    }

    /**
     * @Apidoc\Title("lang(接口设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="api_rate_num,api_rate_time,api_timeout")
     */
    public function apiInfo()
    {
        $data = $this->service::info('api_rate_num,api_rate_time,api_timeout');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(接口设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="api_rate_num,api_rate_time,api_timeout")
     */
    public function apiEdit()
    {
        $param = $this->params(['api_rate_num/d' => 3, 'api_rate_time/d' => 1, 'api_timeout/d' => 60]);

        validate($this->validate)->scene('api_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(Token设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $data = $this->service::info('token_key,token_exp,is_multi_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(Token设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"edit"}, field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param = $this->params(['token_key/s' => '', 'token_exp/d' => 12, 'is_multi_login/d' => 0]);

        validate($this->validate)->scene('token_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(服务器信息)")
     * @Apidoc\Query(ref={Service::class,"serverInfo"})
     */
    public function serverInfo()
    {
        $force = $this->param('force/d', 0);

        $data = $this->service::serverInfo($force);

        return success($data);
    }
}
