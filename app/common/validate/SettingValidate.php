<?php
/*
 * @Description  : 基础设置验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-27
 */

namespace app\common\validate;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'token_name'       => ['require', 'alpha', 'length' => '5,30'],
        'token_key'        => ['require', 'alphaNum', 'length' => '5,30'],
        'token_exp'        => ['require', 'between' => '1,99999'],
        'captcha_register' => ['require', 'in' => '0,1'],
        'captcha_login'    => ['require', 'in' => '0,1'],
        'log_switch'       => ['require', 'in' => '0,1'],
        'api_rate_num'     => ['require', 'between' => '0,999'],
        'api_rate_time'    => ['require', 'between' => '1,999'],
    ];

    // 错误信息
    protected $message = [
        'token_name.require'       => '请输入Token名称',
        'token_name.alpha'         => 'Token名称组成：字母',
        'token_name.length'        => 'Token名称长度：5-30',
        'token_key.require'        => '请输入Token密钥',
        'token_key.alphaNum'       => 'Token密钥组成：字母和数字',
        'token_key.length'         => 'Token密钥长度：5-30',
        'token_exp.require'        => 'token_exp must',
        'token_exp.between'        => 'Token有效时间：1-99999',
        'captcha_register.require' => 'captcha_register must',
        'captcha_register.in'      => '注册验证码：1开启0关闭',
        'captcha_login.require'    => 'captcha_login must',
        'captcha_login.in'         => '登录验证码：1开启0关闭',
        'log_switch.require'       => 'log_switch must',
        'log_switch.in'            => '日志记录：1开启0关闭',
        'api_rate_num.require'     => '请输入速率次数',
        'api_rate_num.between'     => '速率次数：0-999',
        'api_rate_time.require'    => '请输入速率时间',
        'api_rate_time.between'    => '速率时间：1-999',
    ];

    // 验证场景
    protected $scene = [
        'token_edit'   => ['token_name', 'token_key', 'token_exp'],
        'captcha_edit' => ['captcha_register', 'captcha_login'],
        'log_edit'     => ['log_switch'],
        'api_edit'     => ['api_rate_num', 'api_rate_time'],
    ];
}
