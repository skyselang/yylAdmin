<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 消息管理缓存
namespace app\common\cache\admin;

use think\facade\Cache;

class MessageCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_message_id 消息id
     * 
     * @return string
     */
    public static function key($admin_message_id)
    {
        return 'admin_message:' . $admin_message_id;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_message_id 消息id
     * @param array   $admin_message    消息信息
     * @param integer $ttl              有效时间（秒）0永久
     * 
     * @return boolean
     */
    public static function set($admin_message_id, $admin_message, $ttl = 86400)
    {
        return Cache::set(self::key($admin_message_id), $admin_message, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_message_id 消息id
     * 
     * @return array 消息信息
     */
    public static function get($admin_message_id)
    {
        return Cache::get(self::key($admin_message_id));
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_message_id 消息id
     * 
     * @return boolean
     */
    public static function del($admin_message_id)
    {
        return Cache::delete(self::key($admin_message_id));
    }
}
