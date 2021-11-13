<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置缓存
namespace app\common\cache\cms;

use think\facade\Cache;

class SettingCache
{
    /**
     * 缓存key
     *
     * @param integer $setting_id 设置id
     * 
     * @return string
     */
    public static function key($setting_id)
    {
        return 'cms_setting:' . $setting_id;
    }

    /**
     * 缓存设置
     *
     * @param integer $setting_id 设置id
     * @param array   $setting    设置信息
     * @param integer $ttl        有效时间（秒）0永久
     * 
     * @return boolean
     */
    public static function set($setting_id, $setting, $ttl = 86400)
    {
        return Cache::set(self::key($setting_id), $setting, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param integer $setting_id 设置id
     * 
     * @return array 设置信息
     */
    public static function get($setting_id)
    {
        return Cache::get(self::key($setting_id));
    }

    /**
     * 缓存删除
     *
     * @param integer $setting_id 设置id
     * 
     * @return boolean
     */
    public static function del($setting_id)
    {
        return Cache::delete(self::key($setting_id));
    }
}
