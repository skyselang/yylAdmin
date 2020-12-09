<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-01
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\index\validate\VerifyValidate;
use app\index\service\LoginService;
use app\index\service\VerifyService;

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
        $VerifyService = new VerifyService();

        $data = $VerifyService->verify();

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
        $username       = Request::param('username/s', '');
        $password       = Request::param('password/s', '');
        $verify_id      = Request::param('verify_id/s', '');
        $verify_code    = Request::param('verify_code/s', '');
        $request_ip     = Request::ip();
        $request_method = Request::method();

        $param['username']       = $username;
        $param['password']       = $password;
        $param['verify_id']      = $verify_id;
        $param['verify_code']    = $verify_code;
        $param['request_ip']     = $request_ip;
        $param['request_method'] = $request_method;

        $verify_config = VerifyService::config();
        if ($verify_config['switch']) {
            validate(VerifyValidate::class)->scene('check')->check($param);
        }

        validate(MemberValidate::class)->scene('member_login')->check($param);

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
        $member_id = member_id();

        $param['member_id'] = $member_id;

        validate(MemberValidate::class)->scene('member_id')->check($param);

        $data = LoginService::logout($member_id);

        return success($data, '退出成功');
    }
}
