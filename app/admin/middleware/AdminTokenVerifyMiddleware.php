<?php
/*
 * @Description  : Token验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-12-24
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminTokenService;

class AdminTokenVerifyMiddleware
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
        $menu_url       = request_pathinfo();
        $api_white_list = Config::get('admin.api_white_list');

        if (!in_array($menu_url, $api_white_list)) {
            $admin_token = admin_token();

            if (empty($admin_token)) {
                exception('Requests Headers：AdminToken must');
            }

            $admin_admin_id = admin_admin_id();

            if (empty($admin_admin_id)) {
                exception('Requests Headers：AdminAdminId must');
            }

            AdminTokenService::verify($admin_token, $admin_admin_id);
        }

        return $next($request);
    }
}
