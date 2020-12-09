<?php
/*
 * @Description  : 会员日志验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2020-12-02
 */

namespace app\admin\validate;

use think\Validate;
use app\admin\service\LogService;

class LogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'log_id' => ['require', 'checkLog'],
    ];

    // 错误信息
    protected $message = [
        'log_id.require' => '缺少参数：日志id',
    ];

    // 验证场景
    protected $scene = [
        'log_id' => ['log_id'],
    ];

    // 自定义验证规则：日志是否存在
    protected function checkLog($value, $rule, $data = [])
    {
        $log_id = $value;

        $admin_log = LogService::info($log_id);

        if ($admin_log['is_delete'] == 1) {
            return '日志已被删除：' . $log_id;
        }

        return true;
    }
}
