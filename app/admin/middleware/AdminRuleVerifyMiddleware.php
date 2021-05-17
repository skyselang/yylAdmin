<?php
/*
 * @Description  : 权限验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-17
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\common\cache\AdminUserCache;
use app\common\service\AdminMenuService;

class AdminRuleVerifyMiddleware
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
        $menu_url       = request_pathinfo();
        $api_whitelist  = Config::get('admin.api_whitelist');
        $rule_whitelist = Config::get('admin.rule_whitelist');
        $whitelist      = array_merge($rule_whitelist, $api_whitelist);

        if (!in_array($menu_url, $whitelist)) {
            $admin_user_id   = admin_user_id();
            $admin_super_ids = Config::get('admin.super_ids');

            if (!in_array($admin_user_id, $admin_super_ids)) {
                $admin_user = AdminUserCache::get($admin_user_id);

                if (empty($admin_user)) {
                    exception('登录已失效，请重新登录', 401);
                }

                if ($admin_user['is_disable'] == 1) {
                    exception('账号已禁用，请联系用户', 401);
                }

                if (!in_array($menu_url, $admin_user['roles'])) {
                    $admin_menu = AdminMenuService::info($menu_url);
                    exception('你没有权限操作：' . $admin_menu['menu_name'], 403);
                }
            }

            $admin_menu_url = AdminMenuService::list('url')['list'];

            if (!in_array($menu_url, $admin_menu_url)) {
                $msg   = '接口地址错误';
                $debug = Config::get('app.app_debug');
                if ($debug) {
                    $msg .= '：' . $menu_url;
                }
                exception($msg, 404);
            }
        }

        return $next($request);
    }
}
