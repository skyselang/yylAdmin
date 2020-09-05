<?php
/*
 * @Description  : 菜单验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-05
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminMenuValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_menu_id' => ['require'],
        'menu_name'     => ['require', 'checkMenu'],
    ];

    // 错误信息
    protected $message  =   [
        'admin_menu_id.require' => 'admin_menu_id must',
        'menu_name.require'     => '请输入菜单名称',
    ];

    // 验证场景
    protected $scene = [
        'admin_menu_id' => ['admin_menu_id'],
        'menu_name'     => ['menu_name'],
        'menu_add'      => ['menu_name'],
        'menu_edit'     => ['admin_menu_id', 'menu_name'],
    ];

    // 自定义验证规则：菜单是否存在
    protected function checkMenu($value, $rule, $data = [])
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
}
