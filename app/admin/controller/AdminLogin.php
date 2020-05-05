<?php
/*
 * @Description  : 登录|退出
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-26
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminLoginService;
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
        $username = Request::param('username/s', '');
        $password = Request::param('password/s', '');
        $login_ip = Request::ip();

        $param['username'] = $username;
        $param['password'] = $password;
        $param['login_ip'] = $login_ip;

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
}
