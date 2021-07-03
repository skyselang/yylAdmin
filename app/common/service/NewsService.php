<?php
/*
 * @Description  : 新闻管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\NewsCache;
use app\common\utils\ByteUtils;

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
        if (empty($field)) {
            $field = 'news_id,news_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'news_id,news_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['news_id' => 'desc'];
        }

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

        $news_category = NewsCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($news_category as $kp => $vp) {
                if ($v['news_category_id'] == $vp['news_category_id']) {
                    $list[$k]['category_name'] = $vp['category_name'];
                }
            }

            $list[$k]['img_url'] = '';
            $imgs = file_unser($v['imgs']);
            if ($imgs) {
                $list[$k]['img_url'] = $imgs[0]['url'];
            }
            unset($list[$k]['imgs']);
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
     * @param $news_id 新闻id
     * 
     * @return array|Exception
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

            $news_category = NewsCategoryService::info($news['news_category_id']);

            $news['category_name'] = $news_category['category_name'];
            $news['imgs']          = file_unser($news['imgs']);
            $news['files']         = file_unser($news['files']);

            NewsCache::set($news_id, $news);
        }

        // 点击量
        $gate = 10;
        $key = $news_id . 'Hits';
        $hits = NewsCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('news')
                    ->where('news_id', '=', $news_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    NewsCache::del($key);
                }
            } else {
                NewsCache::inc($key, 1);
            }
        } else {
            NewsCache::set($key, 1);
        }

        return $news;
    }

    /**
     * 新闻添加
     *
     * @param $param 新闻信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $news_id = Db::name('news')
            ->insertGetId($param);
        if (empty($news_id)) {
            exception();
        }

        $param['news_id'] = $news_id;
        $param['imgs']    = file_unser($param['imgs']);
        $param['files']   = file_unser($param['files']);

        return $param;
    }

    /**
     * 新闻修改 
     *     
     * @param array $param 新闻信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $news_id = $param['news_id'];

        unset($param['news_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('news')
            ->where('news_id', $news_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        NewsCache::del($news_id);

        $param['news_id'] = $news_id;

        return $param;
    }

    /**
     * 新闻删除
     * 
     * @param array $news 新闻
     * 
     * @return array|Exception
     */
    public static function dele($news)
    {
        $news_ids = array_column($news, 'news_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

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
            ->putFile('cms/news', $file, function () use ($type) {
                return date('Ymd') . '/' . date('YmdHis') . '_' . $type;
            });

        $data['type'] = $type;
        $data['path'] = 'storage/' . $file_name;
        $data['url']  = file_url($data['path']);
        $data['name'] = $file->getOriginalName();
        $data['size'] = ByteUtils::format($file->getSize());

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
        $news     = $param['news'];
        $news_ids = array_column($news, 'news_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

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
        $news     = $param['news'];
        $news_ids = array_column($news, 'news_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

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
        $news     = $param['news'];
        $news_ids = array_column($news, 'news_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

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
        $news     = $param['news'];
        $news_ids = array_column($news, 'news_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

        return $update;
    }

    /**
     * 新闻上一条
     *
     * @param integer $news_id     新闻id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条新闻
     */
    public static function prev($news_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['news_id', '<', $news_id];
        if ($is_category) {
            $news = self::info($news_id);
            $where[] = ['news_category_id', '=', $news['news_category_id']];
        }

        $news = Db::name('news')
            ->field('news_id,name')
            ->where($where)
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
     * @param integer $news_id     新闻id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条新闻
     */
    public static function next($news_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['news_id', '>', $news_id];
        if ($is_category) {
            $news = self::info($news_id);
            $where[] = ['news_category_id', '=', $news['news_category_id']];
        }

        $news = Db::name('news')
            ->field('news_id,name')
            ->where($where)
            ->order('news_id', 'asc')
            ->find();
        if (empty($news)) {
            return [];
        }

        return $news;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = NewsCache::get($key);
        if (empty($field)) {
            $sql = Db::name('news')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            NewsCache::set($key, $field);
        }

        return $field;
    }

    /**
     * 表字段是否存在
     * 
     * @param string $field 要检查的字段
     * 
     * @return bool
     */
    public static function tableFieldExist($field)
    {
        $fields = self::tableField();

        foreach ($fields as $k => $v) {
            if ($v == $field) {
                return true;
            }
        }

        return false;
    }

    /**
     * 新闻回收站恢复
     * 
     * @param array $news 新闻列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($news)
    {
        $news_ids = array_column($news, 'news_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

        return $update;
    }

    /**
     * 新闻回收站删除
     * 
     * @param array $news 新闻列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($news)
    {
        $news_ids = array_column($news, 'news_id');

        $res = Db::name('news')
            ->where('news_id', 'in', $news_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($news_ids as $k => $v) {
            NewsCache::del($v);
        }

        $update['news_ids'] = $news_ids;

        return $update;
    }
}
