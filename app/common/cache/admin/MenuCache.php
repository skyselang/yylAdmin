<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 菜单管理缓存
namespace app\common\cache\admin;

use think\facade\Cache;

class MenuCache
{
    /**
     * 缓存key
     *
     * @param mixed $admin_menu_id 菜单id、key
     * 
     * @return string
     */
    public static function key($admin_menu_id = '')
    {
        return 'admin_menu:' . $admin_menu_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $admin_menu_id 菜单id、key
     * @param array $admin_menu    菜单信息
     * @param int   $ttl           有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($admin_menu_id = '', $admin_menu = [], $ttl = 86400)
    {
        return Cache::set(self::key($admin_menu_id), $admin_menu, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $admin_menu_id 菜单id、key
     * 
     * @return array 菜单信息
     */
    public static function get($admin_menu_id = '')
    {
        return Cache::get(self::key($admin_menu_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $admin_menu_id 菜单id、key
     * 
     * @return bool
     */
    public static function del($admin_menu_id = '')
    {
        if (is_array($admin_menu_id)) {
            $keys = $admin_menu_id;
        } else {
            $keys[] = $admin_menu_id;
        }

        $keys = array_merge($keys, ['list', 'tree', 'urlList', 'unloginUrl', 'unauthUrl', 'unrateUrl']);
        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }

        return $res;
    }
}
