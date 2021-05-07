<?php
/*
 * @Description  : 基础设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-09
 * @LastEditTime : 2021-05-06
 */

namespace app\common\service;

use think\facade\Db;
use app\common\cache\SettingCache;

class SettingService
{
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
            $setting = Db::name('setting')
                ->where('setting_id', $setting_id)
                ->find();

            if (empty($setting)) {
                $setting['setting_id']  = $setting_id;
                $setting['create_time'] = datetime();
                Db::name('setting')
                    ->insert($setting);

                $setting = Db::name('setting')
                    ->where('setting_id', $setting_id)
                    ->find();
            }

            SettingCache::set($setting_id, $setting);
        }

        return $setting;
    }

    /**
     * Token信息
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
     * Token修改
     *
     * @param array $param Token信息
     *
     * @return array
     */
    public static function tokenEdit($param)
    {
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name('setting')
            ->where('setting_id', $setting_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }

    /**
     * 验证码信息
     *
     * @return array
     */
    public static function verifyInfo()
    {
        $setting = self::info();

        $data['verify_register'] = $setting['verify_register'];
        $data['verify_login']    = $setting['verify_login'];

        return $data;
    }

    /**
     * 验证码修改
     * 
     * @param array $param 验证码信息
     *
     * @return array
     */
    public static function verifyEdit($param)
    {
        $setting_id = self::$setting_id;

        $param['update_time'] = datetime();

        $res = Db::name('setting')
            ->where('setting_id', $setting_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        SettingCache::del($setting_id);

        return $param;
    }
}
