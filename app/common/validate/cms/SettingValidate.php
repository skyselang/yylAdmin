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
        'name'  => ['length' => '1,80'],
        'image' => ['require', 'file', 'image', 'fileExt' => 'jpg,png,jpeg', 'fileSize' => '204800'],
    ];

    // 错误信息
    protected $message = [
        'name.length'    => '名称为1到80个字',
        'image.require'  => '请选择图片',
        'image.file'     => '请选择文件',
        'image.image'    => '请选择图片格式文件',
        'image.fileExt'  => '请选择jpg、png、jpeg格式图片',
        'image.fileSize' => '请选择小于200kb的图片',
    ];

    // 验证场景
    protected $scene = [
        'edit'  => ['name'],
        'image' => ['image'],
    ];
}
