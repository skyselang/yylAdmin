<?php
/*
 * @Description  : 微信设置缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-05-06
 */

namespace app\common\cache;

use think\facade\Cache;

class WechatSettingCache
{
    /**
     * 缓存key
     *
     * @param integer $wechat_setting_id 微信设置id
     * 
     * @return string
     */
    public static function key($wechat_setting_id = 0)
    {
        $key = 'WechatSetting:' . $wechat_setting_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $wechat_setting_id 微信设置id
     * @param array   $wechat_setting    微信设置信息
     * @param integer $ttl               有效时间（秒）
     * 
     * @return bool
     */
    public static function set($wechat_setting_id = 0, $wechat_setting = [], $ttl = 0)
    {
        $key = self::key($wechat_setting_id);
        $val = $wechat_setting;
        if (empty($ttl)) {
            $ttl = 7 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $wechat_setting_id 微信设置id
     * 
     * @return array 微信设置信息
     */
    public static function get($wechat_setting_id = 0)
    {
        $key = self::key($wechat_setting_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $wechat_setting_id 微信设置id
     * 
     * @return bool
     */
    public static function del($wechat_setting_id = 0)
    {
        $key = self::key($wechat_setting_id);
        $res = Cache::delete($key);

        return $res;
    }
}
