<?php
/*
 * @Description  : 日志中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-25
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\MemberLogService;
use app\admin\service\ApiService;

class MemberLogMiddleware
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

        $is_log = Config::get('index.is_log', false);

        if ($is_log) {
            $member_id = member_id();

            if ($member_id) {
                $api_url = request_pathinfo();
                $api     = ApiService::info($api_url);

                $admin_log['member_id']      = $member_id;
                $admin_log['api_id']         = $api['api_id'];
                $admin_log['request_method'] = $request->method();
                $admin_log['request_ip']     = $request->ip();
                $admin_log['request_param']  = serialize($request->param());
                MemberLogService::add($admin_log);
            }
        }

        return $response;
    }
}
