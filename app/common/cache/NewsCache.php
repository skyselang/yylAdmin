<?php
/*
 * @Description  : 新闻管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-19
 */

namespace app\common\cache;

use think\facade\Cache;

class NewsCache
{
    /**
     * 缓存键名
     *
     * @param string $news_id 新闻id
     * 
     * @return string
     */
    public static function key($news_id = '')
    {
        $key = 'News:' . $news_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $news_id 新闻id
     * @param mixed   $news    新闻信息
     * @param integer $ttl     有效时间（秒）
     * 
     * @return bool
     */
    public static function set($news_id = '', $news, $ttl = 0)
    {
        $key = self::key($news_id);
        $val = $news;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $news_id 新闻id
     * 
     * @return mixed
     */
    public static function get($news_id = '')
    {
        $key = self::key($news_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $news_id 新闻id
     * 
     * @return bool
     */
    public static function del($news_id = '')
    {
        $key = self::key($news_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $news_id 新闻id
     * @param integer $step    步长
     *
     * @return bool
     */
    public static function inc($news_id = '', $step = 1)
    {
        $key = self::key($news_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
