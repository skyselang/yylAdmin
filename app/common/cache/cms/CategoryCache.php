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
     * @param mixed $id 内容分类id、key
     * 
     * @return string
     */
    public static function key($id)
    {
        return 'cms_category:' . $id;
    }

    /**
     * 缓存写入
     *
     * @param mixed $id   内容分类id、key
     * @param array $info 内容分类信息
     * @param int   $ttl  有效时间（秒，0永久）
     * 
     * @return bool
     */
    public static function set($id, $info, $ttl = 86400)
    {
        return Cache::set(self::key($id), $info, $ttl);
    }

    /**
     * 缓存读取
     *
     * @param mixed $id 内容分类id、key
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
     * @param mixed $id 内容分类id、key
     * 
     * @return bool
     */
    public static function del($id = 0)
    {
        $keys = var_to_array($id);
        $keys = array_merge($keys, ['list', 'tree', 'api']);
        foreach ($keys as $v) {
            $res = Cache::delete(self::key($v));
        }

        return $res;
    }
}
