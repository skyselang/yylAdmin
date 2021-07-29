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
     * @param string $category_id 内容分类id
     * 
     * @return string
     */
    public static function key($category_id = '')
    {
        $key = 'cms_category:' . $category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $category_id 内容分类id
     * @param mixed   $category    内容分类信息
     * @param integer $ttl         有效时间（秒）
     * 
     * @return bool
     */
    public static function set($category_id = '', $category = [], $ttl = 0)
    {
        $key = self::key($category_id);
        $val = $category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 99);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $category_id 内容分类id
     * 
     * @return mixed
     */
    public static function get($category_id = '')
    {
        $key = self::key($category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $category_id 内容分类id
     * 
     * @return bool
     */
    public static function del($category_id = '')
    {
        $key = self::key($category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
