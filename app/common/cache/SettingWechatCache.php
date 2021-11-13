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
    public static function key($setting_wechat_id)
    {
        return 'setting_wechat:' . $setting_wechat_id;
    }

    /**
     * 缓存设置
     *
     * @param integer $setting_wechat_id 微信设置id
     * @param array   $setting_wechat    微信设置信息
     * @param integer $ttl               有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($setting_wechat_id, $setting_wechat, $ttl = 86400)
    {
        return Cache::set(self::key($setting_wechat_id), $setting_wechat, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param integer $setting_wechat_id 微信设置id
     * 
     * @return array 微信设置信息
     */
    public static function get($setting_wechat_id)
    {
        return Cache::get(self::key($setting_wechat_id));
    }

    /**
     * 缓存删除
     *
     * @param integer $setting_wechat_id 微信设置id
     * 
     * @return bool
     */
    public static function del($setting_wechat_id)
    {
        return Cache::delete(self::key($setting_wechat_id));
    }
}
