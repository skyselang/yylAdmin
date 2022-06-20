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
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\model\admin\UserModel;

class MenuValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'           => ['require', 'array'],
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
        'dele'       => ['ids'],
        'pid'        => ['ids'],
        'unauth'     => ['ids'],
        'unlogin'    => ['ids'],
        'hidden'     => ['ids'],
        'disable'    => ['ids'],
        'role'       => ['admin_menu_id'],
        'roleRemove' => ['admin_menu_id'],
        'user'       => ['admin_menu_id'],
        'userRemove' => ['admin_menu_id'],
    ];

    // 验证场景定义：删除
    protected function sceneDele()
    {
        return $this->only(['ids'])
            ->append('ids', 'checkAdminMenuRole');
    }

    // 验证场景定义：角色解除
    protected function sceneRoleRemove()
    {
        return $this->only(['admin_menu_id'])
            ->append('admin_menu_id', 'checkAdminMenuRoleRemove');
    }

    // 自定义验证规则：菜单名称是否已存在
    protected function checkAdminMenuName($value, $rule, $data = [])
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();

        $admin_menu_id = isset($data[$MenuPk]) ? $data[$MenuPk] : '';
        if ($admin_menu_id) {
            if ($data['menu_pid'] == $data[$MenuPk]) {
                return '菜单上级不能等于菜单本身';
            }
        }

        if ($admin_menu_id) {
            $name_where[] = [$MenuPk, '<>', $admin_menu_id];
        }
        $name_where[] = ['menu_pid', '=', $data['menu_pid']];
        $name_where[] = ['menu_name', '=', $data['menu_name']];
        $name_where[] = ['is_delete', '=', 0];
        $menu_name = $MenuModel->field($MenuPk)->where($name_where)->find();
        if ($menu_name) {
            return '菜单名称已存在：' . $data['menu_name'];
        }

        if ($data['menu_url']) {
            if ($admin_menu_id) {
                $url_where[] = [$MenuPk, '<>', $admin_menu_id];
            }
            $url_where[] = ['menu_url', '=', $data['menu_url']];
            $url_where[] = ['is_delete', '=', 0];
            $menu_url = $MenuModel->field($MenuPk)->where($url_where)->find();
            if ($menu_url) {
                return '菜单链接已存在：' . $data['menu_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：菜单是否有下级菜单或分配有角色或分配有用户
    protected function checkAdminMenuRole($value, $rule, $data = [])
    {
        $MenuModel = new MenuModel();
        $MenuPk = $MenuModel->getPk();

        $RoleModel = new RoleModel();
        $RolePk = $RoleModel->getPk();

        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        foreach ($data['ids'] as $v) {
            $menu_where[] = ['menu_pid', '=', $v];
            $menu_where[] = ['is_delete', '=', 0];
            $menu = $MenuModel->field($MenuPk)->where($menu_where)->find();
            if ($menu) {
                return '请删除所有下级菜单后再删除';
            }

            $role_where[] = ['admin_menu_ids', 'like', '%' . str_join($v) . '%'];
            $role_where[] = ['is_delete', '=', 0];
            $role = $RoleModel->field($RolePk)->where($role_where)->find();
            if ($role) {
                return '请在[角色]中解除所有角色后再删除';
            }

            $user_where[] = ['admin_menu_ids', 'like', '%' . str_join($v) . '%'];
            $user_where[] = ['is_delete', '=', 0];
            $user = $UserModel->field($UserPk)->where($user_where)->find();
            if ($user) {
                return '请在[用户]中解除所有用户后再删除';
            }
        }

        return true;
    }
}
