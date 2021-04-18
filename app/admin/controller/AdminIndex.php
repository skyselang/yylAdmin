<?php
/*
 * @Description  : 控制台
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-04-10
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\service\AdminIndexService;
use app\common\service\MemberService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("admin")
 */
class AdminIndex
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function index()
    {
        $data = AdminIndexService::index();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function member()
    {
        $date = Request::param('date/a', []);

        $range = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        $number = [];
        $active = [];
        foreach ($range as $k => $v) {
            $number[$v] = MemberService::statNum($v);
            $active[$v] = MemberService::statNum($v, 'act');
        }
        $data['number']   = $number;
        $data['active']   = $active;
        $data['date_new'] = MemberService::statDate($date);
        $data['date_act'] = MemberService::statDate($date, 'act');

        return success($data);
    }
}
