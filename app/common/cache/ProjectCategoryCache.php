<?php
/*
 * @Description  : 案例分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-17
 */

namespace app\common\cache;

use think\facade\Cache;

class ProjectCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $project_category_id 案例分类id
     * 
     * @return string
     */
    public static function key($project_category_id = '')
    {
        $key = 'ProjectCategory:' . $project_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $project_category_id 案例分类id
     * @param mixed   $project_category    案例分类信息
     * @param integer $ttl                 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($project_category_id = '', $project_category = [], $ttl = 0)
    {
        $key = self::key($project_category_id);
        $val = $project_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $project_category_id 案例分类id
     * 
     * @return mixed
     */
    public static function get($project_category_id = '')
    {
        $key = self::key($project_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $project_category_id 案例分类id
     * 
     * @return bool
     */
    public static function del($project_category_id = '')
    {
        $key = self::key($project_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
