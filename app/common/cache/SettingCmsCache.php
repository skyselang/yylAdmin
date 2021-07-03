<?php
/*
 * @Description  : 内容设置缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-03
 */

namespace app\common\cache;

use think\facade\Cache;

class SettingCmsCache
{
    /**
     * 缓存key
     *
     * @param integer $setting_cms_id 设置id
     * 
     * @return string
     */
    public static function key($setting_cms_id = 0)
    {
        $key = 'SettingCms:' . $setting_cms_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $setting_cms_id 设置id
     * @param array   $setting_cms    设置信息
     * @param integer $ttl            有效时间（秒）
     * 
     * @return bool
     */
    public static function set($setting_cms_id = 0, $setting_cms = [], $ttl = 0)
    {
        $key = self::key($setting_cms_id);
        $val = $setting_cms;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $setting_cms_id 设置id
     * 
     * @return array 设置信息
     */
    public static function get($setting_cms_id = 0)
    {
        $key = self::key($setting_cms_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $setting_cms_id 设置id
     * 
     * @return bool
     */
    public static function del($setting_cms_id = 0)
    {
        $key = self::key($setting_cms_id);
        $res = Cache::delete($key);

        return $res;
    }
}
