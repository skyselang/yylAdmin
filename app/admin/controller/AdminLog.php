<?php
/*
 * @Description  : 日志管理
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminLogService;
use app\admin\validate\AdminLogValidate;

class AdminLog
{
    /**
     * 日志列表
     *
     * @method GET
     * @return json
     */
    public function logList()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $order_field   = Request::param('order_field/s ', '');
        $order_type    = Request::param('order_type/s', '');
        $admin_user_id = Request::param('admin_user_id/d', '');
        $menu_url      = Request::param('menu_url/s', '');
        $insert_time   = Request::param('insert_time/a', '');

        $where = [];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '=', $admin_user_id];
        }
        if ($menu_url) {
            $where[] = ['menu_url', 'like', '%' . $menu_url . '%'];
        }
        if ($insert_time) {
            $where[] = ['insert_time', '>=', $insert_time[0] . ' 00:00:00'];
            $where[] = ['insert_time', '<=', $insert_time[1] . ' 23:59:59'];
        }

        $field = '';

        $order = [];
        if ($order_field && $order_type) {
            $order = [$order_field => $order_type];
        }

        $data = AdminLogService::list($where, $page, $limit, $field, $order);

        return success($data);
    }

    /**
     * 日志信息
     *
     * @method GET
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
