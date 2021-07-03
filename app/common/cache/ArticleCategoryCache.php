<?php
/*
 * @Description  : 文章分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-17
 */

namespace app\common\cache;

use think\facade\Cache;

class ArticleCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $article_category_id 文章分类id
     * 
     * @return string
     */
    public static function key($article_category_id = '')
    {
        $key = 'ArticleCategory:' . $article_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $article_category_id 文章分类id
     * @param mixed   $article_category    文章分类信息
     * @param integer $ttl                 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($article_category_id = '', $article_category = [], $ttl = 0)
    {
        $key = self::key($article_category_id);
        $val = $article_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $article_category_id 文章分类id
     * 
     * @return mixed
     */
    public static function get($article_category_id = '')
    {
        $key = self::key($article_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $article_category_id 文章分类id
     * 
     * @return bool
     */
    public static function del($article_category_id = '')
    {
        $key = self::key($article_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
