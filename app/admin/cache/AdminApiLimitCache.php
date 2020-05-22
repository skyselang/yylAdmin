<?php
/*
 * @Description  : 接口访问频率限制
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-22
 */

namespace app\admin\cache;

use think\facade\Cache;

class AdminApiLimitCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_user_id 用户id
     * @param string $admin_menu_url 菜单url
     * @return string
     */
    public static function key($admin_user_id, $admin_menu_url)
    {
        $admin_menu_url = str_replace('/', '', $admin_menu_url);

        $key = 'adminApiLimit:' . $admin_user_id . ':' . $admin_menu_url;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间
     * @return integer
     */
    public static function exp($expire)
    {
        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @param string $admin_menu_url 菜单url
     * @param integer $expire 有效时间
     * @return bool
     */
    public static function set($admin_user_id, $admin_menu_url, $expire)
    {
        $key = self::key($admin_user_id, $admin_menu_url);
        $val = 1;
        $exp = self::exp($expire);

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_user_id 用户id
     * @param string $admin_menu_url 菜单url
     * @return string
     */
    public static function get($admin_user_id, $admin_menu_url)
    {
        $key = self::key($admin_user_id, $admin_menu_url);

        $res =  Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_user_id 用户id
     * @param string $admin_menu_url 菜单url
     * @return bool
     */
    public static function del($admin_user_id, $admin_menu_url)
    {
        $key = self::key($admin_user_id, $admin_menu_url);

        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param integer $admin_user_id 用户id
     * @param string $admin_menu_url 菜单url
     * @return bool
     */
    public static function inc($admin_user_id, $admin_menu_url)
    {
        $key = self::key($admin_user_id, $admin_menu_url);

        $res = Cache::inc($key);

        return $res;
    }
}
