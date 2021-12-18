<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 注册控制器
namespace app\index\controller;

use think\facade\Request;
use app\common\utils\CaptchaUtils;
use app\common\validate\MemberValidate;
use app\common\service\SettingService;
use app\index\service\RegisterService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("注册")
 * @Apidoc\Sort("220")
 * @Apidoc\Group("login")
 */
class Register
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::captchaInfo();
        if ($setting['captcha_register']) {
            $data = CaptchaUtils::create();
        } else {
            $data['captcha_switch'] = $setting['captcha_register'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("注册")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\MemberModel\password")
     * @Apidoc\Param(ref="app\common\model\MemberModel\nickname")
     * @Apidoc\Param("username",type="string",mock="@string('lower', 6, 12)")
     * @Apidoc\Param("nickname",type="string",mock="@cname")
     * @Apidoc\Param("password",type="string",mock="@string('lower', 6)")
     * @Apidoc\Param("phone",type="string",mock="@phone")
     * @Apidoc\Param("email",type="string",mock="@email")
     * @Apidoc\Param(ref="captchaParam")
     */
    public function register()
    {
        $param['username']     = Request::param('username/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['nickname']     = Request::param('nickname/s', '');
        $param['phone']        = Request::param('phone/s', '');
        $param['email']        = Request::param('email/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('register')->check($param);

        $setting = SettingService::captchaInfo();
        if ($setting['captcha_register']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        unset($param['captcha_id'], $param['captcha_code']);

        $data = RegisterService::register($param);

        return success($data, '注册成功');
    }
}
