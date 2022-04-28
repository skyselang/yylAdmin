<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口速率缓存
namespace app\common\cache\setting;

use think\facade\Cache;

class ApiRateCache
{
    // 缓存标签
    protected static $tag = 'apirate';
    // 缓存前缀
    protected static $prefix = 'apirate:';

    /**
     * 缓存键名
     *
     * @param int    $member_id 会员id
     * @param string $api_url   接口url
     * 
     * @return string
     */
    public static function key($member_id, $api_url)
    {
        return self::$prefix . $member_id . ':' . $api_url;
    }

    /**
     * 缓存设置
     *
     * @param int    $member_id 会员id
     * @param string $api_url   接口url
     * @param int    $ttl       有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($member_id, $api_url, $ttl = 60)
    {
        return Cache::tag(self::$tag)->set(self::key($member_id, $api_url), 1, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int    $member_id 会员id
     * @param string $api_url   接口url
     * 
     * @return string
     */
    public static function get($member_id, $api_url)
    {
        return Cache::get(self::key($member_id, $api_url));
    }

    /**
     * 缓存删除
     *
     * @param int    $member_id 会员id
     * @param string $api_url   接口url
     * 
     * @return bool
     */
    public static function del($member_id, $api_url)
    {
        return Cache::delete(self::key($member_id, $api_url));
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
     * 缓存自增
     *
     * @param int    $member_id 会员id
     * @param string $api_url   接口url
     * 
     * @return bool
     */
    public static function inc($member_id, $api_url)
    {
        return Cache::inc(self::key($member_id, $api_url));
    }
}
