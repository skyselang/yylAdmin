<?php
/*
 * @Description  : 会员管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-05-25
 */

namespace app\common\cache;

use think\facade\Cache;
use app\common\service\MemberService;

class MemberCache
{
    /**
     * 缓存key
     *
     * @param integer|string $member_id 会员id、统计时间
     * 
     * @return string
     */
    public static function key($member_id)
    {
        $key = 'Member:' . $member_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $member_id 会员id、统计时间
     * @param array          $user      会员信息
     * @param integer        $ttl       有效时间（秒）
     * 
     * @return bool
     */
    public static function set($member_id, $user, $ttl = 0)
    {
        $key = self::key($member_id);
        $val = $user;


        if (is_numeric($member_id)) {
            if (empty($ttl)) {
                $ttl = 1 * 24 * 60 * 60;
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
     * @param integer|string $member_id 会员id、统计时间
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
     * @param integer|string $member_id 会员id、统计时间
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
     * @return array 会员信息
     */
    public static function upd($member_id)
    {
        self::del($member_id);

        $data = MemberService::info($member_id);

        return $data;
    }
}
