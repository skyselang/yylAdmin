<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\service\system\SettingService;
use app\common\service\system\SmsLogService;

/**
 * 短信 https://gitee.com/skyselang/easy-sms
 */
class SmsUtils
{
    /**
     * 发送手机验证码
     * @param string $phone 手机
     */
    public static function captcha($phone)
    {
        $cache   = new CaptchaSmsCache();
        $captcha = $cache->get($phone);
        if (empty($captcha)) {
            $captcha  = mt_rand(100000, 999999);
            $content  = lang('您的验证码为') . '：<b>' . $captcha . '</b>。';
            $template = ''; //模板
            $data     = ['code' => $captcha];
            self::send($phone, $content, $template, $data);
            $cache->set($phone, $captcha);
        }
    }

    /**
     * 发送短信
     * @param string $phone    手机号码
     * @param string $content  文字内容，使用在以文字内容发送的平台
     * @param string $template 模板ID，使用在以模板ID来发送短信的平台
     * @param string $data     模板变量，使用在以模板ID来发送短信的平台
     * @param string $intcode  国际码，发送国际短信时需要
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
            $sms_log = self::logs($phone, $content, $template, $data, $intcode);

            $config  = config('easysms', $default);
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
            self::log($error);
            if ($sms_log['log_id'] ?? 0) {
                SmsLogService::edit($sms_log['log_id'], ['error' => $error]);
            }
            $debug = config('app.app_debug');
            if ($debug) {
                exception($error);
            } else {
                exception(lang('短信发送失败'));
            }
        }
    }

    /**
     * 短信异常日志
     * @param array $data
     */
    public static function log($data)
    {
        $log['type'] = 'sms';
        $log['data'] = $data;
        trace($log, 'log');
    }

    /**
     * 短信发送日志
     * @param array $data
     */
    public static function logs($phone, $content = '', $template = '', $data = [], $intcode = '')
    {
        $setting = SettingService::info();
        if ($setting['sms_log_switch']) {
            $data = [
                'intcode'  => $intcode,
                'phone'    => $phone,
                'template' => $template,
                'data'     => $data,
                'content'  => $content,
            ];
            return SmsLogService::add($data);
        }
    }
}
