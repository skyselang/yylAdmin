<?php
/*
 * @Description  : 会员日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\MemberLogService;

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
        'member_log_id'   => ['member_log_id'],
        'member_log_dele' => ['member_log_id'],
    ];

    // 自定义验证规则：日志是否存在
    protected function checkMemberLog($value, $rule, $data = [])
    {
        $member_log_id = $value;

        $admin_log = MemberLogService::info($member_log_id);

        if ($admin_log['is_delete'] == 1) {
            return '日志已被删除：' . $member_log_id;
        }

        return true;
    }
}
