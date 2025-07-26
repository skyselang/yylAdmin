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
use app\common\service\system\UserLogService as Service;
use app\common\model\system\UserLogModel as Model;

/**
 * 用户日志验证器
 */
class UserLogValidate extends Validate
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
    protected $rule = [
        'ids'           => ['require', 'array'],
        'field'         => ['require', 'checkUpdateField'],
        'log_id'        => ['require'],
        'request_ip'    => ['ip'],
        'request_param' => ['checkRequestParam'],
    ];

    // 错误信息
    protected $message = [
        'request_ip.ip' => '请求IP格式错误',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['log_id'],
        'add'     => ['request_ip', 'request_param'],
        'edit'    => ['log_id', 'request_ip', 'request_param'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
    ];

    // 自定义验证规则：批量修改字段
    protected function checkUpdateField($value, $rule, $data = [])
    {
        $edit_field   = $data['field'];
        $update_field = $this->service::$updateField;
        if (!in_array($edit_field, $update_field)) {
            return lang('不允许修改的字段：') . $edit_field;
        }

        return true;
    }

    // 自定义验证规则：请求参数
    protected function checkRequestParam($value, $rule, $data = [])
    {
        $request_param = $data['request_param'] ?? '';
        if ($request_param) {
            $request_param = json_decode($request_param, true);
            if (is_null($request_param)) {
                return lang('请求参数格式错误');
            }
        }

        return true;
    }
}
