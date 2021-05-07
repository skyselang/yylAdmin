<?php
/*
 * @Description  : 基础设置验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-06
 */

namespace app\common\validate;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'verify_register' => ['require', 'in' => [0, 1]],
        'verify_login'    => ['require', 'in' => [0, 1]],
        'token_exp'       => ['require', 'between' => [1, 99999]],
    ];

    // 错误信息
    protected $message = [
        'verify_register.require' => 'verify_register must',
        'verify_register.in'      => '注册验证码开关：1开0关',
        'verify_login.require'    => 'verify_login must',
        'verify_login.in'         => '登录验证码开关：1开0关',
        'token_exp.require'       => 'token_exp must',
        'token_exp.between'       => 'Token有效时间范围：1-99999',
    ];

    // 验证场景
    protected $scene = [
        'verify_edit' => ['verify_register', 'verify_login'],
        'token_edit'  => ['token_exp'],
    ];
}
