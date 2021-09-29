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
     * @param int|string $category_id 内容分类id、all
     * 
     * @return string
     */
    public static function key($category_id)
    {
        $key = 'cms_category:' . $category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param int|string $category_id 内容分类id、all
     * @param array      $category    内容分类信息
     * @param int|null   $ttl         有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($category_id, $category, $ttl = null)
    {
        $key = self::key($category_id);
        $val = $category;
        if ($ttl === null) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param int|string $category_id 内容分类id、all
     * 
     * @return mixed
     */
    public static function get($category_id)
    {
        $key = self::key($category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int|string $category_id 内容分类id、all
     * 
     * @return bool
     */
    public static function del($category_id)
    {
        $key = self::key($category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
