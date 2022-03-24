<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// {$form.controller_title}缓存
namespace {$cache.namespace};

use think\facade\Cache;

class {$cache.class_name}
{
    /**
     * 缓存key
     *
     * @param string $id {$form.controller_title}id
     * 
     * @return string
     */
    public static function key($id)
    {
        return '{$controller.class_name}:' . $id;
    }

    /**
     * 缓存设置
     *
     * @param string  $id   {$form.controller_title}id
     * @param array   $info {$form.controller_title}信息
     * @param integer $ttl  有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 86400)
    {
        return Cache::set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存获取
     *
     * @param string $id {$form.controller_title}id
     * 
     * @return mixed
     */
    public static function get($id)
    {
        return Cache::get(self::key($id));
    }

    /**
     * 缓存删除
     *
     * @param string $id {$form.controller_title}id
     * 
     * @return bool
     */
    public static function del($id)
    {
        return Cache::delete(self::key($id));
    }
}
