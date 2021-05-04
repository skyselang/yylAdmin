<?php
/*
 * @Description  : 用户日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2021-04-16
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\AdminUserLogService;

class AdminUserLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_log_id' => ['require', 'checkAdminUserLog'],
    ];

    // 错误信息
    protected $message = [
        'admin_user_log_id.require' => '缺少参数：用户日志id',
    ];

    // 验证场景
    protected $scene = [
        'id'   => ['admin_user_log_id'],
        'info' => ['admin_user_log_id'],
        'dele' => ['admin_user_log_id'],
    ];

    // 自定义验证规则：用户日志是否存在
    protected function checkAdminUserLog($value, $rule, $data = [])
    {
        $admin_user_log_id = $value;

        $admin_user_log = AdminUserLogService::info($admin_user_log_id);

        if ($admin_user_log['is_delete'] == 1) {
            return '用户日志已被删除：' . $admin_user_log_id;
        }

        return true;
    }
}
