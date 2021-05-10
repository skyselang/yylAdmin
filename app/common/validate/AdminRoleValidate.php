<?php
/*
 * @Description  : 角色管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-10
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\AdminRoleService;

class AdminRoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_role_id' => ['require'],
        'role_name'     => ['require', 'checkAdminRuleName'],
    ];

    // 错误信息
    protected $message = [
        'admin_role_id.require' => '缺少参数：角色id',
        'role_name.require'     => '请输入角色名称',
    ];

    // 验证场景
    protected $scene = [
        'id'      => ['admin_role_id'],
        'info'    => ['admin_role_id'],
        'add'     => ['role_name'],
        'edit'    => ['admin_role_id', 'role_name'],
        'dele'    => ['admin_role_id'],
        'disable' => ['admin_role_id'],
        'user'    => ['admin_role_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['admin_role_id'])
            ->append('admin_role_id', 'checkAdminRoleMenuUser');
    }

    // 自定义验证规则：角色名称是否已存在
    protected function checkAdminRuleName($value, $rule, $data = [])
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
            return '角色名称已存在：' . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色是否有菜单或用户
    protected function checkAdminRoleMenuUser($value, $rule, $data = [])
    {
        $admin_role_id = $value;

        $admin_role = AdminRoleService::info($admin_role_id);
        
        if ($admin_role['admin_menu_ids']) {
            return '请在[修改]中取消所有菜单后再删除';
        }

        $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where)
            ->find();

        if ($admin_user) {
            return '请在[用户]中解除所有用户后再删除';
        }

        return true;
    }
}
