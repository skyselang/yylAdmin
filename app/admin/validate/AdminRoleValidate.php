<?php
/*
 * @Description  : 角色管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-25
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\AdminRoleService;

class AdminRoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_role_id' => ['require', 'checkAdminRuleId'],
        'role_name'     => ['require', 'checkAdminRule'],
    ];

    // 错误信息
    protected $message = [
        'admin_role_id.require' => '缺少参数：角色id',
        'role_name.require'     => '请输入角色名称',
    ];

    // 验证场景
    protected $scene = [
        'role_id'   => ['admin_role_id'],
        'role_add'  => ['role_name'],
        'role_edit' => ['admin_role_id', 'role_name'],
        'role_dele' => ['admin_role_id'],
    ];

    // 验证场景定义：删除
    protected function scenerole_dele()
    {
        return $this->only(['admin_role_id'])
            ->append('admin_role_id', 'checkAdminRoleMenuAdmin');
    }

    // 自定义验证规则：角色是否存在
    protected function checkAdminRuleId($value, $rule, $data = [])
    {
        $admin_role_id = $value;

        $admin_role = AdminRoleService::info($admin_role_id);

        if ($admin_role['is_delete'] == 1) {
            return '角色已被删除：' . $admin_role_id;
        }

        return true;
    }

    // 自定义验证规则：角色是否已存在
    protected function checkAdminRule($value, $rule, $data = [])
    {
        $admin_role_id = isset($data['admin_role_id']) ? $data['admin_role_id'] : '';

        if ($admin_role_id) {
            $where[] = ['admin_role_id', '<>', $admin_role_id];
        }
        $where[] = ['role_name', '=', $data['role_name']];
        $where[] = ['is_delete', '=', 0];

        $admin_role = Db::name('admin_role')
            ->field('admin_role_id')
            ->where($where)
            ->find();

        if ($admin_role) {
            return '角色已存在：' . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色是否有菜单或管理员
    protected function checkAdminRoleMenuAdmin($value, $rule, $data = [])
    {
        $admin_role_id = $value;

        $admin_role = AdminRoleService::info($admin_role_id);
        
        if ($admin_role['admin_menu_ids']) {
            return '请在[修改]中取消所有菜单后再删除';
        }

        $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field('admin_admin_id')
            ->where($where)
            ->find();

        if ($admin_admin) {
            return '请在[管理员]中解除所有管理员后再删除';
        }

        return true;
    }
}
