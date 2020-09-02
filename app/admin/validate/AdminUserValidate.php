<?php
/*
 * @Description  : 用户验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-02
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use think\facade\Config;
use app\admin\service\AdminVerifyService;

class AdminUserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_id' => ['require'],
        'username'      => ['require', 'length' => '3,256'],
        'nickname'      => ['require', 'length' => '1,32'],
        'password'      => ['require', 'length' => '6,18'],
        'passwords'     => ['require', 'length' => '6,18'],
        'verify_code'   => ['checkVerify'],
        'email'         => ['checkEmail', 'email'],
        'avatar'        => ['require', 'file', 'fileExt' => 'jpg,png', 'fileSize' => '102400'],
    ];

    // 错误信息
    protected $message  =   [
        'admin_user_id.require' => '缺少参数admin_user_id',
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为3至256个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'passwords.require'     => '请输入新密码',
        'passwords.length'      => '新密码长度为6至18个字符',
        'email.email'           => '邮箱格式错误',
        'avatar.require'        => '请选择图片',
    ];

    // 验证场景
    protected $scene = [
        'admin_user_id' => ['admin_user_id'],
        'username'      => ['username'],
        'nickname'      => ['nickname'],
        'password'      => ['password'],
        'user_login'    => ['username', 'password', 'verify_code'],
        'user_add'      => ['username', 'nickname', 'password', 'email'],
        'user_edit'     => ['admin_user_id', 'username', 'nickname', 'email'],
        'user_pwd'      => ['admin_user_id', 'password'],
        'users_edit'    => ['admin_user_id', 'username', 'nickname', 'email'],
        'users_pwd'     => ['admin_user_id', 'password', 'passwords'],
        'users_avatar'  => ['admin_user_id', 'avatar'],

    ];

    // 验证场景定义-登录
    public function sceneuser_login()
    {
        return $this->only(['username', 'password', 'verify_code'])
            ->remove('username', ['alphaNum', 'length'])
            ->remove('password', ['alphaNum', 'length']);
    }

    // 验证场景定义-修改信息
    public function sceneusers_edit()
    {
        return $this->only(['username', 'nickname', 'email'])
            ->append('username', ['checkUsername']);
    }

    // 账号是否存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $username      = $data['username'];

        $where[] = ['is_delete', '=', 0];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
            $where[] = ['email', '=', $username];
        } else {
            $where[] = ['email', '=', $username];
        }
        $check = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        return $check ? '账号已存在' : true;
    }

    // 邮箱是否存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $email         = $data['email'];

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

    // 验证码验证
    protected function checkVerify($value, $rule, $data = [])
    {
        $verify_id   = $data['verify_id'];
        $verify_code = $data['verify_code'];
        $is_verify   = Config::get('admin.is_verify', false);
        $check = false;
        if ($is_verify) {
            if (empty($verify_code)) {
                $check = true;
                $msg = '请输入验证码';
            } else {
                $AdminVerifyService = new AdminVerifyService();
                $check_verify = $AdminVerifyService->check($verify_id, $verify_code);
                if (empty($check_verify)) {
                    $check = true;
                    $msg = '验证码错误';
                }
            }
        }

        return $check ? $msg : true;
    }
}
