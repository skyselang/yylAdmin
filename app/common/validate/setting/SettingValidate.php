<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\setting;

use think\Validate;

/**
 * 设置管理验证器
 */
class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'favicon_id'      => ['number'],
        'offi_id'         => ['number'],
        'email_host'      => ['require'],
        'email_secure'    => ['require'],
        'email_port'      => ['require'],
        'email_username'  => ['require'],
        'email_password'  => ['require'],
        'email_recipient' => ['require', 'email'],
        'email_theme'     => ['require'],
        'email_content'   => ['require'],
    ];

    // 错误信息
    protected $message = [
        'email_host.require'      => '请输入SMTP服务器',
        'email_secure.require'    => '请选择SMTP协议',
        'email_port.require'      => '请输入SMTP端口',
        'email_username.require'  => '请输入SMTP账号',
        'email_password.require'  => '请输入SMTP密码',
        'email_recipient.require' => '请输入收件人',
        'email_recipient.email'   => '请输入正确的邮箱地址',
        'email_theme.require'     => '请输入主题',
        'email_content.require'   => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'edit'       => ['favicon_id', 'offi_id'],
        'email_edit' => ['email_host', 'email_secure', 'email_port', 'email_username', 'email_password'],
        'email_test' => ['email_recipient', 'email_theme', 'email_content'],
    ];
}
