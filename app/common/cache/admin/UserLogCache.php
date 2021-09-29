<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 日志管理缓存
namespace app\common\cache\admin;

use think\facade\Cache;

class UserLogCache
{
    /**
     * 缓存key
     *
     * @param int|string $admin_user_log_id 日志id、统计时间
     * 
     * @return string
     */
    public static function key($admin_user_log_id)
    {
        $key = 'admin_user_log:' . $admin_user_log_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param int|string $admin_user_log_id 日志id、统计时间
     * @param array      $admin_user_log    日志信息
     * @param int|null   $ttl               有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($admin_user_log_id, $admin_user_log, $ttl = null)
    {
        $key = self::key($admin_user_log_id);
        $val = $admin_user_log;
        if ($ttl === null) {
            $ttl = 0.5 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param int|string $admin_user_log_id 日志id、统计时间
     * 
     * @return array 日志信息
     */
    public static function get($admin_user_log_id)
    {
        $key = self::key($admin_user_log_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int|string $admin_user_log_id 日志id、统计时间
     * 
     * @return bool
     */
    public static function del($admin_user_log_id)
    {
        $key = self::key($admin_user_log_id);
        $res = Cache::delete($key);

        return $res;
    }
}
