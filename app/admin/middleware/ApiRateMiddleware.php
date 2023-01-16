<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\cache\system\ApiRateCache;
use app\common\service\system\SettingService;

/**
 * 接口速率中间件
 */
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
        $system        = SettingService::info();
        $api_rate_num  = $system['api_rate_num'];
        $api_rate_time = $system['api_rate_time'];

        if ($api_rate_num > 0 && $api_rate_time > 0) {
            $user_id  = user_id();
            $menu_url = menu_url();

            if ($user_id && $menu_url) {
                if (!menu_is_unrate($menu_url)) {
                    $count = ApiRateCache::get($user_id, $menu_url);
                    if ($count) {
                        if ($count >= $api_rate_num) {
                            if ($count >= $api_rate_num + 5) {
                                ApiRateCache::del($user_id, $menu_url);
                            } else {
                                ApiRateCache::inc($user_id, $menu_url);
                            }
                            exception('你的操作过于频繁', 429);
                        } else {
                            ApiRateCache::inc($user_id, $menu_url);
                        }
                    } else {
                        ApiRateCache::set($user_id, $menu_url, $api_rate_time);
                    }
                }
            }
        }

        return $next($request);
    }
}
