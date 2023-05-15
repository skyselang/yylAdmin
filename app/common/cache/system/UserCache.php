<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\system;

use think\facade\Cache;
use app\common\service\system\UserService;

/**
 * 用户管理缓存
 */
class UserCache
{
    // 缓存标签
    public static $tag = 'system_user';
    // 缓存前缀
    protected static $prefix = 'system_user:';
    // token标签
    protected static $tag_token = 'system_user_token';
    // token前缀
    protected static $prefix_token = 'system_user_token:';

    /**
     * 缓存键名
     *
     * @param mixed $id 用户id
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
     * @param mixed $id   用户id
     * @param array $info 用户信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 43200)
    {
        return Cache::tag(self::$tag)->set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $id 用户id
     * 
     * @return array 用户信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $id 用户id
     * 
     * @return bool
     */
    public static function del($id)
    {
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            Cache::delete(self::key($v));
        }
        return true;
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

    /**
     * 缓存更新
     *
     * @param mixed $id 用户id
     * 
     * @return bool
     */
    public static function upd($id)
    {
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            $old = self::get($v);
            if ($old) {
                self::del($v);
                $new = UserService::info($v, false);
                if ($new) {
                    self::set($v, $new);
                }
            }
        }
    }

    /**
     * token键名
     *
     * @param mixed $id 用户id
     * 
     * @return string
     */
    public static function keyToken($id)
    {
        return self::$prefix_token . $id;
    }

    /**
     * Token设置
     *
     * @param int   $id   用户id
     * @param array $info 用户token
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function setToken($id, $info, $ttl = 43200)
    {
        return Cache::tag(self::$tag_token)->set(self::keyToken($id), $info, $ttl);
    }

    /**
     * Token获取
     *
     * @param int $id 用户id
     * 
     * @return string 用户token
     */
    public static function getToken($id)
    {
        return Cache::get(self::keyToken($id));
    }

    /**
     * token删除
     *
     * @param mixed $id 用户id
     * 
     * @return bool
     */
    public static function delToken($id)
    {
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            Cache::delete(self::keyToken($v));
        }
        return true;
    }

    /**
     * token清除
     * 
     * @return bool
     */
    public static function clearToken()
    {
        return Cache::tag(self::$tag_token)->clear();
    }
}
