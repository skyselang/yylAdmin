<?php
/*
 * @Description  : 用户缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2020-09-27
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminUserCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return string
     */
    public static function key($admin_user_id)
    {
        $key = 'adminUser:' . $admin_user_id;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间
     * 
     * @return integer
     */
    public static function exp($expire = 0)
    {
        if (empty($expire)) {
            $expire = 1 * 24 * 60 * 60 + mt_rand(0, 99);
        }

        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @param array   $admin_user    用户信息
     * @param integer $expire        有效时间
     * 
     * @return array 用户信息
     */
    public static function set($admin_user_id, $admin_user, $expire = 0)
    {
        $key = self::key($admin_user_id);
        $val = $admin_user;
        $exp = $expire ?: self::exp();
        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array 用户信息
     */
    public static function get($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return bool
     */
    public static function del($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::delete($key);

        return $res;
    }
}
