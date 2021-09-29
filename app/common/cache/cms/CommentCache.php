<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 留言管理缓存
namespace app\common\cache\cms;

use think\facade\Cache;

class CommentCache
{
    /**
     * 缓存键名
     *
     * @param int $comment_id 留言id
     * 
     * @return string
     */
    public static function key($comment_id)
    {
        $key = 'cms_comment:' . $comment_id;

        return $key;
    }

    /**
     * 缓存写入
     *
     * @param int      $comment_id 留言id
     * @param array    $comment    留言信息
     * @param int|null $ttl        有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($comment_id, $comment, $ttl = null)
    {
        $key = self::key($comment_id);
        $val = $comment;
        if ($ttl === null) {
            $ttl = 1 * 24 * 60 * 60;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存读取
     *
     * @param int $comment_id 留言id
     * 
     * @return mixed
     */
    public static function get($comment_id)
    {
        $key = self::key($comment_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int $comment_id 留言id
     * 
     * @return bool
     */
    public static function del($comment_id)
    {
        $key = self::key($comment_id);
        $res = Cache::delete($key);

        return $res;
    }
}
