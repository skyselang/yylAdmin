<?php
/*
 * @Description  : 会员日志
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\MemberLogValidate;
use app\admin\service\MemberLogService;
use app\admin\service\MemberService;
use app\admin\service\ApiService;

class MemberLog
{
    /**
     * 会员日志列表
     *
     * @method GET
     * 
     * @return json
     */
    public function memberLogList()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $member_log_type = Request::param('member_log_type/d', '');
        $member_keyword  = Request::param('member_keyword/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $api_keyword     = Request::param('api_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $where = [];
        if ($member_log_type) {
            $where[] = ['member_log_type', '=', $member_log_type];
        }
        if ($member_keyword) {
            $member     = MemberService::etQuery($member_keyword);
            $member_ids = array_column($member, 'member_id');
            $where[]    = ['member_id', 'in', $member_ids];
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

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * 会员日志信息
     *
     * @method GET
     * 
     * @return json
     */
    public function memberLogInfo()
    {
        $param['member_log_id'] = Request::param('member_log_id/d', '');

        validate(MemberLogValidate::class)->scene('member_log_id')->check($param);

        $data = MemberLogService::info($param['member_log_id']);

        if ($data['is_delete'] == 1) {
            exception('会员日志已删除：' . $param['member_log_id']);
        }

        return success($data);
    }

    /**
     * 会员日志删除
     *
     * @method POST
     * 
     * @return json
     */
    public function memberLogDele()
    {
        $param['member_log_id'] = Request::param('member_log_id/d', '');

        validate(MemberLogValidate::class)->scene('member_log_dele')->check($param);

        $data = MemberLogService::dele($param['member_log_id']);

        return success($data);
    }

    /**
     * 会员日志统计
     *
     * @method POST
     *
     * @return json
     */
    public function MemberLogSta()
    {
        $type   = Request::param('type/s', '');
        $date   = Request::param('date/a', []);
        $region = Request::param('region/s', 'city');

        $data  = [];
        $dates = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'number') {
            $number = [];
            foreach ($dates as $k => $v) {
                $number[$v] = MemberLogService::staNumber($v);
            }
            $data['number'] = $number;
        } elseif ($type == 'date') {
            $data['date'] = MemberLogService::staDate($date);
        } elseif ($type == 'region') {
            $data['region'] = MemberLogService::staRegion($date, $region);
        } else {
            $number = [];
            foreach ($dates as $k => $v) {
                $number[$v] = MemberLogService::staNumber($v);
            }

            $data['number'] = $number;
            $data['date']   = MemberLogService::staDate($date);
            $data['region'] = MemberLogService::staRegion($date, $region);
        }

        return success($data);
    }
}
