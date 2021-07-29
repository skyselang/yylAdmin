<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 角色管理缓存
namespace app\common\cache\admin;

use think\facade\Cache;

class RoleCache
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
        $key = 'admin_role:' . $admin_role_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_role_id 角色id
     * @param array   $admin_role    角色信息
     * @param integer $ttl           有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_role_id = 0, $admin_role = [], $ttl = 0)
    {
        $key = self::key($admin_role_id);
        $val = $admin_role;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

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
