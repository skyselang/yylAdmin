<?php
/*
 * @Description  : 会员日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2021-04-10
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\MemberLogService;

class MemberLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'member_log_id' => ['require', 'checkMemberLog'],
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

    // 自定义验证规则：会员日志是否存在
    protected function checkMemberLog($value, $rule, $data = [])
    {
        $member_log_id = $value;

        $member_log = MemberLogService::info($member_log_id);

        if ($member_log['is_delete'] == 1) {
            return '会员日志已被删除：' . $member_log_id;
        }

        return true;
    }
}
