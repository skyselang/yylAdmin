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
use app\common\cache\WechatSettingCache;

class WechatSettingService
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
        $wechat_setting_id = self::$offi_id;

        $wechat_setting = WechatSettingCache::get($wechat_setting_id);

        if (empty($wechat_setting)) {
            $wechat_setting = Db::name('wechat_setting')
                ->where('wechat_setting_id', $wechat_setting_id)
                ->find();

            if (empty($wechat_setting)) {
                $wechat_setting['wechat_setting_id'] = $wechat_setting_id;
                $wechat_setting['create_time']       = datetime();

                Db::name('wechat_setting')
                    ->insert($wechat_setting);

                $wechat_setting = Db::name('wechat_setting')
                    ->where('wechat_setting_id', $wechat_setting_id)
                    ->find();
            }

            $wechat_setting['qrcode_url'] = file_url($wechat_setting['qrcode']);

            WechatSettingCache::set($wechat_setting_id, $wechat_setting);
        }

        return $wechat_setting;
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
        $wechat_setting_id = self::$offi_id;

        $param['update_time'] = datetime();

        $wechat_setting = Db::name('wechat_setting')
            ->where('wechat_setting_id', $wechat_setting_id)
            ->update($param);

        if (empty($wechat_setting)) {
            exception();
        }

        WechatSettingCache::del($wechat_setting_id);

        return $param;
    }

    /**
     * 小程序信息
     *
     * @return array
     */
    public static function miniInfo()
    {
        $wechat_setting_id = self::$mini_id;

        $wechat_setting = WechatSettingCache::get($wechat_setting_id);

        if (empty($wechat_setting)) {
            $wechat_setting = Db::name('wechat_setting')
                ->where('wechat_setting_id', $wechat_setting_id)
                ->find();

            if (empty($wechat_setting)) {
                $wechat_setting['wechat_setting_id'] = $wechat_setting_id;
                $wechat_setting['create_time']       = datetime();

                Db::name('wechat_setting')
                    ->insert($wechat_setting);

                $wechat_setting = Db::name('wechat_setting')
                    ->where('wechat_setting_id', $wechat_setting_id)
                    ->find();
            }

            $wechat_setting['qrcode_url'] = file_url($wechat_setting['qrcode']);

            WechatSettingCache::set($wechat_setting_id, $wechat_setting);
        }

        return $wechat_setting;
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
        $wechat_setting_id = self::$mini_id;

        $param['update_time'] = datetime();

        $wechat_setting = Db::name('wechat_setting')
            ->where('wechat_setting_id', $wechat_setting_id)
            ->update($param);

        if (empty($wechat_setting)) {
            exception();
        }

        WechatSettingCache::del($wechat_setting_id);

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
