<?php
/*
 * @Description  : 日志缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-15
 * @LastEditTime : 2020-09-27
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminLogCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_log_id 日志id
     * 
     * @return integer
     */
    public static function key($admin_log_id = 0)
    {
        $key = 'adminLog:' . $admin_log_id;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间（秒）
     * 
     * @return integer
     */
    public static function exp($expire = 0)
    {
        if (empty($expire)) {
            $expire = 3 * 24 * 60 * 60 + mt_rand(0, 99);
        }

        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_log_id 日志id
     * @param array   $admin_log    日志信息
     * @param integer $expire       有效时间
     * 
     * @return array 日志信息
     */
    public static function set($admin_log_id = 0, $admin_log = [], $expire = 0)
    {
        $key = self::key($admin_log_id);
        $val = $admin_log;
        $exp = $expire ?: self::exp();

        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_log_id 日志id
     * 
     * @return array 日志信息
     */
    public static function get($admin_log_id = 0)
    {
        $key = self::key($admin_log_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_log_id 日志id
     * 
     * @return bool
     */
    public static function del($admin_log_id = 0)
    {
        $key = self::key($admin_log_id);
        $res = Cache::delete($key);

        return $res;
    }
}
