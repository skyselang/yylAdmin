<?php
/*
 * @Description  : 设置缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-03-09
 */

namespace app\common\cache;

use think\facade\Cache;

class SettingCache
{
    /**
     * 缓存key
     *
     * @param integer $setting_id 设置id
     * 
     * @return integer
     */
    public static function key($setting_id = 0)
    {
        $key = 'setting:' . $setting_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $setting_id 设置id
     * @param array   $setting    设置信息
     * @param integer $expire     有效时间（秒）
     * 
     * @return bool
     */
    public static function set($setting_id = 0, $setting = [], $expire = 0)
    {
        $key = self::key($setting_id);
        $val = $setting;
        $ttl = 7 * 24 * 60 * 60;
        $exp = $expire ?: $ttl;

        $res = Cache::set($key, $val, $exp);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $setting_id 设置id
     * 
     * @return array 设置信息
     */
    public static function get($setting_id = 0)
    {
        $key = self::key($setting_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $setting_id 设置id
     * 
     * @return bool
     */
    public static function del($setting_id = 0)
    {
        $key = self::key($setting_id);
        $res = Cache::delete($key);

        return $res;
    }
}
