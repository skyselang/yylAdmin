<?php
/*
 * @Description  : 菜单缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 */

namespace app\cache;

use think\facade\Cache;

class AdminMenuCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_menu_id 菜单id
     * @return string
     */
    public static function key($admin_menu_id = 0)
    {
        $key = 'adminMenu:' . $admin_menu_id;

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
     * @param integer $admin_menu_id 菜单id
     * @param array   $admin_menu    菜单信息
     * @param integer $expire        有效时间
     * @return array 菜单信息
     */
    public static function set($admin_menu_id = 0, $admin_menu = [], $expire = 0)
    {
        $key = self::key($admin_menu_id);
        $val = $admin_menu;
        $exp = $expire ?: self::exp();
        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_menu_id 菜单id
     * @return array 菜单信息
     */
    public static function get($admin_menu_id = 0)
    {
        $key = self::key($admin_menu_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_menu_id 菜单id
     * @return bool
     */
    public static function del($admin_menu_id = 0)
    {
        $key = self::key($admin_menu_id);
        $res = Cache::delete($key);

        return $res;
    }
}
