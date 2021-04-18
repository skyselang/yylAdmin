<?php
/*
 * @Description  : 管理员日志缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-15
 * @LastEditTime : 2021-04-10
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminUserLogCache
{
    /**
     * 缓存key
     *
     * @param integer|string $admin_user_log_id 日志id、统计时间
     * 
     * @return integer
     */
    public static function key($admin_user_log_id = '')
    {
        $key = 'AdminUserLog:' . $admin_user_log_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $admin_user_log_id 日志id、统计时间
     * @param array          $admin_user_log    日志信息
     * @param integer        $ttl               有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_user_log_id = '', $admin_user_log = [], $ttl = 0)
    {
        $key = self::key($admin_user_log_id);
        $val = $admin_user_log;

        if (empty($ttl)) {
            $ttl = 1 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $admin_user_log_id 日志id、统计时间
     * 
     * @return array 日志信息
     */
    public static function get($admin_user_log_id = '')
    {
        $key = self::key($admin_user_log_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $admin_user_log_id 日志id、统计时间
     * 
     * @return bool
     */
    public static function del($admin_user_log_id = '')
    {
        $key = self::key($admin_user_log_id);
        $res = Cache::delete($key);

        return $res;
    }
}
