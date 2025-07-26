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
use app\common\service\system\UserService as Service;
use app\common\model\system\UserModel as Model;
use app\common\model\system\UserAttributesModel;

/**
 * 用户管理验证器
 */
class UserValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'ids'      => ['require', 'array'],
        'field'    => ['require', 'checkUpdateField'],
        'user_id'  => ['require'],
        'unique'   => ['checkUnique'],
        'nickname' => ['require', 'checkNickname', 'length' => '1,64'],
        'username' => ['require', 'checkUsername', 'length' => '2,64'],
        'password' => ['require', 'length' => '6,18'],
        'phone'    => ['mobile', 'checkPhone'],
        'email'    => ['email', 'checkEmail'],
        'role_ids' => ['array'],
    ];

    // 错误信息
    protected $message = [
        'nickname.require' => '请输入昵称',
        'nickname.length'  => '昵称长度为1至64个字符',
        'username.require' => '请输入账号',
        'username.length'  => '账号长度为2至64个字符',
        'password.require' => '请输入密码',
        'password.length'  => '密码长度为6至18个字符',
        'phone.mobile'     => '请输入正确手机号码',
        'email.email'      => '请输入正确邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'info'      => ['user_id'],
        'add'       => ['unique', 'nickname', 'username', 'password', 'phone', 'email'],
        'edit'      => ['user_id', 'unique', 'nickname', 'username', 'phone', 'email'],
        'dele'      => ['ids'],
        'disable'   => ['ids'],
        'update'    => ['ids', 'field'],
        'editrole'  => ['ids', 'role_ids'],
        'editpwd'   => ['ids', 'password'],
        'editsuper' => ['ids'],
        'login'     => ['username', 'password'],
    ];

    // 验证场景定义：添加
    protected function sceneAdd()
    {
        return $this->only(['unique', 'nickname', 'username', 'phone', 'email']);
    }

    // 验证场景定义：修改
    protected function sceneEdit()
    {
        return $this->only(['user_id', 'unique', 'nickname', 'username', 'phone', 'email'])
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

    // 验证场景定义：修改密码
    protected function sceneEditpwd()
    {
        return $this->only(['ids', 'password'])
            ->append('ids', ['checkIsSuper']);
    }

    // 验证场景定义：是否超管
    protected function sceneEditsuper()
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

    // 自定义验证规则：用户批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    // 自定义验证规则：编号是否已存在
    protected function checkUnique($value, $rule, $data = [])
    {
        $unique = $data['unique'] ?? '';
        if ($unique) {
            $model = $this->model();
            $pk    = $model->getPk();
            $id    = $data[$pk] ?? 0;

            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        return true;
    }

    // 自定义验证规则：昵称是否已存在
    protected function checkNickname($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], ['nickname', '=', $data['nickname']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('昵称已存在：') . $data['nickname'];
        }

        return true;
    }

    // 自定义验证规则：账号是否已存在
    protected function checkUsername($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], ['username', '=', $data['username']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('账号已存在：') . $data['username'];
        }

        return true;
    }

    // 自定义验证规则：手机是否已存在
    protected function checkPhone($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], ['phone', '=', $data['phone']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('手机已存在：') . $data['phone'];
        }

        return true;
    }

    // 自定义验证规则：邮箱是否已存在
    protected function checkEmail($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $where = where_delete([[$pk, '<>', $id], ['email', '=', $data['email']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('邮箱已存在：') . $data['email'];
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
                return lang('无法对系统超管用户进行操作：') . $id;
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
                return lang('无法对系统超管用户进行操作：') . $id;
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
                return lang('无法对系统超管用户进行操作：') . $id;
            }
        }

        return true;
    }

    // 自定义验证规则：用户是否已存在部门或职位或角色
    protected function checkDeptPostRole($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();

        $info = UserAttributesModel::where($pk, 'in', $data['ids'])->where('role_id', '>', 0)->find();
        if ($info) {
            // return '用户存在角色，请在[角色]中解除后再删除：' . $info[$pk];
        }

        $info = UserAttributesModel::where($pk, 'in', $data['ids'])->where('dept_id', '>', 0)->find();
        if ($info) {
            // return '用户存在部门，请在[修改]中解除后再删除：' . $info[$pk];
        }

        $info = UserAttributesModel::where($pk, 'in', $data['ids'])->where('post_id', '>', 0)->find();
        if ($info) {
            // return '用户存在职位，请在[修改]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
