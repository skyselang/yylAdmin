<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-23
 * @LastEditTime : 2020-09-27
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminVisitService;

class AdminVisit
{
    /**
     * 数量统计
     *
     * @method GET
     *
     * @return json
     */
    public function visitCount()
    {
        $data['total']     = AdminVisitService::visitCount('total');
        $data['today']     = AdminVisitService::visitCount('today');
        $data['yesterday'] = AdminVisitService::visitCount('yesterday');
        $data['thisweek']  = AdminVisitService::visitCount('thisWeek');
        $data['lastweek']  = AdminVisitService::visitCount('lastWeek');
        $data['thismonth'] = AdminVisitService::visitCount('thisMonth');
        $data['lastmonth'] = AdminVisitService::visitCount('lastMonth');

        return success($data);
    }

    /**
     * 日期统计
     *
     * @method GET
     *
     * @return json
     */
    public function visitDate()
    {
        $date = Request::param('date/a', []);

        $data = AdminVisitService::visitDate($date);

        return success($data);
    }

    /**
     * 访问统计
     *
     * @method GET
     *
     * @return json
     */
    public function visitStats()
    {
        $date  = Request::param('date/a', []);
        $stats = Request::param('stats/s', 'city');
        $top   = Request::param('top/d', 20);

        $data = AdminVisitService::visitStats($date, $stats, $top);

        return success($data);
    }
}
