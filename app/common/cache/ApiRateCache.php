<?php
/*
 * @Description  : 接口速率缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-06-12
 * @LastEditTime : 2021-05-26
 */

namespace app\common\cache;

use think\facade\Cache;

class ApiRateCache
{
    /**
     * 缓存key
     *
     * @param integer $member_id 会员id
     * @param string  $api_url   接口url
     * 
     * @return string
     */
    public static function key($member_id, $api_url)
    {
        $key = 'ApiRate:' . $member_id . ':' . $api_url;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $member_id 会员id
     * @param string  $api_url   接口url
     * @param integer $ttl       有效时间（秒）
     * 
     * @return bool
     */
    public static function set($member_id, $api_url, $ttl = 10)
    {
        $key = self::key($member_id, $api_url);
        $val = 1;

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $member_id 会员id
     * @param string  $api_url   接口url
     * 
     * @return string
     */
    public static function get($member_id, $api_url)
    {
        $key = self::key($member_id, $api_url);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $member_id 会员id
     * @param string  $api_url   接口url
     * 
     * @return bool
     */
    public static function del($member_id, $api_url)
    {
        $key = self::key($member_id, $api_url);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param integer $member_id 会员id
     * @param string  $api_url   接口url
     * 
     * @return bool
     */
    public static function inc($member_id, $api_url)
    {
        $key = self::key($member_id, $api_url);
        $res = Cache::inc($key);

        return $res;
    }
}
