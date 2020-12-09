<?php
/*
 * @Description  : 会员缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-12-07
 */

namespace app\common\cache;

use think\facade\Cache;
use app\admin\service\MemberService;

class MemberCache
{
    /**
     * 缓存key
     *
     * @param integer $member_id 会员id
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
     * @param integer $member_id 会员id
     * @param array   $member    会员信息
     * @param integer $expire    有效时间（秒）
     * 
     * @return bool
     */
    public static function set($member_id, $member, $expire = 0)
    {
        $key = self::key($member_id);
        $val = $member;
        $ttl = 7 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $member_id 会员id
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
     * @param integer $member_id 会员id
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
     * @param integer $member_id 会员id
     * 
     * @return bool
     */
    public static function upd($member_id)
    {
        $old = MemberService::info($member_id);

        self::del($member_id);

        $new = MemberService::info($member_id);

        unset($new['token']);

        $member = array_merge($old, $new);

        self::set($member_id, $member);

        return $member;
    }
}
