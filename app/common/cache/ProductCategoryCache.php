<?php
/*
 * @Description  : 产品分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-17
 */

namespace app\common\cache;

use think\facade\Cache;

class ProductCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $product_category_id 产品分类id
     * 
     * @return string
     */
    public static function key($product_category_id = '')
    {
        $key = 'ProductCategory:' . $product_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $product_category_id 产品分类id
     * @param mixed   $product_category    产品分类信息
     * @param integer $ttl                 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($product_category_id = '', $product_category = [], $ttl = 0)
    {
        $key = self::key($product_category_id);
        $val = $product_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $product_category_id 产品分类id
     * 
     * @return mixed
     */
    public static function get($product_category_id = '')
    {
        $key = self::key($product_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $product_category_id 产品分类id
     * 
     * @return bool
     */
    public static function del($product_category_id = '')
    {
        $key = self::key($product_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
