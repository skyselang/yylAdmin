<?php
/*
 * @Description  : 接口访问频率限制
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-22
 * @LastEditTime : 2020-08-14
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
        $limit_num = $api_limit['number'];

        if ($limit_num) {
            $admin_user_id  = admin_user_id();
            $admin_menu_url = admin_menu_url();

            if ($admin_user_id && $admin_menu_url) {
                $expire = $api_limit['expire'];
                $number = AdminApiLimitCache::get($admin_user_id, $admin_menu_url);

                if ($number) {
                    if ($number >= $limit_num) {
                        AdminApiLimitCache::del($admin_user_id, $admin_menu_url);
                        error('你的操作过于频繁', '接口访问限制：' . $limit_num . '次/' . $expire . '秒');
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
