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
     * @param int $group_id 文件分组id
     * 
     * @return string
     */
    public static function key($group_id)
    {
        return 'file_group:' . $group_id;
    }

    /**
     * 缓存设置
     *
     * @param int   $group_id   文件分组id
     * @param array $file_group 文件分组信息
     * @param int   $ttl        有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($group_id, $file_group, $ttl = 86400)
    {
        return Cache::set(self::key($group_id), $file_group, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param int $group_id 文件分组id
     * 
     * @return array 文件分组信息
     */
    public static function get($group_id)
    {
        return Cache::get(self::key($group_id));
    }

    /**
     * 缓存删除
     *
     * @param int $group_id 文件分组id
     * 
     * @return bool
     */
    public static function del($group_id)
    {
        return Cache::delete(self::key($group_id));
    }
}
