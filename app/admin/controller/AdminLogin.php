<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-03-26
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\service\VerifyService;
use app\admin\validate\AdminVerifyValidate;
use app\admin\validate\AdminAdminValidate;
use app\admin\service\AdminLoginService;
use app\admin\service\AdminSettingService;

class AdminLogin
{
    /**
     * 验证码
     *
     * @method GET
     *
     * @return json
     */
    public function verify()
    {
        $config = AdminSettingService::settingVerify();

        $data = VerifyService::create($config);

        return success($data);
    }

    /**
     * 登录
     *
     * @method POST
     * 
     * @return json
     */
    public function login()
    {
        $param['username']    = Request::param('username/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['verify_id']   = Request::param('verify_id/s', '');
        $param['verify_code'] = Request::param('verify_code/s', '');

        $config = AdminSettingService::settingVerify();
        
        if ($config['switch']) {
            validate(AdminVerifyValidate::class)->scene('check')->check($param);
        }

        validate(AdminAdminValidate::class)->scene('admin_login')->check($param);

        $data = AdminLoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * 退出
     *
     * @method POST
     * 
     * @return json
     */
    public function logout()
    {
        $param['admin_admin_id'] = admin_admin_id();

        validate(AdminAdminValidate::class)->scene('admin_id')->check($param);

        $data = AdminLoginService::logout($param['admin_admin_id']);

        return success($data, '退出成功');
    }
}
