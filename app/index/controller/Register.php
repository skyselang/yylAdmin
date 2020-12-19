<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2020-12-19
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\admin\service\MemberService;

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
        $param['nickname'] = $param['username'];

        validate(MemberValidate::class)->scene('member_register')->check($param);

        $data = MemberService::add($param, 'post');

        return success($data, '注册成功');
    }
}
