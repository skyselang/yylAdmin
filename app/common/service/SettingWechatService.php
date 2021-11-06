<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 微信设置
namespace app\common\service;

use think\facade\Db;
use app\common\cache\SettingWechatCache;
use app\common\service\file\FileService;
use app\common\utils\StringUtils;

class SettingWechatService
{
    // 表名
    protected static $t_name = 'setting_wechat';
    // 表主键
    protected static $t_pk = 'setting_wechat_id';
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
            $setting_wechat = Db::name(self::$t_name)
                ->where(self::$t_pk, $setting_wechat_id)
                ->find();
            if (empty($setting_wechat)) {
                $setting_wechat[self::$t_pk]         = $setting_wechat_id;
                $setting_wechat['token']             = StringUtils::random(32);
                $setting_wechat['encoding_aes_key']  = StringUtils::random(43);
                $setting_wechat['create_time']       = datetime();

                Db::name(self::$t_name)
                    ->insert($setting_wechat);

                $setting_wechat = Db::name(self::$t_name)
                    ->where(self::$t_pk, $setting_wechat_id)
                    ->find();
            }

            $setting_wechat['server_url'] = server_url() . '/index/Wechat/access';
            $setting_wechat['qrcode_url'] = FileService::fileUrl($setting_wechat['qrcode_id']);

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

        $setting_wechat = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_wechat_id)
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
            $setting_wechat = Db::name(self::$t_name)
                ->where(self::$t_pk, $setting_wechat_id)
                ->find();
            if (empty($setting_wechat)) {
                $setting_wechat[self::$t_pk] = $setting_wechat_id;
                $setting_wechat['create_time']       = datetime();

                Db::name(self::$t_name)
                    ->insert($setting_wechat);

                $setting_wechat = Db::name(self::$t_name)
                    ->where(self::$t_pk, $setting_wechat_id)
                    ->find();
            }

            $setting_wechat['qrcode_url'] = FileService::fileUrl($setting_wechat['qrcode_id']);

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

        $setting_wechat = Db::name(self::$t_name)
            ->where(self::$t_pk, $setting_wechat_id)
            ->update($param);
        if (empty($setting_wechat)) {
            exception();
        }

        SettingWechatCache::del($setting_wechat_id);

        return $param;
    }
}
