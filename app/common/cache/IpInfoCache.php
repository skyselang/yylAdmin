<?php
/*
 * @Description  : IP信息缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-12-03
 */

namespace app\common\cache;

use think\facade\Cache;

class IpInfoCache
{
    /**
     * 缓存key
     *
     * @param string $ip ip地址
     * 
     * @return string
     */
    public static function key($ip = '')
    {
        $key = 'IpInfo:' . $ip;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param string  $ip     ip地址
     * @param array   $ipinfo ip信息
     * @param integer $expire 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($ip = '', $ipinfo = [], $expire = 0)
    {
        $key = self::key($ip);
        $val = $ipinfo;
        $ttl = 15 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param string $ip ip地址
     * 
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
     * 
     * @return bool
     */
    public static function del($ip = '')
    {
        $key = self::key($ip);
        $res = Cache::delete($key);

        return $res;
    }
}
