<?php
/*
 * @Description  : 日志中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2020-08-26
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminLogService;
use app\admin\service\AdminMenuService;

class AdminLog
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

        $is_admin_log = Config::get('admin.is_admin_log', false);

        if ($is_admin_log) {
            $admin_user_id = admin_user_id();

            if ($admin_user_id) {
                $menu_url   = admin_menu_url();
                $admin_menu = AdminMenuService::info($menu_url, true);

                $admin_log['admin_user_id']  = $admin_user_id;
                $admin_log['admin_menu_id']  = $admin_menu['admin_menu_id'];
                $admin_log['request_method'] = $request->method();
                $admin_log['request_ip']     = $request->ip();
                $admin_log['request_param']  = serialize($request->param());
                AdminLogService::add($admin_log);
            }
        }

        return $response;
    }
}
