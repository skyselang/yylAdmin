<?php
/*
 * @Description  : 内容管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-13
 */

namespace app\common\cache\cms;

use think\facade\Cache;

class ContentCache
{
    /**
     * 缓存键名
     *
     * @param string $content_id 内容id
     * 
     * @return string
     */
    public static function key($content_id = '')
    {
        $key = 'cms:content' . $content_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $content_id 内容id
     * @param mixed   $content    内容信息
     * @param integer $ttl        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($content_id = '', $content, $ttl = 0)
    {
        $key = self::key($content_id);
        $val = $content;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 99);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $content_id 内容id
     * 
     * @return mixed
     */
    public static function get($content_id = '')
    {
        $key = self::key($content_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $content_id 内容id
     * 
     * @return bool
     */
    public static function del($content_id = '')
    {
        $key = self::key($content_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $content_id 内容id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($content_id = '', $step = 1)
    {
        $key = self::key($content_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
