<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache\cms;

use think\facade\Cache;

/**
 * 内容管理缓存
 */
class ContentCache
{
    // 缓存标签
    protected static $tag = 'cms_content';
    // 缓存前缀
    protected static $prefix = 'cms_content:';

    /**
     * 缓存键名
     *
     * @param mixed $id 内容id
     * 
     * @return string
     */
    public static function key($id)
    {
        return self::$prefix . $id;
    }

    /**
     * 缓存写入
     *
     * @param mixed $id   内容id
     * @param mixed $info 内容信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 86400)
    {
        return Cache::tag(self::$tag)->set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存读取
     *
     * @param mixed $id 内容id
     * 
     * @return mixed
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $id 内容id
     * 
     * @return bool
     */
    public static function del($id)
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

    /**
     * 缓存自增
     *
     * @param string $key  内容key
     * @param int    $step 步长
     *
     * @return bool
     */
    public static function inc($key, $step = 1)
    {
        return Cache::inc(self::key($key), $step);
    }
}
