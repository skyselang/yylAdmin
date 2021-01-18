<?php
/*
 * @Description  : 菜单缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2021-01-18
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminMenuCache
{
    /**
     * 缓存key
     *
     * @param integer|string $admin_menu_id 菜单id
     * 
     * @return string
     */
    public static function key($admin_menu_id = '')
    {
        if (empty($admin_menu_id)) {
            $admin_menu_id = 'all';
        }

        $key = 'adminMenu:' . $admin_menu_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $admin_menu_id 菜单id
     * @param array          $admin_menu    菜单信息
     * @param integer        $expire        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_menu_id = '', $admin_menu = [], $expire = 0)
    {
        $key = self::key($admin_menu_id);
        $val = $admin_menu;
        $ttl = 1 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $admin_menu_id 菜单id
     * 
     * @return array 菜单信息
     */
    public static function get($admin_menu_id = '')
    {
        $key = self::key($admin_menu_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $admin_menu_id 菜单id
     * 
     * @return bool
     */
    public static function del($admin_menu_id = '')
    {
        $key = self::key($admin_menu_id);
        $res = Cache::delete($key);

        return $res;
    }
}
