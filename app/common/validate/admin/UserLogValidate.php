<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户日志验证器
namespace app\common\validate\admin;

use think\Validate;

class UserLogValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'               => ['require', 'array'],
        'admin_user_log_id' => ['require'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'id'   => ['admin_user_log_id'],
        'info' => ['admin_user_log_id'],
        'dele' => ['ids'],
    ];
}
