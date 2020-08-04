<?php
/*
 * @Description  : 访问统计
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-07-23
 * @LastEditTime : 2020-08-03
 */

namespace app\admin\controller;

use app\admin\service\AdminVisitService;
use think\facade\Request;

class AdminVisit
{
    /**
     * 访问统计
     *
     * @method GET
     *
     * @return json
     */
    public function visit()
    {
        $date_date = Request::param('date_date/a', []);
        $city_date = Request::param('city_date/a', []);
        $isp_date  = Request::param('isp_date/a', []);

        $visit['total']     = AdminVisitService::count('total');
        $visit['today']     = AdminVisitService::count('today');
        $visit['yesterday'] = AdminVisitService::count('yesterday');
        $visit['thisweek']  = AdminVisitService::count('thisWeek');
        $visit['lastweek']  = AdminVisitService::count('lastWeek');
        $visit['thismonth'] = AdminVisitService::count('thisMonth');
        $visit['lastmonth'] = AdminVisitService::count('lastMonth');

        $data['count'] = $visit;
        $data['date']  = AdminVisitService::date($date_date);
        $data['city']  = AdminVisitService::city($city_date, 20);
        $data['isp']   = AdminVisitService::isp($isp_date, 20);

        return success($data);
    }
}
