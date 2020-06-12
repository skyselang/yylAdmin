<?php
/*
 * @Description  : 权限验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-25
 */

namespace app\admin\validate;

use think\Validate;

class AdminRuleValidate extends Validate
{
    protected $rule = [
        'admin_rule_id' => ['require'],
        'rule_name'     => ['require'],
    ];

    protected $message  =   [
        'admin_rule_id.require' => '缺少参数admin_rule_id',
        'rule_name.require'     => '请输入权限',
    ];

    protected $scene = [
        'admin_rule_id' => ['admin_rule_id'],
        'rule_name'     => ['rule_name'],
        'rule_add'      => ['rule_name'],
        'rule_edit'     => ['admin_rule_id', 'rule_name'],
    ];
}
