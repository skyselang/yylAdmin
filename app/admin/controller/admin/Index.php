<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 控制台控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\service\admin\IndexService;
use app\common\service\cms\ContentService;
use app\common\service\MemberService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("01")
 */
class Index
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = 'yylAdmin后端安装成功，感谢使用，如有帮助，欢迎Star！';

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
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

    /**
     * @Apidoc\Title("内容统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function cms()
    {
        $data = ContentService::statistics();

        return success($data);
    }
}
