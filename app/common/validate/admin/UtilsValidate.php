<?php
/*
 * @Description  : 实用工具验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-15
 * @LastEditTime : 2021-07-14
 */

namespace app\common\validate\admin;

use think\Validate;

class UtilsValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'strrand_ids' => ['require'],
        'strrand_len' => ['require', 'egt:1'],
    ];

    // 错误信息
    protected $message = [
        'strrand_ids.require' => '请选择所用字符',
        'strrand_len.require' => '请选择字符长度',
        'strrand_len.egt'     => '字符长度必须大于0',
    ];

    // 验证场景
    protected $scene = [
        'strrand' => ['strrand_ids', 'strrand_len'],
    ];
}
