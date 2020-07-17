<?php
/*
 * @Description  : ip信息缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-14
 */

namespace app\cache;

use think\facade\Cache;

class AdminIpInfoCache
{
    /**
     * 缓存key
     *
     * @param string $ip ip地址
     * @return string
     */
    public static function key($ip = '')
    {
        $key = 'adminIpInfo:' . $ip;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间
     * @return integer
     */
    public static function exp($expire = 0)
    {
        if (empty($expire)) {
            $expire = 30 * 24 * 60 * 60;
        }

        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param string  $ip      ip地址
     * @param array   $ip_info ip信息
     * @param integer $expire  有效时间
     * @return array ip信息
     */
    public static function set($ip = '', $ip_info = [], $expire = 0)
    {
        $key = self::key($ip);
        $val = $ip_info;
        $exp = $expire ?: self::exp();

        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param string $ip ip地址
     * @return array ip信息
     */
    public static function get($ip = '')
    {
        $key = self::key($ip);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $ip ip地址
     * @return bool
     */
    public static function del($ip = '')
    {
        $key = self::key($ip);
        $res = Cache::delete($key);

        return $res;
    }
}
