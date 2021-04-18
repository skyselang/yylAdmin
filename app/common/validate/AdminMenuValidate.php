<?php
/*
 * @Description  : 菜单管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-04-14
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;
use app\common\service\AdminMenuService;

class AdminMenuValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_menu_id' => ['require', 'checkAdminMenuId'],
        'menu_name'     => ['require', 'checkAdminMenuName'],
    ];

    // 错误信息
    protected $message = [
        'admin_menu_id.require' => '缺少参数：菜单id',
        'menu_name.require'     => '请输入菜单名称',
    ];

    // 验证场景
    protected $scene = [
        'id'         => ['admin_menu_id'],
        'info'       => ['admin_menu_id'],
        'add'        => ['menu_name'],
        'edit'       => ['admin_menu_id', 'menu_name'],
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

    // 自定义验证规则：菜单是否存在
    protected function checkAdminMenuId($value, $rule, $data = [])
    {
        $admin_menu_id = $value;

        $admin_menu = AdminMenuService::info($admin_menu_id);

        if ($admin_menu['is_delete'] == 1) {
            return '菜单已被删除：' . $admin_menu_id;
        }

        return true;
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

        $menu_name = Db::name('admin_menu')
            ->field('admin_menu_id')
            ->where('admin_menu_id', '<>', $admin_menu_id)
            ->where('menu_pid', '=', $data['menu_pid'])
            ->where('menu_name', '=', $data['menu_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($menu_name) {
            return '菜单名称已存在：' . $data['menu_name'];
        }

        $menu_url = Db::name('admin_menu')
            ->field('admin_menu_id')
            ->where('admin_menu_id', '<>', $admin_menu_id)
            ->where('menu_url', '=', $data['menu_url'])
            ->where('menu_url', '<>', '')
            ->where('is_delete', '=', 0)
            ->find();

        if ($menu_url) {
            return '菜单链接已存在：' . $data['menu_url'];
        }

        return true;
    }

    // 自定义验证规则：菜单是否有子菜单或分配有角色或分配有管理员
    protected function checkAdminMenuRole($value, $rule, $data = [])
    {
        $admin_menu_id = $value;

        $admin_menu = Db::name('admin_menu')
            ->field('admin_menu_id')
            ->where('menu_pid', '=', $admin_menu_id)
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_menu) {
            return '请删除所有子菜单后再删除';
        }

        $where_role[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
        $where_role[] = ['is_delete', '=', 0];

        $admin_role = Db::name('admin_role')
            ->field('admin_role_id')
            ->where($where_role)
            ->find();

        if ($admin_role) {
            return '请在[角色]中解除所有角色后再删除';
        }

        $where_admin[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
        $where_admin[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where($where_admin)
            ->find();

        if ($admin_user) {
            return '请在[管理员]中解除所有管理员后再删除';
        }

        return true;
    }
}
