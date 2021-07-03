<?php
/*
 * @Description  : 产品分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-29
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\ProductService;
use app\common\service\ProductCategoryService;

class ProductCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'product_category'    => ['require', 'array'],
        'product_category_id' => ['require'],
        'category_name'       => ['require', 'checkCategoryName'],
        'image'               => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'             => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'product_category_id'   => '缺少参数：产品分类id',
        'category_name.require' => '请输入分类名称',
        'image.require'         => '请选择图片',
        'image.file'            => '请选择上传图片',
        'image.image'           => '请选择图片格式文件',
        'image.fileExt'         => '请选择jpg、png、gif、jpeg格式图片',
        'image.fileSize'        => '请选择小于500kb的图片',
        'is_hide.require'       => 'is_hide must',
        'is_hide.in'            => 'is_hide 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['product_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['product_category_id', 'category_name'],
        'dele'   => ['product_category'],
        'ishide' => ['product_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类名称删除
    protected function scenedele()
    {
        return $this->only(['product_category'])
            ->append('product_category', ['checkProductCategory', 'checkProduct']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = ProductCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在产品
    protected function checkProduct($value, $rule, $data = [])
    {
        $product_category_ids = array_column($value, 'product_category_id');
        
        $where[] = ['product_category_id', 'in', $product_category_ids];
        $where[] = ['is_delete', '=', 0];

        $product = ProductService::list($where);
        if ($product['list']) {
            return '分类下存在产品，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkProductCategory($value, $rule, $data = [])
    {
        $product_category_ids = array_column($value, 'product_category_id');

        $product_category = ProductCategoryService::list('list');
        foreach ($product_category as $k => $v) {
            foreach ($product_category_ids as $ka => $va) {
                if ($v['product_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
