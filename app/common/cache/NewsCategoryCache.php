<?php
/*
 * @Description  : 新闻分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-19
 */

namespace app\common\cache;

use think\facade\Cache;

class NewsCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $news_category_id 新闻分类id
     * 
     * @return string
     */
    public static function key($news_category_id = '')
    {
        $key = 'NewsCategory:' . $news_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $news_category_id 新闻分类id
     * @param mixed   $news_category    新闻分类信息
     * @param integer $ttl              有效时间（秒）
     * 
     * @return bool
     */
    public static function set($news_category_id = '', $news_category = [], $ttl = 0)
    {
        $key = self::key($news_category_id);
        $val = $news_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $news_category_id 新闻分类id
     * 
     * @return mixed
     */
    public static function get($news_category_id = '')
    {
        $key = self::key($news_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $news_category_id 新闻分类id
     * 
     * @return bool
     */
    public static function del($news_category_id = '')
    {
        $key = self::key($news_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
