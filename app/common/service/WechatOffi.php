<?php
/*
 * @Description  : 微信公众号
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-23
 * @LastEditTime : 2021-04-23
 */

namespace app\common\service;

use EasyWeChat\Factory;

class WechatOffi
{
    protected static $app;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $offi_info = SettingWechatService::offiInfo();

        $config = [
            'app_id' => $offi_info['appid'],
            'secret' => $offi_info['appsecret'],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => '/oauth_callback',
            ],
        ];

        self::$app = Factory::officialAccount($config);
    }

    /**
     * 登录
     */
    public static function login()
    {
        $offi_info = SettingWechatService::offiInfo();

        $config = [
            'app_id' => $offi_info['appid'],
            'secret' => $offi_info['appsecret'],
        ];

        $app = Factory::officialAccount($config);
        $redirectUrl = $app
            ->oauth;

        dump($redirectUrl);
    }
}
