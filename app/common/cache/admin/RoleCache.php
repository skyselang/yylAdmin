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
    // 缓存标签
    protected static $tag = 'admin_role';
    // 缓存前缀
    protected static $prefix = 'admin_role:';

    /**
     * 缓存键名
     *
     * @param int $id 角色id
     * 
     * @return string
     */
    public static function key($id)
    {
        return self::$prefix . $id;
    }

    /**
     * 缓存设置
     *
     * @param int   $id   角色id
     * @param array $info 角色信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 86400)
    {
        return Cache::tag(self::$tag)->set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $id 角色id
     * 
     * @return array 角色信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param int $id 角色id
     * 
     * @return bool
     */
    public static function del($id)
    {
        return Cache::delete(self::key($id));
    }

    /**
     * 缓存清除
     * 
     * @return bool
     */
    public static function clear()
    {
        return Cache::tag(self::$tag)->clear();
    }
}
