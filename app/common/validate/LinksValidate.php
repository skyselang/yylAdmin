<?php
/*
 * @Description  : 友链管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-01
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\LinksService;

class LinksValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'links'      => ['require', 'array'],
        'links_id'   => ['require'],
        'name'       => ['require'],
        'url'        => ['require'],
        'image'      => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '204800'],
        'is_top'     => ['require', 'in' => '0,1'],
        'is_hot'     => ['require', 'in' => '0,1'],
        'is_rec'     => ['require', 'in' => '0,1'],
        'is_hide'    => ['require', 'in' => '0,1'],
        'sort_field' => ['checkSort'],
        'sort_type'  => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'links_id'        => 'links_id must',
        'name.require'    => '请输入名称',
        'url.require'     => '请输入链接',
        'image.require'   => '请选择图片',
        'image.file'      => '请选择上传图片',
        'image.image'     => '请选择图片格式文件',
        'image.fileExt'   => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'  => '请选择小于200kb的图片',
        'is_top.require'  => 'is_top must',
        'is_top.in'       => 'is_top 1是0否',
        'is_hot.require'  => 'is_hot must',
        'is_hot.in'       => 'is_hot 1是0否',
        'is_rec.require'  => 'is_rec must',
        'is_rec.in'       => 'is_rec 1是0否',
        'is_hide.require' => 'is_hide must',
        'is_hide.in'      => 'is_hide 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['links_id'],
        'add'    => ['name', 'url'],
        'edit'   => ['links_id', 'name', 'url'],
        'dele'   => ['links'],
        'istop'  => ['links', 'is_top'],
        'ishot'  => ['links', 'is_hot'],
        'isrec'  => ['links', 'is_rec'],
        'ishide' => ['links', 'is_hide'],
        'image'  => ['image'],
        'sort'   => ['sort_field', 'sort_type'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_type  = $data['sort_type'];

        $field_exist = LinksService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }

        if (!empty($sort_type) && $sort_type != 'asc' && $sort_type != 'desc') {
            return '排序类型sort_type只能为asc升序或desc降序';
        }

        return true;
    }
}
