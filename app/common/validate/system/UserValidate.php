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
use app\common\model\system\UserModel;
use app\common\model\system\UserAttributesModel;

/**
 * 用户管理验证器
 */
class UserValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'      => ['require', 'array'],
        'user_id'  => ['require'],
        'nickname' => ['require', 'checkNickname', 'length' => '1,32'],
        'username' => ['require', 'checkUsername', 'length' => '2,32'],
        'password' => ['require', 'length' => '6,18'],
        'phone'    => ['mobile', 'checkPhone'],
        'email'    => ['email', 'checkEmail'],
        'dept_ids' => ['array'],
        'post_ids' => ['array'],
        'role_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'nickname.require' => '请输入昵称',
        'nickname.length'  => '昵称长度为1至32个字符',
        'username.require' => '请输入账号/手机/邮箱',
        'username.length'  => '账号长度为2至32个字符',
        'password.require' => '请输入密码',
        'password.length'  => '密码长度为6至18个字符',
        'phone.mobile'     => '请输入正确的手机号码',
        'email.email'      => '请输入正确的邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['user_id'],
        'add'      => ['nickname', 'username', 'password', 'phone', 'email'],
        'edit'     => ['user_id', 'nickname', 'username', 'phone', 'email'],
        'dele'     => ['ids'],
        'editdept' => ['ids', 'dept_ids'],
        'editpost' => ['ids', 'post_ids'],
        'editrole' => ['ids', 'role_ids'],
        'repwd'    => ['ids', 'password'],
        'super'    => ['ids'],
        'disable'  => ['ids'],
        'login'    => ['username', 'password'],
    ];

    // 验证场景定义：修改
    protected function sceneEdit()
    {
        return $this->only(['user_id', 'nickname', 'username', 'email', 'phone'])
            ->append('user_id', ['checkIsSuper']);
    }

    // 验证场景定义：删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkIsDelete', 'checkDeptPostRole']);
    }

    // 验证场景定义：修改角色
    protected function sceneEditrole()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkIsSuper']);
    }

    // 验证场景定义：重置密码
    protected function sceneRepwd()
    {
        return $this->only(['ids', 'password'])
            ->append('ids', ['checkIsSuper']);
    }

    // 验证场景定义：是否超管
    protected function sceneSuper()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkIsSuper']);
    }

    // 验证场景定义：是否禁用
    protected function sceneDisable()
    {
        return $this->only(['ids'])
            ->append('ids', ['checkIsDisable']);
    }

    // 验证场景定义：登录
    protected function sceneLogin()
    {
        return $this->only(['username', 'password'])
            ->remove('username', ['length', 'checkUsername'])
            ->remove('password', ['length']);
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $model = new UserModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['nickname', '=', $data['nickname']];
        $where[] = where_delete();
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
        $where[] = where_delete();
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
        $where[] = where_delete();
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
        $where[] = where_delete();
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '邮箱已存在：' . $data['email'];
        }

        return true;
    }

    // 自定义验证规则：用户是否超管
    protected function checkIsSuper($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        if ($data['user_id'] ?? 0) {
            $ids[] = $data['user_id'];
        }

        foreach ($ids as $id) {
            $is_super = user_is_super(user_id());
            $user_id  = user_is_super($id);
            if (!$is_super && $user_id) {
                return '无法对系统超管用户进行操作:' . $id;
            }
        }

        return true;
    }

    // 自定义验证规则：用户是否禁用
    protected function checkIsDisable($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        foreach ($ids as $id) {
            if (user_is_super($id)) {
                return '无法对系统超管用户进行操作:' . $id;
            }
        }

        return true;
    }

    // 自定义验证规则：用户删除
    protected function checkIsDelete($value, $rule, $data = [])
    {
        $ids = $data['ids'] ?? [];
        foreach ($ids as $id) {
            if (user_is_super($id)) {
                return '无法对系统超管用户进行操作:' . $id;
            }
        }

        return true;
    }

    // 自定义验证规则：用户是否已存在部门或职位或角色
    protected function checkDeptPostRole($value, $rule, $data = [])
    {
        $info = UserAttributesModel::where('user_id', 'in', $data['ids'])->where('role_id', '>', 0)->find();
        if ($info) {
            return '用户存在角色，请在[角色]中解除后再删除：' . $info['user_id'];
        }

        $info = UserAttributesModel::where('user_id', 'in', $data['ids'])->where('dept_id', '>', 0)->find();
        if ($info) {
            return '用户存在部门，请在[修改]中解除后再删除：' . $info['user_id'];
        }

        $info = UserAttributesModel::where('user_id', 'in', $data['ids'])->where('post_id', '>', 0)->find();
        if ($info) {
            return '用户存在职位，请在[修改]中解除后再删除：' . $info['user_id'];
        }

        return true;
    }
}
