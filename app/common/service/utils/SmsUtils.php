<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\utils;

use think\facade\Log;
use think\facade\Config;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\service\system\SettingService;

/**
 * 短信 https://gitee.com/skyselang/easy-sms
 */
class SmsUtils
{
    /**
     * 发送手机验证码
     *
     * @param string $phone 手机
     *
     * @return void
     */
    public static function captcha($phone)
    {
        $captcha = CaptchaSmsCache::get($phone);
        if (empty($captcha)) {
            $setting  = SettingService::info();
            $captcha  = mt_rand(100000, 999999);
            $content  = $setting['system_name'] . ', 您的验证码为：<b>' . $captcha . '</b>。';
            $template = '';                                                             //模板
            $data     = ['code' => $captcha];

            self::send($phone, $content, $template, $data);
            CaptchaSmsCache::set($phone, $captcha);
        }
    }

    /**
     * 发送短信
     *
     * @param string $phone    手机号码
     * @param string $content  文字内容，使用在以文字内容发送的平台
     * @param string $template 模板ID，使用在以模板ID来发送短信的平台
     * @param string $data     模板变量，使用在以模板ID来发送短信的平台
     * @param string $intcode  国际码，发送国际短信时需要
     *
     * @return void
     */
    public static function send($phone, $content = '', $template = '', $data = [], $intcode = '')
    {
        $default = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                // 默认可用的发送网关
                'gateways' => [],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => runtime_path() . '/easysms/' . date('Ym') . '/' . date('Ymd') . 'easysms.log',
                ],
            ]
        ];

        try {
            $config  = Config::get('easysms', $default);
            $easySms = new EasySms($config);
            if ($intcode) {
                // 国际短信
                $number = new PhoneNumber($phone, $intcode);
                $easySms->send($number, [
                    'content'  => $content,
                    'template' => $template,
                    'data'     => $data,
                ]);
            } else {
                // 国内短信
                $easySms->send($phone, [
                    'content'  => $content,
                    'template' => $template,
                    'data'     => $data,
                ]);
            }
        } catch (NoGatewayAvailableException $e) {
            $error = $e->getLastException()->getMessage();
            Log::write($error, 'easysms');
            $debug = Config::get('app.app_debug');
            if ($debug) {
                exception($error);
            } else {
                exception('短信发送失败');
            }
        }
    }
}
