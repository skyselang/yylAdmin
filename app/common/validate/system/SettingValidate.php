<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\system;

use think\Validate;

/**
 * 系统设置验证器
 */
class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'page_limit'     => ['require', 'between' => '1,1000'],
        'token_key'      => ['require', 'alphaNum', 'length' => '6,32'],
        'token_exp'      => ['require', 'between' => '1,8760'],
        'captcha_switch' => ['require', 'in' => '0,1'],
        'log_switch'     => ['require', 'in' => '0,1'],
        'log_save_time'  => ['require', 'between' => '0,99999'],
        'api_rate_num'   => ['require', 'between' => '0,999'],
        'api_rate_time'  => ['require', 'between' => '1,999'],
        'email_secure'   => ['require'],
        'email_test'     => ['require'],
    ];

    // 错误信息
    protected $message = [
        'page_limit.between'     => '分页每页数量范围：1-1000',
        'token_key.require'      => '请输入Token密钥',
        'token_key.alphaNum'     => 'Token密钥组成：字母和数字',
        'token_key.length'       => 'Token密钥长度：6-32',
        'token_exp.require'      => 'token_exp must',
        'token_exp.between'      => 'Token有效时间范围：1-8760',
        'captcha_switch.require' => 'captcha_switch must',
        'captcha_switch.in'      => '验证码：1开启0关闭',
        'log_switch.require'     => 'log_switch must',
        'log_switch.in'          => '日志记录：1开启0关闭',
        'log_save_time.between'  => '日志保留时间范围：0-99999',
        'api_rate_num.require'   => '请输入速率次数',
        'api_rate_num.between'   => '速率次数范围：0-999',
        'api_rate_time.require'  => '请输入速率时间',
        'api_rate_time.between'  => '速率时间范围：1-999',
        'email_secure.require'   => '请选择SMTP协议',
        'email_test.require'     => '请输入测试邮箱',
    ];

    // 验证场景
    protected $scene = [
        'system_edit'  => ['page_limit'],
        'token_edit'   => ['token_key', 'token_exp'],
        'captcha_edit' => ['captcha_switch'],
        'log_edit'     => ['log_switch', 'log_save_time'],
        'api_edit'     => ['api_rate_num', 'api_rate_time'],
        'email_edit'   => ['email_secure'],
        'email_test'   => ['email_test'],
    ];
}
