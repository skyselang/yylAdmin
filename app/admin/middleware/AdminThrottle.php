<?php
/*
 * @Description  : 请求频率限制中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-22
 * @LastEditTime : 2020-10-23
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\common\cache\AdminThrottleCache;

class AdminThrottle
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
        $throttle = Config::get('admin.throttle');
        $number   = $throttle['number'];

        if ($number > 0) {
            $admin_user_id  = admin_user_id();
            $admin_menu_url = menu_url();

            if ($admin_user_id && $admin_menu_url) {
                $expire = $throttle['expire'];
                $count  = AdminThrottleCache::get($admin_user_id, $admin_menu_url);

                if ($count) {
                    if ($count >= $number) {
                        AdminThrottleCache::del($admin_user_id, $admin_menu_url);
                        exception('你的操作过于频繁', 429);
                    } else {
                        AdminThrottleCache::inc($admin_user_id, $admin_menu_url);
                    }
                } else {
                    AdminThrottleCache::set($admin_user_id, $admin_menu_url, $expire);
                }
            }
        }

        return $next($request);
    }
}
