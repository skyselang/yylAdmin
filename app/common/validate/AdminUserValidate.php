<?php
/*
 * @Description  : 用户管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-10
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\AdminUserService;

class AdminUserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_id' => ['require'],
        'username'      => ['require', 'checkUsername', 'length' => '2,32'],
        'nickname'      => ['require', 'checkNickname', 'length' => '1,32'],
        'password'      => ['require', 'length' => '6,18'],
        'email'         => ['email', 'checkEmail'],
        'phone'         => ['mobile', 'checkPhone'],
        'avatar'        => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '102400'],
    ];

    // 错误信息
    protected $message = [
        'admin_user_id.require' => '缺少参数：用户ID',
        'username.require'      => '请输入账号/邮箱/手机',
        'username.length'       => '账号长度为2至32个字符',
        'nickname.require'      => '请输入昵称',
        'nickname.length'       => '昵称长度为1至32个字符',
        'password.require'      => '请输入密码',
        'password.length'       => '密码长度为6至18个字符',
        'email.email'           => '请输入正确的邮箱地址',
        'phone.mobile'          => '请输入正确的手机号码',
        'avatar.require'        => '请选择图片',
        'avatar.file'           => '请选择图片文件',
        'avatar.image'          => '请选择图片格式文件',
        'avatar.fileExt'        => '请选择jpg、png、gif格式图片',
        'avatar.fileSize'       => '请选择大小小于100kb图片',
    ];

    // 验证场景
    protected $scene = [
        'id'      => ['admin_user_id'],
        'info'    => ['admin_user_id'],
        'login'   => ['username', 'password'],
        'add'     => ['username', 'nickname', 'password', 'email', 'phone'],
        'edit'    => ['admin_user_id', 'username', 'nickname', 'email', 'phone'],
        'dele'    => ['admin_user_id'],
        'super'   => ['admin_user_id'],
        'disable' => ['admin_user_id'],
        'rule'    => ['admin_user_id'],
        'pwd'     => ['admin_user_id', 'password'],
        'avatar'  => ['admin_user_id', 'avatar'],

    ];

    // 验证场景定义：登录
    protected function scenelogin()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'checkUsername'])
            ->remove('password', ['length']);
    }

    // 验证场景定义：修改
    protected function sceneedit()
    {
        return $this->only(['admin_user_id', 'username', 'nickname', 'email', 'phone'])
            ->append('admin_user_id', ['checkAdminUserIsSuper']);
    }

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsDelete', 'checkAdminUserRoleMenu']);
    }

    // 验证场景定义：是否超管
    protected function scenesuper()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsSuper']);
    }

    // 验证场景定义：是否禁用
    protected function scenedisable()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsDisable']);
    }

    // 验证场景定义：分配权限
    protected function scenerule()
    {
        return $this->only(['admin_user_id'])
            ->append('admin_user_id', ['checkAdminUserIsSuper']);
    }

    // 验证场景定义：重置密码
    protected function scenepwd()
    {
        return $this->only(['admin_user_id', 'password'])
            ->append('admin_user_id', ['checkAdminUserIsSuper']);
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

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $admin_user_id = isset($data['admin_user_id']) ? $data['admin_user_id'] : '';
        $phone         = $data['phone'];

        if ($admin_user_id) {
            $where[] = ['admin_user_id', '<>', $admin_user_id];
        }
        $where[] = ['phone', '=', $phone];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        if ($admin_user) {
            return '手机已存在：' . $phone;
        }

        return true;
    }

    // 自定义验证规则：用户是否已分配角色或菜单
    protected function checkAdminUserRoleMenu($value, $rule, $data = [])
    {
        $admin_user_id = $value;

        $admin_user = AdminUserService::info($admin_user_id);

        if ($admin_user['admin_role_ids'] || $admin_user['admin_menu_ids']) {
            return '请在[权限]中取消所有角色和菜单后再删除';
        }

        return true;
    }

    // 自定义验证规则：用户是否超管
    protected function checkAdminUserIsSuper($value, $rule, $data = [])
    {
        $admin_is_super = admin_is_super(admin_user_id());
        $admin_user_id  = admin_is_super($value);

        if (!$admin_is_super && $admin_user_id) {
            return '无法对系统用户进行操作';
        }

        return true;
    }

    // 自定义验证规则：用户是否禁用
    protected function checkAdminUserIsDisable($value, $rule, $data = [])
    {
        $admin_is_super = admin_is_super($value);

        if ($admin_is_super) {
            return '无法对系统用户进行操作';
        }

        return true;
    }

    // 自定义验证规则：用户删除
    protected function checkAdminUserIsDelete($value, $rule, $data = [])
    {
        $admin_is_super = admin_is_super($value);

        if ($admin_is_super) {
            return '无法对系统用户进行操作';
        }

        return true;
    }
}
