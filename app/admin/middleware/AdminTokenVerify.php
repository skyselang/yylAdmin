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

            $token_key = Config::get('admin.token_key');
            $admin_token = $request->header($token_key, '');
            if (empty($admin_token)) {
                return error('AdminToken must');
            }

            $admin_user_id_key = Config::get('admin.admin_user_id_key');
            $admin_user_id = $request->header($admin_user_id_key, '');
            if (empty($admin_user_id)) {
                return error('AdminUserId must');
            }

            AdminTokenService::verify($admin_token, $admin_user_id);
        }

        return $next($request);
    }
}
