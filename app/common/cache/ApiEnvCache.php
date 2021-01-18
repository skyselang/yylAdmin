<?php
/*
 * @Description  : 接口环境缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-01-14
 * @LastEditTime : 2021-01-15
 */

namespace app\common\cache;

use think\facade\Cache;

class ApiEnvCache
{
    /**
     * 缓存key
     *
     * @param integer $api_env_id 接口环境id
     * 
     * @return string
     */
    public static function key($api_env_id = 0)
    {
        $key = 'apiEnv:' . $api_env_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $api_env_id 接口环境id
     * @param array   $api_env    接口环境信息
     * @param integer $expire     有效时间（秒）
     * 
     * @return bool
     */
    public static function set($api_env_id = 0, $api_env = [], $expire = 0)
    {
        $key = self::key($api_env_id);
        $val = $api_env;
        $ttl = 1 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $api_env_id 接口环境id
     * 
     * @return array
     */
    public static function get($api_env_id = 0)
    {
        $key = self::key($api_env_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $api_env_id 接口环境id
     * 
     * @return bool
     */
    public static function del($api_env_id = 0)
    {
        $key = self::key($api_env_id);
        $res = Cache::delete($key);

        return $res;
    }
}
