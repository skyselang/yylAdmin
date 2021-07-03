<?php
/*
 * @Description  : 友链管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\common\cache;

use think\facade\Cache;

class LinksCache
{
    /**
     * 缓存键名
     *
     * @param string $links_id 友链id
     * 
     * @return string
     */
    public static function key($links_id = '')
    {
        $key = 'Links:' . $links_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $links_id 友链id
     * @param mixed   $links    友链信息
     * @param integer $ttl      有效时间（秒）
     * 
     * @return bool
     */
    public static function set($links_id = '', $links, $ttl = 0)
    {
        $key = self::key($links_id);
        $val = $links;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $links_id 友链id
     * 
     * @return mixed
     */
    public static function get($links_id = '')
    {
        $key = self::key($links_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $links_id 友链id
     * 
     * @return bool
     */
    public static function del($links_id = '')
    {
        $key = self::key($links_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $links_id 友链id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($links_id = '', $step = 1)
    {
        $key = self::key($links_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
