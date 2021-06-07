<?php
/*
 * @Description  : 微信
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-17
 * @LastEditTime : 2021-06-05
 */

namespace app\common\service;

use EasyWeChat\Factory;
use think\facade\Config;

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

        $log_channel = Config::get('app.app_debug') ? 'dev' : 'prod';

        $config = [
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  => $offi_info['appid'],              // AppID
            'secret'  => $offi_info['appsecret'],          // AppSecret
            'token'   => $offi_info['token'],              // Token
            'aes_key' => $offi_info['encoding_aes_key'],   // EncodingAESKey，兼容与安全模式下请一定要填写！！！

            /**
             * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
             * 使用自定义类名时，构造函数将会接收一个 `EasyWeChat\Kernel\Http\Response` 实例
             */
            'response_type' => 'array',

            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
             * path：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log' => [
                'default' => $log_channel, // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => '../runtime/easywechat/officialAccount.log',
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => '../runtime/easywechat/officialAccount.log',
                        'level' => 'info',
                    ],
                ],
            ],
        ];

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

        $config = [
            'app_id' => $mini_info['appid'],
            'secret' => $mini_info['appsecret'],

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
             * path：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log' => [
                'level' => 'debug',
                'file' => '../runtime/easywechat/miniProgram.log',
            ],
        ];

        $app = Factory::miniProgram($config);

        return $app;
    }
}
