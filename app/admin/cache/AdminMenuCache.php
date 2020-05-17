<?php
/*
 * @Description  : 菜单缓存
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-06
 */

namespace app\admin\cache;

use think\facade\Db;
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
        return 'admin_menu_id:' . $admin_menu_id;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $exp 有效时间
     * @return integer
     */
    public static function exp($exp = 0)
    {
        if ($exp) {
            return $exp;
        }

        return 30 * 24 * 60 * 60;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_menu_id 菜单id
     * @return array
     */
    public static function set($admin_menu_id = 0)
    {
        if ($admin_menu_id == 0) {
            $admin_menu = Db::name('admin_menu')
                ->where('is_delete', 0)
                ->select()
                ->toArray();
        } else {
            $admin_menu = Db::name('admin_menu')
                ->where('admin_menu_id', $admin_menu_id)
                ->where('is_delete', 0)
                ->find();
        }

        Cache::set(self::key($admin_menu_id), $admin_menu, self::exp());

        return $admin_menu;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_menu_id 菜单id
     * @return array
     */
    public static function get($admin_menu_id = 0)
    {
        $admin_user =  Cache::get(self::key($admin_menu_id));
        if ($admin_user) {
            return $admin_user;
        }

        return self::set($admin_menu_id);
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_menu_id 菜单id
     * @return bool
     */
    public static function del($admin_menu_id = 0)
    {
        return Cache::delete(self::key($admin_menu_id));
    }
}
