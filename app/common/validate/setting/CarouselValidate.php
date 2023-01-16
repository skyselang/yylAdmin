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
 * 轮播管理验证器
 */
class CarouselValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'carousel_id' => ['require'],
        'file_type'   => ['require'],
        'title'       => ['require'],
    ];

    // 错误信息
    protected $message = [
        'title.require'     => '请输入标题',
        'file_type.require' => '请选择类型',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['carousel_id'],
        'add'      => ['file_type', 'title'],
        'edit'     => ['carousel_id', 'file_type', 'title'],
        'dele'     => ['ids'],
        'position' => ['ids'],
        'disable'  => ['ids'],
    ];
}
