<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\setting;

use think\facade\Cache;

/**
 * 地区管理缓存
 */
class RegionCache
{
    // 缓存标签
    protected static $tag = 'region';
    // 缓存前缀
    protected static $prefix = 'region:';

    /**
     * 缓存键名
     *
     * @param mixed $id 地区id
     * 
     * @return string
     */
    public static function key($id)
    {
        return self::$prefix . $id;
    }

    /**
     * 缓存设置
     *
     * @param mixed $id   地区id
     * @param array $info 地区信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool 
     */
    public static function set($id, $info, $ttl = 86400)
    {
        return Cache::tag(self::$tag)->set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param mixed $id 地区id
     * 
     * @return array 地区信息
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $id 地区id
     * 
     * @return bool
     */
    public static function del($id = '')
    {
        $ids = var_to_array($id);
        foreach ($ids as $v) {
            $res = Cache::delete(self::key($v));
        }
        return $res;
    }

    /**
     * 缓存清除
     * 
     * @return bool
     */
    public static function clear()
    {
        return Cache::tag(self::$tag)->clear();
    }
}
