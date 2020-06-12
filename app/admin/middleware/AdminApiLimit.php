<?php
/*
 * @Description  : 接口访问频率限制
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-22
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\cache\AdminApiLimitCache;

class AdminApiLimit
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
        $api_limit = Config::get('admin.api_limit');
        $number    = $api_limit['number'];

        if ($number) {
            $admin_user_id_key = Config::get('admin.admin_user_id_key');
            $admin_user_id     = $request->header($admin_user_id_key, '');
            $admin_menu_url    = app('http')->getName() . '/' . $request->pathinfo();

            if ($admin_user_id && $admin_menu_url) {
                $expire = $api_limit['expire'];
                $limit  = AdminApiLimitCache::get($admin_user_id, $admin_menu_url);

                if ($limit) {
                    if ($limit >= $number) {
                        AdminApiLimitCache::del($admin_user_id, $admin_menu_url);
                        return error('你的操作过于频繁', '接口访问限制：' . $number . '次/' . $expire . '秒');
                    } else {
                        AdminApiLimitCache::inc($admin_user_id, $admin_menu_url);
                    }
                } else {
                    AdminApiLimitCache::set($admin_user_id, $admin_menu_url, $expire);
                }
            }
        }

        return $next($request);
    }
}
