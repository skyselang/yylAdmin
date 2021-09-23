<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 角色管理验证器
namespace app\common\validate\admin;

use think\Validate;
use app\common\service\admin\RoleService;
use app\common\service\admin\UserService;

class RoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_role_id' => ['require'],
        'role_name'     => ['require', 'checkAdminRuleName'],
    ];

    // 错误信息
    protected $message = [
        'role_name.require' => '请输入角色名称',
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
            $where_role[] = ['admin_role_id', '<>', $admin_role_id];
        }
        $where_role[] = ['role_name', '=', $data['role_name']];
        $admin_role = RoleService::list($where_role, 1, 1, [], 'admin_role_id');
        if ($admin_role['list']) {
            return '角色名称已存在：' . $data['role_name'];
        }

        return true;
    }

    // 自定义验证规则：角色是否有菜单或用户
    protected function checkAdminRoleMenuUser($value, $rule, $data = [])
    {
        $admin_role_id = $value;

        $admin_role = RoleService::info($admin_role_id);
        if ($admin_role['admin_menu_ids']) {
            return '请在[修改]中取消所有菜单后再删除';
        }

        $where_user[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];
        $admin_user = UserService::list($where_user, 1, 1, [], 'admin_user_id');
        if ($admin_user['list']) {
            return '请在[用户]中解除所有用户后再删除';
        }

        return true;
    }
}
