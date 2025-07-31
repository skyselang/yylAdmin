<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\system;

use think\Validate;
use app\common\service\system\SmsLogService as Service;
use app\common\model\system\SmsLogModel as Model;

/**
 * 短信日志验证器
 */
class SmsLogValidate extends Validate
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

    /**
     * 验证规则
     */
    protected $rule = [
        'ids'         => ['require', 'array'],
        'field'       => ['require', 'checkUpdateField'],
        'log_id'      => ['require'],
        'phone'       => ['require'],
        'create_time' => ['dateFormat:Y-m-d H:i:s']
    ];

    /**
     * 错误信息
     */
    protected $message = [
        'phone.require'          => '请输入手机号码',
        'create_time.dateFormat' => '请输入正确时间格式'
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'info'    => ['log_id'],
        'add'     => ['phone', 'create_time'],
        'edit'    => ['log_id', 'phone', 'create_time'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
    ];

    /**
     * 自定义验证规则：批量修改字段
     */
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }
}
