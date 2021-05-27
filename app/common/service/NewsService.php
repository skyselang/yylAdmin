<?php
/*
 * @Description  : 新闻管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-05-22
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\NewsCache;

class NewsService
{
    /**
     * 新闻列表
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
        if ($field) {
            $field = str_merge($field, 'news_id,news_category_id,title');
        } else {
            $field = 'news_id,news_category_id,img,title,time,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'news_id' => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

        $count = Db::name('news')
            ->where($where)
            ->count('news_id');

        $list = Db::name('news')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $news_category_ids = array_column($list, 'news_category_id');
        $news_category_ids = array_unique($news_category_ids);
        $news_category = Db::name('news_category')
            ->where('news_category_id', 'in', $news_category_ids)
            ->select()
            ->toArray();

        foreach ($list as $k => $v) {
            foreach ($news_category as $kn => $vn) {
                if ($v['news_category_id'] == $vn['news_category_id']) {
                    $list[$k]['category_name'] = $vn['category_name'];
                }
            }
            $list[$k]['img_url'] = file_url($v['img']);
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 新闻信息
     *
     * @param integer $news_id 新闻id
     * 
     * @return array
     */
    public static function info($news_id)
    {
        $news = NewsCache::get($news_id);

        if (empty($news)) {
            $news = Db::name('news')
                ->where('news_id', $news_id)
                ->find();

            if (empty($news)) {
                exception('新闻不存在：' . $news_id);
            }

            $news['img_url'] = file_url($news['img']);

            NewsCache::set($news_id, $news);
        }

        Db::name('news')->where('news_id', $news_id)->inc('hits')->update();

        return $news;
    }

    /**
     * 新闻添加
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();

        $news_id = Db::name('news')
            ->insertGetId($param);

        if (empty($news_id)) {
            exception();
        }

        $param['news_id'] = $news_id;

        return $param;
    }

    /**
     * 新闻修改
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $news_id = $param['news_id'];

        unset($param['news_id']);

        $param['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        $param['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $param;
    }

    /**
     * 新闻删除
     *
     * @param integer $news_id 新闻id
     * 
     * @return array
     */
    public static function dele($news_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $update;
    }

    /**
     * 新闻上传文件
     *
     * @param array $param 文件信息
     * 
     * @return array
     */
    public static function upload($param)
    {
        $type = $param['type'];
        $file = $param['file'];

        $file_name = Filesystem::disk('public')
            ->putFile('news', $file, function () use ($type) {
                return date('Ymd') . '/' . date('YmdHis') . '_' . mt_rand(1, 9);
            });

        $data['type']      = $type;
        $data['file_path'] = 'storage/' . $file_name;
        $data['file_url']  = file_url($data['file_path']);

        return $data;
    }

    /**
     * 新闻是否置顶
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $news_id = $param['news_id'];

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $update;
    }

    /**
     * 新闻是否热门
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $news_id = $param['news_id'];

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $update;
    }

    /**
     * 新闻是否推荐
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $news_id = $param['news_id'];

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $update;
    }

    /**
     * 新闻是否隐藏
     *
     * @param array $param 新闻信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $news_id = $param['news_id'];

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['news_id'] = $news_id;

        NewsCache::del($news_id);

        return $update;
    }

    /**
     * 新闻上一条
     *
     * @param integer $news_id 新闻id
     * 
     * @return array 上一条新闻
     */
    public static function last($news_id)
    {
        $news = Db::name('news')
            ->field('news_id,title')
            ->where('is_delete', 0)
            ->where('news_id', '<', $news_id)
            ->order('news_id', 'desc')
            ->find();

        if (empty($news)) {
            return [];
        }

        return $news;
    }

    /**
     * 新闻下一条
     *
     * @param integer $news_id 新闻id
     * 
     * @return array 下一条新闻
     */
    public static function next($news_id)
    {
        $news = Db::name('news')
            ->field('news_id,title')
            ->where('is_delete', 0)
            ->where('news_id', '>', $news_id)
            ->order('news_id', 'asc')
            ->find();

        if (empty($news)) {
            return [];
        }

        return $news;
    }
}
