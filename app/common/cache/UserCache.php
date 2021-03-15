<?php
/*
 * @Description  : 用户缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-03-08
 */

namespace app\common\cache;

use think\facade\Cache;
use app\admin\service\UserService;

class UserCache
{
    /**
     * 缓存key
     *
     * @param integer $user_id 用户id
     * 
     * @return string
     */
    public static function key($user_id)
    {
        $key = 'user:' . $user_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $user_id 用户id
     * @param array   $user    用户信息
     * @param integer $expire  有效时间（秒）
     * 
     * @return bool
     */
    public static function set($user_id, $user, $expire = 0)
    {
        $key = self::key($user_id);
        $val = $user;
        $ttl = 7 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $user_id 用户id
     * 
     * @return array 用户信息
     */
    public static function get($user_id)
    {
        $key = self::key($user_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $user_id 用户id
     * 
     * @return bool
     */
    public static function del($user_id)
    {
        $key = self::key($user_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存更新
     *
     * @param integer $user_id 用户id
     * 
     * @return array 用户信息
     */
    public static function upd($user_id)
    {
        $old = UserService::info($user_id);

        self::del($user_id);

        $new = UserService::info($user_id);

        unset($new['user_token']);

        $user = array_merge($old, $new);

        self::set($user_id, $user);

        return $user;
    }
}
