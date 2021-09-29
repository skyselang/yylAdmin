<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理缓存
namespace app\common\cache;

use think\facade\Cache;
use app\common\service\MemberService;

class MemberCache
{
    /**
     * 缓存key
     *
     * @param int|string $member_id 会员id、统计时间
     * 
     * @return string
     */
    public static function key($member_id)
    {
        $key = 'member:' . $member_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param int|string $member_id 会员id、统计时间
     * @param array      $member    会员信息
     * @param int|null   $ttl       有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($member_id, $member, $ttl = null)
    {
        $key = self::key($member_id);
        $val = $member;
        if (is_numeric($member_id)) {
            if ($ttl === null) {
                $ttl = 1 * 24 * 60 * 60;
            }
        } else {
            if ($ttl === null) {
                $ttl = 1 * 60 * 60;
            }
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param int|string $member_id 会员id、统计时间
     * 
     * @return array 会员信息
     */
    public static function get($member_id)
    {
        $key = self::key($member_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int|string $member_id 会员id、统计时间
     * 
     * @return bool
     */
    public static function del($member_id)
    {
        $key = self::key($member_id);
        $res = Cache::delete($key);

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

        $data = MemberService::info($member_id);

        return $data;
    }
}
