<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理缓存
namespace app\common\cache;

use think\facade\Cache;

class ApiCache
{
    /**
     * 缓存key
     *
     * @param int|string $api_id 接口id
     * 
     * @return string
     */
    public static function key($api_id)
    {
        $key = 'api:' . $api_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param int|string $api_id 接口id
     * @param array      $api    接口信息
     * @param int|null   $ttl    有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($api_id = '', $api = [], $ttl = null)
    {
        $key = self::key($api_id);
        $val = $api;
        if ($ttl === null) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param int|string $api_id 接口id
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
     * @param int|string $api_id 接口id
     * 
     * @return bool
     */
    public static function del($api_id = '')
    {
        if (empty($api_id)) {
            $res = Cache::delete(self::key('tree'));
            $res = Cache::delete(self::key('list'));
            $res = Cache::delete(self::key('urlList'));
            $res = Cache::delete(self::key('unloginList'));
        } else {
            $key = self::key($api_id);
            $res = Cache::delete($key);
        }

        return $res;
    }
}
