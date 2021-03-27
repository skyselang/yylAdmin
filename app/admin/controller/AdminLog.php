<?php
/*
 * @Description  : 管理员日志
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminLogValidate;
use app\admin\service\AdminLogService;
use app\admin\service\AdminMenuService;
use app\admin\service\AdminAdminService;

class AdminLog
{
    /**
     * 管理员日志列表
     *
     * @method GET
     * 
     * @return json
     */
    public function logList()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $log_type        = Request::param('log_type/d', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $admin_keyword   = Request::param('admin_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);
        $response_code   = Request::param('response_code/s', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($admin_keyword) {
            $admin_admin     = AdminAdminService::etQuery($admin_keyword);
            $admin_admin_ids = array_column($admin_admin, 'admin_admin_id');
            $where[]         = ['admin_admin_id', 'in', $admin_admin_ids];
        }
        if ($menu_keyword) {
            $admin_menu     = AdminMenuService::etQuery($menu_keyword);
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            $where[]        = ['admin_menu_id', 'in', $admin_menu_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }
        if ($response_code) {
            $where[] = ['response_code', '=', $response_code];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * 管理员日志信息
     *
     * @method GET
     * 
     * @return json
     */
    public function logInfo()
    {
        $param['admin_log_id'] = Request::param('admin_log_id/d', '');

        validate(AdminLogValidate::class)->scene('log_id')->check($param);

        $data = AdminLogService::info($param['admin_log_id']);

        if ($data['is_delete'] == 1) {
            exception('日志已被删除：' . $param['admin_log_id']);
        }

        return success($data);
    }

    /**
     * 管理员日志删除
     *
     * @method POST
     * 
     * @return json
     */
    public function logDele()
    {
        $param['admin_log_id'] = Request::param('admin_log_id/d', '');

        validate(AdminLogValidate::class)->scene('log_dele')->check($param);

        $data = AdminLogService::dele($param['admin_log_id']);

        return success($data);
    }

    /**
     * 管理员日志统计
     *
     * @method POST
     *
     * @return json
     */
    public function LogStatistic()
    {
        $type   = Request::param('type/s', '');
        $date   = Request::param('date/a', []);
        $region = Request::param('region/s', 'city');

        $data  = [];
        $range = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'number') {
            $number = [];
            foreach ($range as $k => $v) {
                $number[$v] = AdminLogService::staNumber($v);
            }
            $data['number'] = $number;
        } elseif ($type == 'date') {
            $data['date'] = AdminLogService::staDate($date);
        } elseif ($type == 'region') {
            $data['region'] = AdminLogService::staRegion($date, $region);
        } else {
            $number = [];
            foreach ($range as $k => $v) {
                $number[$v] = AdminLogService::staNumber($v);
            }

            $data['number'] = $number;
            $data['date']   = AdminLogService::staDate($date);
            $data['region'] = AdminLogService::staRegion($date, $region);
        }

        return success($data);
    }
}
