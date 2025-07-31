<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\member;

use think\Validate;
use app\common\service\member\SettingService as Service;
use app\common\model\member\SettingModel as Model;

/**
 * 会员设置管理验证器
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

    /**
     * 验证规则
     */
    protected $rule = [
        'token_key'     => ['require', 'alphaNum', 'length' => '6,32'],
        'token_exp'     => ['require', 'between' => '0.1,999999999'],
        'is_member_api' => ['require', 'in' => '0,1'],
        'log_switch'    => ['require', 'in' => '0,1'],
        'log_save_time' => ['require', 'between' => '0,999999999'],
        'api_rate_num'  => ['require', 'between' => '0,999'],
        'api_rate_time' => ['require', 'between' => '1,999'],
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'token_key.require'     => '请输入Token密钥',
        'token_key.alphaNum'    => 'Token密钥组成：字母和数字',
        'token_key.length'      => 'Token密钥长度：6-32',
        'token_exp.between'     => 'Token有效时间：0.1-999999999',
        'log_save_time.between' => '日志保留时间：0-999999999',
        'api_rate_num.require'  => '请输入速率次数',
        'api_rate_num.between'  => '速率次数：0-999',
        'api_rate_time.require' => '请输入速率时间',
        'api_rate_time.between' => '速率时间：1-999',
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'token_edit' => ['token_key', 'token_exp'],
        'log_edit'   => ['log_switch', 'log_save_time'],
        'api_edit'   => ['api_rate_num', 'api_rate_time'],
    ];
}
