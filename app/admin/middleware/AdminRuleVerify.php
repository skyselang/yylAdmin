<?php
/*
 * @Description  : 权限验证中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-25
 */

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\admin\service\AdminUserService;
use app\admin\service\AdminMenuService;

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
        $rule_url        = app('http')->getName() . '/' . $request->pathinfo();
        $api_white_list  = Config::get('admin.api_white_list');
        $rule_white_list = Config::get('admin.rule_white_list');
        $white_list      = array_merge($rule_white_list, $api_white_list);

        if (!in_array($rule_url, $white_list)) {
            $admin_user_id_key = Config::get('admin.admin_user_id_key');
            $admin_user_id     = $request->header($admin_user_id_key, '');
            $super_admin       = Config::get('admin.super_admin');

            if (!in_array($admin_user_id, $super_admin)) {
                $admin_user = AdminUserService::info($admin_user_id);
                
                if (empty($admin_user)) {
                    return error('登录已失效，请重新登录', '', 401);
                }

                if ($admin_user['is_prohibit'] == 1) {
                    return error('账号已被禁用', '请联系管理员', 401);
                }

                if (!in_array($rule_url, $admin_user['roles'])) {
                    return error('你没有权限操作', '未授权：' . $rule_url, 403);
                }
            }

            $menu_url = AdminMenuService::info(0);

            if (!in_array($rule_url, $menu_url)) {
                return error('接口地址错误', '不存在或未授权：' . $rule_url, 404);
            }
        }

        return $next($request);
    }
}
