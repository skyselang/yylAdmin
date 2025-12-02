<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\system\UserLogService;
use app\common\service\system\SettingService;
use app\common\service\system\MenuService;

/**
 * 用户日志中间件
 */
class UserLogMiddleware
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
        // 用户日志记录是否开启
        if ($setting['log_switch']) {
            $user_id  = user_id();
            $menu_url = menu_url();

            // 未登录是否记录免登日志
            $log_unlogin = false;
            if (empty($user_id)) {
                // 尝试从应用容器获取登录失败时设置的 user_id
                if (app()->has('login_fail_user_id')) {
                    $user_id = app()->get('login_fail_user_id');
                    // 清理应用容器中的登录失败 user_id，避免影响后续请求
                    app()->delete('login_fail_user_id');
                }
                if ($setting['log_unlogin']) {
                    $log_unlogin = true;
                } else {
                    if (menu_is_exist($menu_url)) {
                        $menu_info = MenuService::info($menu_url);
                        if ($menu_info['log_type'] === SettingService::LOG_TYPE_LOGIN) {
                            $log_unlogin = true;
                        }
                    }
                }
            }

            if ($user_id || $log_unlogin) {
                $response_data = $response->getData();
                if (isset($response_data['code'])) {
                    $user_log['response_code'] = $response_data['code'];
                }
                if (isset($response_data['msg'])) {
                    $user_log['response_msg'] = $response_data['msg'];
                } else {
                    if (isset($response_data['message'])) {
                        $user_log['response_msg'] = $response_data['message'];
                    }
                }
                $user_log['user_id']       = $user_id;
                $user_log['response_data'] = $response_data;
                UserLogService::add($user_log);
            }
        }

        return $response;
    }
}
