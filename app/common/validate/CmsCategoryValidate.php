<?php
/*
 * @Description  : 内容分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-07-08
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\CmsService;
use app\common\service\CmsCategoryService;

class CmsCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'category'      => ['require', 'array'],
        'category_id'   => ['require'],
        'category_name' => ['require', 'checkCategoryName'],
        'image'         => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'       => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'category_name.require' => '请输入内容分类名称',
        'image.require'         => '请选择图片',
        'image.file'            => '请选择上传图片',
        'image.image'           => '请选择图片格式文件',
        'image.fileExt'         => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'        => '请选择小于500kb的图片',
        'is_hide.in'            => '是否隐藏 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['category_id'],
        'add'    => ['category_name'],
        'edit'   => ['category_id', 'category_name'],
        'dele'   => ['category'],
        'ishide' => ['category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：内容分类删除
    protected function scenedele()
    {
        return $this->only(['category'])
            ->append('category', ['checkCategory', 'check']);
    }

    // 自定义验证规则：内容分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = CmsCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：内容分类下是否存在内容
    protected function checkCategoryCms($value, $rule, $data = [])
    {
        $category_ids = array_column($value, 'category_id');
        $cms_type     = $data['cms_type'];

        $where[] = ['category_id', 'in', $category_ids];
        $where[] = ['is_delete', '=', 0];

        $article = CmsService::list($where, 1, 1, [], 'cms_id,category_id,imgs');
        if ($article['list']) {
            return '内容分类下存在内容，无法删除';
        }

        return true;
    }

    // 自定义验证规则：内容分类下是否存在子内容分类
    protected function checkCategory($value, $rule, $data = [])
    {
        $category_ids = array_column($value, 'category_id');

        $category = CmsCategoryService::list('list');
        foreach ($category as $k => $v) {
            foreach ($category_ids as $ka => $va) {
                if ($v['category_id'] == $va) {
                    return '内容分类下存在子内容分类，无法删除';
                }
            }
        }

        return true;
    }
}
