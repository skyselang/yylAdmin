<?php
/*
 * @Description  : 管理员缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2021-03-25
 */

namespace app\common\cache;

use app\admin\service\AdminAdminService;
use think\facade\Cache;

class AdminAdminCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return string
     */
    public static function key($admin_admin_id)
    {
        $key = 'AdminAdmin:' . $admin_admin_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_admin_id 管理员id
     * @param array   $admin_admin    管理员信息
     * @param integer $expire         有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_admin_id, $admin_admin, $expire = 0)
    {
        $key = self::key($admin_admin_id);
        $val = $admin_admin;
        $ttl = 7 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return array 管理员信息
     */
    public static function get($admin_admin_id)
    {
        $key = self::key($admin_admin_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return bool
     */
    public static function del($admin_admin_id)
    {
        $key = self::key($admin_admin_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存更新
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return bool
     */
    public static function upd($admin_admin_id)
    {
        $old = AdminAdminService::info($admin_admin_id);

        self::del($admin_admin_id);

        $new = AdminAdminService::info($admin_admin_id);

        unset($new['admin_token']);

        $user = array_merge($old, $new);

        $res = self::set($admin_admin_id, $user);

        return $res;
    }
}
