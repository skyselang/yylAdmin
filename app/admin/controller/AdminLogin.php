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
use app\cache\AdminVerifyCache;
use think\facade\Config;

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
        $username    = Request::param('username/s', '');
        $password    = Request::param('password/s', '');
        $verify_id   = Request::param('verify_id/s', '');
        $verify_code = Request::param('verify_code/s', '');
        $login_ip    = Request::ip();

        $param['username'] = $username;
        $param['password'] = $password;
        $param['login_ip'] = $login_ip;

        $is_verify = Config::get('admin.is_verify', false);
        if ($is_verify) {
            if (empty($verify_code)) {
                error('请输入验证码');
            }

            $AdminVerifyService = new AdminVerifyService();
            $check_verify = $AdminVerifyService->check($verify_id, $verify_code);
            if (empty($check_verify)) {
                error('验证码错误');
            }
        }

        validate(AdminUserValidate::class)->scene('user_login')->check($param);

        $data = AdminLoginService::login($param);

        AdminVerifyCache::del($verify_id);

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
     * @return void
     */
    public function verify()
    {
        $is_verify = Config::get('admin.is_verify', false);
        $res['is_verify'] = $is_verify;

        if ($is_verify) {
            $AdminVerifyService = new AdminVerifyService();
            $verify = $AdminVerifyService->create();

            $res['verify_id']  = $verify['verify_id'];
            $res['verify_src'] = $verify['verify_src'];
        }

        return success($res);
    }
}
