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
use app\common\service\utils\SmsUtils;
use app\common\service\utils\EmailUtils;
use app\common\service\utils\CaptchaUtils;
use app\common\validate\member\MemberValidate;
use app\common\service\member\SettingService;
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
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,username,password")
     * @Apidoc\Param(ref="captchaParam")
     */
    public function register()
    {
        $param['nickname']     = $this->request->param('nickname/s', '');
        $param['username']     = $this->request->param('username/s', '');
        $param['password']     = $this->request->param('password/s', '');
        $param['captcha_id']   = $this->request->param('captcha_id/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');
        $param['reg_channel']  = $this->request->param('reg_channel/s', SettingService::REG_CHANNEL_UNKNOWN);;
        $param['reg_type']     = SettingService::REG_TYPE_USERNAME;

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
        
        validate(MemberValidate::class)->scene('usernameRegister')->check($param);

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);

        return success($data, '注册成功');
    }

    /**
     * @Apidoc\Title("手机注册验证码")
     * @Apidoc\Query("phone", type="string", require=true, desc="手机", mock="@phone")
     */
    public function phoneCaptcha()
    {
        $param['phone'] = $this->request->param('phone/s', '');

        validate(MemberValidate::class)->scene('phoneRegisterCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("手机注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("phone", type="string", require=true, desc="手机", mock="@phone")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,password")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     */
    public function phoneRegister()
    {
        $param['phone']        = $this->request->param('phone/s', '');
        $param['nickname']     = $this->request->param('nickname/s', '');
        $param['password']     = $this->request->param('password/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');
        $param['reg_channel']  = $this->request->param('reg_channel/s', SettingService::REG_CHANNEL_UNKNOWN);;
        $param['reg_type']     =  SettingService::REG_TYPE_PHONE;

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
     * @Apidoc\Title("邮箱注册验证码")
     * @Apidoc\Query("email", type="string", require=true, desc="邮箱", mock="@email")
     */
    public function emailCaptcha()
    {
        $param['email'] = $this->request->param('email/s', '');

        validate(MemberValidate::class)->scene('emailRegisterCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email", type="string", require=true, desc="邮箱", mock="@email")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="nickname,password")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     */
    public function emailRegister()
    {
        $param['email']        = $this->request->param('email/s', '');
        $param['nickname']     = $this->request->param('nickname/s', '');
        $param['password']     = $this->request->param('password/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');
        $param['reg_channel']  = $this->request->param('reg_channel/s', SettingService::REG_CHANNEL_UNKNOWN);;
        $param['reg_type']     =  SettingService::REG_TYPE_EMAIL;

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
