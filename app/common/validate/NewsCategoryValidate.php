<?php
/*
 * @Description  : 新闻分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-29
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\NewsService;
use app\common\service\NewsCategoryService;

class NewsCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'news_category'    => ['require', 'array'],
        'news_category_id' => ['require'],
        'category_name'    => ['require', 'checkCategoryName'],
        'image'            => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'          => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'news_category_id'      => 'news_category_id must',
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
        'info'   => ['news_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['news_category_id', 'category_name'],
        'dele'   => ['news_category'],
        'ishide' => ['news_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类名称删除
    protected function scenedele()
    {
        return $this->only(['news_category'])
            ->append('news_category', ['checkNewsCategory', 'checkNews']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = NewsCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在新闻
    protected function checkNews($value, $rule, $data = [])
    {
        $news_category_ids = array_column($value, 'news_category_id');
        
        $where[] = ['news_category_id', 'in', $news_category_ids];
        $where[] = ['is_delete', '=', 0];

        $news = NewsService::list($where);
        if ($news['list']) {
            return '分类下存在新闻，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkNewsCategory($value, $rule, $data = [])
    {
        $news_category_ids = array_column($value, 'news_category_id');

        $news_category = NewsCategoryService::list('list');
        foreach ($news_category as $k => $v) {
            foreach ($news_category_ids as $ka => $va) {
                if ($v['news_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
