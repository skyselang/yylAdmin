<?php
/*
 * @Description  : 日志缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2020-12-03
 */

namespace app\common\cache;

use think\facade\Cache;

class LogCache
{
    /**
     * 缓存key
     *
     * @param integer|string $log_id 日志id、统计时间
     * 
     * @return integer
     */
    public static function key($log_id = 0)
    {
        $key = 'log:' . $log_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $log_id    日志id、统计时间
     * @param array          $admin_log 日志信息
     * @param integer        $expire    有效时间（秒）
     * 
     * @return bool
     */
    public static function set($log_id = 0, $admin_log = [], $expire = 0)
    {
        $key = self::key($log_id);
        $val = $admin_log;

        if (is_numeric($log_id)) {
            $ttl = 1 * 24 * 60 * 60;
        } else {
            $ttl = 1 * 60 * 60;
        }
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $log_id 日志id、统计时间
     * 
     * @return array 日志信息
     */
    public static function get($log_id = 0)
    {
        $key = self::key($log_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $log_id 日志id、统计时间
     * 
     * @return bool
     */
    public static function del($log_id = 0)
    {
        $key = self::key($log_id);
        $res = Cache::delete($key);

        return $res;
    }
}
