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
use app\common\model\setting\FeedbackModel;

/**
 * 反馈管理验证器
 */
class FeedbackValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'feedback_id' => ['require'],
        'title'       => ['require'],
        'content'     => ['require'],
        'images'      => ['array'],
        'phone'       => ['mobile'],
        'email'       => ['email'],
        'receipt_no'  => ['checkReceiptNo'],
        'status'      => ['require'],
        'is_disable'  => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'title.require'   => '请输入标题',
        'phone.mobile'    => '请输入正确手机号码',
        'email.email'     => '请输入正确邮箱地址',
        'content.require' => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'info'    => ['feedback_id'],
        'add'     => ['title', 'content', 'images', 'phone', 'email', 'receipt_no'],
        'edit'    => ['feedback_id', 'title', 'content', 'images', 'phone', 'email', 'receipt_no'],
        'dele'    => ['ids'],
        'status'  => ['ids', 'status'],
        'disable' => ['ids', 'is_disable'],
    ];

    // 自定义验证规则：回执编号是否已存在
    protected function checkReceiptNo($value, $rule, $data = [])
    {
        $receipt_no = $data['receipt_no'] ?? '';
        if ($receipt_no) {
            if (is_numeric($receipt_no)) {
                return '回执编号不能为纯数字';
            }

            $model = new FeedbackModel();
            $pk = $model->getPk();
            $id = $data[$pk] ?? 0;

            $where[] = [$pk, '<>', $id];
            $where[] = ['receipt_no', '=', $receipt_no];
            $where = where_delete($where);
            $info = $model->field($pk)->where($where)->find();
            if ($info) {
                return '回执编号已存在：' . $receipt_no;
            }
        }

        return true;
    }
}
