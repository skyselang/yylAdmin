<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$cache.namespace};

use think\facade\Cache;

/**
 * {$form.controller_title}缓存
 */
class {$cache.class_name}
{
    // 缓存标签
    protected static $tag = '{$tables[0].table_name}';
    // 缓存前缀
    protected static $prefix = '{$tables[0].table_name}:';

    /**
     * 缓存键名
     *
     * @param mixed $id {$form.controller_title}id
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
     * @param mixed $id   {$form.controller_title}id
     * @param array $info {$form.controller_title}信息
     * @param int   $ttl  有效时间（秒）0永久
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
     * @param mixed $id {$form.controller_title}id
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
     * @param mixed $id {$form.controller_title}id
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
}
