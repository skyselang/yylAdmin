<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理控制器
namespace app\admin\controller\member;

use think\facade\Request;
use app\common\validate\member\MemberValidate;
use app\common\service\member\MemberService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员管理")
 * @Apidoc\Group("adminMember")
 * @Apidoc\Sort("210")
 */
class Member
{
    /**
     * @Apidoc\Title("会员列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\member\MemberModel\listReturn")
     * )
     */
    public function list()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['member_id', 'is_disable'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 0];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = MemberService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员信息")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\id")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\infoReturn")
     */
    public function info()
    {
        $param['member_id'] = Request::param('member_id/d', '');

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id']);

        unset($data['password'], $data['token']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\addParam")
     * @Apidoc\Param("username", type="string", mock="@string('lower', 6, 12)")
     * @Apidoc\Param("nickname", type="string", mock="@ctitle(6, 12)")
     * @Apidoc\Param("password", type="string", mock="@string('lower', 6)")
     * @Apidoc\Param("phone", type="string", mock="@phone")
     * @Apidoc\Param("email", type="string", mock="@email")
     */
    public function add()
    {
        $param['avatar_id']   = Request::param('avatar_id/d', 0);
        $param['username']    = Request::param('username/s', '');
        $param['nickname']    = Request::param('nickname/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['phone']       = Request::param('phone/s', '');
        $param['email']       = Request::param('email/s', '');
        $param['region_id']   = Request::param('region_id/d', 0);
        $param['remark']      = Request::param('remark/s', '');
        $param['sort']        = Request::param('sort/d', 250);
        $param['reg_channel'] = 6;
        $param['reg_type']    = 6;

        validate(MemberValidate::class)->scene('add')->check($param);

        $data = MemberService::add($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\editParam")
     */
    public function edit()
    {
        $param['member_id'] = Request::param('member_id/d', '');
        $param['avatar_id'] = Request::param('avatar_id/d', 0);
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['region_id'] = Request::param('region_id/d', 0);
        $param['remark']    = Request::param('remark/s', '');
        $param['sort']      = Request::param('sort/d', 250);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param['member_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(MemberValidate::class)->scene('dele')->check($param);

        $data = MemberService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改地区")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\setting\RegionModel\id")
     */
    public function region()
    {
        $param['ids']       = Request::param('ids/a', '');
        $param['region_id'] = Request::param('region_id/d', 0);

        validate(MemberValidate::class)->scene('region')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     */
    public function repwd()
    {
        $param['ids']      = Request::param('ids/a', '');
        $param['password'] = Request::param('password/s', '');

        validate(MemberValidate::class)->scene('repwd')->check($param);

        $data = MemberService::edit($param['ids'], ['password' => md5($param['password'])]);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(MemberValidate::class)->scene('disable')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员回收站")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *    @Apidoc\Returned(ref="app\common\model\member\MemberModel\listReturn")
     * )
     */
    public function recover()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');

        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['member_id', 'is_disable'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        $where[] = ['is_delete', '=', 1];
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = ['delete_time' => 'desc', 'create_time' => 'desc'];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = MemberService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员回收站恢复")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverReco()
    {
        $param['ids']       = Request::param('ids/a', '');
        $param['is_delete'] = 0;

        validate(MemberValidate::class)->scene('recoverReco')->check($param);

        $data = MemberService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员回收站删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function recoverDele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(MemberValidate::class)->scene('recoverDele')->check($param);

        $data = MemberService::dele($param['ids'], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Method("GET")
     * @Apidoc\Param("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Param("date", type="array", default="[]", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("count", type="object", desc="数量统计",
     *     @Apidoc\Returned("name", type="string", desc="名称"),
     *     @Apidoc\Returned("date", type="string", desc="时间"),
     *     @Apidoc\Returned("count", type="string", desc="数量"),
     *     @Apidoc\Returned("title", type="string", desc="title")
     * )
     * @Apidoc\Returned("echart", type="array", desc="图表数据",
     *     @Apidoc\Returned("type", type="string", desc="日期类型"),
     *     @Apidoc\Returned("date", type="array", desc="日期范围"),
     *     @Apidoc\Returned("title", type="string", desc="图表title.text"),
     *     @Apidoc\Returned("legend", type="array", desc="图表legend.data"),
     *     @Apidoc\Returned("xAxis", type="string", desc="图表xAxis.data"),
     *     @Apidoc\Returned("series", type="string", desc="图表series")
     * )
     */
    public function stat()
    {
        $type = Request::param('type/s', '');
        $date = Request::param('date/a', []);

        $data['count'] = MemberService::stat($type, $date, 'count');

        $stat = ['number', 'reg_channel', 'reg_type'];
        foreach ($stat as $v) {
            $echart[] = MemberService::stat($type, $date, $v);
        }
        $data['echart'] = $echart ?? [];

        return success($data);
    }
}
