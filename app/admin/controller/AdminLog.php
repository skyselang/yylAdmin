<?php
/*
 * @Description  : 日志管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2020-09-27
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminLogValidate;
use app\admin\service\AdminLogService;
use app\admin\service\AdminMenuService;
use app\admin\service\AdminUserService;

class AdminLog
{
    /**
     * 日志列表
     *
     * @method GET
     * 
     * @return json
     */
    public function logList()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $type            = Request::param('type/d', 1);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $admin_user_id   = Request::param('admin_user_id/d', 0);
        $user_keyword    = Request::param('user_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $where = [];
        if ($type) {
            $where[] = ['admin_log_type', '=', $type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '=', $admin_user_id];
        }
        if ($user_keyword) {
            $admin_user    = AdminUserService::etQuery($user_keyword);
            $admin_user_id = array_column($admin_user, 'admin_user_id');
            $where[] = ['admin_user_id', 'in', $admin_user_id];
        }
        if ($menu_keyword) {
            $admin_menu    = AdminMenuService::likeQuery($menu_keyword);
            $admin_menu_id = array_column($admin_menu, 'admin_menu_id');
            $where[] = ['admin_menu_id', 'in', $admin_menu_id];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminLogService::list($where, $page, $limit, $field, $order);

        return success($data);
    }

    /**
     * 日志信息
     *
     * @method GET
     * 
     * @return json
     */
    public function logInfo()
    {
        $admin_log_id = Request::param('admin_log_id/d', '');

        validate(AdminLogValidate::class)->scene('admin_log_id')->check(['admin_log_id' => $admin_log_id]);

        $admin_log = AdminLogService::info($admin_log_id);

        return success($admin_log);
    }

    /**
     * 日志删除
     *
     * @method POST
     * 
     * @return json
     */
    public function logDele()
    {
        $admin_log_id = Request::param('admin_log_id/d', '');

        validate(AdminLogValidate::class)->scene('admin_log_id')->check(['admin_log_id' => $admin_log_id]);

        $data = AdminLogService::dele($admin_log_id);

        return success($data);
    }
}
