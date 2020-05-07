<?php
/*
 * @Description  : 日志验证器
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-06
 */

namespace app\admin\validate;

use think\Validate;

class AdminLogValidate extends Validate
{
    protected $rule = [
        'admin_log_id' => ['require'],
    ];

    protected $message  =   [
        'admin_log_id.require' => '缺少参数admin_log_id',
    ];

    protected $scene = [
        'admin_log_id' => ['admin_log_id'],
    ];
}
