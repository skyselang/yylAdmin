<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 公告管理验证器
namespace app\common\validate\admin;

use think\Validate;

class NoticeValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'             => ['require', 'array'],
        'admin_notice_id' => ['require'],
        'title'           => ['require'],
        'open_time_start' => ['require', 'date'],
        'open_time_end'   => ['require', 'date'],
    ];

    // 错误信息
    protected $message = [
        'title.require'           => '请输入标题',
        'open_time_start.require' => '请选择开始时间',
        'open_time_end.require'   => '请选择结束时间',
    ];

    // 验证场景
    protected $scene = [
        'id'       => ['admin_notice_id'],
        'info'     => ['admin_notice_id'],
        'add'      => ['title', 'open_time_start', 'open_time_end'],
        'edit'     => ['admin_notice_id', 'title', 'open_time_start', 'open_time_end'],
        'dele'     => ['ids'],
        'isopen'   => ['ids'],
        'opentime' => ['ids', 'open_time_start', 'open_time_end'],
    ];
}
