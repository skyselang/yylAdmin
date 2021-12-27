<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理
namespace app\common\service\admin;

use think\facade\Db;
use think\facade\Cache;
use app\common\cache\admin\UserCache;
use app\common\cache\admin\SettingCache;
use app\common\service\file\FileService;
use app\common\model\admin\UserModel;

class SettingService
{
    // 表名
    protected static $t_name = 'admin_setting';
    // 表主键
    protected static $t_pk = 'admin_setting_id';
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

        $admin_setting = SettingCache::get($admin_setting_id);
        if (empty($admin_setting)) {
            $admin_setting = Db::name(self::$t_name)
                ->where(self::$t_pk, $admin_setting_id)
                ->find();
            if (empty($admin_setting)) {
                $admin_setting[self::$t_pk]   = $admin_setting_id;
                $admin_setting['create_time'] = datetime();
                Db::name(self::$t_name)
                    ->insert($admin_setting);

                $admin_setting = Db::name(self::$t_name)
                    ->where(self::$t_pk, $admin_setting_id)
                    ->find();
            }

            $admin_setting['logo_url'] = '';
            if ($admin_setting['logo_id']) {
                $admin_setting['logo_url'] = FileService::fileUrl($admin_setting['logo_id']);
            }

            $admin_setting['favicon_url'] = '';
            if ($admin_setting['favicon_id']) {
                $admin_setting['favicon_url'] = FileService::fileUrl($admin_setting['favicon_id']);
            } else {
                $admin_setting['favicon_url'] = $admin_setting['logo_url'];
            }

            $admin_setting['login_bg_url'] = '';
            if ($admin_setting['login_bg_id']) {
                $admin_setting['login_bg_url'] = FileService::fileUrl($admin_setting['login_bg_id']);
            }

            SettingCache::set($admin_setting_id, $admin_setting);
        }

        return $admin_setting;
    }

    /**
     * 设置修改
     *
     * @param array $param
     *
     * @return boolean|Exception
     */
    public static function edit($param)
    {
        $admin_setting_id = self::$admin_setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($admin_setting_id);

        return $res;
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
        $AdminUser = new UserModel();
        $admin_user = $AdminUser
            ->field('admin_user_id')
            ->where('is_delete', 0)
            ->select();

        $admin_user_cache = [];
        foreach ($admin_user as $k => $v) {
            $user_cache = UserCache::get($v['admin_user_id']);
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
            $admin_user_new = UserService::info($v['admin_user_id']);
            $admin_user_new['admin_token'] = $v['admin_token'];
            UserCache::set($admin_user_new['admin_user_id'], $admin_user_new);
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

        // token_name为空则设置token_name
        if (empty($setting['token_name'])) {
            $token_name = 'AdminToken';
            self::tokenEdit(['token_name' => $token_name]);
            $setting = self::info();
        }

        // token_key为空则生成token_key
        if (empty($setting['token_key'])) {
            $token_key = uniqid();
            self::tokenEdit(['token_key' => $token_key]);
            $setting = self::info();
        }

        $data['token_name'] = $setting['token_name'];
        $data['token_key']  = $setting['token_key'];
        $data['token_exp']  = $setting['token_exp'];

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
        self::edit($param);

        return $param;
    }

    /**
     * 验证码设置信息
     *
     * @return array
     */
    public static function captchaInfo()
    {
        $setting = self::info();

        $data['captcha_switch'] = $setting['captcha_switch'];

        return $data;
    }

    /**
     * 验证码设置修改
     * 
     * @param array $param 验证码信息
     *
     * @return array
     */
    public static function captchaEdit($param)
    {
        self::edit($param);

        return $param;
    }

    /**
     * 日志设置信息
     *
     * @return array
     */
    public static function logInfo()
    {
        $setting = self::info();

        $data['log_switch']    = $setting['log_switch'];
        $data['log_save_time'] = $setting['log_save_time'];

        return $data;
    }

    /**
     * 日志设置修改
     * 
     * @param array $param 日志记录信息
     *
     * @return array
     */
    public static function logEdit($param)
    {
        self::edit($param);

        return $param;
    }

    /**
     * 接口设置信息
     *
     * @return array
     */
    public static function apiInfo()
    {
        $setting = self::info();

        $data['api_rate_num']  = $setting['api_rate_num'];
        $data['api_rate_time'] = $setting['api_rate_time'];

        return $data;
    }

    /**
     * 接口设置修改
     *
     * @param array $param 接口设置信息
     *
     * @return array
     */
    public static function apiEdit($param)
    {
        self::edit($param);

        return $param;
    }

    /**
     * 系统设置信息
     *
     * @return array
     */
    public static function systemInfo()
    {
        $setting = self::info();

        $data = [];
        $field = ['logo_id', 'logo_url', 'favicon_id', 'favicon_url', 'login_bg_id', 'login_bg_url', 'system_name', 'page_title'];
        foreach ($field as $k => $v) {
            $data[$v] = $setting[$v];
        }

        return $data;
    }

    /**
     * 系统设置修改
     *
     * @param array $param 系统设置信息
     *
     * @return array
     */
    public static function systemEdit($param)
    {
        self::edit($param);

        return $param;
    }
}
