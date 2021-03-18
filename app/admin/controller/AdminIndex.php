<?php
/*
 * @Description  : 控制台
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-18
 */

namespace app\admin\controller;

use app\admin\service\AdminIndexService;
use app\admin\service\UserService;
use think\facade\Request;

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
        $data = AdminIndexService::index();

        return success($data);
    }

    /**
     * 数据统计(用户)
     *
     * @method GET
     *
     * @return json
     */
    public function statisticUser()
    {
        $date = Request::param('date/a', []);

        $date_arr = [
            'total',
            'today',
            'yesterday',
            'thisweek',
            'lastweek',
            'thismonth',
            'lastmonth'
        ];

        $number = [];
        $active = [];
        foreach ($date_arr as $k => $v) {
            $number[$v] = UserService::staNumber($v);
            $active[$v] = UserService::staNumber($v, 'act');
        }
        $data['number']   = $number;
        $data['active']   = $active;
        $data['date_new'] = UserService::staDate($date);
        $data['date_act'] = UserService::staDate($date, 'act');

        return success($data);
    }
}
