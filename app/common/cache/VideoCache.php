<?php
/*
 * @Description  : 视频管理缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-09
 * @LastEditTime : 2021-06-30
 */

namespace app\common\cache;

use think\facade\Cache;

class VideoCache
{
    /**
     * 缓存键名
     *
     * @param string $video_id 视频id
     * 
     * @return string
     */
    public static function key($video_id = '')
    {
        $key = 'Video:' . $video_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $video_id 视频id
     * @param mixed   $video    视频信息
     * @param integer $ttl      有效时间（秒）
     * 
     * @return bool
     */
    public static function set($video_id = '', $video, $ttl = 0)
    {
        $key = self::key($video_id);
        $val = $video;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60 + mt_rand(0, 9);
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $video_id 视频id
     * 
     * @return mixed
     */
    public static function get($video_id = '')
    {
        $key = self::key($video_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $video_id 视频id
     * 
     * @return bool
     */
    public static function del($video_id = '')
    {
        $key = self::key($video_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存自增
     *
     * @param string  $video_id 视频id
     * @param integer $step       步长
     *
     * @return bool
     */
    public static function inc($video_id = '', $step = 1)
    {
        $key = self::key($video_id);
        $res = Cache::inc($key, $step);

        return $res;
    }
}
