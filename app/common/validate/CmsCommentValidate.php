<?php
/*
 * @Description  : 留言管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-09
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\CmsCommentService;

class CmsCommentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'comment'    => ['require', 'array'],
        'comment_id' => ['require'],
        'call'       => ['require'],
        'mobile'     => ['require','mobile'],
        'title'      => ['require'],
        'content'    => ['require'],
        'sort_field' => ['checkSort'],
        'sort_type'  => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'comment_id.require' => 'comment_id must',
        'call.require'       => '请输入称呼',
        'mobile.require'     => '请输入手机',
        'mobile.mobile'      => '请输入正确手机号',
        'title.require'      => '请输入标题',
        'content.require'    => '请输入内容',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['comment_id'],
        'add'    => ['call', 'mobile', 'title', 'content'],
        'edit'   => ['comment_id'],
        'dele'   => ['comment'],
        'isread' => ['comment'],
        'sort'   => ['sort_field', 'sort_type'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_type  = $data['sort_type'];

        $field_exist = CmsCommentService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }

        if (!empty($sort_type) && $sort_type != 'asc' && $sort_type != 'desc') {
            return '排序类型sort_type只能为asc升序或desc降序';
        }

        return true;
    }
}
