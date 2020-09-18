<?php
/*
 * @Description  : 开发文档
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-18
 * @LastEditTime : 2020-09-18
 */

namespace app\cache;

use think\facade\Cache;

class AdminDevdocCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return string
     */
    public static function key($admin_devdoc_id = 0)
    {
        $key = 'adminDevdoc:' . $admin_devdoc_id;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间
     * 
     * @return integer
     */
    public static function exp($expire = 0)
    {
        if (empty($expire)) {
            $expire = 30 * 24 * 60 * 60;
        }

        return $expire;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_devdoc_id 文档id
     * @param array   $admin_devdoc    文档信息
     * @param integer $expire          有效时间（秒）
     * 
     * @return array 文档信息
     */
    public static function set($admin_devdoc_id = 0, $admin_devdoc = [], $expire = 0)
    {
        $key = self::key($admin_devdoc_id);
        $val = $admin_devdoc;
        $exp = $expire ?: self::exp();
        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return array 文档信息
     */
    public static function get($admin_devdoc_id = 0)
    {
        $key = self::key($admin_devdoc_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_devdoc_id 文档id
     * 
     * @return bool
     */
    public static function del($admin_devdoc_id = 0)
    {
        $key = self::key($admin_devdoc_id);
        $res = Cache::delete($key);

        return $res;
    }
}
