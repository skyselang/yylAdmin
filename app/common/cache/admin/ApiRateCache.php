<?php
/*
 * @Description  : 接口速率缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2021-07-14
 */

namespace app\common\cache\admin;

use think\facade\Cache;

class ApiRateCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_user_id 用户id
     * @param string  $menu_url      菜单url
     * 
     * @return string
     */
    public static function key($admin_user_id, $menu_url)
    {
        $key = 'admin:apiRate:' . $admin_user_id . ':' . $menu_url;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @param string  $menu_url      菜单url
     * @param integer $ttl           有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_user_id, $menu_url, $ttl = 10)
    {
        $key = self::key($admin_user_id, $menu_url);
        $val = 1;

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_user_id 用户id
     * @param string  $menu_url      菜单url
     * 
     * @return string
     */
    public static function get($admin_user_id, $menu_url)
    {
        $key = self::key($admin_user_id, $menu_url);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_user_id 用户id
     * @param string  $menu_url      菜单url
     * 
     * @return bool
     */
    public static function del($admin_user_id, $menu_url)
    {
        $key = self::key($admin_user_id, $menu_url);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param integer $admin_user_id 用户id
     * @param string  $menu_url      菜单url
     * 
     * @return bool
     */
    public static function inc($admin_user_id, $menu_url)
    {
        $key = self::key($admin_user_id, $menu_url);
        $res = Cache::inc($key);

        return $res;
    }
}
