<?php
/*
 * @Description  : 接口速率中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-22
 * @LastEditTime : 2021-05-27
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\AdminApiRateCache;
use app\common\service\AdminSettingService;

class AdminApiRateMiddleware
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
        $set_api_info  = AdminSettingService::apiInfo();
        $api_rate_num  = $set_api_info['api_rate_num'];
        $api_rate_time = $set_api_info['api_rate_time'];

        if ($api_rate_num > 0 && $api_rate_time > 0) {
            $admin_user_id = admin_user_id();
            $menu_url      = menu_url();

            if ($admin_user_id && $menu_url) {
                $count = AdminApiRateCache::get($admin_user_id, $menu_url);
                
                if ($count) {
                    if ($count >= $api_rate_num) {
                        AdminApiRateCache::del($admin_user_id, $menu_url);
                        exception('你的操作过于频繁', 429);
                    } else {
                        AdminApiRateCache::inc($admin_user_id, $menu_url);
                    }
                } else {
                    AdminApiRateCache::set($admin_user_id, $menu_url, $api_rate_time);
                }
            }
        }

        return $next($request);
    }
}
