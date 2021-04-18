<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-04-17
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminVerifyValidate;
use app\common\validate\AdminUserValidate;
use app\common\service\VerifyService;
use app\common\service\AdminLoginService;
use app\common\service\AdminSettingService;
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
        $config = AdminSettingService::info();
        $verify = $config['verify'];

        $data = VerifyService::create($verify);

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

        $config = AdminSettingService::info();
        $verify = $config['verify'];
        if ($verify['switch']) {
            validate(AdminVerifyValidate::class)->scene('check')->check($param);
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
