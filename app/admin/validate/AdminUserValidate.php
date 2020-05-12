<?php
/*
 * @Description  : 用户验证器
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-24
 */

namespace app\admin\validate;

use think\Validate;

class AdminUserValidate extends Validate
{
    protected $rule = [
        'admin_user_id' => ['require'],
        'username'      => ['require', 'alphaNum', 'length' => '3,64'],
        'nickname'      => ['require', 'chsDash', 'length' => '1,32'],
        'password'      => ['require', 'alphaNum', 'length' => '6,18'],
        'passwords'     => ['require', 'alphaNum', 'length' => '6,18'],
    ];

    protected $message  =   [
        'admin_user_id.require' => '缺少参数admin_user_id',
        'username.require'      => '请输入账号',
        'username.alphaNum'     => '账号组成为字母或数字',
        'username.length'       => '账号长度为3至64个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.chsDash'      => '昵称组成为中文、字母、数字、下划线_、破折号-',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'password.alphaNum'     => '密码组成为字母、数字',
        'passwords.require'     => '请输入新密码',
        'passwords.length'      => '新密码长度为6至18个字符',
        'passwords.alphaNum'    => '新密码组成为字母、数字',
    ];

    protected $scene = [
        'admin_user_id' => ['admin_user_id'],
        'username'      => ['username'],
        'nickname'      => ['nickname'],
        'password'      => ['password'],
        'user_add'      => ['username', 'nickname', 'password'],
        'user_edit'     => ['admin_user_id', 'username', 'nickname'],
        'user_repwd'    => ['admin_user_id', 'password'],
        'user_center1'  => ['admin_user_id', 'username', 'nickname'],
        'user_center2'  => ['admin_user_id', 'username', 'nickname', 'password', 'passwords'],
    ];

    public function sceneuser_login()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['alphaNum', 'length'])
            ->remove('password', ['alphaNum', 'length']);
    }
}
