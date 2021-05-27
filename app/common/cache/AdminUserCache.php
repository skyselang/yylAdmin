<?php
/*
 * @Description  : 用户管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2021-05-06
 */

namespace app\common\cache;

use think\facade\Cache;
use app\common\service\AdminUserService;
use app\common\service\AdminSettingService;

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
        $key = 'AdminUser:' . $admin_user_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @param array   $admin_user    用户信息
     * @param integer $ttl           有效时间（秒）
     * 
     * @return bool
     */
    public static function set($admin_user_id, $admin_user, $ttl = 0)
    {
        $key = self::key($admin_user_id);
        $val = $admin_user;
        if (empty($ttl)) {
            $setting = AdminSettingService::tokenInfo();
            $ttl     = $setting['token_exp'] * 3600;
        }

        $res = Cache::set($key, $val, $ttl);

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
