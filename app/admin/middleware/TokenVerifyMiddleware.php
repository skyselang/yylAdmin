<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// Token验证中间件
namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;

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
        // 菜单是否免登
        if (!menu_is_unlogin()) {

            // 用户token获取
            $admin_token = admin_token();
            if (empty($admin_token)) {
                exception('请登录', 401);
            }

            // 用户Token验证
            admin_token_verify($admin_token);
        }

        return $next($request);
    }
}
