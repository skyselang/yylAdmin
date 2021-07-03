<?php
/*
 * @Description  : 案例管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-19
 */

namespace app\common\cache;

use think\facade\Cache;

class ProjectCache
{
    /**
     * 缓存键名
     *
     * @param string $project_id 案例id
     * 
     * @return string
     */
    public static function key($project_id = '')
    {
        $key = 'Project:' . $project_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $project_id 案例id
     * @param mixed   $project    案例信息
     * @param integer $ttl        有效时间（秒）
     * 
     * @return bool
     */
    public static function set($project_id = '', $project, $ttl = 0)
    {
        $key = self::key($project_id);
        $val = $project;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $project_id 案例id
     * 
     * @return mixed
     */
    public static function get($project_id = '')
    {
        $key = self::key($project_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $project_id 案例id
     * 
     * @return bool
     */
    public static function del($project_id = '')
    {
        $key = self::key($project_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $project_id 案例id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($project_id = '', $step = 1)
    {
        $key = self::key($project_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
