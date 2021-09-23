<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件分组缓存
namespace app\common\cache\file;

use think\facade\Cache;

class GroupCache
{
    /**
     * 缓存key
     *
     * @param integer $group_id 文件分组id
     * 
     * @return string
     */
    public static function key($group_id = 0)
    {
        $key = 'file_group:' . $group_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param integer $group_id   文件分组id
     * @param array   $file_group 文件分组信息
     * @param integer $ttl        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($group_id = 0, $file_group = [], $ttl = '')
    {
        $key = self::key($group_id);
        $val = $file_group;
        if ($ttl == '') {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param integer $group_id 文件分组id
     * 
     * @return array 文件分组信息
     */
    public static function get($group_id = 0)
    {
        $key = self::key($group_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param integer $group_id 文件分组id
     * 
     * @return bool
     */
    public static function del($group_id = 0)
    {
        $key = self::key($group_id);
        $res = Cache::delete($key);

        return $res;
    }
}
