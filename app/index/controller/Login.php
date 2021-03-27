<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-03-26
 */

namespace app\index\controller;


use think\facade\Request;
use app\common\service\VerifyService;
use app\admin\service\SettingService;
use app\admin\validate\UserValidate;
use app\admin\validate\VerifyValidate;
use app\index\service\LoginService;

class Login
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
        $config = SettingService::verify();

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

        $verify_config = SettingService::verify();

        if ($verify_config['switch']) {
            validate(VerifyValidate::class)->scene('check')->check($param);
        }

        validate(UserValidate::class)->scene('user_login')->check($param);

        $data = LoginService::login($param);

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
        $param['user_id'] = user_id();

        validate(UserValidate::class)->scene('user_id')->check($param);

        $data = LoginService::logout($param['user_id']);

        return success($data, '退出成功');
    }
}
