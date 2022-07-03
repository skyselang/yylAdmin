<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口速率中间件
namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\setting\ApiRateCache;
use app\common\service\setting\SettingService;

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
        $setting       = SettingService::info();
        $api_rate_num  = $setting['api_rate_num'];
        $api_rate_time = $setting['api_rate_time'];

        if ($api_rate_num > 0 && $api_rate_time > 0) {
            $member_id = member_id();
            $api_url   = api_url();

            if ($member_id && $api_url) {
                if (!api_is_unrate($api_url)) {
                    $count = ApiRateCache::get($member_id, $api_url);

                    if ($count) {
                        if ($count >= $api_rate_num) {
                            exception('慢点，太快了！', 429);
                        } else {
                            ApiRateCache::inc($member_id, $api_url);
                        }
                    } else {
                        ApiRateCache::set($member_id, $api_url, $api_rate_time);
                    }
                }
            }
        }

        return $next($request);
    }
}
