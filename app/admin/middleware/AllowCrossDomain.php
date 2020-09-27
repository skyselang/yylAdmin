<?php
/*
 * @Description  : 跨域请求中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-08-27
 * @LastEditTime : 2020-09-27
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;

class AllowCrossDomain
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
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Content-type:application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, HEAD');

        if ($request->isOptions()) {
            return Response::create();
        }

        return $next($request);
    }
}
