<?php
/*
 * @Description  : 接口中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-04-10
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Env;
use app\common\service\ApiService;
use app\common\cache\MemberCache;

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
        $api_url   = request_pathinfo();
        $whitelist = ApiService::whiteList();

        if (!in_array($api_url, $whitelist)) {
            $member_id = member_id();
            $member    = MemberCache::get($member_id);

            if (empty($member)) {
                exception('登录已失效，请重新登录', 401);
            }

            if ($member['is_disable'] == 1) {
                exception('账号已禁用，请联系客服', 401);
            }

            if ($member['is_delete'] == 1) {
                exception('账号已注销，请重新注册', 401);
            }

            $api_list = ApiService::list('url')['list'];

            if (!in_array($api_url, $api_list)) {
                $msg   = '接口地址错误';
                $debug = Env::get('app_debug');
                if ($debug) {
                    $msg .= '：' . $api_url;
                }
                exception($msg, 404);
            }
        }

        return $next($request);
    }
}
