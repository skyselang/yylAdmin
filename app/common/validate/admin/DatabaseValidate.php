<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 数据库管理验证器
namespace app\common\validate\admin;

use think\Validate;

class DatabaseValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'               => ['require', 'array'],
        'table'             => ['require', 'array'],
        'table_name'        => ['require'],
        'admin_database_id' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'table.require'      => '请选择表',
        'table_name.require' => '请选择表',
    ];

    // 验证场景
    protected $scene = [
        'id'       => ['admin_database_id'],
        'info'     => ['admin_database_id'],
        'add'      => ['table'],
        'edit'     => ['admin_database_id'],
        'dele'     => ['ids'],
        'down'     => ['admin_database_id'],
        'restore'  => ['admin_database_id'],
        'optimize' => ['table'],
        'repair'   => ['table'],
    ];
}
