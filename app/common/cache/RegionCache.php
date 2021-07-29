<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 地区管理缓存
namespace app\common\cache;

use think\facade\Cache;

class RegionCache
{
    /**
     * 缓存key
     *
     * @param integer|string $region_id 地区id
     * 
     * @return string
     */
    public static function key($region_id = 0)
    {
        $key = 'region:' . $region_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $region_id 地区id
     * @param array          $region    地区信息
     * @param integer        $ttl       有效时间（秒）
     * 
     * @return array 地区信息
     */
    public static function set($region_id = 0, $region = [], $ttl = 0)
    {
        $key = self::key($region_id);
        $val = $region;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $region_id 地区id
     * 
     * @return array 地区信息
     */
    public static function get($region_id = 0)
    {
        $key = self::key($region_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $region_id 地区id
     * 
     * @return bool
     */
    public static function del($region_id = 0)
    {
        $key = self::key($region_id);
        $res = Cache::delete($key);

        return $res;
    }
}
