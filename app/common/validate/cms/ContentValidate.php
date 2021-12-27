<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容管理验证器
namespace app\common\validate\cms;

use think\Validate;
use app\common\service\cms\ContentService;

class ContentValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'         => ['require', 'array'],
        'content_id'  => ['require'],
        'category_id' => ['require'],
        'name'        => ['require'],
        'image'       => ['array'],
        'video'       => ['array'],
        'file'        => ['array'],
        'is_top'      => ['require', 'in' => '0,1'],
        'is_hot'      => ['require', 'in' => '0,1'],
        'is_rec'      => ['require', 'in' => '0,1'],
        'is_hide'     => ['require', 'in' => '0,1'],
        'sort_field'  => ['checkSort'],
        'sort_value'  => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'category_id.require' => '请选择分类',
        'name.require'        => '请输入名称',
        'is_top.in'           => '是否置顶，1是0否',
        'is_hot.in'           => '是否热门，1是0否',
        'is_rec.in'           => '是否推荐，1是0否',
        'is_hide.in'          => '是否隐藏，1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['content_id'],
        'add'    => ['category_id', 'name', 'image', 'video', 'file'],
        'edit'   => ['content_id', 'category_id', 'name', 'image', 'video', 'file'],
        'dele'   => ['ids'],
        'cate'   => ['ids', 'category_id'],
        'istop'  => ['ids', 'is_top'],
        'ishot'  => ['ids', 'is_hot'],
        'isrec'  => ['ids', 'is_rec'],
        'ishide' => ['ids', 'is_hide'],
        'reco'   => ['ids'],
        'sort'   => ['sort_field', 'sort_value'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_value = $data['sort_value'];
        $field_exist = ContentService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }
        if (!empty($sort_value) && $sort_value != 'asc' && $sort_value != 'desc') {
            return '排序类型sort_value只能为asc升序或desc降序';
        }

        return true;
    }
}
