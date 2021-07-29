<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 菜单管理验证器
namespace app\common\validate\admin;

use think\Validate;
use app\common\service\admin\MenuService;
use app\common\service\admin\RoleService;
use app\common\service\admin\UserService;

class MenuValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_menu_id' => ['require'],
        'menu_name'     => ['require', 'checkAdminMenuName'],
    ];

    // 错误信息
    protected $message = [
        'menu_name.require' => '请输入菜单名称',
    ];

    // 验证场景
    protected $scene = [
        'id'         => ['admin_menu_id'],
        'info'       => ['admin_menu_id'],
        'add'        => ['menu_name'],
        'edit'       => ['admin_menu_id', 'menu_name'],
        'dele'       => ['admin_menu_id'],
        'disable'    => ['admin_menu_id', 'is_disable'],
        'unauth'     => ['admin_menu_id'],
        'unlogin'    => ['admin_menu_id'],
        'role'       => ['admin_menu_id'],
        'roleRemove' => ['admin_menu_id'],
        'user'       => ['admin_menu_id'],
        'userRemove' => ['admin_menu_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['admin_menu_id'])
            ->append('admin_menu_id', 'checkAdminMenuRole');
    }

    // 验证场景定义：角色解除
    protected function sceneroleRemove()
    {
        return $this->only(['admin_menu_id'])
            ->append('admin_menu_id', 'checkAdminMenuRoleRemove');
    }

    // 自定义验证规则：菜单名称是否已存在
    protected function checkAdminMenuName($value, $rule, $data = [])
    {
        $admin_menu_id = isset($data['admin_menu_id']) ? $data['admin_menu_id'] : '';

        if ($admin_menu_id) {
            if ($data['menu_pid'] == $data['admin_menu_id']) {
                return '菜单父级不能等于菜单本身';
            }
        }

        $menu = MenuService::list();
        $menu_name_msg = '菜单名称已存在：' . $data['menu_name'];
        $menu_url_msg  = '菜单链接已存在：' . $data['menu_url'];
        foreach ($menu as $k => $v) {
            if ($admin_menu_id) {
                if ($v['menu_pid'] == $data['menu_pid'] && $v['menu_name'] == $data['menu_name'] && $v['admin_menu_id'] != $admin_menu_id) {
                    return $menu_name_msg;
                }
                if ($v['menu_url'] == $data['menu_url'] && $v['menu_url'] != '' && $v['admin_menu_id'] != $admin_menu_id) {
                    return $menu_url_msg;
                }
            } else {
                if ($v['menu_pid'] == $data['menu_pid'] && $v['menu_name'] == $data['menu_name']) {
                    return $menu_name_msg;
                }
                if ($v['menu_url'] == $data['menu_url'] && $v['menu_url'] != '') {
                    return $menu_url_msg;
                }
            }
        }

        return true;
    }

    // 自定义验证规则：菜单是否有子菜单或分配有角色或分配有用户
    protected function checkAdminMenuRole($value, $rule, $data = [])
    {
        $admin_menu_id = $data['admin_menu_id'];

        $menu = MenuService::list();
        foreach ($menu as $k => $v) {
            if ($v['menu_pid'] == $admin_menu_id) {
                return '请删除所有子菜单后再删除';
            }
        }

        $where_role[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
        $admin_role = RoleService::list($where_role, 1, 1, [], 'admin_role_id');
        if ($admin_role['list']) {
            return '请在[角色]中解除所有角色后再删除';
        }

        $where_user[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
        $admin_user = UserService::list($where_user, 1, 1, [], 'admin_user_id');
        if ($admin_user['list']) {
            return '请在[用户]中解除所有用户后再删除';
        }

        return true;
    }
}
