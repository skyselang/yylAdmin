<?php
/*
 * @Description  : 用户验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-05
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminUserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_id' => ['require', 'checkAdminUser'],
        'username'      => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname'      => ['require', 'checkNickname', 'length' => '1,32'],
        'password'      => ['require', 'length' => '6,18'],
        'passwords'     => ['require', 'length' => '6,18'],
        'email'         => ['email', 'checkEmail'],
        'avatar'        => ['require', 'file', 'image', 'fileExt' => 'jpg,png', 'fileSize' => '51200'],
    ];

    // 错误信息
    protected $message  =   [
        'admin_user_id.require' => 'admin_user_id must',
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为2至32个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'passwords.require'     => '请输入新密码',
        'passwords.length'      => '新密码长度为6至18个字符',
        'email.email'           => '请输入正确的邮箱地址',
        'avatar.require'        => '请选择图片',
    ];

    // 验证场景
    protected $scene = [
        'admin_user_id' => ['admin_user_id'],
        'username'      => ['username'],
        'nickname'      => ['nickname'],
        'password'      => ['password'],
        'user_login'    => ['username', 'password'],
        'user_add'      => ['username', 'nickname', 'password', 'email'],
        'user_edit'     => ['admin_user_id', 'username', 'nickname', 'email'],
        'user_pwd'      => ['admin_user_id', 'password'],
        'users_edit'    => ['admin_user_id', 'username', 'nickname', 'email'],
        'users_pwd'     => ['admin_user_id', 'password', 'passwords'],
        'users_avatar'  => ['admin_user_id', 'avatar'],

    ];

    // 验证场景定义：登录
    public function sceneuser_login()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length'])
            ->remove('password', ['length']);
    }

    // 自定义验证规则：用户是否存在
    protected function checkAdminUser($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';

        $where[] = ['is_delete', '=', 0];
        $where[] = ['admin_user_id', '=', $admin_user_id];

        $check = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        return $check ? true : '用户不存在：' . $admin_user_id;
    }

    // 自定义验证规则：账号是否存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $username      = $data['username'];

        $where[] = ['is_delete', '=', 0];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
            $where[] = ['username', '=', $username];
        } else {
            $where[] = ['username', '=', $username];
        }

        $check = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        return $check ? '账号已存在' : true;
    }

    // 自定义验证规则：昵称是否存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $nickname      = $data['nickname'];

        $where[] = ['is_delete', '=', 0];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
            $where[] = ['nickname', '=', $nickname];
        } else {
            $where[] = ['nickname', '=', $nickname];
        }

        $check = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        return $check ? '昵称已存在' : true;
    }

    // 自定义验证规则：邮箱是否存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $email         = $data['email'];

        $where[] = ['is_delete', '=', 0];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
            $where[] = ['email', '=', $email];
        } else {
            $where[] = ['email', '=', $email];
        }

        $check = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        return $check ? '邮箱已存在' : true;
    }
}
