<?php
/*
 * @Description  : 轮播管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-07-02
 */

namespace app\common\cache;

use think\facade\Cache;

class CarouselCache
{
    /**
     * 缓存键名
     *
     * @param string $carousel_id 轮播id
     * 
     * @return string
     */
    public static function key($carousel_id = '')
    {
        $key = 'Carousel:' . $carousel_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $carousel_id 轮播id
     * @param mixed   $carousel    轮播信息
     * @param integer $ttl         有效时间（秒）
     * 
     * @return bool
     */
    public static function set($carousel_id = '', $carousel, $ttl = 0)
    {
        $key = self::key($carousel_id);
        $val = $carousel;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $carousel_id 轮播id
     * 
     * @return mixed
     */
    public static function get($carousel_id = '')
    {
        $key = self::key($carousel_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $carousel_id 轮播id
     * 
     * @return bool
     */
    public static function del($carousel_id = '')
    {
        $key = self::key($carousel_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $carousel_id 轮播id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($carousel_id = '', $step = 1)
    {
        $key = self::key($carousel_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
