<?php
/*
 * @Description  : 权限验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-08-14
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminMenuService;
use app\cache\AdminUserCache;

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
        $admin_menu_url  = admin_menu_url();
        $api_white_list  = Config::get('admin.api_white_list');
        $rule_white_list = Config::get('admin.rule_white_list');
        $white_list      = array_merge($rule_white_list, $api_white_list);

        if (!in_array($admin_menu_url, $white_list)) {
            $admin_user_id = admin_user_id();
            $super_admin   = Config::get('admin.super_admin');

            if (!in_array($admin_user_id, $super_admin)) {
                $admin_user = AdminUserCache::get($admin_user_id);

                if (empty($admin_user)) {
                    error('登录已失效，请重新登录', '', 401);
                }

                if ($admin_user['is_prohibit'] == 1) {
                    error('账号已禁用，请联系管理员', '', 401);
                }

                if (!in_array($admin_menu_url, $admin_user['roles'])) {
                    error('你没有权限操作', '未授权：' . $admin_menu_url, 403);
                }
            }

            $menu_url = AdminMenuService::info(0);

            if (!in_array($admin_menu_url, $menu_url)) {
                error('接口地址错误', '不存在或未授权：' . $admin_menu_url, 404);
            }
        }

        return $next($request);
    }
}
