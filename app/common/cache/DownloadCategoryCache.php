<?php
/*
 * @Description  : 下载分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-17
 */

namespace app\common\cache;

use think\facade\Cache;

class DownloadCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $download_category_id 下载分类id
     * 
     * @return string
     */
    public static function key($download_category_id = '')
    {
        $key = 'DownloadCategory:' . $download_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $download_category_id 下载分类id
     * @param mixed   $download_category    下载分类信息
     * @param integer $ttl                 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($download_category_id = '', $download_category = [], $ttl = 0)
    {
        $key = self::key($download_category_id);
        $val = $download_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $download_category_id 下载分类id
     * 
     * @return mixed
     */
    public static function get($download_category_id = '')
    {
        $key = self::key($download_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $download_category_id 下载分类id
     * 
     * @return bool
     */
    public static function del($download_category_id = '')
    {
        $key = self::key($download_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
