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
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("系统设置")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("1000")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("系统设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="system_name,page_title,logo_id,favicon_id,login_bg_id")
     * @Apidoc\Returned(ref="app\common\service\system\SettingService\info", field="logo_url,favicon_url,login_bg_url")
     */
    public function systemInfo()
    {
        $data = SettingService::info('system_name,page_title,logo_id,favicon_id,login_bg_id,logo_url,favicon_url,login_bg_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("系统设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="system_name,page_title,logo_id,favicon_id,login_bg_id")
     */
    public function systemEdit()
    {
        $param = $this->params([
            'system_name/s' => '',
            'page_title/s'  => '',
            'logo_id/d'     => 0,
            'favicon_id/d'  => 0,
            'login_bg_id/d' => 0,
        ]);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存设置信息")
     * @Apidoc\Returned(ref="app\common\service\system\SettingService\info", field="cache_type")
     */
    public function cacheInfo()
    {
        $data = SettingService::info('cache_type');

        return success($data);
    }

    /**
     * @Apidoc\Title("缓存设置清除")
     * @Apidoc\Method("POST")
     */
    public function cacheClear()
    {
        $data = SettingService::cacheClear();

        return success($data, '清除缓存成功');
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $data = SettingService::info('token_key,token_exp,is_multi_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param = $this->params(['token_key/s' => '', 'token_exp/d' => 12, 'is_multi_login/d' => 0]);

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
        $data = SettingService::info('captcha_switch,captcha_mode,captcha_type');

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="captcha_switch,captcha_mode,captcha_type")
     */
    public function captchaEdit()
    {
        $param = $this->params(['captcha_switch/d' => 0, 'captcha_mode/d' => 1, 'captcha_type/d' => 1]);

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
        $data = SettingService::info('log_switch,log_save_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="log_switch,log_save_time")
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
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
        $data = SettingService::info('api_rate_num,api_rate_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param = $this->params(['api_rate_num/d' => 3, 'api_rate_time/d' => 1]);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置信息")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test")
     */
    public function emailInfo()
    {
        $data = SettingService::info('email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test');

        return success($data);
    }

    /**
     * @Apidoc\Title("邮件设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\SettingModel", field="email_host,email_port,email_secure,email_username,email_password,email_setfrom,email_test")
     */
    public function emailEdit()
    {
        $param = $this->params([
            'email_host/s'     => '',
            'email_port/s'     => '',
            'email_secure/s'   => 'ssl',
            'email_username/s' => '',
            'email_password/s' => '',
            'email_setfrom/s'  => '',
            'email_test/s'     => '',
        ]);

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
        $param = $this->params(['email_test/s' => '']);

        validate(SettingValidate::class)->scene('email_test')->check($param);

        $data = SettingService::emailTest($param);

        return success($data, '发送成功');
    }

    /**
     * @Apidoc\Title("服务器信息")
     * @Apidoc\Param("force", type="int", default=0, desc="是否强制刷新")
     */
    public function serverInfo()
    {
        $force = $this->param('force/d', 0);

        $data = SettingService::serverInfo($force);

        return success($data);
    }
}
