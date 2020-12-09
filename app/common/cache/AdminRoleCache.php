<?php
/*
 * @Description  : 角色缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-24
 * @LastEditTime : 2020-12-03
 */

namespace app\common\cache;

use think\facade\Cache;

class AdminRoleCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_role_id 角色id
     * 
     * @return string
     */
    public static function key($admin_role_id = 0)
    {
        $key = 'adminRole:' . $admin_role_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_role_id 角色id
     * @param array   $admin_role    角色信息
     * @param integer $expire        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_role_id = 0, $admin_role = [], $expire = 0)
    {
        $key = self::key($admin_role_id);
        $val = $admin_role;
        $ttl = 1 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_role_id 角色id
     * 
     * @return array 角色信息
     */
    public static function get($admin_role_id = 0)
    {
        $key = self::key($admin_role_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_role_id 角色id
     * 
     * @return bool
     */
    public static function del($admin_role_id = 0)
    {
        $key = self::key($admin_role_id);
        $res = Cache::delete($key);

        return $res;
    }
}
