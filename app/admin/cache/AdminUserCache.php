<?php
/*
 * @Description  : 用户缓存
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-25
 */

namespace app\admin\cache;

use think\facade\Db;
use think\facade\Cache;
use app\admin\service\AdminTokenService;

class AdminUserCache
{
    /**
     * 缓存key
     *
     * @param integer $admin_user_id 用户id
     * @return string
     */
    public static function key($admin_user_id)
    {
        return 'admin_user_id:' . $admin_user_id;
    }

    /**
     * 缓存有效时间
     *
     * @param integer $exp 有效时间
     * @return integer
     */
    public static function exp($exp = '')
    {
        if ($exp) {
            return $exp;
        }

        return 1 * 24 * 60 * 60;
    }

    /**
     * 缓存设置
     *
     * @param integer $admin_user_id 用户id
     * @return array
     */
    public static function set($admin_user_id)
    {
        if (empty($admin_user_id)) {
            return [];
        }

        $admin_user = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->where('is_delete', 0)
            ->find();

        if (empty($admin_user)) {
            return [];
        }

        unset($admin_user['password']);
        $admin_user['avatar'] = file_url($admin_user['avatar']);

        if (super_admin($admin_user_id)) {
            $admin_menu = Db::name('admin_menu')
                ->field('admin_menu_id')
                ->where('is_delete', 0)
                ->where('menu_url', '<>', '')
                ->column('menu_url');
        } elseif ($admin_user['is_super_admin'] == 1) {
            $admin_menu = Db::name('admin_menu')
                ->field('admin_menu_id')
                ->where('is_delete', 0)
                ->where('is_prohibit', 0)
                ->where('menu_url', '<>', '')
                ->column('menu_url');
        } else {
            $admin_rule = Db::name('admin_rule')
                ->field('admin_rule_id')
                ->where('admin_rule_id', 'in', $admin_user['admin_rule_ids'])
                ->where('is_delete', 0)
                ->where('is_prohibit', 0)
                ->column('admin_menu_ids');
            foreach ($admin_rule as $k => $v) {
                if (empty($v)) {
                    unset($admin_rule[$k]);
                }
            }

            $admin_menu_ids_str = implode(',', $admin_rule);
            $admin_menu_ids_arr = explode(',', $admin_menu_ids_str);
            $admin_menu_ids = array_unique($admin_menu_ids_arr);

            $admin_menu = Db::name('admin_menu')
                ->field('admin_menu_id')
                ->where('admin_menu_id', 'in', $admin_menu_ids)
                ->where('is_delete', 0)
                ->where('is_prohibit', 0)
                ->where('menu_url', '<>', '')
                ->whereOr('is_unauth', 1)
                ->column('menu_url');
        }

        $admin_user['admin_token'] = AdminTokenService::create($admin_user);
        $admin_user['roles'] = $admin_menu;

        Cache::set(self::key($admin_user_id), $admin_user, self::exp());

        return $admin_user;
    }

    /**
     * 缓存获取
     *
     * @param integer $admin_user_id 用户id
     * @return array
     */
    public static function get($admin_user_id)
    {
        $admin_user =  Cache::get(self::key($admin_user_id));
        if ($admin_user) {
            return $admin_user;
        }

        return self::set($admin_user_id);
    }

    /**
     * 缓存删除
     *
     * @param integer $admin_user_id 用户id
     * @return bool
     */
    public static function del($admin_user_id)
    {
        return Cache::delete(self::key($admin_user_id));
    }
}
