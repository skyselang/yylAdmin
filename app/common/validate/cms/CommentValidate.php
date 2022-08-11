<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\cms;

use think\Validate;

/**
 * 留言管理验证器
 */
class CommentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'comment_id' => ['require'],
        'call'       => ['require'],
        'mobile'     => ['require', 'mobile'],
        'title'      => ['require'],
        'content'    => ['require'],
    ];

    // 错误信息
    protected $message = [
        'call.require'    => '请输入称呼',
        'mobile.require'  => '请输入手机',
        'mobile.mobile'   => '请输入正确手机号',
        'title.require'   => '请输入标题',
        'content.require' => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['comment_id'],
        'add'    => ['call', 'mobile', 'title', 'content'],
        'edit'   => ['comment_id'],
        'dele'   => ['ids'],
        'reco'   => ['ids'],
        'isread' => ['ids'],
    ];
}
