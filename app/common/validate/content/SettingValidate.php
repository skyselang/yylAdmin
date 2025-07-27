<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\content;

use think\Validate;
use app\common\service\content\SettingService as Service;
use app\common\model\content\SettingModel as Model;

/**
 * 内容设置验证器
 */
class SettingValidate extends Validate
{
    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * 模型
     */
    protected function model()
    {
        return new Model();
    }

    // 验证规则
    protected $rule = [];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'info' => [],
        'edit' => [],
    ];
}
