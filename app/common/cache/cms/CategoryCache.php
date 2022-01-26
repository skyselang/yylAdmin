<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容分类缓存
namespace app\common\cache\cms;

use think\facade\Cache;

class CategoryCache
{
    /**
     * 缓存键名
     *
     * @param mixed $category_id 内容分类id、key
     * 
     * @return string
     */
    public static function key($category_id)
    {
        return 'cms_category:' . $category_id;
    }

    /**
     * 缓存写入
     *
     * @param mixed $category_id 内容分类id、key
     * @param array $category    内容分类信息
     * @param int   $ttl         有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($category_id, $category, $ttl = 86400)
    {
        return Cache::set(self::key($category_id), $category, $ttl);
    }

    /**
     * 缓存读取
     *
     * @param mixed $category_id 内容分类id、key
     * 
     * @return mixed
     */
    public static function get($category_id)
    {
        return Cache::get(self::key($category_id));
    }

    /**
     * 缓存删除
     *
     * @param mixed $category_id 内容分类id、key
     * 
     * @return bool
     */
    public static function del($category_id = '')
    {
        if (is_array($category_id)) {
            $keys = $category_id;
        } else {
            $keys[] = $category_id;
        }

        $keys = array_merge($keys, ['list', 'tree']);
        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }

        return $res;
    }
}
