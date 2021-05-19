<?php
/*
 * @Description  : 新闻分类验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-19
 * @LastEditTime : 2021-05-19
 */

namespace app\common\validate;

use think\Validate;
use think\facade\Db;

class NewsCategoryValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'news_category_id' => ['require'],
        'category_name'    => ['require', 'checkClassName'],
    ];

    // 错误信息
    protected $message = [
        'news_category_id.require' => '缺少参数：新闻分类ID',
        'category_name.require'    => '请输入分类名称',
    ];

    // 验证场景
    protected $scene = [
        'id'     => ['news_category_id'],
        'info'   => ['news_category_id'],
        'add'    => ['category_name'],
        'edit'   => ['news_category_id', 'category_name'],
        'dele'   => ['news_category_id'],
        'ishide' => ['news_category_id'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['news_category_id'])
            ->append('news_category_id', ['checkClassNews']);
    }

    // 自定义验证规则：分类名称是否已存在
    protected function checkClassName($value, $rule, $data = [])
    {
        $news_category_id = isset($data['news_category_id']) ? $data['news_category_id'] : '';
        $category_name    = $data['category_name'];

        if ($news_category_id) {
            $where[] = ['news_category_id', '<>', $news_category_id];
        }
        $where[] = ['category_name', '=', $category_name];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('news_category')
            ->field('news_category_id')
            ->where($where)
            ->find();
        if ($member) {
            return '分类名称已存在：' . $category_name;
        }

        return true;
    }

    // 自定义验证规则：分类下是否存在新闻
    protected function checkClassNews($value, $rule, $data = [])
    {
        $news_category_id = $value;

        $where[] = ['news_category_id', '=', $news_category_id];
        $where[] = ['is_delete', '=', 0];

        $member = Db::name('news')
            ->field('news_category_id')
            ->where($where)
            ->find();
        if ($member) {
            return '该分类下存在新闻，无法删除';
        }

        return true;
    }
}
