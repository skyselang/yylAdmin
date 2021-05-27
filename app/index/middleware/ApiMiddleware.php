<?php
/*
 * @Description  : 接口中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-25
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;

class ApiMiddleware
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
        $debug = Config::get('app.app_debug');

        // 接口是否存在
        if (!api_is_exist()) {
            $msg = 'api url error';
            if ($debug) {
                $msg .= '：' . api_url();
            }
            exception($msg, 404);
        }

        // 接口是否已禁用
        if (api_is_disable()) {
            $msg = 'api is disable';
            if ($debug) {
                $msg .= '：' . api_url();
            }
            exception($msg, 404);
        }

        return $next($request);
    }
}
