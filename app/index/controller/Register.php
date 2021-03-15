<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2021-03-08
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\UserValidate;
use app\admin\service\UserService;

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

        if (empty($param['nickname'])) {
            $param['nickname'] = $param['username'];
        }

        validate(UserValidate::class)->scene('user_register')->check($param);

        $data = UserService::add($param, 'post');

        return success($data, '注册成功');
    }
}
