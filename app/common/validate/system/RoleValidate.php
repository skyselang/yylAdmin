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
use app\common\service\system\RoleService as Service;
use app\common\model\system\RoleModel as Model;
use app\common\model\system\UserAttributesModel;
use app\common\model\system\RoleMenusModel;

/**
 * 角色管理验证器
 */
class RoleValidate extends Validate
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
        'ids'       => ['require', 'array'],
        'field'     => ['require', 'checkUpdateField'],
        'role_id'   => ['require'],
        'role_name' => ['require', 'checkExisted'],
        'menu_ids'  => ['array'],
        'user_ids'  => ['array'],
    ];

    // 错误信息
    protected $message = [
        'role_name.require' => '请输入名称',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['role_id'],
        'add'      => ['role_name'],
        'edit'     => ['role_id', 'role_name'],
        'dele'     => ['ids'],
        'disable'  => ['ids'],
        'update'   => ['ids', 'field'],
        'userList' => ['role_id'],
        'userLift' => ['role_id', 'user_ids'],
    ];

    // 验证场景定义：删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkMenuUser');
    }

    // 自定义验证规则：角色是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['role_unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['role_unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        $where = where_delete([[$pk, '<>', $id], ['role_name', '=', $data['role_name']]]);
        $info  = $model->field($pk)->where($where)->find();
        if ($info) {
            return lang('名称已存在：') . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    // 自定义验证规则：角色是否存在菜单或用户
    protected function checkMenuUser($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();

        $info = RoleMenusModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '角色存在菜单，请在[菜单]或[修改]中解除后再删除：' . $info[$pk];
        }

        $info = UserAttributesModel::where($pk, 'in', $data['ids'])->find();
        if ($info) {
            // return '角色存在用户，请在[用户]中解除后再删除：' . $info[$pk];
        }

        return true;
    }
}
