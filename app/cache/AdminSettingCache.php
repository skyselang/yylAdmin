<?php
/*
 * @Description  : 设置缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-09-07
 * @LastEditTime : 2020-09-09
 */

namespace app\cache;

use think\facade\Cache;

class AdminSettingCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_setting_id 设置id
     * 
     * @return integer
     */
    public static function key($admin_setting_id = 0)
    {
        $key = 'adminSetting:' . $admin_setting_id;

        return $key;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $expire 有效时间（秒）
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
     * @param integer $admin_setting_id 设置id
     * @param array   $admin_setting    设置信息
     * @param integer $expire           有效时间（秒）
     * 
     * @return array 设置信息
     */
    public static function set($admin_setting_id = 0, $admin_setting = [], $expire = 0)
    {
        $key = self::key($admin_setting_id);
        $val = $admin_setting;
        $exp = $expire ?: self::exp();

        Cache::set($key, $val, $exp);

        return $val;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_setting_id 设置id
     * 
     * @return array 设置信息
     */
    public static function get($admin_setting_id = 0)
    {
        $key = self::key($admin_setting_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_setting_id 设置id
     * 
     * @return bool
     */
    public static function del($admin_setting_id = 0)
    {
        $key = self::key($admin_setting_id);
        $res = Cache::delete($key);

        return $res;
    }
}
