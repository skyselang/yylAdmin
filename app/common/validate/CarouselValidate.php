<?php
/*
 * @Description  : 轮播管理验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\CarouselService;

class CarouselValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'carousel'    => ['require', 'array'],
        'carousel_id' => ['require'],
        'name'        => ['require'],
        'url'         => ['require'],
        'image'       => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_top'      => ['require', 'in' => '0,1'],
        'is_hot'      => ['require', 'in' => '0,1'],
        'is_rec'      => ['require', 'in' => '0,1'],
        'is_hide'     => ['require', 'in' => '0,1'],
        'sort_field'  => ['checkSort'],
        'sort_type'   => ['checkSort'],
    ];

    // 错误信息
    protected $message = [
        'carousel_id'     => 'carousel_id must',
        'name.require'    => '请输入名称',
        'url.require'     => '请输入链接',
        'image.require'   => '请选择图片',
        'image.file'      => '请选择上传图片',
        'image.image'     => '请选择图片格式文件',
        'image.fileExt'   => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'  => '请选择小于500kb的图片',
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
        'info'   => ['carousel_id'],
        'add'    => ['name', 'url'],
        'edit'   => ['carousel_id', 'name', 'url'],
        'dele'   => ['carousel'],
        'istop'  => ['carousel', 'is_top'],
        'ishot'  => ['carousel', 'is_hot'],
        'isrec'  => ['carousel', 'is_rec'],
        'ishide' => ['carousel', 'is_hide'],
        'image'  => ['image'],
        'sort'   => ['sort_field', 'sort_type'],
    ];

    // 自定义验证规则：排序字段是否存在，排序类型是否为asc、desc
    protected function checkSort($value, $rule, $data = [])
    {
        $sort_field = $data['sort_field'];
        $sort_type  = $data['sort_type'];

        $field_exist = CarouselService::tableFieldExist($sort_field);
        if (!$field_exist) {
            return '排序字段sort_field：' . $sort_field . ' 不存在';
        }

        if (!empty($sort_type) && $sort_type != 'asc' && $sort_type != 'desc') {
            return '排序类型sort_type只能为asc升序或desc降序';
        }

        return true;
    }
}
