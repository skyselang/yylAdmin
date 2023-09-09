<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\SettingService;
use app\common\service\utils\SmsUtils;
use app\common\service\utils\EmailUtils;
use app\common\service\utils\CaptchaUtils;
use app\common\service\utils\AjCaptchaUtils;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\cache\utils\CaptchaEmailCache;
use app\api\service\RegisterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("注册")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("100")
 */
class Register extends BaseController
{
    /**
     * @Apidoc\Title("用户名注册验证码")
     * @Apidoc\Desc("get获取验证码，post验证行为验证码")
     * @Apidoc\Method("GET,POST")
     * @Apidoc\Query("captchaType", type="string", require=true, desc="行为，验证码方式：blockPuzzle、clickWord")
     * @Apidoc\Query("clientUid", type="string", default="", desc="行为，唯一标识UUID")
     * @Apidoc\Query("ts", type="int", default="", desc="行为，时间戳/毫秒")
     * @Apidoc\Param("captchaType", type="string", require=true, desc="行为，验证码方式：blockPuzzle、clickWord")
     * @Apidoc\Param("pointJson", type="string", default="", desc="行为，pointJson")
     * @Apidoc\Param("token", type="string", default="", desc="行为，token")
     * @Apidoc\Returned(ref="captchaReturn")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function captcha()
    {
        $data = SettingService::info('is_captcha_register,captcha_mode,captcha_type');

        $data['captcha_switch'] = $data['is_captcha_register'];
        if ($this->request->isGet()) {
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
            $data = $AjCaptchaUtils->check($data['captcha_type'], $captchaData);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户名注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,username,password,application")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function register()
    {
        $setting = SettingService::info();
        if (!$setting['is_register']) {
            return error('系统维护，无法注册');
        }

        $param = $this->params([
            'application/s'  => SettingService::APP_UNKNOWN,
            'nickname/s'     => '',
            'username/s'     => '',
            'password/s'     => '',
            'captcha_id/s'   => '',
            'captcha_code/s' => '',
            'ajcaptcha',
        ]);

        $setting = SettingService::info();
        if ($setting['is_captcha_register']) {
            if ($setting['captcha_mode'] == 2) {
                $AjCaptchaUtils = new AjCaptchaUtils();
                $captcha_check = $AjCaptchaUtils->checkTwo($setting['captcha_type'], $param['ajcaptcha']);
                if ($captcha_check['error']) {
                    exception('验证码错误');
                }
            } else {
                if (empty($param['captcha_id'])) {
                    return error('captcha_id must');
                }
                if (empty($param['captcha_code'])) {
                    return error('请输入验证码');
                }
                $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
                if (empty($captcha_check)) {
                    return error('验证码错误');
                }
            }
        }

        validate(MemberValidate::class)->scene('usernameRegister')->check($param);

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);
        unset($data['password']);

        return success($data, '注册成功');
    }

    /**
     * @Apidoc\Title("手机注册验证码")
     * @Apidoc\Query("phone", type="string", require=true, desc="手机", mock="@phone")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function phoneCaptcha()
    {
        $setting = SettingService::info();
        if (!$setting['is_phone_register']) {
            return error('系统维护，无法注册');
        }

        $param = $this->params(['phone/s' => '']);

        validate(MemberValidate::class)->scene('phoneRegisterCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success($param, '发送成功');
    }

    /**
     * @Apidoc\Title("手机注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("phone", type="string", require=true, desc="手机", mock="@phone")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,password,application")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function phoneRegister()
    {
        $setting = SettingService::info();
        if (!$setting['is_phone_register']) {
            return error('系统维护，无法注册');
        }

        $param = $this->params([
            'phone/s'        => '',
            'nickname/s'     => '',
            'password/s'     => '',
            'captcha_id/s'   => '',
            'captcha_code/s' => '',
            'application/s'  => SettingService::APP_UNKNOWN,
        ]);

        if (empty($param['captcha_code'])) {
            return error('请输入验证码');
        }
        $captcha = CaptchaSmsCache::get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            return error('验证码错误');
        }
        validate(MemberValidate::class)->scene('phoneRegister')->check($param);

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);
        CaptchaSmsCache::del($param['phone']);
        unset($data['password']);

        return success($data, '注册成功');
    }

    /**
     * @Apidoc\Title("邮箱注册验证码")
     * @Apidoc\Query("email", type="string", require=true, desc="邮箱", mock="@email")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function emailCaptcha()
    {
        $setting = SettingService::info();
        if (!$setting['is_email_register']) {
            return error('系统维护，无法注册');
        }

        $param = $this->params(['email/s' => '']);

        validate(MemberValidate::class)->scene('emailRegisterCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success($param, '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email", type="string", require=true, desc="邮箱", mock="@email")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,password,application")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function emailRegister()
    {
        $setting = SettingService::info();
        if (!$setting['is_email_register']) {
            return error('系统维护，无法注册');
        }

        $param = $this->params([
            'email/s'        => '',
            'nickname/s'     => '',
            'password/s'     => '',
            'captcha_id/s'   => '',
            'captcha_code/s' => '',
            'application/s'  => SettingService::APP_UNKNOWN,
        ]);

        if (empty($param['captcha_code'])) {
            return error('请输入验证码');
        }
        $captcha = CaptchaEmailCache::get($param['email']);
        if ($captcha != $param['captcha_code']) {
            return error('验证码错误');
        }
        validate(MemberValidate::class)->scene('emailRegister')->check($param);

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);
        CaptchaEmailCache::del($param['email']);
        unset($data['password']);

        return success($data, '注册成功');
    }
}
