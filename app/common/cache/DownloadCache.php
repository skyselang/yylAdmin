<?php
/*
 * @Description  : 下载管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\cache;

use think\facade\Cache;

class DownloadCache
{
    /**
     * 缓存键名
     *
     * @param string $download_id 下载id
     * 
     * @return string
     */
    public static function key($download_id = '')
    {
        $key = 'Download:' . $download_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $download_id 下载id
     * @param mixed   $download    下载信息
     * @param integer $ttl         有效时间（秒）
     * 
     * @return bool
     */
    public static function set($download_id = '', $download, $ttl = 0)
    {
        $key = self::key($download_id);
        $val = $download;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $download_id 下载id
     * 
     * @return mixed
     */
    public static function get($download_id = '')
    {
        $key = self::key($download_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $download_id 下载id
     * 
     * @return bool
     */
    public static function del($download_id = '')
    {
        $key = self::key($download_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $download_id 下载id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($download_id = '', $step = 1)
    {
        $key = self::key($download_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
