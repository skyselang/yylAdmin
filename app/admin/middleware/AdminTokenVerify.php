<?php
/*
 * @Description  : token验证中间件
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-26
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminTokenService;


class AdminTokenVerify
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
        $app_name = app('http')->getName();
        $pathinfo = $request->pathinfo();
        $rule_url = $app_name . '/' . $pathinfo;
        $login_url = Config::get('admin.login_url');

        if ($rule_url != $login_url) {
            $admin_token = $request->header('Admin-Token', '');
            if (empty($admin_token)) {
                error('Admin-Token must');
            }

            $admin_user_id = $request->header('Admin-User-Id', '');
            if (empty($admin_user_id)) {
                error('Admin-User-Id must');
            }

            $AdminTokenService = new AdminTokenService;
            $AdminTokenService->verify($admin_token, $admin_user_id);
        }

        return $next($request);
    }
}
