<?php
/*
 * @Description  : 产品管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-19
 */

namespace app\common\cache;

use think\facade\Cache;

class ProductCache
{
    /**
     * 缓存键名
     *
     * @param string $product_id 产品id
     * 
     * @return string
     */
    public static function key($product_id = '')
    {
        $key = 'Product:' . $product_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $product_id 产品id
     * @param mixed   $product    产品信息
     * @param integer $ttl        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($product_id = '', $product, $ttl = 0)
    {
        $key = self::key($product_id);
        $val = $product;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $product_id 产品id
     * 
     * @return mixed
     */
    public static function get($product_id = '')
    {
        $key = self::key($product_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $product_id 产品id
     * 
     * @return bool
     */
    public static function del($product_id = '')
    {
        $key = self::key($product_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $product_id 产品id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($product_id = '', $step = 1)
    {
        $key = self::key($product_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
