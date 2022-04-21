<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件设置缓存
namespace app\common\cache\file;

use think\facade\Cache;

class SettingCache
{
    /**
     * 缓存key
     *
     * @param int $id 文件设置id
     * 
     * @return string
     */
    public static function key($id)
    {
        return 'file_setting:' . $id;
    }

    /**
     * 缓存设置
     *
     * @param int   $id   文件设置id
     * @param array $info 文件设置信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 7 * 86400)
    {
        return Cache::set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $id 文件设置id
     * 
     * @return array 文件设置信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param int $id 文件设置id
     * 
     * @return bool
     */
    public static function del($id)
    {
        return Cache::delete(self::key($id));
    }
}
