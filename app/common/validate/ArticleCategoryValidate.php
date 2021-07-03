<?php
/*
 * @Description  : 文章分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-29
 */

namespace app\common\validate;

use think\Validate;
use app\common\service\ArticleService;
use app\common\service\ArticleCategoryService;

class ArticleCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'article_category'    => ['require', 'array'],
        'article_category_id' => ['require'],
        'category_name'       => ['require', 'checkCategoryName'],
        'image'               => ['require', 'file', 'image', 'fileExt' => 'jpg,png,gif,jpeg', 'fileSize' => '512000'],
        'is_hide'             => ['require', 'in' => '0,1'],
    ];

    // 错误信息
    protected $message = [
        'article_category_id'   => '缺少参数：文章分类id',
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
        'info'   => ['article_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['article_category_id', 'category_name'],
        'dele'   => ['article_category'],
        'ishide' => ['article_category', 'is_hide'],
        'image'  => ['image'],
    ];

    // 验证场景定义：分类删除
    protected function scenedele()
    {
        return $this->only(['article_category'])
            ->append('article_category', ['checkArticleCategory', 'checkArticle']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkCategoryName($value, $rule, $data = [])
    {
        $check = ArticleCategoryService::checkCategoryName($data);

        return $check;
    }

    // 自定义验证规则：分类下是否存在文章
    protected function checkArticle($value, $rule, $data = [])
    {
        $article_category_ids = array_column($value, 'article_category_id');

        $where[] = ['article_category_id', 'in', $article_category_ids];
        $where[] = ['is_delete', '=', 0];

        $article = ArticleService::list($where);
        if ($article['list']) {
            return '分类下存在文章，无法删除';
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在子分类
    protected function checkArticleCategory($value, $rule, $data = [])
    {
        $article_category_ids = array_column($value, 'article_category_id');

        $article_category = ArticleCategoryService::list('list');
        foreach ($article_category as $k => $v) {
            foreach ($article_category_ids as $ka => $va) {
                if ($v['article_category_pid'] == $va) {
                    return '分类下存在子分类，无法删除';
                }
            }
        }

        return true;
    }
}
