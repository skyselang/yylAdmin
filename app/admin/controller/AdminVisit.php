<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-23
 * @LastEditTime : 2020-08-14
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminVisitService;

class AdminVisit
{
    /**
     * 访问统计
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
     * 城市统计
     *
     * @method GET
     *
     * @return json
     */
    public function visitCity()
    {
        $date = Request::param('date/a', []);
        $top  = Request::param('top/d', 20);

        $data = AdminVisitService::visitCity($date, $top);

        return success($data);
    }

    /**
     * ISP统计
     *
     * @method GET
     *
     * @return json
     */
    public function visitIsp()
    {
        $date = Request::param('date/a', []);
        $top  = Request::param('top/d', 20);

        $data = AdminVisitService::visitIsp($date, $top);

        return success($data);
    }
}
