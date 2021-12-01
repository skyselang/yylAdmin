<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类验证器
namespace app\common\validate\cms;

use think\Validate;
use app\common\service\cms\ContentService;
use app\common\service\cms\CategoryService;

class CategoryValidate extends Validate
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
        'category_name.require' => '请输入分类名称',
        'image.require'         => '请选择图片',
        'image.file'            => '请选择上传图片',
        'image.image'           => '请选择图片格式文件',
        'image.fileExt'         => '请选择jpg、png、jpeg格式图片',
        'image.fileSize'        => '请选择小于500kb的图片',
        'is_hide.in'            => '是否隐藏 1是0否',
    ];

    // 验证场景
    protected $scene = [
        'info'   => ['category_id'],
        'add'    => ['category_name'],
        'edit'   => ['category_id', 'category_name'],
        'dele'   => ['category'],
        'pid'    => ['category'],
        'ishide' => ['category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类删除
    protected function scenedele()
    {
        return $this->only(['category'])
            ->append('category', ['checkCategoryPid', 'checkCategoryContent']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = CategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkCategoryPid($value, $rule, $data = [])
    {
        $category_ids = array_column($value, 'category_id');

        $category = CategoryService::list('list');
        foreach ($category as $k => $v) {
            foreach ($category_ids as $kc => $vc) {
                if ($v['category_pid'] == $vc) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在内容
    protected function checkCategoryContent($value, $rule, $data = [])
    {
        $category_ids = array_column($value, 'category_id');

        $where[] = ['category_id', 'in', $category_ids];
        $where[] = ['is_delete', '=', 0];
        $content = ContentService::list($where, 1, 1, [], 'content_id,category_id');
        if ($content['list']) {
            return '分类下存在内容，无法删除';
        }

        return true;
    }
}
