<?php
/*
 * @Description  : 日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\AdminLogService;

class AdminLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_log_id' => ['require', 'checkAdminLog'],
    ];

    // 错误信息
    protected $message = [
        'admin_log_id.require' => '缺少参数：日志id',
    ];

    // 验证场景
    protected $scene = [
        'log_id'   => ['admin_log_id'],
        'log_dele' => ['admin_log_id'],
    ];

    // 自定义验证规则：日志是否存在
    protected function checkAdminLog($value, $rule, $data = [])
    {
        $admin_log_id = $value;

        $admin_log = AdminLogService::info($admin_log_id);

        if ($admin_log['is_delete'] == 1) {
            return '日志已被删除：' . $admin_log_id;
        }

        return true;
    }
}
