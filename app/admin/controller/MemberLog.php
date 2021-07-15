<?php
/*
 * @Description  : 会员日志
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-12-01
 * @LastEditTime : 2021-07-14
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\MemberLogValidate;
use app\common\service\MemberLogService;
use app\common\service\MemberService;
use app\common\service\ApiService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员日志")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("20")
 */
class MemberLog
{
    /**
     * @Apidoc\Title("会员日志列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\log")
     * @Apidoc\Param("request_keyword", type="string", default="", desc="请求地区/ip/isp")
     * @Apidoc\Param("menu_keyword", type="string", default="", desc="菜单链接/名称")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\MemberLogModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $log_type        = Request::param('log_type/d', '');
        $member_keyword  = Request::param('member_keyword/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $api_keyword     = Request::param('api_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($member_keyword) {
            $member     = MemberService::equQuery($member_keyword);
            $member_ids = array_column($member, 'member_id');
            $where[]    = ['member_id', 'in', $member_ids];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($api_keyword) {
            $api     = ApiService::equQuery($api_keyword);
            $api_ids = array_column($api, 'api_id');
            $where[] = ['api_id', 'in', $api_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\MemberLogModel\info")
     * )
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
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['member_log_id'] = Request::param('member_log_id/d', '');

        validate(MemberLogValidate::class)->scene('dele')->check($param);

        $data = MemberLogService::dele($param['member_log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\id")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\ApiModel\id")
     * @Apidoc\Param(ref="app\common\model\ApiModel\api_url")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */ 
    public function clear()
    {
        $param['member_id']  = Request::param('member_id/d', '');
        $param['username']   = Request::param('username/s', '');
        $param['api_id']     = Request::param('api_id/d', '');
        $param['api_url']    = Request::param('api_url/s', '');
        $param['date_range'] = Request::param('date_range/a', []);

        $data = MemberLogService::clear($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员日志统计")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("type", type="string", default="", desc="类型")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Param("field", type="string", default="", desc="统计字段")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */  
    public function stat()
    {
        $type   = Request::param('type/s', '');
        $date   = Request::param('date/a', []);
        $field = Request::param('field/s', 'member');

        $data  = [];
        $dates = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'num') {
            $num = [];
            foreach ($dates as $k => $v) {
                $num[$v] = MemberLogService::statNum($v);
            }
            $data['num'] = $num;
        } elseif ($type == 'date') {
            $data['date'] = MemberLogService::statDate($date);
        } elseif ($type == 'field') {
            $data['field'] = MemberLogService::statField($date, $field);
        } else {
            $num = [];
            foreach ($dates as $k => $v) {
                $num[$v] = MemberLogService::statNum($v);
            }

            $data['num']   = $num;
            $data['date']  = MemberLogService::statDate($date);
            $data['field'] = MemberLogService::statField($date, $field);
        }

        return success($data);
    }
}
