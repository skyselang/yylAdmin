<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\service\member\MemberService;

/**
 * @Apidoc\Title("lang(会员统计)")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Statistic extends BaseController
{
    /**
     * @Apidoc\Title("lang(会员统计)")
     * @Apidoc\Method("GET")
     * @Apidoc\Query(ref={MemberService::class,"statistic"})
     * @Apidoc\Returned(ref={MemberService::class,"statistic"})
     */
    public function statistic()
    {
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);

        $data['count'] = MemberService::statistic($type, $date, 'count');

        $field = ['number', 'application', 'platform'];
        foreach ($field as $v) {
            $echart[] = MemberService::statistic($type, $date, $v);
        }
        $data['echart'] = $echart ?? [];

        return success($data);
    }
}
