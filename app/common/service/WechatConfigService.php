<?php
/*
 * @Description  : 微信配置
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-22
 * @LastEditTime : 2021-04-23
 */

namespace app\common\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\WechatConfigCache;

class WechatConfigService
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
        $wechat_config_id = self::$offi_id;

        $wechat_config = WechatConfigCache::get($wechat_config_id);

        if (empty($wechat_config)) {
            $wechat_config = Db::name('wechat_config')
                ->where('wechat_config_id', $wechat_config_id)
                ->find();

            if (empty($wechat_config)) {
                $wechat_config['wechat_config_id'] = $wechat_config_id;
                $wechat_config['create_time']      = datetime();

                Db::name('wechat_config')
                    ->insert($wechat_config);

                $wechat_config = Db::name('wechat_config')
                    ->where('wechat_config_id', $wechat_config_id)
                    ->find();
            }

            $wechat_config['qrcode_url'] = file_url($wechat_config['qrcode']);

            WechatConfigCache::set($wechat_config_id, $wechat_config);
        }

        return $wechat_config;
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
        $wechat_config_id = self::$offi_id;

        $param['update_time'] = datetime();

        $wechat_config = Db::name('wechat_config')
            ->where('wechat_config_id', $wechat_config_id)
            ->update($param);

        if (empty($wechat_config)) {
            exception();
        }

        WechatConfigCache::del($wechat_config_id);

        return $param;
    }


    /**
     * 小程序信息
     *
     * @return array
     */
    public static function miniInfo()
    {
        $wechat_config_id = self::$mini_id;

        $wechat_config = WechatConfigCache::get($wechat_config_id);

        if (empty($wechat_config)) {
            $wechat_config = Db::name('wechat_config')
                ->where('wechat_config_id', $wechat_config_id)
                ->find();

            if (empty($wechat_config)) {
                $wechat_config['wechat_config_id'] = $wechat_config_id;
                $wechat_config['create_time']      = datetime();

                Db::name('wechat_config')
                    ->insert($wechat_config);

                $wechat_config = Db::name('wechat_config')
                    ->where('wechat_config_id', $wechat_config_id)
                    ->find();
            }

            $wechat_config['qrcode_url'] = file_url($wechat_config['qrcode']);

            WechatConfigCache::set($wechat_config_id, $wechat_config);
        }

        return $wechat_config;
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
        $wechat_config_id = self::$mini_id;

        $param['update_time'] = datetime();

        $wechat_config = Db::name('wechat_config')
            ->where('wechat_config_id', $wechat_config_id)
            ->update($param);

        if (empty($wechat_config)) {
            exception();
        }

        WechatConfigCache::del($wechat_config_id);

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
