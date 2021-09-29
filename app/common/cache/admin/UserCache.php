<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户管理缓存
namespace app\common\cache\admin;

use think\facade\Cache;
use app\common\service\admin\UserService;
use app\common\service\admin\SettingService;

class UserCache
{
    /**
     * 缓存key
     *
     * @param int $admin_user_id 用户id
     * 
     * @return string
     */
    public static function key($admin_user_id)
    {
        $key = 'admin_user:' . $admin_user_id;

        return $key;
    }

    /**
     * 缓存设置
     *
     * @param int      $admin_user_id 用户id
     * @param array    $admin_user    用户信息
     * @param int|null $ttl           有效时间（秒）0永久
     * 
     * @return bool
     */
    public static function set($admin_user_id, $admin_user, $ttl = null)
    {
        $key = self::key($admin_user_id);
        $val = $admin_user;
        if ($ttl === null) {
            $setting = SettingService::tokenInfo();
            $ttl     = $setting['token_exp'] * 3600;
        }

        $res = Cache::set($key, $val, $ttl);

        return $res;
    }

    /**
     * 缓存获取
     *
     * @param int $admin_user_id 用户id
     * 
     * @return array 用户信息
     */
    public static function get($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::get($key);

        return $res;
    }

    /**
     * 缓存删除
     *
     * @param int $admin_user_id 用户id
     * 
     * @return bool
     */
    public static function del($admin_user_id)
    {
        $key = self::key($admin_user_id);
        $res = Cache::delete($key);

        return $res;
    }

    /**
     * 缓存更新
     *
     * @param int $admin_user_id 用户id
     * 
     * @return bool
     */
    public static function upd($admin_user_id)
    {
        $old = UserService::info($admin_user_id);
        self::del($admin_user_id);

        $new = UserService::info($admin_user_id);
        unset($new['admin_token']);

        $user = array_merge($old, $new);
        $res  = self::set($admin_user_id, $user);

        return $res;
    }
}
