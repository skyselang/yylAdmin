<?php
/*
 * @Description  : 权限验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-10-29
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminMenuService;
use app\common\cache\AdminUserCache;

class AdminRuleVerify
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
        $admin_menu_url  = request_pathinfo();
        $api_white_list  = Config::get('admin.api_white_list');
        $rule_white_list = Config::get('admin.rule_white_list');
        $white_list      = array_merge($rule_white_list, $api_white_list);

        if (!in_array($admin_menu_url, $white_list)) {
            $admin_user_id = admin_user_id();
            $admin_ids     = Config::get('admin.admin_ids');

            if (!in_array($admin_user_id, $admin_ids)) {
                $admin_user = AdminUserCache::get($admin_user_id);

                if (empty($admin_user)) {
                    exception('登录已失效，请重新登录', 401);
                }

                if ($admin_user['is_disable'] == 1) {
                    exception('账号已禁用，请联系管理员', 401);
                }

                if (!in_array($admin_menu_url, $admin_user['roles'])) {
                    exception('你没有权限操作', 403);
                }
            }

            $menu_url = AdminMenuService::list('url')['list'];

            if (!in_array($admin_menu_url, $menu_url)) {
                exception('接口地址错误', 404);
            }
        }

        return $next($request);
    }
}
