<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 系统设置验证器
namespace app\common\validate\admin;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'token_key'      => ['require', 'alphaNum', 'length' => '6,32'],
        'token_exp'      => ['require', 'between' => '1,9999'],
        'captcha_switch' => ['require', 'in' => '0,1'],
        'log_switch'     => ['require', 'in' => '0,1'],
        'log_save_time'  => ['require', 'between' => '0,99999'],
        'api_rate_num'   => ['require', 'between' => '0,999'],
        'api_rate_time'  => ['require', 'between' => '1,999'],
        'email_host'     => ['require'],
        'email_port'     => ['require'],
        'email_secure'   => ['require'],
        'email_username' => ['require'],
        'email_password' => ['require'],
        'email_setfrom'  => ['require'],
        'email_test'     => ['require'],
    ];

    // 错误信息
    protected $message = [
        'token_key.require'      => '请输入Token密钥',
        'token_key.alphaNum'     => 'Token密钥组成：字母和数字',
        'token_key.length'       => 'Token密钥长度：6-32',
        'token_exp.require'      => 'token_exp must',
        'token_exp.between'      => 'Token有效时间：1-9999',
        'captcha_switch.require' => 'captcha_switch must',
        'captcha_switch.in'      => '验证码：1开启0关闭',
        'log_switch.require'     => 'log_switch must',
        'log_switch.in'          => '日志记录：1开启0关闭',
        'log_save_time.between'  => '保留时间：0-99999',
        'api_rate_num.require'   => '请输入速率次数',
        'api_rate_num.between'   => '速率次数：0-999',
        'api_rate_time.require'  => '请输入速率时间',
        'api_rate_time.between'  => '速率时间：1-999',
        'email_host.require'     => '请输入邮箱服务器',
        'email_port.require'     => '请输入邮箱端口',
        'email_secure.require'   => '请选择邮箱协议',
        'email_username.require' => '请输入邮箱账号',
        'email_password.require' => '请输入邮箱密码',
        'email_setfrom.require'  => '请输入发件人邮箱',
        'email_test.require'     => '请输入测试邮箱',
    ];

    // 验证场景
    protected $scene = [
        'token_edit'   => ['token_key', 'token_exp'],
        'captcha_edit' => ['captcha_switch'],
        'log_edit'     => ['log_switch', 'log_save_time'],
        'api_edit'     => ['api_rate_num', 'api_rate_time'],
        'email_edit'   => ['email_host', 'email_port', 'email_secure', 'email_username', 'email_password', 'email_setfrom'],
        'email_test'   => ['email_test'],
    ];
}
