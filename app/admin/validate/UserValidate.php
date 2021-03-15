<?php
/*
 * @Description  : 用户验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-03-08
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\UserService;

class UserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'user_id'      => ['require', 'checkUser'],
        'username'     => ['require', 'alphaDash', 'checkUsername', 'length' => '2,32'],
        'nickname'     => ['checkNickname', 'length' => '1,32'],
        'password'     => ['require', 'alphaNum', 'length' => '6,18'],
        'password_old' => ['require', 'checkPwdOld'],
        'password_new' => ['require', 'alphaNum', 'length' => '6,18'],
        'phone'        => ['mobile', 'checkPhone'],
        'email'        => ['email', 'checkEmail'],
        'avatar'       => ['require', 'file', 'image', 'fileExt' => 'jpg,png', 'fileSize' => '102400'],
    ];

    // 错误信息
    protected $message = [
        'user_id.require'       => '缺少参数：用户id',
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为2至32个字符',
        'username.alphaDash'    => '账号由字母、数字、下划线、破折号组成',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'password.alphaNum'     => '密码只能为数字和字母',
        'password_old.require'  => '请输入旧密码',
        'password_new.require'  => '请输入新密码',
        'password_new.length'   => '新密码长度为6至18个字符',
        'password_new.alphaNum' => '新密码只能为数字和字母',
        'phone.mobile'          => '请输入正确的手机号码',
        'email.email'           => '请输入正确的邮箱地址',
        'avatar.require'        => '请选择图片',
        'avatar.file'           => '请选择图片文件',
        'avatar.image'          => '请选择图片格式文件',
        'avatar.fileExt'        => '请选择jpg、png格式图片',
        'avatar.fileSize'       => '请选择大小小于100kb图片',
    ];

    // 验证场景
    protected $scene = [
        'user_id'       => ['user_id'],
        'user_add'      => ['username', 'nickname', 'password', 'phone', 'email'],
        'user_edit'     => ['user_id', 'username', 'nickname', 'phone', 'email'],
        'user_dele'     => ['user_id'],
        'user_password' => ['user_id', 'password'],
        'user_pwdedit'  => ['user_id', 'password_old', 'password_new'],
        'user_disable'  => ['user_id'],
        'user_avatar'   => ['user_id', 'avatar'],
        'user_register' => ['username', 'nickname', 'password', 'phone', 'email'],
        'user_login'    => ['username', 'password'],
    ];

    // 验证场景定义：登录
    protected function sceneuser_login()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'alphaNum', 'checkUsername'])
            ->remove('password', ['length', 'alphaNum']);
    }

    // 自定义验证规则：用户是否存在
    protected function checkUser($value, $rule, $data = [])
    {
        $user_id = $value;

        $user = UserService::info($user_id);

        if ($user['is_delete'] == 1) {
            return '用户已被删除：' . $user_id;
        }

        return true;
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $user_id = isset($data['user_id']) ? $data['user_id'] : '';
        $username  = $data['username'];

        if ($user_id) {
            $where[] = ['user_id', '<>', $user_id];
        }
        $where[] = ['username', '=', $username];
        $where[] = ['is_delete', '=', 0];

        $user = Db::name('user')
            ->field('user_id')
            ->where($where)
            ->find();

        if ($user) {
            return '账号已存在：' . $username;
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $user_id  = isset($data['user_id']) ? $data['user_id'] : '';
        $nickname = $data['nickname'];

        if ($user_id) {
            $where[] = ['user_id', '<>', $user_id];
        }
        $where[] = ['nickname', '=', $nickname];
        $where[] = ['is_delete', '=', 0];

        $user = Db::name('user')
            ->field('user_id')
            ->where($where)
            ->find();

        if ($user) {
            return '昵称已存在：' . $nickname;
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $user_id = isset($data['user_id']) ? $data['user_id'] : '';
        $phone   = $data['phone'];

        if ($user_id) {
            $where[] = ['user_id', '<>', $user_id];
        }
        $where[] = ['phone', '=', $phone];
        $where[] = ['is_delete', '=', 0];

        $user = Db::name('user')
            ->field('user_id')
            ->where($where)
            ->find();

        if ($user) {
            return '手机已存在：' . $phone;
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $user_id = isset($data['user_id']) ? $data['user_id'] : '';
        $email     = $data['email'];

        if ($user_id) {
            $where[] = ['user_id', '<>', $user_id];
        }
        $where[] = ['email', '=', $email];
        $where[] = ['is_delete', '=', 0];

        $user = Db::name('user')
            ->field('user_id')
            ->where($where)
            ->find();

        if ($user) {
            return '邮箱已存在：' . $email;
        }

        return true;
    }

    // 自定义验证规则：旧密码是否正确
    protected function checkPwdOld($value, $rule, $data = [])
    {
        $user_id      = user_id();
        $user         = UserService::info($user_id);
        $password     = $user['password'];
        $password_old = md5($data['password_old']);

        if ($password != $password_old) {
            return '旧密码错误';
        }

        return true;
    }
}
