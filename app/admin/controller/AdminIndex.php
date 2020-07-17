<?php
/*
 * @Description  : 首页
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-04-25
 */

namespace app\admin\controller;

use app\admin\service\AdminVisitService;

class AdminIndex
{
    /**
     * 首页
     *
     * @method GET
     * 
     * @return json
     */
    public function index()
    {

        return success();
    }

    /**
     * 访问量
     *
     * @method GET
     *
     * @return json
     */
    public function visit()
    {
        $visit['total']     = AdminVisitService::count('total');
        $visit['today']     = AdminVisitService::count('today');
        $visit['yesterday'] = AdminVisitService::count('yesterday');
        $visit['thisweek']  = AdminVisitService::count('thisWeek');
        $visit['lastweek']  = AdminVisitService::count('lastWeek');
        $visit['thismonth'] = AdminVisitService::count('thisMonth');
        $visit['lastmonth'] = AdminVisitService::count('lastMonth');

        $data['count'] = $visit;
        $data['date']  = AdminVisitService::date(30);
        $data['city']  = AdminVisitService::city(20);
        $data['isp']   = AdminVisitService::isp(20);

        return success($data);
    }
}
