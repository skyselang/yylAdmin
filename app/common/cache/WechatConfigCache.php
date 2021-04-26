<?php
/*
 * @Description  : 微信配置缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-04-23
 */

namespace app\common\cache;

use think\facade\Cache;

class WechatConfigCache
{
    /**
     * 缓存key
     *
     * @param integer $wechat_config_id 微信配置id
     * 
     * @return string
     */
    public static function key($wechat_config_id = 0)
    {
        $key = 'WechatConfig:' . $wechat_config_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $wechat_config_id 微信配置id
     * @param array   $wechat_config    微信配置信息
     * @param integer $ttl              有效时间（秒）
     * 
     * @return bool
     */
    public static function set($wechat_config_id = 0, $wechat_config = [], $ttl = 0)
    {
        $key = self::key($wechat_config_id);
        $val = $wechat_config;
        if (empty($ttl)) {
            $ttl = 7 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $wechat_config_id 微信配置id
     * 
     * @return array 微信配置信息
     */
    public static function get($wechat_config_id = 0)
    {
        $key = self::key($wechat_config_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $wechat_config_id 微信配置id
     * 
     * @return bool
     */
    public static function del($wechat_config_id = 0)
    {
        $key = self::key($wechat_config_id);
        $res = Cache::delete($key);

        return $res;
    }
}
