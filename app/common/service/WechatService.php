<?php
/*
 * @Description  : 微信
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-17
 * @LastEditTime : 2021-05-17
 */

namespace app\common\service;

use EasyWeChat\Factory;

class WechatService
{
    /**
     * 微信公众号
     * 
     * @param array $config 配置
     * 
     * @return Factory
     */
    public static function offi($config = [])
    {
        $offi_info = SettingWechatService::offiInfo();

        $config['app_id'] = $offi_info['appid'];
        $config['secret'] = $offi_info['appsecret'];

        $app = Factory::officialAccount($config);

        return $app;
    }

    /**
     * 微信小程序
     * 
     * @param array $config 配置
     * 
     * @return Factory
     */
    public static function mini($config = [])
    {
        $mini_info = SettingWechatService::miniInfo();

        $config['app_id'] = $mini_info['appid'];
        $config['secret'] = $mini_info['appsecret'];

        $app = Factory::miniProgram($config);

        return $app;
    }
}
