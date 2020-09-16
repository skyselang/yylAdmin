<?php
/*
 * @Description  : 角色验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-15
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminRoleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_role_id' => ['require'],
        'role_name'     => ['require', 'checkRuleName'],
    ];

    // 错误信息
    protected $message  =   [
        'admin_role_id.require' => 'admin_role_id must',
        'role_name.require'     => '请输入角色名称',
    ];

    // 验证场景
    protected $scene = [
        'admin_role_id' => ['admin_role_id'],
        'role_name'     => ['role_name'],
        'role_add'      => ['role_name'],
        'role_edit'     => ['admin_role_id', 'role_name'],
    ];

    // 自定义验证规则：角色是否存在
    protected function checkRuleName($value, $rule, $data = [])
    {
        $admin_role_id = isset($data['admin_role_id']) ? $data['admin_role_id'] : '';

        $admin_role = Db::name('admin_role')
            ->field('admin_role_id')
            ->where('admin_role_id', '<>', $admin_role_id)
            ->where('role_name', '=', $data['role_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_role) {
            return '角色已存在：' . $data['role_name'];
        }

        return true;
    }
}
