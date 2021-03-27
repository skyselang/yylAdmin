<?php
/*
 * @Description  : 用户日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2021-03-25
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\UserLogService;

class UserLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'user_log_id' => ['require', 'checkUserLog'],
    ];

    // 错误信息
    protected $message = [
        'user_log_id.require' => '缺少参数：日志id',
    ];

    // 验证场景
    protected $scene = [
        'user_log_id'   => ['user_log_id'],
        'user_log_dele' => ['user_log_id'],
    ];

    // 自定义验证规则：日志是否存在
    protected function checkUserLog($value, $rule, $data = [])
    {
        $user_log_id = $value;

        $admin_log = UserLogService::info($user_log_id);

        if ($admin_log['is_delete'] == 1) {
            return '日志已被删除：' . $user_log_id;
        }

        return true;
    }
}
