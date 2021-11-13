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
     * @param integer $comment_id 留言id
     * 
     * @return string
     */
    public static function key($comment_id)
    {
        return 'cms_comment:' . $comment_id;
    }

    /**
     * 缓存写入
     *
     * @param integer $comment_id 留言id
     * @param array   $comment    留言信息
     * @param integer $ttl        有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($comment_id, $comment, $ttl = 86400)
    {
        return Cache::set(self::key($comment_id), $comment, $ttl);
    }

    /**
     * 缓存读取
     *
     * @param integer $comment_id 留言id
     * 
     * @return mixed
     */
    public static function get($comment_id)
    {
        return Cache::get(self::key($comment_id));
    }

    /**
     * 缓存删除
     *
     * @param integer $comment_id 留言id
     * 
     * @return bool
     */
    public static function del($comment_id)
    {
        return Cache::delete(self::key($comment_id));
    }
}
