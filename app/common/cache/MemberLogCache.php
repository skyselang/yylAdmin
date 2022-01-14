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
     * @param mixed $member_log_id 会员日志id、统计时间
     * 
     * @return string
     */
    public static function key($member_log_id)
    {
        return 'member_log:' . $member_log_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $member_log_id 会员日志id、统计时间
     * @param array $member_log    会员日志信息
     * @param int   $ttl           有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($member_log_id, $member_log, $ttl = 3600)
    {
        return Cache::set(self::key($member_log_id), $member_log, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $member_log_id 会员日志id、统计时间
     * 
     * @return array 会员日志信息
     */
    public static function get($member_log_id)
    {
        return Cache::get(self::key($member_log_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $member_log_id 会员日志id、统计时间
     * 
     * @return bool
     */
    public static function del($member_log_id)
    {
        return Cache::delete(self::key($member_log_id));
    }
}
