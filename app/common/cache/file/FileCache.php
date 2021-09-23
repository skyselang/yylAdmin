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
     * @param int|string $file_id 文件id
     * 
     * @return string
     */
    public static function key($file_id = '')
    {
        $key = 'file:' . $file_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param int|string $file_id 文件id
     * @param array      $file    文件信息
     * @param int        $ttl     有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($file_id = '', $file = [], $ttl = '')
    {
        $key = self::key($file_id);
        $val = $file;
        if ($ttl == '') {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param int|string $file_id 文件id
     * 
     * @return array
     */
    public static function get($file_id = '')
    {
        $key = self::key($file_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int|string $file_id 文件id
     * 
     * @return bool
     */
    public static function del($file_id = '')
    {
        $key = self::key($file_id);
        $res = Cache::delete($key);

        return $res;
    }
}
