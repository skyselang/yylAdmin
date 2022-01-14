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
     * @param int $admin_role_id 角色id
     * 
     * @return string
     */
    public static function key($admin_role_id)
    {
        return 'admin_role:' . $admin_role_id;
    }

    /**
     * 缓存设置
     *
     * @param int   $admin_role_id 角色id
     * @param array $admin_role    角色信息
     * @param int   $ttl           有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($admin_role_id, $admin_role, $ttl = 86400)
    {
        return Cache::set(self::key($admin_role_id), $admin_role, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $admin_role_id 角色id
     * 
     * @return array 角色信息
     */
    public static function get($admin_role_id)
    {
        return Cache::get(self::key($admin_role_id));
    }

    /**
     * 缓存删除
     *
     * @param int $admin_role_id 角色id
     * 
     * @return bool
     */
    public static function del($admin_role_id)
    {
        return Cache::delete(self::key($admin_role_id));
    }
}
