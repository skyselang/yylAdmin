<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;

/**
 * 接口Token中间件
 */
class ApiTokenMiddleware
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
        // 接口是否免登
        if (!api_is_unlogin()) {

            // 接口token获取
            $api_token = api_token();
            if (empty($api_token)) {
                exception('请登录', 401);
            }

            // 接口token验证
            api_token_verify($api_token);
        }

        return $next($request);
    }
}
