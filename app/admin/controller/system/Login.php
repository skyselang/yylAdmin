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
use app\common\validate\system\UserValidate;
use app\common\service\system\SettingService;
use app\common\service\system\LoginService;
use app\common\service\utils\AjCaptchaUtils;
use app\common\service\utils\CaptchaUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Group("logout")
 * @Apidoc\Sort("100")
 */
class Login extends BaseController
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Desc("系统信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="app\common\model\system\SettingModel", field="system_name,page_title,captcha_switch,captcha_mode,captcha_type")
     * @Apidoc\Returned(ref="app\common\service\system\SettingService\info")
     */
    public function setting()
    {
        $setting = SettingService::info();

        $data = [];
        $field = 'system_name,page_title,logo_url,favicon_url,login_bg_url,captcha_switch,captcha_mode,captcha_type,token_type,token_name';
        $field = explode(',', $field);
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
     * @Apidoc\Query("captchaType", type="string", require=true, desc="行为，验证码方式：blockPuzzle、clickWord")
     * @Apidoc\Query("clientUid", type="string", default="", desc="行为，唯一标识UUID")
     * @Apidoc\Query("ts", type="int", default="", desc="行为，时间戳/毫秒")
     * @Apidoc\Param("captchaType", type="string", require=true, desc="行为，验证码方式：blockPuzzle、clickWord")
     * @Apidoc\Param("pointJson", type="string", default="", desc="行为，pointJson")
     * @Apidoc\Param("token", type="string", default="", desc="行为，token")
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
            $captchaData = $this->request->param('');
            $AjCaptchaUtils = new AjCaptchaUtils();
            $data = $AjCaptchaUtils->check($setting['captcha_type'], $captchaData);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\system\UserModel", field="username,password")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\system\UserModel", field="user_id")
     * @Apidoc\Returned(ref="app\common\service\system\UserService\login")
     * @Apidoc\After(event="setGlobalParam", key="AdminToken", value="res.data.data.AdminToken", desc="AdminToken")
     * @Apidoc\After(event="setGlobalHeader", key="AdminToken", value="res.data.data.AdminToken", desc="AdminToken")
     */
    public function login()
    {
        $param['username']     = $this->request->param('username/s', '');
        $param['password']     = $this->request->param('password/s', '');
        $param['captcha_id']   = $this->request->param('captcha_id/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');
        $param['ajcaptcha']    = $this->request->param('ajcaptcha');

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::info();
        if ($setting['captcha_switch']) {
            if ($setting['captcha_mode'] == 2) {
                $AjCaptchaUtils = new AjCaptchaUtils();
                $captcha_check = $AjCaptchaUtils->checkTwo($setting['captcha_type'], $param['ajcaptcha']);
                if ($captcha_check['error']) {
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
        $param['user_id'] = user_id();

        validate(UserValidate::class)->scene('info')->check($param);

        $data = LoginService::logout($param['user_id']);

        return success($data, '退出成功');
    }
}
