<?php
/*
 * @Description  : 接口缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-01-15
 */

namespace app\common\cache;

use think\facade\Cache;

class ApiCache
{
    /**
     * 缓存key
     *
     * @param integer|string $api_id 接口id
     * 
     * @return string
     */
    public static function key($api_id = '')
    {
        if (empty($api_id)) {
            $api_id = 'all';
        }

        $key = 'api:' . $api_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer|string $api_id 接口id
     * @param array          $api    接口信息
     * @param integer        $expire 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($api_id = '', $api = [], $expire = 0)
    {
        $key = self::key($api_id);
        $ttl = 7 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $api, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer|string $api_id 接口id
     * 
     * @return array
     */
    public static function get($api_id = '')
    {
        $key = self::key($api_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer|string $api_id 接口id
     * 
     * @return bool
     */
    public static function del($api_id = '')
    {
        $key = self::key($api_id);
        $res = Cache::delete($key);

        if (empty($api_id)) {
            $key = self::key('whiteList');
            $res = Cache::delete($key);
        }

        return $res;
    }
}
