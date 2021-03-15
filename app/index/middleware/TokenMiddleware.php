<?php
/*
 * @Description  : Token中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-03-09
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use app\admin\service\ApiService;
use app\admin\service\TokenService;

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
            $user_token = user_token();

            if (empty($user_token)) {
                exception('Requests Headers：UserToken must');
            }

            TokenService::verify($user_token);
        }

        return $next($request);
    }
}
