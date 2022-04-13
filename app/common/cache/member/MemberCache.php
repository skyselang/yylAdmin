<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理缓存
namespace app\common\cache\member;

use think\facade\Cache;
use app\common\service\member\MemberService;

class MemberCache
{
    /**
     * 缓存key
     *
     * @param mixed $member_id 会员id、统计时间
     * 
     * @return string
     */
    public static function key($member_id)
    {
        return 'member:' . $member_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $member_id 会员id、统计时间
     * @param array $member    会员信息
     * @param int   $ttl       有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($member_id, $member, $ttl = 86400)
    {
        return Cache::set(self::key($member_id), $member, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $member_id 会员id、统计时间
     * 
     * @return array 会员信息
     */
    public static function get($member_id)
    {
        return Cache::get(self::key($member_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $member_id 会员id、统计时间
     * 
     * @return bool
     */
    public static function del($member_id = '')
    {
        if (is_array($member_id)) {
            $keys = $member_id;
        } else {
            $keys = [$member_id];
        }

        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }

        return $res;
    }

    /**
     * 缓存更新
     *
     * @param int $member_id 会员id
     * 
     * @return array 会员信息
     */
    public static function upd($member_id)
    {
        self::del($member_id);

        return MemberService::info($member_id);
    }
}
