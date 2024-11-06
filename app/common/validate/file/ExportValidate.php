<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\file;

use think\Validate;

/**
 * 导出文件验证器
 */
class ExportValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'export_id' => ['require'],
        'file_name' => ['require'],
    ];

    // 错误信息
    protected $message = [
        'file_name.require' => '请输入文件名称'
    ];

    // 验证场景
    protected $scene = [
        'info'        => ['export_id'],
        'edit'        => ['export_id', 'file_name'],
        'dele'        => ['ids'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];
}
