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
use app\common\validate\admin\UserValidate;
use app\common\service\admin\SettingService;
use app\common\service\admin\LoginService;
use app\common\utils\AjCaptchaUtils;
use app\common\utils\CaptchaUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Group("adminLogout")
 * @Apidoc\Sort("660")
 */
class Login extends BaseController
{
    /**
     * @Apidoc\Title("系统信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\loginSettingParam")
     */
    public function setting()
    {
        $setting = SettingService::info();

        $data = [];
        $field = ['system_name', 'page_title', 'logo_url', 'favicon_url', 'login_bg_url', 'captcha_switch', 'captcha_mode', 'captcha_type'];
        foreach ($field as $v) {
            $data[$v] = $setting[$v] ?? '';
        }

        if ($data['captcha_switch']) {
            if ($data['captcha_mode'] == 1) {
                $captcha = CaptchaUtils::create($data['captcha_type']);
                $data = array_merge($data, $captcha);
            }
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Desc("get获取验证码，post验证行为验证码")
     * @Apidoc\Method("GET,POST")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data = [];
        if ($this->request->isGet()) {
            $field = ['captcha_switch', 'captcha_mode', 'captcha_type'];
            foreach ($field as $v) {
                $data[$v] = $setting[$v] ?? '';
            }

            if ($data['captcha_switch']) {
                if ($data['captcha_mode'] == 2) {
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
            $data = $AjCaptchaUtils->check($setting['captcha_type'], $captchaData);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\loginParam")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\loginReturn")
     * @Apidoc\After(event="setGlobalParam", key="AdminToken", value="res.data.data.admin_token", desc="admin_token")
     * @Apidoc\After(event="setGlobalHeader", key="AdminToken", value="res.data.data.admin_token", desc="admin_token")
     */
    public function login()
    {
        $param['username']     = $this->param('username/s', '');
        $param['password']     = $this->param('password/s', '');
        $param['captcha_id']   = $this->param('captcha_id/s', '');
        $param['captcha_code'] = $this->param('captcha_code/s', '');
        $param['ajcaptcha']    = $this->param('ajcaptcha');

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::info();
        if ($setting['captcha_switch']) {
            if ($setting['captcha_mode'] == 2) {
                $AjCaptchaUtils = new AjCaptchaUtils();
                $captcha_check = $AjCaptchaUtils->check($setting['captcha_type'], $param['ajcaptcha']);
                if (empty($captcha_check)) {
                    exception('验证码错误');
                }
            } else {
                if (empty($param['captcha_code'])) {
                    exception('请输入验证码');
                }
                $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
                if (empty($captcha_check)) {
                    exception('验证码错误');
                }
            }
        }

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     * @Apidoc\Before(event="clearGlobalHeader",key="AdminToken")
     */
    public function logout()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserValidate::class)->scene('id')->check($param);

        $data = LoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
