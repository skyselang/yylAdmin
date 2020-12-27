<?php
/*
 * @Description  : Token中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-24
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use app\admin\service\ApiService;
use app\index\service\TokenService;

class TokenMiddleware
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

            $member_id = member_id();

            if (empty($member_id)) {
                exception('Requests Headers：MemberId must');
            }

            TokenService::verify($member_token, $member_id);
        }

        return $next($request);
    }
}
