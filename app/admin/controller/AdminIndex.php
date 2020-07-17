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
        $visit['thisweek']  = AdminVisitService::count('thisweek');
        $visit['lastweek']  = AdminVisitService::count('lastweek');
        $visit['thismonth'] = AdminVisitService::count('thismonth');
        $visit['lastmonth'] = AdminVisitService::count('lastmonth');

        $data['count'] = $visit;
        $data['line']  = AdminVisitService::line(30);
        $data['city']  = AdminVisitService::city(20);

        return success($data);
    }
}
