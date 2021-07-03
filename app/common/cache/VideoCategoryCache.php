<?php
/*
 * @Description  : 视频分类缓存
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-08
 * @LastEditTime : 2021-06-17
 */

namespace app\common\cache;

use think\facade\Cache;

class VideoCategoryCache
{
    /**
     * 缓存键名
     *
     * @param string $video_category_id 视频分类id
     * 
     * @return string
     */
    public static function key($video_category_id = '')
    {
        $key = 'VideoCategory:' . $video_category_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param string  $video_category_id 视频分类id
     * @param mixed   $video_category    视频分类信息
     * @param integer $ttl                 有效时间（秒）
     * 
     * @return bool
     */
    public static function set($video_category_id = '', $video_category = [], $ttl = 0)
    {
        $key = self::key($video_category_id);
        $val = $video_category;
        if (empty($ttl)) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param string $video_category_id 视频分类id
     * 
     * @return mixed
     */
    public static function get($video_category_id = '')
    {
        $key = self::key($video_category_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param string $video_category_id 视频分类id
     * 
     * @return bool
     */
    public static function del($video_category_id = '')
    {
        $key = self::key($video_category_id);
        $res = Cache::delete($key);

        return $res;
    }
}
