<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2020-12-07
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\index\service\UserService;

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
        $param = Request::only(
            [
                'username' => '',
                'password' => '',
            ]
        );
        $param['nickname'] = $param['username'];

        validate(MemberValidate::class)->scene('member_register')->check($param);

        $data = UserService::register($param);

        return success($data, '注册成功');
    }
}
