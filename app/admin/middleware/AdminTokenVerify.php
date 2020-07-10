<?php
/*
 * @Description  : token验证中间件
 * @Author       : https://github.com/skyselang
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
        $api_url        = app('http')->getName() . '/' . $request->pathinfo();
        $api_white_list = Config::get('admin.api_white_list');

        if (!in_array($api_url, $api_white_list)) {
            $token_key   = Config::get('admin.token_key');
            $admin_token = $request->header($token_key, '');

            if (empty($admin_token)) {
                return error('AdminToken must');
            }

            $admin_user_id_key = Config::get('admin.admin_user_id_key');
            $admin_user_id     = $request->header($admin_user_id_key, '');

            if (empty($admin_user_id)) {
                return error('AdminUserId must');
            }

            AdminTokenService::verify($admin_token, $admin_user_id);
        }

        return $next($request);
    }
}
