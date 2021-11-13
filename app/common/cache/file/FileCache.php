<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件管理缓存
namespace app\common\cache\file;

use think\facade\Cache;

class FileCache
{
    /**
     * 缓存key
     *
     * @param mixed $file_id 文件id、文件统计key
     * 
     * @return string
     */
    public static function key($file_id)
    {
        return 'file:' . $file_id;
    }

    /**
     * 缓存设置
     *
     * @param mixed   $file_id 文件id、文件统计key
     * @param array   $file    文件信息
     * @param integer $ttl     有效时间（秒）0永久
     * 
     * @return boolean
     */
    public static function set($file_id, $file, $ttl = 86400)
    {
        return Cache::set(self::key($file_id), $file, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $file_id 文件id、文件统计key
     * 
     * @return array
     */
    public static function get($file_id)
    {
        return Cache::get(self::key($file_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $file_id 文件id、文件统计key
     * 
     * @return boolean
     */
    public static function del($file_id)
    {
        return Cache::delete(self::key($file_id));
    }
}
