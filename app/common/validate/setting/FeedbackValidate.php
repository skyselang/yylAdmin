<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\setting;

use think\Validate;

/**
 * 反馈管理验证器
 */
class FeedbackValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'feedback_id' => ['require'],
        'title'       => ['require'],
        'content'     => ['require'],
        'phone'       => ['mobile'],
        'email'       => ['email'],
        'images'      => ['array'],
    ];

    // 错误信息
    protected $message = [
        'title.require'   => '请输入标题',
        'content.require' => '请输入内容',
        'phone.mobile'    => '请输入正确手机号码',
        'email.email'     => '请输入正确邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['feedback_id'],
        'add'    => ['title', 'content', 'images', 'phone', 'email'],
        'edit'   => ['feedback_id', 'title', 'content', 'images', 'phone', 'email'],
        'dele'   => ['ids'],
        'readed' => ['ids'],
    ];
}
