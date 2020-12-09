<?php
/*
 * @Description  : 接口缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-03
 */

namespace app\common\cache;

use think\facade\Cache;

class ApiCache
{
    /**
     * 缓存key
     *
     * @param integer $api_id 接口id
     * 
     * @return string
     */
    public static function key($api_id = 0)
    {
        $key = 'api:' . $api_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $api_id 接口id
     * @param array   $api    接口信息
     * @param integer $expire 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($api_id = 0, $api = [], $expire = 0)
    {
        $key = self::key($api_id);
        $ttl = 15 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $api, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $api_id 接口id
     * 
     * @return array 接口信息
     */
    public static function get($api_id = 0)
    {
        $key = self::key($api_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $api_id 接口id
     * 
     * @return bool
     */
    public static function del($api_id = 0)
    {
        $key = self::key($api_id);
        $res = Cache::delete($key);

        return $res;
    }
}
