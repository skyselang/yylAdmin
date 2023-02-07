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
 * 设置管理验证器
 */
class SettingValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'is_feedback' => ['require', 'in' => '0,1'],
        'diy_config'  => ['array', 'checkDiyConfig'],
    ];

    // 错误信息
    protected $message = [];

    // 验证场景
    protected $scene = [
        'edit' => ['is_feedback', 'diy_config'],
    ];

    // 自定义验证规则：自定义设置验证
    protected function checkDiyConfig($value, $rule, $data = [])
    {
        foreach ($data['diy_config'] as $v) {
            if (empty($v['config_key'])) {
                return '请输入键名';
            }
        }

        $key_array  = array_column($data['diy_config'], 'config_key');
        $key_unique = array_unique($key_array);
        $key_repeat = array_diff_assoc($key_array, $key_unique);
        if ($key_repeat) {
            return  '存在重复键名：' . implode(',', $key_repeat);
        }

        return true;
    }
}
