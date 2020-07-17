<?php
/*
 * @Description  : 登录，退出，验证码
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminLoginService;
use app\admin\service\AdminVerifyService;
use app\admin\validate\AdminUserValidate;

class AdminLogin
{
    /**
     * 登录
     *
     * @method POST
     * @return json
     */
    public function login()
    {
        $username       = Request::param('username/s', '');
        $password       = Request::param('password/s', '');
        $verify_id      = Request::param('verify_id/s', '');
        $verify_code    = Request::param('verify_code/s', '');
        $request_ip     = Request::ip();
        $request_method = Request::method();
        $menu_url       = app('http')->getName() . '/' . Request::pathinfo();

        $param['username']       = $username;
        $param['password']       = $password;
        $param['verify_id']      = $verify_id;
        $param['verify_code']    = $verify_code;
        $param['request_ip']     = $request_ip;
        $param['request_method'] = $request_method;
        $param['menu_url']       = $menu_url;

        validate(AdminUserValidate::class)->scene('user_login')->check($param);

        $data = AdminLoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * 退出
     *
     * @method POST
     * @return json
     */
    public function logout()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        $param['admin_user_id'] = $admin_user_id;

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminLoginService::logout($param);

        return success($data, '退出成功');
    }

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
}
