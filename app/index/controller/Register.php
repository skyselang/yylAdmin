<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2021-03-27
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\UserValidate;
use app\admin\service\SettingService;
use app\admin\validate\VerifyValidate;
use app\index\service\RegisterService;

class Register
{
    /**
     * 注册
     *
     * @method POST
     *
     * @return json
     */
    public function register()
    {
        $param['username'] = Request::param('username/s', '');
        $param['password'] = Request::param('password/s', '');
        $param['nickname'] = Request::param('nickname/s', '');

        $verify['verify_id']   = Request::param('verify_id/s', '');
        $verify['verify_code'] = Request::param('verify_code/s', '');

        if (empty($param['nickname'])) {
            $param['nickname'] = $param['username'];
        }

        $verify_config = SettingService::verify();

        if ($verify_config['switch']) {
            validate(VerifyValidate::class)->scene('check')->check($verify);
        }

        validate(UserValidate::class)->scene('user_register')->check($param);

        $data = RegisterService::register($param);

        return success($data, '注册成功');
    }
}
