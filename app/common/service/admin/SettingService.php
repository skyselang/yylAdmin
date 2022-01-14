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

use think\facade\Cache;
use app\common\cache\admin\UserCache;
use app\common\cache\admin\SettingCache;
use app\common\service\file\FileService;
use app\common\model\admin\UserModel;
use app\common\model\admin\SettingModel;

class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $id = self::$id;
        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_time'] = datetime();
                $model->insert($info);

                $info = $model->where($pk, $id)->find();
            }
            $info = $info->toArray();

            $info['logo_url'] = '';
            if ($info['logo_id']) {
                $info['logo_url'] = FileService::fileUrl($info['logo_id']);
            }

            $info['favicon_url'] = '';
            if ($info['favicon_id']) {
                $info['favicon_url'] = FileService::fileUrl($info['favicon_id']);
            } else {
                $info['favicon_url'] = $info['logo_url'];
            }

            $info['login_bg_url'] = '';
            if ($info['login_bg_id']) {
                $info['login_bg_url'] = FileService::fileUrl($info['login_bg_id']);
            }

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param
     *
     * @return bool|Exception
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $pk = $model->getPk();

        $id = self::$id;
        unset($param[$pk]);

        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($id);

        return $param;
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
        $UserModel = new UserModel();
        $UserPk = $UserModel->getPk();

        $admin_user = $UserModel->field($UserPk)->where('is_delete', 0)->select();
        $admin_user_cache = [];
        foreach ($admin_user as $v) {
            $user_cache = UserCache::get($v[$UserPk]);
            if ($user_cache) {
                $user_cache_temp[$UserPk]       = $user_cache[$UserPk];
                $user_cache_temp['admin_token'] = $user_cache['admin_token'];
                $admin_user_cache[] = $user_cache_temp;
            }
        }

        $res = Cache::clear();
        if (empty($res)) {
            exception();
        }

        foreach ($admin_user_cache as $v) {
            $admin_user_new = UserService::info($v[$UserPk]);
            $admin_user_new['admin_token'] = $v['admin_token'];
            UserCache::set($admin_user_new[$UserPk], $admin_user_new);
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
        foreach ($field as $v) {
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
