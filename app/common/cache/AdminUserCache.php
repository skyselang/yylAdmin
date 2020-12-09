<?php
/*
 * @Description  : 用户缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2020-12-03
 */

namespace app\common\cache;

use app\admin\service\AdminUserService;
use think\facade\Cache;

class AdminUserCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return string
     */
    public static function key($admin_user_id)
    {
        $key = 'adminUser:' . $admin_user_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @param array   $admin_user    用户信息
     * @param integer $expire        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_user_id, $admin_user, $expire = 0)
    {
        $key = self::key($admin_user_id);
        $val = $admin_user;
        $ttl = 1 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array 用户信息
     */
    public static function get($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return bool
     */
    public static function del($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存更新
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return bool
     */
    public static function upd($admin_user_id)
    {
        $old = AdminUserService::info($admin_user_id);

        self::del($admin_user_id);

        $new = AdminUserService::info($admin_user_id);

        unset($new['admin_token']);

        $user = array_merge($old, $new);

        $res = self::set($admin_user_id, $user);

        return $res;
    }
}
