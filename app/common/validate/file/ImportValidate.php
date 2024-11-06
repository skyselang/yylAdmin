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
 * 导入文件验证器
 */
class ImportValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'       => ['require', 'array'],
        'import_id' => ['require'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'info'        => ['import_id'],
        'edit'        => ['import_id'],
        'dele'        => ['ids'],
        'recycleReco' => ['ids'],
        'recycleDele' => ['ids'],
    ];
}
