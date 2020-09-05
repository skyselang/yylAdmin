<?php
/*
 * @Description  : 权限验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-05
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;

class AdminRuleValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_rule_id' => ['require'],
        'rule_name'     => ['require', 'checkRuleName'],
    ];

    // 错误信息
    protected $message  =   [
        'admin_rule_id.require' => 'admin_rule_id must',
        'rule_name.require'     => '请输入权限',
    ];

    // 验证场景
    protected $scene = [
        'admin_rule_id' => ['admin_rule_id'],
        'rule_name'     => ['rule_name'],
        'rule_add'      => ['rule_name'],
        'rule_edit'     => ['admin_rule_id', 'rule_name'],
    ];

    // 自定义验证规则：权限是否存在
    protected function checkRuleName($value, $rule, $data = [])
    {
        $admin_rule_id = isset($data['admin_rule_id']) ? $data['admin_rule_id'] : '';

        $admin_rule = Db::name('admin_rule')
            ->field('admin_rule_id')
            ->where('admin_rule_id', '<>', $admin_rule_id)
            ->where('rule_name', '=', $data['rule_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($admin_rule) {
            return '权限已存在：' . $data['rule_name'];
        }

        return true;
    }
}
