<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\system;

use think\Validate;
use app\common\service\system\UserService;
use app\common\model\system\UserModel;

/**
 * 个人中心验证器
 */
class UserCenterValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'user_id'      => ['require'],
        'nickname'     => ['require', 'checkNickname', 'length' => '1,32'],
        'username'     => ['require', 'checkUsername', 'length' => '2,32'],
        'password_old' => ['require', 'checkOldPwd'],
        'password_new' => ['require', 'length' => '6,18'],
        'phone'        => ['mobile', 'checkPhone'],
        'email'        => ['email', 'checkEmail'],
    ];

    // 错误信息
    protected $message = [
        'nickname.require'     => '请输入昵称',
        'nickname.length'      => '昵称长度为1至32个字符',
        'username.require'     => '请输入账号',
        'username.length'      => '账号长度为2至32个字符',
        'password_old.require' => '请输入旧密码',
        'password_new.require' => '请输入新密码',
        'password_new.length'  => '新密码长度为6至18个字符',
        'phone.mobile'         => '请输入正确的手机号码',
        'email.email'          => '请输入正确的邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'info' => ['user_id'],
        'edit' => ['user_id', 'username', 'nickname', 'phone', 'email'],
        'pwd'  => ['user_id', 'password_old', 'password_new', 'phone'],
        'log'  => ['user_id'],
    ];

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['nickname', '=', $data['nickname']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '昵称已存在：' . $data['nickname'];
        }

        return true;
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['username', '=', $data['username']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '账号已存在：' . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['phone', '=', $data['phone']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '手机已存在：' . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['email', '=', $data['email']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '邮箱已存在：' . $data['email'];
        }

        return true;
    }

    // 自定义验证规则：旧密码是否正确
    protected function checkOldPwd($value, $rule, $data = [])
    {
        $info = UserService::info($data['user_id'] ?? 0);
        if (!password_verify($data['password_old'], $info['password'])) {
            return '旧密码错误';
        }

        return true;
    }
}
