<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 微信设置缓存
namespace app\common\cache;

use think\facade\Cache;

class SettingWechatCache
{
    /**
     * 缓存key
     *
     * @param integer $setting_wechat_id 微信设置id
     * 
     * @return string
     */
    public static function key($setting_wechat_id = 0)
    {
        $key = 'setting_wechat:' . $setting_wechat_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $setting_wechat_id 微信设置id
     * @param array   $setting_wechat    微信设置信息
     * @param integer $ttl               有效时间（秒）
     * 
     * @return bool
     */
    public static function set($setting_wechat_id = 0, $setting_wechat = [], $ttl = 0)
    {
        $key = self::key($setting_wechat_id);
        $val = $setting_wechat;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $setting_wechat_id 微信设置id
     * 
     * @return array 微信设置信息
     */
    public static function get($setting_wechat_id = 0)
    {
        $key = self::key($setting_wechat_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $setting_wechat_id 微信设置id
     * 
     * @return bool
     */
    public static function del($setting_wechat_id = 0)
    {
        $key = self::key($setting_wechat_id);
        $res = Cache::delete($key);

        return $res;
    }
}
