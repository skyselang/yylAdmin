<?php
/*
 * @Description  : 内容设置验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-09
 */

namespace app\common\validate;

use think\Validate;

class CmsSettingValidate extends Validate
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
