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
use app\common\service\setting\FeedbackService as Service;
use app\common\model\setting\FeedbackModel as Model;

/**
 * 反馈管理验证器
 */
class FeedbackValidate extends Validate
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
        'ids'         => ['require', 'array'],
        'feedback_id' => ['require'],
        'title'       => ['require', 'checkExisted'],
        'content'     => ['require'],
        'images'      => ['array'],
        'phone'       => ['mobile'],
        'email'       => ['email'],
    ];

    // 错误信息
    protected $message = [
        'title.require'   => '请输入标题',
        'content.require' => '请输入内容',
        'phone.mobile'    => '请输入正确手机号码',
        'email.email'     => '请输入正确邮箱地址',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['feedback_id'],
        'add'     => ['title', 'content', 'images', 'phone', 'email'],
        'edit'    => ['feedback_id', 'title', 'content', 'images', 'phone', 'email'],
        'dele'    => ['ids'],
        'disable' => ['ids'],
        'update'  => ['ids', 'field'],
    ];

    // 自定义验证规则：反馈是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = $this->model();
        $pk    = $model->getPk();
        $id    = $data[$pk] ?? 0;

        $unique = $data['unique'] ?? '';
        if ($unique) {
            if (is_numeric($unique)) {
                return lang('编号不能为纯数字');
            }
            $where = where_delete([[$pk, '<>', $id], ['unique', '=', $unique]]);
            $info  = $model->field($pk)->where($where)->find();
            if ($info) {
                return lang('编号已存在：') . $unique;
            }
        }

        return true;
    }

    // 自定义验证规则：反馈批量修改字段
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
