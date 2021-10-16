<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口速率中间件
namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\admin\ApiRateCache;
use app\common\service\admin\SettingService;

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
            $admin_user_id = admin_user_id();
            $menu_url      = menu_url();

            if ($admin_user_id && $menu_url) {
                $count = ApiRateCache::get($admin_user_id, $menu_url);
                
                if ($count) {
                    if ($count >= $api_rate_num) {
                        ApiRateCache::del($admin_user_id, $menu_url);
                        exception('你的操作过于频繁', 429);
                    } else {
                        ApiRateCache::inc($admin_user_id, $menu_url);
                    }
                } else {
                    ApiRateCache::set($admin_user_id, $menu_url, $api_rate_time);
                }
            }
        }

        return $next($request);
    }
}
