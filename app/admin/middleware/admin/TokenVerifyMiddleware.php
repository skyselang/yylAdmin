<?php
/*
 * @Description  : Token验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-07-14
 */

namespace app\admin\middleware\admin;

use Closure;
use think\Request;
use think\Response;
use app\common\service\admin\TokenService;

class TokenVerifyMiddleware
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
        // 菜单是否无需登录
        if (!menu_is_unlogin()) {
            $admin_token = admin_token();

            if (empty($admin_token)) {
                exception('Requests Headers：AdminToken must');
            }

            // 用户Token验证
            TokenService::verify($admin_token);
        }

        return $next($request);
    }
}
