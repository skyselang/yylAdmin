<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言管理验证器
namespace app\common\validate\cms;

use think\Validate;
use app\common\service\cms\CommentService;

class CommentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'comment_id' => ['require'],
        'call'       => ['require'],
        'mobile'     => ['require','mobile'],
        'title'      => ['require'],
        'content'    => ['require'],
        'sort_field' => ['checkSort'],
        'sort_value' => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'call.require'    => '请输入称呼',
        'mobile.require'  => '请输入手机',
        'mobile.mobile'   => '请输入正确手机号',
        'title.require'   => '请输入标题',
        'content.require' => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['comment_id'],
        'add'    => ['call', 'mobile', 'title', 'content'],
        'edit'   => ['comment_id'],
        'dele'   => ['ids'],
        'reco'   => ['ids'],
        'isread' => ['ids'],
        'sort'   => ['sort_field', 'sort_value'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_value = $data['sort_value'];
        $field_exist = CommentService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }
        if (!empty($sort_value) && $sort_value != 'asc' && $sort_value != 'desc') {
            return '排序类型sort_value只能为asc升序或desc降序';
        }

        return true;
    }
}
