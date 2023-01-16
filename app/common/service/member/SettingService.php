<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\member;

use think\facade\Config;
use app\common\cache\member\SettingCache;
use app\common\model\member\SettingModel;

/**
 * 会员设置
 */
class SettingService
{
    // 设置id
    private static $id = 1;

    /**
     * 性别：未知
     */
    const GENDER_UNKNOWN = 0;
    /**
     * 性别：男
     */
    const GENDER_MAN = 1;
    /**
     * 性别：女
     */
    const GENDER_WOMAN = 2;
    /**
     * 性别
     *
     * @param string $gender 性别
     *
     * @return array|string 性别数组或名称
     */
    public static function genders($gender = '')
    {
        $genders = [
            self::GENDER_UNKNOWN => '未知',
            self::GENDER_MAN => '男',
            self::GENDER_WOMAN => '女',
        ];
        if ($gender !== '') {
            return $genders[$gender] ?? '';
        }
        return $genders;
    }

    /**
     * 注册渠道：未知
     */
    const REG_CHANNEL_UNKNOWN  = 0;
    /**
     * 注册渠道：后台
     */
    const REG_CHANNEL_ADMIN = 1;
    /**
     * 注册渠道：小程序
     */
    const REG_CHANNEL_MINI = 2;
    /**
     * 注册渠道：公众号
     */
    const REG_CHANNEL_OFFI = 3;
    /**
     * 注册渠道：H5
     */
    const REG_CHANNEL_H5 = 4;
    /**
     * 注册渠道：PC
     */
    const REG_CHANNEL_PC = 5;
    /**
     * 注册渠道：安卓
     */
    const REG_CHANNEL_ANDROID = 6;
    /**
     * 注册渠道：苹果
     */
    const REG_CHANNEL_IOS = 7;
    /**
     * 注册渠道
     *
     * @param string $reg_channel 注册渠道
     *
     * @return array|string 注册渠道数组或名称
     */
    public static function reg_channels($reg_channel = '')
    {
        $reg_channels = [
            self::REG_CHANNEL_UNKNOWN => '未知',
            self::REG_CHANNEL_ADMIN => '后台',
            self::REG_CHANNEL_MINI => '小程序',
            self::REG_CHANNEL_OFFI => '公众号',
            self::REG_CHANNEL_H5 => 'H5',
            self::REG_CHANNEL_PC => 'PC',
            self::REG_CHANNEL_ANDROID => '安卓',
            self::REG_CHANNEL_IOS => '苹果',
        ];
        if ($reg_channel !== '') {
            return $reg_channels[$reg_channel] ?? '';
        }
        return $reg_channels;
    }

    /**
     * 注册方式：用户名
     */
    const REG_TYPE_USERNAME = 0;
    /**
     * 注册方式：手机
     */
    const REG_TYPE_PHONE = 1;
    /**
     * 注册方式：邮箱
     */
    const REG_TYPE_EMAIL = 2;
    /**
     * 注册方式：微信
     */
    const REG_TYPE_WECHAT = 3;
    /**
     * 注册方式
     *
     * @param string $reg_type 注册方式
     *
     * @return array|string 注册方式数组或名称
     */
    public static function reg_types($reg_type = '')
    {
        $reg_types = [
            self::REG_TYPE_USERNAME => '用户名',
            self::REG_TYPE_PHONE => '手机',
            self::REG_TYPE_EMAIL => '邮箱',
            self::REG_TYPE_WECHAT => '微信',
        ];
        if ($reg_type !== '') {
            return $reg_types[$reg_type] ?? '';
        }
        return $reg_types;
    }

    /**
     * 日志类型：注册
     */
    const LOG_TYPE_REGISTER = 0;
    /**
     * 日志类型：登录
     */
    const LOG_TYPE_LOGIN = 1;
    /**
     * 日志类型：操作
     */
    const LOG_TYPE_OPERATION = 2;
    /**
     * 日志类型：退出
     */
    const LOG_TYPE_LOGOUT = 3;
    /**
     * 日志类型
     *
     * @param string $log_type 日志类型
     *
     * @return array|string 日志类型数组或名称
     */
    public static function log_types($log_type = '')
    {
        $log_types = [
            self::LOG_TYPE_REGISTER => '注册',
            self::LOG_TYPE_LOGIN => '登录',
            self::LOG_TYPE_OPERATION => '操作',
            self::LOG_TYPE_LOGOUT => '退出',
        ];
        if ($log_type !== '') {
            return $log_types[$log_type] ?? '';
        }
        return $log_types;
    }

    /**
     * 设置信息
     *
     * @return array
     */
    public static function info($param = [])
    {
        $id = self::$id;

        $info = SettingCache::get($id);
        if (empty($info)) {
            $model = new SettingModel();
            $pk = $model->getPk();

            $info = $model->find($id);
            if (empty($info)) {
                $info[$pk]           = $id;
                $info['token_key']   = uniqid();
                $info['diy_config']  = [];
                $info['create_uid']  = $param['create_uid'] ?? 0;
                $info['create_time'] = datetime();
                $model->save($info);
                $info = $model->find($id);
            }
            $info = $info->append(['diy_con_obj'])->toArray();

            $info['token_type'] = Config::get('api.token_type');
            $info['token_name'] = Config::get('api.token_name');

            SettingCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 设置修改
     *
     * @param array $param 设置信息
     *
     * @return array
     */
    public static function edit($param)
    {
        $model = new SettingModel();
        $id = self::$id;

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();

        $info = $model->find($id);
        $res = $info->save($param);
        if (empty($res)) {
            exception();
        }

        SettingCache::del($id);

        return $param;
    }
}
