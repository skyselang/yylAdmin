<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminUserValidate;
use app\common\service\AdminLoginService;
use app\common\service\AdminSettingService;
use app\common\utils\VerifyUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Group("admin")
 */
class AdminLogin
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Method("GET")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned(ref="returnVerify")
     */
    public function verify()
    {
        $setting = AdminSettingService::verifyInfo();
        if ($setting['verify_switch']) {
            $data = VerifyUtils::create();
        } else {
            $data['verify_switch'] = $setting['verify_switch'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("username", type="string", require=true, desc="账号/手机/邮箱")
     * @Apidoc\Param("password", type="string", require=true, desc="密码")
     * @Apidoc\Param(ref="paramVerify")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Param(ref="app\common\model\AdminUserModel\login")
     * )
     */
    public function login()
    {
        $param['username']    = Request::param('username/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['verify_id']   = Request::param('verify_id/s', '');
        $param['verify_code'] = Request::param('verify_code/s', '');

        $setting = AdminSettingService::verifyInfo();
        if ($setting['verify_switch']) {
            $check = VerifyUtils::check($param['verify_id'], $param['verify_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        validate(AdminUserValidate::class)->scene('login')->check($param);

        $data = AdminLoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function logout()
    {
        $param['admin_user_id'] = admin_user_id();

        validate(AdminUserValidate::class)->scene('id')->check($param);

        $data = AdminLoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
