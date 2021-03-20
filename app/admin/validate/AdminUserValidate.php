<?php
/*
 * @Description  : 管理员验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\AdminUserService;

class AdminUserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_id' => ['require', 'checkAdminUser'],
        'username'      => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname'      => ['require', 'checkNickname', 'length' => '1,32'],
        'password'      => ['require', 'length' => '6,18'],
        'email'         => ['email', 'checkEmail'],
        'avatar'        => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif', 'fileSize' => '51200'],
    ];

    // 错误信息
    protected $message = [
        'admin_user_id.require' => '缺少参数：管理员ID',
        'username.require'      => '请输入账号',
        'username.length'       => '账号长度为2至32个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'email.email'           => '请输入正确的邮箱地址',
        'avatar.require'        => '请选择图片',
        'avatar.file'           => '请选择图片文件',
        'avatar.image'          => '请选择图片格式文件',
        'avatar.fileExt'        => '请选择jpg、png、gif格式图片',
        'avatar.fileSize'       => '请选择大小小于50kb图片',
    ];

    // 验证场景
    protected $scene = [
        'user_id'      => ['admin_user_id'],
        'user_login'   => ['username', 'password'],
        'user_add'     => ['username', 'nickname', 'password', 'email'],
        'user_edit'    => ['admin_user_id', 'username', 'nickname', 'email'],
        'user_dele'    => ['admin_user_id'],
        'user_admin'   => ['admin_user_id'],
        'user_disable' => ['admin_user_id'],
        'user_rule'    => ['admin_user_id'],
        'user_pwd'     => ['admin_user_id', 'password'],
        'user_avatar'  => ['admin_user_id', 'avatar'],

    ];

    // 验证场景定义：登录
    protected function sceneuser_login()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'checkUsername'])
            ->remove('password', ['length']);
    }

    // 验证场景定义：修改
    protected function sceneuser_edit()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsAdmin']);
    }

    // 验证场景定义：删除
    protected function sceneuser_dele()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsDelete', 'checkAdminUserRoleMenu']);
    }

    // 验证场景定义：是否超管
    protected function sceneuser_admin()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsAdmin']);
    }

    // 验证场景定义：是否禁用
    protected function sceneuser_disable()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsDisable']);
    }

    // 验证场景定义：权限分配
    protected function sceneuser_rule()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsAdmin']);
    }

    // 验证场景定义：密码重置
    protected function sceneuser_pwd()
    {
        return $this->only(['admin_user_id', 'password'])
            ->append('admin_user_id', ['checkAdminUserIsAdmin']);
    }

    // 自定义验证规则：管理员是否存在
    protected function checkAdminUser($value, $rule, $data = [])
    {
        $admin_user_id = $value;

        $admin_user = AdminUserService::info($admin_user_id);

        if ($admin_user['is_delete'] == 1) {
            return '管理员已被删除：' . $admin_user_id;
        }

        return true;
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $username      = $data['username'];

        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
        }
        $where[] = ['username', '=', $username];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        if ($admin_user) {
            return '账号已存在：' . $username;
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $nickname      = $data['nickname'];

        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
        }
        $where[] = ['nickname', '=', $nickname];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        if ($admin_user) {
            return '昵称已存在：' . $nickname;
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $email         = $data['email'];

        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
        }
        $where[] = ['email', '=', $email];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        if ($admin_user) {
            return '邮箱已存在：' . $email;
        }

        return true;
    }

    // 自定义验证规则：管理员是否已分配角色或菜单
    protected function checkAdminUserRoleMenu($value, $rule, $data = [])
    {
        $admin_user_id = $value;

        $admin_user = AdminUserService::info($admin_user_id);

        if ($admin_user['admin_role_ids'] || $admin_user['admin_menu_ids']) {
            return '请在[权限]中取消所有角色和菜单后再删除';
        }

        return true;
    }

    // 自定义验证规则：管理员是否系统管理员
    protected function checkAdminUserIsAdmin($value, $rule, $data = [])
    {
        $is_admin_user  = admin_is_system($value);
        $is_admin_admin = admin_is_system(admin_user_id());

        if ($is_admin_user && !$is_admin_admin) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }

    // 自定义验证规则：管理员是否禁用
    protected function checkAdminUserIsDisable($value, $rule, $data = [])
    {
        $is_admin_user = admin_is_system($value);

        if ($is_admin_user) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }

    // 自定义验证规则：管理员删除
    protected function checkAdminUserIsDelete($value, $rule, $data = [])
    {
        $is_admin_user = admin_is_system($value);

        if ($is_admin_user) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }
}
