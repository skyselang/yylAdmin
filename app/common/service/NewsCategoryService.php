<?php
/*
 * @Description  : 新闻分类
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-19
 * @LastEditTime : 2021-05-19
 */

namespace app\common\service;

use think\facade\Db;

class NewsCategoryService
{
    /**
     * 新闻分类列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'news_category_id,category_name,category_sort,is_hide,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['category_sort' => 'desc', 'news_category_id' => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

        $count = Db::name('news_category')
            ->where($where)
            ->count('news_category_id');

        $list = Db::name('news_category')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 新闻分类信息
     *
     * @param integer $news_category_id 新闻分类id
     * 
     * @return array
     */
    public static function info($news_category_id)
    {
        $news_category = Db::name('news_category')
            ->where('news_category_id', $news_category_id)
            ->find();

        if (empty($news_category)) {
            exception('新闻分类不存在：' . $news_category_id);
        }

        return $news_category;
    }

    /**
     * 新闻分类添加
     *
     * @param array $param 新闻分类信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $news_category_id = Db::name('news_category')
            ->insertGetId($param);

        if (empty($news_category_id)) {
            exception();
        }

        $param['news_category_id'] = $news_category_id;

        return $param;
    }

    /**
     * 新闻分类修改
     *
     * @param array $param 新闻分类信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $news_category_id = $param['news_category_id'];

        unset($param['news_category_id']);

        $param['update_time'] = datetime();

        $res = Db::name('news_category')
            ->where('news_category_id', $news_category_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        $param['news_category_id'] = $news_category_id;

        return $param;
    }

    /**
     * 新闻分类删除
     *
     * @param integer $news_category_id 新闻分类id
     * 
     * @return array
     */
    public static function dele($news_category_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('news_category')
            ->where('news_category_id', $news_category_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_category_id'] = $news_category_id;

        return $update;
    }

    /**
     * 新闻分类是否隐藏
     *
     * @param array $param 新闻分类信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $news_category_id = $param['news_category_id'];

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('news_category')
            ->where('news_category_id', $news_category_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_category_id'] = $news_category_id;

        return $update;
    }
}
