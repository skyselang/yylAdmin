<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 登录退出控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\LoginService;
use app\common\service\admin\SettingService;
use app\common\utils\CaptchaUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("90")
 */
class Login
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnCaptcha")
     */
    public function captcha()
    {
        $setting = SettingService::captchaInfo();
        if ($setting['captcha_switch']) {
            $data = CaptchaUtils::create();
        } else {
            $data['captcha_switch'] = $setting['captcha_switch'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("username", type="string", require=true, desc="账号/手机/邮箱")
     * @Apidoc\Param("password", type="string", require=true, desc="密码")
     * @Apidoc\Param(ref="paramCaptcha")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Param(ref="app\common\model\admin\UserModel\login")
     * )
     */
    public function login()
    {
        $param['username']     = Request::param('username/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::captchaInfo();
        if ($setting['captcha_switch']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function logout()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserValidate::class)->scene('id')->check($param);

        $data = LoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
