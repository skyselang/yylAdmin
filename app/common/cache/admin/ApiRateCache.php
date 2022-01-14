<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口速率缓存
namespace app\common\cache\admin;

use think\facade\Cache;

class ApiRateCache
{
    /**
     * 缓存key
     *
     * @param int    $admin_user_id 用户id
     * @param string $menu_url      菜单url
     * 
     * @return string
     */
    public static function key($admin_user_id, $menu_url)
    {
        return 'admin_apirate:' . $admin_user_id . ':' . $menu_url;
    }

    /**
     * 缓存设置
     *
     * @param int    $admin_user_id 用户id
     * @param string $menu_url      菜单url
     * @param int    $ttl           有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($admin_user_id, $menu_url, $ttl = 60)
    {
        return Cache::set(self::key($admin_user_id, $menu_url), 1, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int    $admin_user_id 用户id
     * @param string $menu_url      菜单url
     * 
     * @return string
     */
    public static function get($admin_user_id, $menu_url)
    {
        return Cache::get(self::key($admin_user_id, $menu_url));
    }

    /**
     * 缓存删除
     *
     * @param int    $admin_user_id 用户id
     * @param string $menu_url      菜单url
     * 
     * @return bool
     */
    public static function del($admin_user_id, $menu_url)
    {
        return Cache::delete(self::key($admin_user_id, $menu_url));
    }

    /**
     * 缓存自增
     *
     * @param int    $admin_user_id 用户id
     * @param string $menu_url      菜单url
     * @param int    $step          步长
     * 
     * @return bool
     */
    public static function inc($admin_user_id, $menu_url, $step = 1)
    {
        return Cache::inc(self::key($admin_user_id, $menu_url), $step);
    }
}
