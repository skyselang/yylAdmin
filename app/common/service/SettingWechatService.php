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

use app\common\utils\StringUtils;
use app\common\cache\SettingWechatCache;
use app\common\model\SettingWechatModel;
use app\common\service\file\FileService;

class SettingWechatService
{
    // 表名
    protected static $t_name = 'info';
    // 表主键
    protected static $t_pk = 'id';
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
        $id = self::$offi_id;
        $info = SettingWechatCache::get($id);
        if (empty($info)) {
            $model = new SettingWechatModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                $info[$pk]                = $id;
                $info['token']            = StringUtils::random(32);
                $info['encoding_aes_key'] = StringUtils::random(43);
                $info['create_time']      = datetime();
                $model->insert($info);

                $info = $model->where($pk, $id)->find();
            }
            $info = $info->toArray();

            $info['server_url'] = server_url() . '/index/Wechat/access';
            $info['qrcode_url'] = FileService::fileUrl($info['qrcode_id']);

            SettingWechatCache::set($id, $info);
        }

        return $info;
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
        $model = new SettingWechatModel();
        $pk = $model->getPk();

        $id = self::$offi_id;
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        SettingWechatCache::del($id);

        return $param;
    }

    /**
     * 小程序信息
     *
     * @return array
     */
    public static function miniInfo()
    {
        $id = self::$mini_id;
        $info = SettingWechatCache::get($id);
        if (empty($info)) {
            $model = new SettingWechatModel();
            $pk = $model->getPk();

            $info = $model->where($pk, $id)->find();
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['create_time'] = datetime();
                $model->insert($info);

                $info = $model->where($pk, $id)->find();
            }
            $info = $info->toArray();

            $info['qrcode_url'] = FileService::fileUrl($info['qrcode_id']);

            SettingWechatCache::set($id, $info);
        }

        return $info;
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
        $model = new SettingWechatModel();
        $pk = $model->getPk();

        $id = self::$mini_id;
        $param['update_time'] = datetime();

        $res = $model->where($pk, $id)->update($param);
        if (empty($res)) {
            exception();
        }

        SettingWechatCache::del($id);

        return $param;
    }
}
