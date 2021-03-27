<?php
/*
 * @Description  : 个人中心验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\AdminAdminService;

class AdminMyValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_admin_id' => ['require', 'checkAdminAdmin'],
        'username'       => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname'       => ['require', 'checkNickname', 'length' => '1,32'],
        'password_old'   => ['require'],
        'password_new'   => ['require', 'length' => '6,18'],
        'email'          => ['email', 'checkEmail'],
        'phone'          => ['mobile', 'checkPhone'],
        'avatar'         => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif', 'fileSize' => '51200'],
    ];

    // 错误信息
    protected $message = [
        'admin_admin_id.require' => '缺少参数：管理员id',
        'username.require'       => '请输入账号',
        'username.length'        => '账号长度为2至32个字符',
        'nickname.require'       => '请输入昵称',
        'nickname.length'        => '昵称长度为1至32个字符',
        'password_old.require'   => '请输入旧密码',
        'password_new.require'   => '请输入新密码',
        'password_new.length'    => '新密码长度为6至18个字符',
        'email.email'            => '请输入正确的邮箱地址',
        'phone.mobile'           => '请输入正确的手机号码',
        'avatar.require'         => '请选择图片',
        'avatar.file'            => '请选择图片文件',
        'avatar.image'           => '请选择图片格式文件',
        'avatar.fileExt'         => '请选择jpg、png、gif格式图片',
        'avatar.fileSize'        => '请选择大小小于50kb图片',
    ];

    // 验证场景
    protected $scene = [
        'admin_id'  => ['admin_admin_id'],
        'my_edit'   => ['admin_admin_id', 'username', 'nickname', 'email', 'phone'],
        'my_pwd'    => ['admin_admin_id', 'password_old', 'password_new', 'phone'],
        'my_avatar' => ['admin_admin_id', 'avatar'],

    ];

    // 自定义验证规则：管理员是否存在
    protected function checkAdminAdmin($value, $rule, $data = [])
    {
        $admin_admin_id = $value;

        $admin_admin = AdminAdminService::info($admin_admin_id);

        if ($admin_admin['is_delete'] == 1) {
            return '管理员已被删除：' . $admin_admin_id;
        }

        return true;
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $admin_admin_id = $data['admin_admin_id'];
        $username       = $data['username'];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where('admin_admin_id', '<>', $admin_admin_id)
            ->where('username', '=', $username)
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_admin) {
            return '账号已存在：' . $username;
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $admin_admin_id = $data['admin_admin_id'];
        $nickname       = $data['nickname'];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where('admin_admin_id', '<>', $admin_admin_id)
            ->where('nickname', '=', $nickname)
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_admin) {
            return '昵称已存在：' . $nickname;
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $admin_admin_id = $data['admin_admin_id'];
        $email          = $data['email'];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where('admin_admin_id', '<>', $admin_admin_id)
            ->where('email', '=', $email)
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_admin) {
            return '邮箱已存在：' . $email;
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $admin_admin_id = $data['admin_admin_id'];
        $phone          = $data['phone'];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where('admin_admin_id', '<>', $admin_admin_id)
            ->where('phone', '=', $phone)
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_admin) {
            return '手机已存在：' . $phone;
        }

        return true;
    }
}
