<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminVerifyValidate;
use app\admin\validate\AdminUserValidate;
use app\admin\service\AdminVerifyService;
use app\admin\service\AdminLoginService;

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
        $AdminVerifyService = new AdminVerifyService();

        $data = $AdminVerifyService->verify();

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
        $param['username']       = Request::param('username/s', '');
        $param['password']       = Request::param('password/s', '');
        $param['verify_id']      = Request::param('verify_id/s', '');
        $param['verify_code']    = Request::param('verify_code/s', '');
        $param['request_ip']     = Request::ip();
        $param['request_method'] = Request::method();

        $verify_config = AdminVerifyService::config();
        
        if ($verify_config['switch']) {
            validate(AdminVerifyValidate::class)->scene('check')->check($param);
        }

        validate(AdminUserValidate::class)->scene('user_login')->check($param);

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
        $param['admin_user_id'] = admin_user_id();

        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data = AdminLoginService::logout($param['admin_user_id']);

        return success($data, '退出成功');
    }
}
