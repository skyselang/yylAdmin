<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\member\LogService;
use app\common\service\member\SettingService;
use app\common\service\member\ApiService;

/**
 * 会员日志中间件
 */
class MemberLogMiddleware
{
    /**
     * 处理请求
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $setting = SettingService::info();
        // 会员日记记录是否开启
        if ($setting['log_switch']) {
            $member_id = member_id();
            $api_url   = api_url();

            // 未登录是否记录免登日志
            $log_unlogin = false;
            if (empty($member_id)) {
                if (app()->has('login_fail_member_id')) {
                    $member_id = app()->get('login_fail_member_id');
                    app()->delete('login_fail_member_id');
                }
                if ($setting['log_unlogin']) {
                    $log_unlogin = true;
                } else {
                    if (api_is_exist($api_url)) {
                        $api_info = ApiService::info($api_url);
                        if (in_array($api_info['log_type'], [SettingService::LOG_TYPE_LOGIN, SettingService::LOG_TYPE_REGISTER])) {
                            $log_unlogin = true;
                        }
                    }
                }
            }

            if ($member_id || $log_unlogin) {
                $response_data = $response->getData();
                if (isset($response_data['code'])) {
                    $member_log['response_code'] = $response_data['code'];
                }
                if (isset($response_data['msg'])) {
                    $member_log['response_msg'] = $response_data['msg'];
                } else {
                    if (isset($response_data['message'])) {
                        $member_log['response_msg'] = $response_data['message'];
                    }
                }
                $member_log['member_id']     = $member_id;
                $member_log['response_data'] = $response_data;
                $member_log['platform']      = member_platform();
                $member_log['application']   = member_application();
                LogService::add($member_log);
            }
        }


        return $response;
    }
}
