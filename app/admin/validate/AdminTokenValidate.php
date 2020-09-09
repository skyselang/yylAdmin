<?php
/*
 * @Description  : Token验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-08
 * @LastEditTime : 2020-09-09
 */

namespace app\admin\validate;

use think\Validate;

class AdminTokenValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'iss' => ['require', 'length' => '2,32'],
        'exp' => ['require', 'between' => '1,720'],
    ];

    // 错误信息
    protected $message  =   [
        'iss.require' => '请输入签发者',
        'iss.length'  => '签发者为2-32个字符',
        'exp.require' => '请输入有效时间',
        'exp.between' => '有效时间范围为1-720',
    ];

    // 验证场景
    protected $scene = [
        'edit' => ['iss', 'exp'],
    ];
}
