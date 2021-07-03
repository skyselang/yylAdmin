<?php
/*
 * @Description  : 文章管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\ArticleCache;
use app\common\utils\ByteUtils;

class ArticleService
{
    /**
     * 文章列表
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
            $field = 'article_id,article_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time,update_time';
        } else {
            $field = str_merge($field, 'article_id,article_category_id,name,imgs,sort,hits,is_top,is_hot,is_rec,is_hide,create_time');
        }

        if (empty($order)) {
            $order = ['article_id' => 'desc'];
        }

        $count = Db::name('article')
            ->where($where)
            ->count('article_id');

        $list = Db::name('article')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        $article_category = ArticleCategoryService::list('list');
        foreach ($list as $k => $v) {
            $list[$k]['category_name'] = '';
            foreach ($article_category as $kp => $vp) {
                if ($v['article_category_id'] == $vp['article_category_id']) {
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
     * 文章信息
     * 
     * @param $article_id 文章id
     * 
     * @return array|Exception
     */
    public static function info($article_id)
    {
        $article = ArticleCache::get($article_id);

        if (empty($article)) {
            $article = Db::name('article')
                ->where('article_id', $article_id)
                ->find();
            if (empty($article)) {
                exception('文章不存在：' . $article_id);
            }

            $article_category = ArticleCategoryService::info($article['article_category_id']);

            $article['category_name'] = $article_category['category_name'];
            $article['imgs']          = file_unser($article['imgs']);
            $article['files']         = file_unser($article['files']);

            ArticleCache::set($article_id, $article);
        }

        // 点击量
        $gate = 10;
        $key = $article_id . 'Hits';
        $hits = ArticleCache::get($key);
        if ($hits) {
            if ($hits >= $gate) {
                $res = Db::name('article')
                    ->where('article_id', '=', $article_id)
                    ->inc('hits', $hits)
                    ->update();
                if ($res) {
                    ArticleCache::del($key);
                }
            } else {
                ArticleCache::inc($key, 1);
            }
        } else {
            ArticleCache::set($key, 1);
        }

        return $article;
    }

    /**
     * 文章添加
     *
     * @param $param 文章信息
     *
     * @return array|Exception
     */
    public static function add($param)
    {
        $param['create_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $article_id = Db::name('article')
            ->insertGetId($param);
        if (empty($article_id)) {
            exception();
        }

        $param['article_id'] = $article_id;
        $param['imgs']       = file_unser($param['imgs']);
        $param['files']      = file_unser($param['files']);

        return $param;
    }

    /**
     * 文章修改 
     *     
     * @param $param 文章信息
     *     
     * @return array|Exception
     */
    public static function edit($param)
    {
        $article_id = $param['article_id'];

        unset($param['article_id']);

        $param['update_time'] = datetime();
        $param['imgs']        = file_ser($param['imgs']);
        $param['files']       = file_ser($param['files']);

        $res = Db::name('article')
            ->where('article_id', $article_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        ArticleCache::del($article_id);

        $param['article_id'] = $article_id;

        return $param;
    }

    /**
     * 文章删除
     * 
     * @param array $article 文章列表
     * 
     * @return array|Exception
     */
    public static function dele($article)
    {
        $article_ids = array_column($article, 'article_id');

        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章上传文件
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
            ->putFile('cms/article', $file, function () use ($type) {
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
     * 文章是否置顶
     *
     * @param array $param 文章信息
     * 
     * @return array
     */
    public static function istop($param)
    {
        $article     = $param['article'];
        $article_ids = array_column($article, 'article_id');

        $update['is_top']      = $param['is_top'];
        $update['update_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章是否热门
     *
     * @param array $param 文章信息
     * 
     * @return array
     */
    public static function ishot($param)
    {
        $article     = $param['article'];
        $article_ids = array_column($article, 'article_id');

        $update['is_hot']      = $param['is_hot'];
        $update['update_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章是否推荐
     *
     * @param array $param 文章信息
     * 
     * @return array
     */
    public static function isrec($param)
    {
        $article     = $param['article'];
        $article_ids = array_column($article, 'article_id');

        $update['is_rec']      = $param['is_rec'];
        $update['update_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章是否隐藏
     *
     * @param array $param 文章信息
     * 
     * @return array
     */
    public static function ishide($param)
    {
        $article     = $param['article'];
        $article_ids = array_column($article, 'article_id');

        $update['is_hide']     = $param['is_hide'];
        $update['update_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章上一条
     *
     * @param integer $article_id  文章id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 上一条文章
     */
    public static function prev($article_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['article_id', '<', $article_id];
        if ($is_category) {
            $article = self::info($article_id);
            $where[] = ['article_category_id', '=', $article['article_category_id']];
        }

        $article = Db::name('article')
            ->field('article_id,name')
            ->where($where)
            ->order('article_id', 'desc')
            ->find();
        if (empty($article)) {
            return [];
        }

        return $article;
    }

    /**
     * 文章下一条
     *
     * @param integer $article_id  文章id
     * @param integer $is_category 是否当前分类
     * 
     * @return array 下一条文章
     */
    public static function next($article_id, $is_category = 0)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['article_id', '>', $article_id];
        if ($is_category) {
            $article = self::info($article_id);
            $where[] = ['article_category_id', '=', $article['article_category_id']];
        }

        $article = Db::name('article')
            ->field('article_id,name')
            ->where($where)
            ->order('article_id', 'asc')
            ->find();
        if (empty($article)) {
            return [];
        }

        return $article;
    }

    /**
     * 表字段
     * 
     * @return array
     */
    public static function tableField()
    {
        $key = 'field';
        $field = ArticleCache::get($key);
        if (empty($field)) {
            $sql = Db::name('article')
                ->field('show COLUMNS')
                ->fetchSql(true)
                ->select();

            $sql = str_replace('SELECT', '', $sql);
            $field = Db::query($sql);
            $field = array_column($field, 'Field');

            ArticleCache::set($key, $field);
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
     * 文章回收站恢复
     * 
     * @param array $article 文章列表
     * 
     * @return array|Exception
     */
    public static function recoverReco($article)
    {
        $article_ids = array_column($article, 'article_id');

        $update['is_delete']   = 0;
        $update['update_time'] = datetime();

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }

    /**
     * 文章回收站删除
     * 
     * @param array $article 文章列表
     * 
     * @return array|Exception
     */
    public static function recoverDele($article)
    {
        $article_ids = array_column($article, 'article_id');

        $res = Db::name('article')
            ->where('article_id', 'in', $article_ids)
            ->delete();
        if (empty($res)) {
            exception();
        }

        foreach ($article_ids as $k => $v) {
            ArticleCache::del($v);
        }

        $update['article_ids'] = $article_ids;

        return $update;
    }
}
