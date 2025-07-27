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
use app\common\service\system\SettingService as Service;
use app\common\model\system\SettingModel as Model;

/**
 * 系统设置验证器
 */
class SettingValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [
        'system_name'    => ['length' => '0,256'],
        'token_key'      => ['require', 'alphaNum', 'length' => '6,32'],
        'token_exp'      => ['require', 'between' => '1,8760'],
        'captcha_switch' => ['require', 'in' => '0,1'],
        'email_host'     => ['require'],
        'email_secure'   => ['require'],
        'email_port'     => ['require'],
        'email_username' => ['require'],
        'email_password' => ['require'],
        'log_switch'     => ['require', 'in' => '0,1'],
        'log_save_time'  => ['require', 'between' => '0,99999'],
        'api_rate_num'   => ['require', 'between' => '0,999'],
        'api_rate_time'  => ['require', 'between' => '1,999'],
        'api_timeout'    => ['require', 'between' => '5,300'],
    ];

    // 错误信息
    protected $message = [
        'token_key.require'      => '请输入Token密钥',
        'token_key.alphaNum'     => 'Token密钥组成：字母和数字',
        'token_key.length'       => 'Token密钥长度：6-32',
        'token_exp.between'      => 'Token有效时间：1-8760',
        'email_host.require'     => '请输入SMTP服务器',
        'email_secure.require'   => '请选择SMTP协议',
        'email_port.require'     => '请输入SMTP端口',
        'email_username.require' => '请输入SMTP账号',
        'email_password.require' => '请输入SMTP密码',
        'log_save_time.between'  => '日志保留时间：0-99999',
        'api_rate_num.require'   => '请输入速率次数',
        'api_rate_num.between'   => '速率次数：0-999',
        'api_rate_time.require'  => '请输入速率时间',
        'api_rate_time.between'  => '速率时间：1-999',
        'api_timeout.require'    => '请输入请求超时',
        'api_timeout.between'    => '请求超时范围：5-300',
    ];

    // 验证场景
    protected $scene = [
        'system_edit'  => ['system_name'],
        'token_edit'   => ['token_key', 'token_exp'],
        'captcha_edit' => ['captcha_switch'],
        'email_edit'   => ['email_host', 'email_secure', 'email_port', 'email_username', 'email_password'],
        'log_edit'     => ['log_switch', 'log_save_time'],
        'api_edit'     => ['api_rate_num', 'api_rate_time', 'api_timeout'],
    ];
}
