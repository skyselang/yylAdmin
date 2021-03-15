<?php
/*
 * @Description  : 用户日志
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2021-03-08
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\UserLogValidate;
use app\admin\service\UserLogService;
use app\admin\service\UserService;
use app\admin\service\ApiService;

class UserLog
{
    /**
     * 用户日志列表
     *
     * @method GET
     * 
     * @return json
     */
    public function userLogList()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $log_type        = Request::param('log_type/d', '');
        $user_keyword    = Request::param('user_keyword/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $api_keyword     = Request::param('api_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($user_keyword) {
            $user     = UserService::etQuery($user_keyword);
            $user_ids = array_column($user, 'user_id');
            $where[]  = ['user_id', 'in', $user_ids];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($api_keyword) {
            $api     = ApiService::etQuery($api_keyword);
            $api_ids = array_column($api, 'api_id');
            $where[] = ['api_id', 'in', $api_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = UserLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * 用户日志信息
     *
     * @method GET
     * 
     * @return json
     */
    public function userLogInfo()
    {
        $param['user_log_id'] = Request::param('user_log_id/d', '');

        validate(UserLogValidate::class)->scene('user_log_id')->check($param);

        $data = UserLogService::info($param['user_log_id']);

        if ($data['is_delete'] == 1) {
            exception('用户日志已删除：' . $param['user_log_id']);
        }

        return success($data);
    }

    /**
     * 用户日志删除
     *
     * @method POST
     * 
     * @return json
     */
    public function userLogDele()
    {
        $param['user_log_id'] = Request::param('user_log_id/d', '');

        validate(UserLogValidate::class)->scene('user_log_dele')->check($param);

        $data = UserLogService::dele($param['user_log_id']);

        return success($data);
    }

    /**
     * 用户日志统计
     *
     * @method POST
     *
     * @return json
     */
    public function UserLogSta()
    {
        $type   = Request::param('type/s', '');
        $date   = Request::param('date/a', []);
        $region = Request::param('region/s', 'city');

        $data  = [];
        $dates = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'number') {
            $number = [];
            foreach ($dates as $k => $v) {
                $number[$v] = UserLogService::staNumber($v);
            }
            $data['number'] = $number;
        } elseif ($type == 'date') {
            $data['date'] = UserLogService::staDate($date);
        } elseif ($type == 'region') {
            $data['region'] = UserLogService::staRegion($date, $region);
        } else {
            $number = [];
            foreach ($dates as $k => $v) {
                $number[$v] = UserLogService::staNumber($v);
            }

            $data['number'] = $number;
            $data['date']   = UserLogService::staDate($date);
            $data['region'] = UserLogService::staRegion($date, $region);
        }

        return success($data);
    }
}
