<?php
/*
 * @Description  : 文章管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-19
 */

namespace app\common\cache;

use think\facade\Cache;

class ArticleCache
{
    /**
     * 缓存键名
     *
     * @param string $article_id 文章id
     * 
     * @return string
     */
    public static function key($article_id = '')
    {
        $key = 'Article:' . $article_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $article_id 文章id
     * @param mixed   $article    文章信息
     * @param integer $ttl        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($article_id = '', $article, $ttl = 0)
    {
        $key = self::key($article_id);
        $val = $article;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $article_id 文章id
     * 
     * @return mixed
     */
    public static function get($article_id = '')
    {
        $key = self::key($article_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $article_id 文章id
     * 
     * @return bool
     */
    public static function del($article_id = '')
    {
        $key = self::key($article_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $article_id 文章id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($article_id = '', $step = 1)
    {
        $key = self::key($article_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
