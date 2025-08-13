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
use app\common\validate\system\UserValidate;
use app\common\service\system\SettingService;
use app\common\service\system\LoginService;
use app\common\utils\AjCaptchaUtils;
use app\common\utils\CaptchaUtils;

/**
 * @Apidoc\Title("lang(登录退出)")
 * @Apidoc\Group("logout")
 * @Apidoc\Sort("100")
 */
class Login extends BaseController
{
    /**
     * @Apidoc\Title("lang(设置信息)")
     * @Apidoc\Desc("系统信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref={SettingService::class,"info"}, field="system_name,page_title,favicon_url,logo_url,login_bg_url,login_bg_color,page_limit,is_watermark,api_timeout,token_type,token_name,captcha_switch,captcha_mode,captcha_type")
     * @Apidoc\Returned(ref="captchaReturn")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function setting()
    {
        $data = SettingService::info('system_name,page_title,favicon_url,logo_url,login_bg_url,login_bg_color,page_limit,is_watermark,api_timeout,token_type,token_name,captcha_switch,captcha_mode,captcha_type,login_msg_switch,login_msg_type,login_msg_time,login_msg_text');

        $data['captcha_img'] = '';
        if ($data['captcha_switch']) {
            if ($data['captcha_mode'] == 1) {
                $captcha = CaptchaUtils::create($data['captcha_type']);
                $data    = array_merge($data, $captcha);
            }
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(验证码)")
     * @Apidoc\Desc("get获取验证码，post验证行为验证码")
     * @Apidoc\Method("POST,GET")
     * @Apidoc\Param("captchaType", type="string", require=true, desc="lang(行为，验证码方式：blockPuzzle、clickWord)")
     * @Apidoc\Param("pointJson", type="string", default="", desc="lang(行为，pointJson)")
     * @Apidoc\Param("token", type="string", default="", desc="lang(行为，token)")
     * @Apidoc\Returned(ref={SettingService::class,"info"}, field="captcha_switch,captcha_mode,captcha_type")
     * @Apidoc\Returned(ref="captchaReturn")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function captcha()
    {
        $data = SettingService::info('captcha_switch,captcha_mode,captcha_type');

        if ($this->request->isGet()) {
            $data['captcha_img'] = '';
            if ($data['captcha_switch']) {
                if ($data['captcha_mode'] == 2) {
                    ini_set('memory_limit', '512M');
                    $AjCaptchaUtils = new AjCaptchaUtils();
                    $captcha = $AjCaptchaUtils->get($data['captcha_type']);
                    $data = array_merge($data, $captcha);
                } else {
                    $captcha = CaptchaUtils::create($data['captcha_type']);
                    $data = array_merge($data, $captcha);
                }
            }
        } else {
            $captchaData = $this->param('');
            $AjCaptchaUtils = new AjCaptchaUtils();
            $data = $AjCaptchaUtils->check($data['captcha_type'], $captchaData);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(登录)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={LoginService::class,"login"})
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref={LoginService::class,"login"})
     * @Apidoc\After(event="setGlobalBody", key="AdminToken", value="res.data.data.AdminToken", desc="AdminToken")
     * @Apidoc\After(event="setGlobalQuery", key="AdminToken", value="res.data.data.AdminToken", desc="AdminToken")
     * @Apidoc\After(event="setGlobalHeader", key="AdminToken", value="res.data.data.AdminToken", desc="AdminToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function login()
    {
        $param = $this->params([
            'username/s'     => '',
            'password/s'     => '',
            'captcha_id/s'   => '',
            'captcha_code/s' => '',
            'ajcaptcha',
        ]);

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::info();
        if ($setting['captcha_switch']) {
            if ($setting['captcha_mode'] == 2) {
                $AjCaptchaUtils = new AjCaptchaUtils();
                $captcha_check = $AjCaptchaUtils->checkTwo($setting['captcha_type'], $param['ajcaptcha']);
                if ($captcha_check['error']) {
                    return error(lang('验证码错误'));
                }
            } else {
                if (empty($param['captcha_code'])) {
                    return error(lang('请输入验证码'));
                }
                $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
                if (empty($captcha_check)) {
                    return error(lang('验证码错误'));
                }
            }
        }

        $data = LoginService::login($param);

        return success($data, lang('登录成功'));
    }

    /**
     * @Apidoc\Title("lang(退出)")
     * @Apidoc\Method("POST")
     * @Apidoc\Before(event="clearGlobalHeader", key="AdminToken")
     * @Apidoc\Before(event="clearGlobalQuery", key="AdminToken")
     * @Apidoc\Before(event="clearGlobalBody", key="AdminToken")
     */
    public function logout()
    {
        $param['user_id'] = user_id();

        validate(UserValidate::class)->scene('info')->check($param);

        $data = LoginService::logout($param['user_id']);

        return success($data);
    }
}
