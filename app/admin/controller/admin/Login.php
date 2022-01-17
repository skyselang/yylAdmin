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
use app\common\service\admin\SettingService;
use app\common\service\admin\LoginService;
use app\common\utils\CaptchaUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Group("adminAuthority")
 * @Apidoc\Sort("660")
 */
class Login
{
    /**
     * @Apidoc\Title("设置信息")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="app\common\model\admin\SettingModel\infoReturn")
     */
    public function setting()
    {
        $setting = SettingService::info();

        $data['system_name']  = $setting['system_name'];
        $data['page_title']   = $setting['page_title'];
        $data['logo_url']     = $setting['logo_url'];
        $data['favicon_url']  = $setting['favicon_url'];
        $data['login_bg_url'] = $setting['login_bg_url'];

        if ($setting['captcha_switch']) {
            $captcha = CaptchaUtils::create($setting['captcha_type']);
            $data    = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data['captcha_switch'] = $setting['captcha_switch'];

        if ($setting['captcha_switch']) {
            $captcha = CaptchaUtils::create($setting['captcha_type']);
            $data    = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\loginParam")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\loginReturn")
     */
    public function login()
    {
        $param['username']     = Request::param('username/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(UserValidate::class)->scene('login')->check($param);

        $setting = SettingService::info();
        if ($setting['captcha_switch']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($captcha_check)) {
                exception('验证码错误');
            }
        }

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     */
    public function logout()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(UserValidate::class)->scene('id')->check($param);

        $data = LoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
