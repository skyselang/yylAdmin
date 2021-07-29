<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员Token中间件
namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;

class MemberTokenMiddleware
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
        // 接口是否无需登录
        if (!api_is_unlogin()) {
            
            // 会员token是否已设置
            if (!member_token_has()) {
                exception('Requests Headers：Token must');
            }

            // 会员token是否为空
            if (empty(member_token())) {
                exception('请登录', 401);
            }

            // 会员token验证
            member_token_verify();
        }

        return $next($request);
    }
}
