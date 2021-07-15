<?php
/*
 * @Description  : 日志管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2021-07-14
 */

namespace app\common\validate\admin;

use think\Validate;

class UserLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'admin_user_log_id' => ['require'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'id'   => ['admin_user_log_id'],
        'info' => ['admin_user_log_id'],
        'dele' => ['admin_user_log_id'],
    ];
}
