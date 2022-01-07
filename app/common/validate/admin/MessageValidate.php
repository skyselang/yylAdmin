<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 消息管理验证器
namespace app\common\validate\admin;

use think\Validate;

class MessageValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'              => ['require', 'array'],
        'admin_message_id' => ['require'],
        'title'            => ['require']
    ];

    // 错误信息
    protected $message = [
        'title.require' => '请输入标题'
    ];

    // 验证场景
    protected $scene = [
        'id'     => ['admin_message_id'],
        'info'   => ['admin_message_id'],
        'add'    => ['title'],
        'edit'   => ['admin_message_id', 'title'],
        'dele'   => ['ids'],
        'isopen' => ['ids']
    ];
}
