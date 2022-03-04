<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 注册控制器
namespace app\api\controller;

use think\facade\Request;
use app\common\utils\CaptchaUtils;
use app\common\utils\SmsUtils;
use app\common\utils\EmailUtils;
use app\common\validate\member\MemberValidate;
use app\common\service\setting\SettingService;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\cache\utils\CaptchaEmailCache;
use app\api\service\RegisterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("注册")
 * @Apidoc\Sort("220")
 * @Apidoc\Group("login")
 */
class Register
{
    /**
     * @Apidoc\Title("用户名验证码")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data['captcha_switch'] = $setting['captcha_register'];

        if ($setting['captcha_register']) {
            $captcha = CaptchaUtils::create();
            $data    = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户名注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\nickname")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     * @Apidoc\Param("username", mock="@string('lower', 6, 12)")
     * @Apidoc\Param("nickname", mock="@ctitle(6, 12)")
     * @Apidoc\Param("password", mock="@string(6)")
     * @Apidoc\Param(ref="captchaParam")
     */
    public function register()
    {
        $param['username']     = Request::param('username/s', '');
        $param['nickname']     = Request::param('nickname/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('usernameRegister')->check($param);

        $setting = SettingService::info();
        if ($setting['captcha_register']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($captcha_check)) {
                exception('验证码错误');
            }
        }

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);

        return success($data, '注册成功');
    }

    /**
     * @Apidoc\Title("手机验证码")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\phone")
     */
    public function phoneCaptcha()
    {
        $param['phone'] = Request::param('phone/s', '');

        validate(MemberValidate::class)->scene('phoneRegisterCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success('', '发送成功');
    }

    /**
     * @Apidoc\Title("手机注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\phone")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\nickname")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     * @Apidoc\Param("phone", mock="@phone")
     * @Apidoc\Param("nickname", mock="@ctitle(6, 12)")
     * @Apidoc\Param("password", mock="@string(6)")
     * @Apidoc\Param("captcha_code", type="string", desc="手机验证码")
     */
    public function phoneRegister()
    {
        $param['phone']        = Request::param('phone/s', '');
        $param['nickname']     = Request::param('nickname/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('phoneRegister')->check($param);
        if (empty($param['captcha_code'])) {
            exception('请输入验证码');
        }
        $captcha = CaptchaSmsCache::get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        unset($param['captcha_code']);

        $data = RegisterService::register($param);
        CaptchaSmsCache::del($param['phone']);

        return success($data, '注册成功');
    }

    /**
     * @Apidoc\Title("邮箱验证码")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\email")
     */
    public function emailCaptcha()
    {
        $param['email'] = Request::param('email/s', '');

        validate(MemberValidate::class)->scene('emailRegisterCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success('', '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\email")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\nickname")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     * @Apidoc\Param("email", mock="@email")
     * @Apidoc\Param("nickname", mock="@ctitle(6, 12)")
     * @Apidoc\Param("password", mock="@string(6)")
     * @Apidoc\Param("captcha_code", type="string", desc="邮箱验证码")
     */
    public function emailRegister()
    {
        $param['email']        = Request::param('email/s', '');
        $param['nickname']     = Request::param('nickname/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('emailRegister')->check($param);
        if (empty($param['captcha_code'])) {
            exception('请输入验证码');
        }
        $captcha = CaptchaEmailCache::get($param['email']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        unset($param['captcha_code']);

        $data = RegisterService::register($param);
        CaptchaEmailCache::del($param['email']);

        return success($data, '注册成功');
    }
}
