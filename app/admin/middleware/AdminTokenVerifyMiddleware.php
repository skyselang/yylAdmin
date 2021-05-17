<?php
/*
 * @Description  : Token验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-17
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\common\service\AdminTokenService;

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
        $menu_url      = request_pathinfo();
        $api_whitelist = Config::get('admin.api_whitelist');

        if (!in_array($menu_url, $api_whitelist)) {
            $admin_token = admin_token();

            if (empty($admin_token)) {
                exception('Requests Headers：AdminToken must');
            }

            $admin_user_id = admin_user_id();

            if (empty($admin_user_id)) {
                exception('Requests Headers：AdminUserId must');
            }

            AdminTokenService::verify($admin_token, $admin_user_id);
        }

        return $next($request);
    }
}
