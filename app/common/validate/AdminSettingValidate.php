<?php
/*
 * @Description  : 设置管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-06
 */

namespace app\common\validate;

use think\Validate;

class AdminSettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'verify_switch' => ['require', 'in' => [0, 1]],
        'token_exp'     => ['require', 'between' => [1, 9999]],
    ];

    // 错误信息
    protected $message = [
        'verify_switch.require' => 'verify_switch must',
        'verify_switch.in'      => '验证码开关：1开0关',
        'token_exp.require'     => 'token_exp must',
        'token_exp.between'     => 'Token有效时间范围：1-9999',
    ];

    // 验证场景
    protected $scene = [
        'verify_edit' => ['verify_switch'],
        'token_edit'  => ['token_exp'],
    ];
}
