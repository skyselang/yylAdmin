<?php
/*
 * @Description  : 实用工具验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-15
 * @LastEditTime : 2020-09-11
 */

namespace app\admin\validate;

use think\Validate;

class AdminToolValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'strtran_str' => ['require'],
        'strrand_ids' => ['require'],
        'strrand_len' => ['require', 'egt:1'],
        'qrcode_str'  => ['require'],
    ];

    // 错误信息
    protected $message  =   [
        'strtran_str.require' => '请输入字符串',
        'strrand_ids.require' => '请选择所用字符',
        'strrand_len.require' => '请选择字符长度',
        'strrand_len.egt'     => '字符长度必须大于0',
        'qrcode_str.require'  => '请输入文本内容',
    ];

    // 验证场景
    protected $scene = [
        'strtran' => ['strtran_str'],
        'strrand' => ['strrand_ids', 'strrand_len'],
        'qrcode'  => ['qrcode_str'],
    ];
}
