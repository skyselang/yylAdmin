<?php
/*
 * @Description  : 接口速率中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-22
 * @LastEditTime : 2021-05-27
 */

namespace app\index\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\ApiRateCache;
use app\common\service\SettingService;

class ApiRateMiddleware
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
        $set_api_info  = SettingService::apiInfo();
        $api_rate_num  = $set_api_info['api_rate_num'];
        $api_rate_time = $set_api_info['api_rate_time'];

        if ($api_rate_num > 0 && $api_rate_time > 0) {
            $member_id = member_id();
            $api_url   = api_url();

            if ($member_id && $api_url) {
                $count = ApiRateCache::get($member_id, $api_url);

                if ($count) {
                    if ($count >= $api_rate_num) {
                        ApiRateCache::del($member_id, $api_url);
                        exception('慢点，太快了！', 429);
                    } else {
                        ApiRateCache::inc($member_id, $api_url);
                    }
                } else {
                    ApiRateCache::set($member_id, $api_url, $api_rate_time);
                }
            }
        }

        return $next($request);
    }
}
