<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置
namespace app\common\service;

use think\facade\Db;
use app\common\cache\SettingCache;

class SettingService
{
    // 表名
    protected static $t_name = 'setting';
    // 表主键
    protected static $t_pk = 'setting_id';
    // 设置id
    private static $setting_id = 1;

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info()
    {
        $setting_id = self::$setting_id;

        $setting = SettingCache::get($setting_id);
        if (empty($setting)) {
            $setting = Db::name(self::$t_name)
                ->where(self::$t_pk, $setting_id)
                ->find();
            if (empty($setting)) {
                $setting[self::$t_pk]   = $setting_id;
                $setting['create_time'] = datetime();
                Db::name(self::$t_name)
                    ->insert($setting);

                $setting = Db::name(self::$t_name)
                    ->where(self::$t_pk, $setting_id)
                    ->find();
            }

            SettingCache::set($setting_id, $setting);
        }

        return $setting;
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
            $token_name = 'MemberToken';
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
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

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

        $data['captcha_register'] = $setting['captcha_register'];
        $data['captcha_login']    = $setting['captcha_login'];

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
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

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

        $data['log_switch'] = $setting['log_switch'];

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
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

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
     * @param array $param API信息
     *
     * @return array
     */
    public static function apiEdit($param)
    {
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }
}
