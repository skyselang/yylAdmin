<?php
/*
 * @Description  : 微信设置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-05-06
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\SettingWechatCache;

class SettingWechatService
{
    // 公众号id
    private static $offi_id = 1;
    // 小程序id
    private static $mini_id = 2;

    /**
     * 公众号信息
     *
     * @return array
     */
    public static function offiInfo()
    {
        $setting_wechat_id = self::$offi_id;

        $setting_wechat = SettingWechatCache::get($setting_wechat_id);

        if (empty($setting_wechat)) {
            $setting_wechat = Db::name('setting_wechat')
                ->where('setting_wechat_id', $setting_wechat_id)
                ->find();

            if (empty($setting_wechat)) {
                $setting_wechat['setting_wechat_id'] = $setting_wechat_id;
                $setting_wechat['create_time']       = datetime();

                Db::name('setting_wechat')
                    ->insert($setting_wechat);

                $setting_wechat = Db::name('setting_wechat')
                    ->where('setting_wechat_id', $setting_wechat_id)
                    ->find();
            }

            $setting_wechat['qrcode_url'] = file_url($setting_wechat['qrcode']);

            SettingWechatCache::set($setting_wechat_id, $setting_wechat);
        }

        return $setting_wechat;
    }

    /**
     * 公众号修改
     *
     * @param array $param 公众号信息
     *
     * @return array
     */
    public static function offiEdit($param)
    {
        $setting_wechat_id = self::$offi_id;

        $param['update_time'] = datetime();

        $setting_wechat = Db::name('setting_wechat')
            ->where('setting_wechat_id', $setting_wechat_id)
            ->update($param);

        if (empty($setting_wechat)) {
            exception();
        }

        SettingWechatCache::del($setting_wechat_id);

        return $param;
    }

    /**
     * 小程序信息
     *
     * @return array
     */
    public static function miniInfo()
    {
        $setting_wechat_id = self::$mini_id;

        $setting_wechat = SettingWechatCache::get($setting_wechat_id);

        if (empty($setting_wechat)) {
            $setting_wechat = Db::name('setting_wechat')
                ->where('setting_wechat_id', $setting_wechat_id)
                ->find();

            if (empty($setting_wechat)) {
                $setting_wechat['setting_wechat_id'] = $setting_wechat_id;
                $setting_wechat['create_time']       = datetime();

                Db::name('setting_wechat')
                    ->insert($setting_wechat);

                $setting_wechat = Db::name('setting_wechat')
                    ->where('setting_wechat_id', $setting_wechat_id)
                    ->find();
            }

            $setting_wechat['qrcode_url'] = file_url($setting_wechat['qrcode']);

            SettingWechatCache::set($setting_wechat_id, $setting_wechat);
        }

        return $setting_wechat;
    }

    /**
     * 小程序修改
     *
     * @param array $param 小程序信息
     *
     * @return array
     */
    public static function miniEdit($param)
    {
        $setting_wechat_id = self::$mini_id;

        $param['update_time'] = datetime();

        $setting_wechat = Db::name('setting_wechat')
            ->where('setting_wechat_id', $setting_wechat_id)
            ->update($param);

        if (empty($setting_wechat)) {
            exception();
        }

        SettingWechatCache::del($setting_wechat_id);

        return $param;
    }

    /**
     * 上传二维码
     *
     * @param array $param 二维码图片
     * 
     * @return array
     */
    public static function qrcode($param)
    {
        $type = $param['type'];
        $file = $param['file'];

        $file_name = Filesystem::disk('public')
            ->putFile('wechat', $file, function () use ($type) {
                return $type . '/qrcode';
            });

        $data['type']      = $type;
        $data['file_path'] = 'storage/' . $file_name . '?t=' . date('YmdHis');
        $data['file_url']  = file_url($data['file_path']);

        return $data;
    }
}
