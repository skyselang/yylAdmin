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

use app\common\cache\SettingCache;
use app\common\model\SettingModel;

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
                $info[$pk] = $id;
                $info['create_time'] = datetime();
                $model->insert($info);

                $info = $model->where($pk, $id)->find();
            }
            $info = $info->toArray();

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
     * Token设置信息
     *
     * @return array
     */
    public static function tokenInfo()
    {
        $info = self::info();

        // token_name为空则设置token_name
        if (empty($info['token_name'])) {
            $token_name = 'MemberToken';
            self::tokenEdit(['token_name' => $token_name]);
            $info = self::info();
        }

        // token_key为空则生成token_key
        if (empty($info['token_key'])) {
            $token_key = uniqid();
            self::tokenEdit(['token_key' => $token_key]);
            $info = self::info();
        }

        $data['token_name'] = $info['token_name'];
        $data['token_key']  = $info['token_key'];
        $data['token_exp']  = $info['token_exp'];

        return $data;
    }

    /**
     * Token设置修改
     *
     * @param array $param Token设置信息
     *
     * @return array
     */
    public static function tokenEdit($param)
    {
        $param = self::edit($param);

        return $param;
    }

    /**
     * 验证码设置信息
     *
     * @return array
     */
    public static function captchaInfo()
    {
        $info = self::info();

        $data['captcha_register'] = $info['captcha_register'];
        $data['captcha_login']    = $info['captcha_login'];

        return $data;
    }

    /**
     * 验证码设置修改
     * 
     * @param array $param 验证码设置信息
     *
     * @return array
     */
    public static function captchaEdit($param)
    {
        $param = self::edit($param);

        return $param;
    }

    /**
     * 日志设置信息
     *
     * @return array
     */
    public static function logInfo()
    {
        $info = self::info();

        $data['log_switch']    = $info['log_switch'];
        $data['log_save_time'] = $info['log_save_time'];

        return $data;
    }

    /**
     * 日志设置修改
     * 
     * @param array $param 日志设置信息
     *
     * @return array
     */
    public static function logEdit($param)
    {
        $param = self::edit($param);

        return $param;
    }

    /**
     * 接口设置信息
     *
     * @return array
     */
    public static function apiInfo()
    {
        $info = self::info();

        $data['api_rate_num']  = $info['api_rate_num'];
        $data['api_rate_time'] = $info['api_rate_time'];

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
        $param = self::edit($param);

        return $param;
    }
}
