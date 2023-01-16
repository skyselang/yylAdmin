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
use app\common\model\system\RoleModel;
use app\common\model\system\RoleMenusModel;
use app\common\model\system\UserAttributesModel;

/**
 * 角色管理验证器
 */
class RoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
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
        'info'       => ['role_id'],
        'add'        => ['role_name'],
        'edit'       => ['role_id', 'role_name'],
        'editmenu'   => ['ids', 'menu_ids'],
        'dele'       => ['ids'],
        'disable'    => ['ids'],
        'user'       => ['role_id'],
        'userRemove' => ['role_id', 'user_ids'],
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
        $model = new RoleModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['role_name', '=', $data['role_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '名称已存在：' . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色是否存在菜单或用户
    protected function checkMenuUser($value, $rule, $data = [])
    {
        $menu = RoleMenusModel::where('role_id', 'in', $data['ids'])->find();
        if ($menu) {
            return '角色下存在菜单，请在[菜单]或[修改]中解除后再删除：' . $menu['role_id'];
        }

        $user = UserAttributesModel::where('role_id', 'in', $data['ids'])->find();
        if ($user) {
            return '角色下存在用户，请在[用户]中解除后再删除：' . $user['role_id'];
        }

        return true;
    }
}
