<?php
/*
 * @Description  : 管理员验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-25
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\AdminAdminService;

class AdminAdminValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_admin_id' => ['require', 'checkAdminAdmin'],
        'username'       => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname'       => ['require', 'checkNickname', 'length' => '1,32'],
        'password'       => ['require', 'length' => '6,18'],
        'email'          => ['email', 'checkEmail'],
        'phone'          => ['mobile', 'checkPhone'],
        'avatar'         => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif', 'fileSize' => '51200'],
    ];

    // 错误信息
    protected $message = [
        'admin_admin_id.require' => '缺少参数：管理员ID',
        'username.require'       => '请输入账号/邮箱/手机',
        'username.length'        => '账号长度为2至32个字符',
        'nickname.require'       => '请输入昵称',
        'nickname.length'        => '昵称长度为1至32个字符',
        'password.require'       => '请输入密码',
        'password.length'        => '密码长度为6至18个字符',
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
        'admin_id'      => ['admin_admin_id'],
        'admin_login'   => ['username', 'password'],
        'admin_add'     => ['username', 'nickname', 'password', 'email', 'phone'],
        'admin_edit'    => ['admin_admin_id', 'username', 'nickname', 'email', 'phone'],
        'admin_dele'    => ['admin_admin_id'],
        'admin_admin'   => ['admin_admin_id'],
        'admin_disable' => ['admin_admin_id'],
        'admin_rule'    => ['admin_admin_id'],
        'admin_pwd'     => ['admin_admin_id', 'password'],
        'admin_avatar'  => ['admin_admin_id', 'avatar'],

    ];

    // 验证场景定义：登录
    protected function sceneadmin_login()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'checkUsername'])
            ->remove('password', ['length']);
    }

    // 验证场景定义：修改
    protected function sceneadmin_edit()
    {
        return $this->only(['admin_admin_id', 'username', 'nickname', 'email', 'phone'])
            ->append('admin_admin_id', ['checkAdminAdminIsSysAdmin']);
    }

    // 验证场景定义：删除
    protected function sceneadmin_dele()
    {
        return $this->only(['admin_admin_id'])
            ->append('admin_admin_id', ['checkAdminAdminIsDelete', 'checkAdminAdminRoleMenu']);
    }

    // 验证场景定义：是否超管
    protected function sceneadmin_admin()
    {
        return $this->only(['admin_admin_id'])
            ->append('admin_admin_id', ['checkAdminAdminIsSysAdmin']);
    }

    // 验证场景定义：是否禁用
    protected function sceneadmin_disable()
    {
        return $this->only(['admin_admin_id'])
            ->append('admin_admin_id', ['checkAdminAdminIsDisable']);
    }

    // 验证场景定义：权限分配
    protected function sceneadmin_rule()
    {
        return $this->only(['admin_admin_id'])
            ->append('admin_admin_id', ['checkAdminAdminIsSysAdmin']);
    }

    // 验证场景定义：密码重置
    protected function sceneadmin_pwd()
    {
        return $this->only(['admin_admin_id', 'password'])
            ->append('admin_admin_id', ['checkAdminAdminIsSysAdmin']);
    }

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
        $admin_admin_id = isset($data['admin_admin_id']) ? $data['admin_admin_id'] : '';
        $username       = $data['username'];

        if ($admin_admin_id) {
            $where[] = ['admin_admin_id', '<>', $admin_admin_id];
        }
        $where[] = ['username', '=', $username];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where($where)
            ->find();

        if ($admin_admin) {
            return '账号已存在：' . $username;
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $admin_admin_id = isset($data['admin_admin_id']) ? $data['admin_admin_id'] : '';
        $nickname       = $data['nickname'];

        if ($admin_admin_id) {
            $where[] = ['admin_admin_id', '<>', $admin_admin_id];
        }
        $where[] = ['nickname', '=', $nickname];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where($where)
            ->find();

        if ($admin_admin) {
            return '昵称已存在：' . $nickname;
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $admin_admin_id = isset($data['admin_admin_id']) ? $data['admin_admin_id'] : '';
        $email          = $data['email'];

        if ($admin_admin_id) {
            $where[] = ['admin_admin_id', '<>', $admin_admin_id];
        }
        $where[] = ['email', '=', $email];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where($where)
            ->find();

        if ($admin_admin) {
            return '邮箱已存在：' . $email;
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $admin_admin_id = isset($data['admin_admin_id']) ? $data['admin_admin_id'] : '';
        $phone          = $data['phone'];

        if ($admin_admin_id) {
            $where[] = ['admin_admin_id', '<>', $admin_admin_id];
        }
        $where[] = ['phone', '=', $phone];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where($where)
            ->find();

        if ($admin_admin) {
            return '手机已存在：' . $phone;
        }

        return true;
    }

    // 自定义验证规则：管理员是否已分配角色或菜单
    protected function checkAdminAdminRoleMenu($value, $rule, $data = [])
    {
        $admin_admin_id = $value;

        $admin_admin = AdminAdminService::info($admin_admin_id);

        if ($admin_admin['admin_role_ids'] || $admin_admin['admin_menu_ids']) {
            return '请在[权限]中取消所有角色和菜单后再删除';
        }

        return true;
    }

    // 自定义验证规则：管理员是否系统管理员
    protected function checkAdminAdminIsSysAdmin($value, $rule, $data = [])
    {
        $admin_is_system = admin_is_system(admin_admin_id());
        $admin_admin_id  = admin_is_system($value);

        if (!$admin_is_system && $admin_admin_id) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }

    // 自定义验证规则：管理员是否禁用
    protected function checkAdminAdminIsDisable($value, $rule, $data = [])
    {
        $admin_is_system = admin_is_system($value);

        if ($admin_is_system) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }

    // 自定义验证规则：管理员删除
    protected function checkAdminAdminIsDelete($value, $rule, $data = [])
    {
        $admin_is_system = admin_is_system($value);

        if ($admin_is_system) {
            return '无法对系统管理员进行操作';
        }

        return true;
    }
}
