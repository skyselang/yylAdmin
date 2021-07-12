<?php
/*
 * @Description  : 内容管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-07
 */

namespace app\common\cache;

use think\facade\Cache;

class CmsCache
{
    /**
     * 缓存键名
     *
     * @param string $cms_id 内容id
     * 
     * @return string
     */
    public static function key($cms_id = '')
    {
        $key = 'Cms:' . $cms_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $cms_id 内容id
     * @param mixed   $cms    内容信息
     * @param integer $ttl    有效时间（秒）
     * 
     * @return bool
     */
    public static function set($cms_id = '', $cms, $ttl = 0)
    {
        $key = self::key($cms_id);
        $val = $cms;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $cms_id 内容id
     * 
     * @return mixed
     */
    public static function get($cms_id = '')
    {
        $key = self::key($cms_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $cms_id 内容id
     * 
     * @return bool
     */
    public static function del($cms_id = '')
    {
        $key = self::key($cms_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $cms_id 内容id
     * @param integer $step   步长
     *
     * @return bool
     */
    public static function inc($cms_id = '', $step = 1)
    {
        $key = self::key($cms_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
