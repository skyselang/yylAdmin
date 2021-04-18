<?php
/*
 * @Description  : 会员Token中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-04-10
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\ApiService;
use app\common\service\TokenService;

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
        $api_url   = request_pathinfo();
        $whitelist = ApiService::whiteList();

        if (!in_array($api_url, $whitelist)) {
            $member_token = member_token();

            if (empty($member_token)) {
                exception('Requests Headers：MemberToken must');
            }

            TokenService::verify($member_token);
        }

        return $next($request);
    }
}
