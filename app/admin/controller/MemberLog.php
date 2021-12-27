<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员日志控制器
namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\MemberLogValidate;
use app\common\service\MemberLogService;
use app\common\service\MemberService;
use app\common\service\ApiService;
use app\common\model\MemberModel;
use app\common\model\ApiModel;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员日志")
 * @Apidoc\Group("adminMember")
 * @Apidoc\Sort("220")
 */
class MemberLog
{
    /**
     * @Apidoc\Title("会员日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\log_type")
     * @Apidoc\Param("log_type", require=false, default=" ")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="会员日志列表", 
     *     @Apidoc\Returned(ref="app\common\model\MemberLogModel\listReturn")
     * )
     */
    public function list()
    {
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $log_type     = Request::param('log_type/d', '');
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $date_field   = Request::param('date_field/s', 'create_time');
        $date_value   = Request::param('date_value/a', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_value) {
            if ($search_field == 'member_id' || $search_field == 'username') {
                $where_member[] = [$search_field, '=', $search_value];
                $where_member[] = ['is_delete', '=', 0];
                $member     = MemberService::list($where_member, 1, 9999, [], 'member_id');
                $member_ids = array_column($member['list'], 'member_id');
                $where[]    = ['member_id', 'in', $member_ids];
            } elseif ($search_field == 'api_url' || $search_field == 'api_name') {
                $where_api[] = [$search_field, '=', $search_value];
                $where_api[] = ['is_delete', '=', 0];
                $api     = ApiService::list($where_api, 1, 9999, [], 'api_id');
                $api_ids = array_column($api['list'], 'api_id');
                $where[] = ['api_id', 'in', $api_ids];
            } else {
                $where[] = [$search_field, '=', $search_value];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\id")
     * @Apidoc\Returned(ref="app\common\model\MemberLogModel\infoReturn")
     */
    public function info()
    {
        $param['member_log_id'] = Request::param('member_log_id/d', '');

        validate(MemberLogValidate::class)->scene('info')->check($param);

        $data = MemberLogService::info($param['member_log_id']);
        if ($data['is_delete'] == 1) {
            exception('会员日志已被删除：' . $param['member_log_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\deleParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(MemberLogValidate::class)->scene('dele')->check($param);

        $data = MemberLogService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\id")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\ApiModel\id")
     * @Apidoc\Param(ref="app\common\model\ApiModel\api_url")
     * @Apidoc\Param(ref="dateParam")
     */
    public function clear()
    {
        $member_id  = Request::param('member_id/s', '');
        $username   = Request::param('username/s', '');
        $api_id     = Request::param('api_id/s', '');
        $api_url    = Request::param('api_url/s', '');
        $date_value = Request::param('date_value/a', '');
        $clean      = Request::param('clean/b', false);

        $where = [];
        $member_ids = [];
        if ($member_id) {
            $member_ids = array_merge(explode(',', $member_id), $member_ids);
        }
        if ($username) {
            $exp_member = strstr($username, ',') ? 'in' : '=';
            $Member = new MemberModel();
            $member = $Member
                ->field('member_id')
                ->where('username', $exp_member, $username)
                ->select()
                ->toArray();
            if ($member) {
                $member_ids = array_merge(array_column($member, 'member_id'), $member_ids);
            }
        }
        if ($member_ids) {
            $where[] = ['member_id', 'in', $member_ids];
        }

        $api_ids = [];
        if ($api_id) {
            $api_ids = array_merge(explode(',', $api_id), $api_ids);
        }
        if ($api_url) {
            $exp_api = strstr($api_url, ',') ? 'in' : '=';
            $Api = new ApiModel();
            $api = $Api
                ->field('api_id')
                ->where('api_url', $exp_api, $api_url)
                ->select()
                ->toArray();
            if ($api) {
                $api_ids = array_merge(array_column($api, 'api_id'), $api_ids);
            }
        }
        if ($api_ids) {
            $where[] = ['api_id', 'in', $api_ids];
        }

        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = MemberLogService::clear($where, $clean);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志统计")
     * @Apidoc\Param("type", type="string", default=" ", desc="类型")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param("field", type="string", default=" ", desc="统计字段")
     */
    public function stat()
    {
        $type  = Request::param('type/s', '');
        $date  = Request::param('date/a', []);
        $field = Request::param('field/s', 'member');

        $data  = [];
        $dates = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'num') {
            $num = [];
            foreach ($dates as $v) {
                $num[$v] = MemberLogService::statNum($v);
            }
            $data['num'] = $num;
        } elseif ($type == 'date') {
            $data['date'] = MemberLogService::statDate($date);
        } elseif ($type == 'field') {
            $data['field'] = MemberLogService::statField($date, $field);
        } else {
            $num = [];
            foreach ($dates as $v) {
                $num[$v] = MemberLogService::statNum($v);
            }

            $data['num']   = $num;
            $data['date']  = MemberLogService::statDate($date);
            $data['field'] = MemberLogService::statField($date, $field);
        }

        return success($data);
    }
}
