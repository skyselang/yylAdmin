<?php
/*
 * @Description  : Token验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-03-09
 */

namespace app\common\validate;

use think\Validate;

class TokenValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'iss' => ['require', 'length' => '2,32'],
        'exp' => ['require', 'between' => '1,9999'],
    ];

    // 错误信息
    protected $message = [
        'iss.require' => '请输入签发者',
        'iss.length'  => '签发者为2-32个字符',
        'exp.require' => '请输入有效时间',
        'exp.between' => '有效时间范围为1-9999小时',
    ];

    // 验证场景
    protected $scene = [
        'edit' => ['iss', 'exp'],
    ];
}
