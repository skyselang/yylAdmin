<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员日志缓存
namespace app\common\cache;

use think\facade\Cache;

class MemberLogCache
{
    /**
     * 缓存key
     *
     * @param integer|string $member_log_id 会员日志id、统计时间
     * 
     * @return string
     */
    public static function key($member_log_id = 0)
    {
        $key = 'member_log:' . $member_log_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $member_log_id 会员日志id、统计时间
     * @param array          $member_log    会员日志信息
     * @param integer        $ttl           有效时间（秒）
     * 
     * @return bool
     */
    public static function set($member_log_id = 0, $member_log = [], $ttl = 0)
    {
        $key = self::key($member_log_id);
        $val = $member_log;
        if (is_numeric($member_log_id)) {
            if (empty($ttl)) {
                $ttl = 1 * 60 * 60;
            }
        } else {
            if (empty($ttl)) {
                $ttl = 1 * 60 * 60;
            }
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $member_log_id 会员日志id、统计时间
     * 
     * @return array 会员日志信息
     */
    public static function get($member_log_id = 0)
    {
        $key = self::key($member_log_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $member_log_id 会员日志id、统计时间
     * 
     * @return bool
     */
    public static function del($member_log_id = 0)
    {
        $key = self::key($member_log_id);
        $res = Cache::delete($key);

        return $res;
    }
}
