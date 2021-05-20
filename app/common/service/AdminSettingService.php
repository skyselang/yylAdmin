<?php
/*
 * @Description  : 设置管理业务逻辑
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-05-20
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Cache;
use app\common\cache\AdminUserCache;
use app\common\cache\AdminSettingCache;

class AdminSettingService
{
    // 设置id
    private static $admin_setting_id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $admin_setting_id = self::$admin_setting_id;

        $admin_setting = AdminSettingCache::get($admin_setting_id);
        if (empty($admin_setting)) {
            $admin_setting = Db::name('admin_setting')
                ->where('admin_setting_id', $admin_setting_id)
                ->find();

            if (empty($admin_setting)) {
                $admin_setting['admin_setting_id'] = $admin_setting_id;
                $admin_setting['create_time']      = datetime();
                Db::name('admin_setting')
                    ->insert($admin_setting);

                $admin_setting = Db::name('admin_setting')
                    ->where('admin_setting_id', $admin_setting_id)
                    ->find();
            }

            AdminSettingCache::set($admin_setting_id, $admin_setting);
        }

        return $admin_setting;
    }

    /**
     * 缓存设置信息
     *
     * @return array
     */
    public static function cacheInfo()
    {
        $config = Cache::getConfig();

        $data['type'] = $config['default'];

        return $data;
    }

    /**
     * 缓存设置清除
     *
     * @return array
     */
    public static function cacheClear()
    {
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('is_delete', 0)
            ->select();

        $admin_user_cache = [];
        foreach ($admin_user as $k => $v) {
            $user_cache = AdminUserCache::get($v['admin_user_id']);
            if ($user_cache) {
                $user_cache_temp['admin_user_id'] = $user_cache['admin_user_id'];
                $user_cache_temp['admin_token']   = $user_cache['admin_token'];
                $admin_user_cache[] = $user_cache_temp;
            }
        }

        $res = Cache::clear();
        if (empty($res)) {
            exception();
        }

        foreach ($admin_user_cache as $k => $v) {
            $admin_user_new = AdminUserService::info($v['admin_user_id']);
            $admin_user_new['admin_token'] = $v['admin_token'];
            AdminUserCache::set($admin_user_new['admin_user_id'], $admin_user_new);
        }

        $data['clear'] = $res;

        return $data;
    }

    /**
     * Token设置信息
     *
     * @return array
     */
    public static function tokenInfo()
    {
        $setting = self::info();

        $data['token_exp'] = $setting['token_exp'];

        return $data;
    }

    /**
     * Token设置修改
     *
     * @param array $param Token信息
     *
     * @return array
     */
    public static function tokenEdit($param)
    {
        $admin_setting_id = self::$admin_setting_id;

        $param['update_time'] = datetime();

        $res = Db::name('admin_setting')
            ->where('admin_setting_id', $admin_setting_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        AdminSettingCache::del($admin_setting_id);

        return $param;
    }

    /**
     * 验证码设置信息
     *
     * @return array
     */
    public static function verifyInfo()
    {
        $setting = self::info();

        $data['verify_switch'] = $setting['verify_switch'];

        return $data;
    }

    /**
     * 验证码设置修改
     * 
     * @param array $param 验证码信息
     *
     * @return array
     */
    public static function verifyEdit($param)
    {
        $admin_setting_id = self::$admin_setting_id;

        $param['update_time'] = datetime();

        $res = Db::name('admin_setting')
            ->where('admin_setting_id', $admin_setting_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        AdminSettingCache::del($admin_setting_id);

        return $param;
    }
}
