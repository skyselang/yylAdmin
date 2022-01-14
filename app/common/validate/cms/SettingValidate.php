<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置验证器
namespace app\common\validate\cms;

use think\Validate;

class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'name' => ['length' => '1,80'],
    ];

    // 错误信息
    protected $message = [
        'name.length' => '名称为1到80个字',
    ];

    // 验证场景
    protected $scene = [
        'edit' => ['name'],
    ];
}
