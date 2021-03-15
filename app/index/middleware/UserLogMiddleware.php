<?php
/*
 * @Description  : 日志中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-03-09
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\UserLogService;
use app\admin\service\ApiService;

class UserLogMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $api_url   = request_pathinfo();
        $whitelist = ApiService::whiteList();

        if (!in_array($api_url, $whitelist)) {
            $is_log = Config::get('index.is_log', false);

            if ($is_log) {
                $user_id = user_id();

                if ($user_id) {
                    $api_url = request_pathinfo();
                    $api     = ApiService::info($api_url);

                    $admin_log['user_id']        = $user_id;
                    $admin_log['api_id']         = $api['api_id'];
                    $admin_log['request_method'] = $request->method();
                    $admin_log['request_ip']     = $request->ip();
                    $admin_log['request_param']  = serialize($request->param());
                    UserLogService::add($admin_log);
                }
            }
        }
        
        return $response;
    }
}
