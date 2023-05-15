<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\cache;

use think\facade\Cache as TpCache;

/**
 * 缓存通用类
 */
class Cache extends TpCache
{
    // 缓存标签
    public static $tag = 'cache';
    // 缓存前缀
    protected static $prefix = 'cache:';

    /**
     * 缓存键名
     * @param  int $name 缓存变量名
     * @return string
     */
    public static function key($name)
    {
        return self::$prefix . $name;
    }

    /**
     * 写入缓存
     * @access public
     * @param  string            $name   缓存变量名
     * @param  mixed             $value  存储数据
     * @param  integer|\DateTime $expire 有效时间（秒）
     * @return bool
     */
    public static function set($name, $value, $expire = null)
    {
        return TpCache::tag(self::$tag)->set(self::key($name), $value, $expire);
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name    缓存变量名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        return TpCache::get(self::key($name), $default);
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public static function del($name)
    {
        $keys = var_to_array($name);
        foreach ($keys as $key) {
            TpCache::delete(self::key($key));
        }
        return true;
    }
    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public static function delete($name)
    {
        return self::del(self::key($name));
    }

    /**
     * 清除缓存
     * @access public
     * @return bool
     */
    public static function clear()
    {
        return TpCache::tag(self::$tag)->clear();
    }
}
