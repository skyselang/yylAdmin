<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

use think\Response;
use think\middleware\throttle\CounterFixed;
use app\common\utils\ReturnCodeUtils;
use app\common\service\system\SettingService as SystemSettingService;
use app\common\service\member\SettingService as MemberSettingService;

// 接口速率设置：https://github.com/top-think/think-throttle
return [
    // 缓存键前缀，防止键值与其他应用冲突
    'prefix' => 'throttle_',
    // 缓存的键，true 表示使用来源ip；值 false 或 null 表示不限制
    'key' => function ($throttle, $request) {
        $app_name = app('http')->getName();
        if ($app_name == 'admin') {
            if (user_api_rate()) {
                return false;
            }
            $setting   = SystemSettingService::info();
            $rate_num  = $setting['api_rate_num'];
            $rate_time = $setting['api_rate_time'];
            if ($rate_num > 0 && $rate_time > 0) {
                $user_id  = user_id();
                $menu_url = menu_url();
                if ($user_id && $menu_url) {
                    if (!menu_is_unrate($menu_url)) {
                        $throttle->setRate($rate_num . '/' . $rate_time);
                        return 'admin_throttle:' . $user_id . ':' . $menu_url;
                    }
                }
            }
        } elseif ($app_name == 'api') {
            $setting   = MemberSettingService::info();
            $rate_num  = $setting['api_rate_num'];
            $rate_time = $setting['api_rate_time'];
            if ($rate_num > 0 && $rate_time > 0) {
                $member_id = member_id();
                $api_url   = api_url();
                if ($member_id && $api_url) {
                    if (!api_is_unrate($api_url)) {
                        $throttle->setRate($rate_num . '/' . $rate_time);
                        return 'api_throttle:' . $member_id . ':' . $api_url;
                    }
                }
            }
        }
        return false;
    },
    // 要被限制的请求类型, eg: GET POST PUT DELETE HEAD 等
    'visit_method' => ['GET', 'POST', 'PUT', 'DELETE'],
    // 设置访问频率，例如 '10/m' 指的是允许每分钟请求10次;'10/60'指允许每60秒请求10次。值null表示不限制，eg: null 10/m 20/h 300/d 200/300
    'visit_rate' => '60/m',
    /*
     * 设置节流算法，组件提供了四种算法：
     *  - CounterFixed： 计数固定窗口
     *  - CounterSlider: 滑动窗口
     *  - TokenBucket:   令牌桶算法
     *  - LeakyBucket:   漏桶限流算法
     */
    'driver_name' => CounterFixed::class,
    // 响应体中设置速率限制的头部信息，含义见：https://docs.github.com/en/rest/overview/resources-in-the-rest-api#rate-limiting
    'visit_enable_show_rate_limit' => false,
    // 访问受限时返回的响应
    'visit_fail_response' => function ($throttle, $request, $wait_seconds) {
        $app_name = app('http')->getName();
        if ($app_name == 'admin') {
            exception(lang('你的操作过于频繁，请在秒后重试', ['second' => $wait_seconds]), ReturnCodeUtils::FREQUENT_OPERATION);
        } elseif ($app_name == 'api') {
            exception(lang('太快了，请在秒后再试', ['second' => $wait_seconds]), ReturnCodeUtils::FREQUENT_OPERATION);
        }
        return Response::create('Too many requests, try again after ' . $wait_seconds . ' seconds.')->code(429);
    },
];
