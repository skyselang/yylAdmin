<?php
/*
 * @Description  : 会员日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2021-05-10
 */

namespace app\common\validate;

use think\Validate;

class MemberLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'member_log_id' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'member_log_id.require' => '缺少参数：会员日志id',
    ];

    // 验证场景
    protected $scene = [
        'id'   => ['member_log_id'],
        'info' => ['member_log_id'],
        'dele' => ['member_log_id'],
    ];
}
