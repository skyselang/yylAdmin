<?php
/*
 * @Description  : 日志管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2021-05-27
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminUserLogValidate;
use app\common\service\AdminUserLogService;
use app\common\service\AdminMenuService;
use app\common\service\AdminUserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("日志管理")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("40")
 */
class AdminUserLog
{
    /**
     * @Apidoc\Title("日志管理列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\AdminUserLogModel\log")
     * @Apidoc\Param("request_keyword", type="string", default="", desc="请求地区/ip/isp")
     * @Apidoc\Param("menu_keyword", type="string", default="", desc="菜单链接/名称")
     * @Apidoc\Param("create_time", type="array", default="[]", desc="开始与结束日期eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\AdminUserLogModel\list")
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
        $request_keyword = Request::param('request_keyword/s', '');
        $user_keyword    = Request::param('user_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);
        $response_code   = Request::param('response_code/s', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($user_keyword) {
            $admin_user     = AdminUserService::equQuery($user_keyword);
            $admin_user_ids = array_column($admin_user, 'admin_user_id');
            $where[]        = ['admin_user_id', 'in', $admin_user_ids];
        }
        if ($menu_keyword) {
            $admin_menu     = AdminMenuService::equQuery($menu_keyword);
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            $where[]        = ['admin_menu_id', 'in', $admin_menu_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }
        if ($response_code) {
            $where[] = ['response_code', '=', $response_code];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminUserLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志管理信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserLogModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\AdminUserLogModel\info")
     * )
     */ 
    public function info()
    {
        $param['admin_user_log_id'] = Request::param('admin_user_log_id/d', '');

        validate(AdminUserLogValidate::class)->scene('info')->check($param);

        $data = AdminUserLogService::info($param['admin_user_log_id']);

        if ($data['is_delete'] == 1) {
            exception('日志已被删除：' . $param['admin_user_log_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("日志管理删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserLogModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function dele()
    {
        $param['admin_user_log_id'] = Request::param('admin_user_log_id/d', '');

        validate(AdminUserLogValidate::class)->scene('dele')->check($param);

        $data = AdminUserLogService::dele($param['admin_user_log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志管理清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\id", require=false)
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\username", require=false)
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id", require=false)
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\menu_url", require=false)
     * @Apidoc\Param("date_range", type="array", default="[]", desc="日期范围eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */ 
    public function clear()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['username']      = Request::param('username/s', '');
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['menu_url']      = Request::param('menu_url/s', '');
        $param['date_range']    = Request::param('date_range/a', []);

        $data = AdminUserLogService::clear($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志管理统计")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("type", type="string", default="", desc="类型")
     * @Apidoc\Param("date", type="array", default="[]", desc="日期范围eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Param("field", type="string", default="", desc="统计字段")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */  
    public function stat()
    {
        $type  = Request::param('type/s', '');
        $date  = Request::param('date/a', []);
        $field = Request::param('field/s', 'user');

        $data  = [];
        $range = ['total', 'today', 'yesterday', 'thisweek', 'lastweek', 'thismonth', 'lastmonth'];

        if ($type == 'num') {
            $num = [];
            foreach ($range as $k => $v) {
                $num[$v] = AdminUserLogService::statNum($v);
            }
            $data['num'] = $num;
        } elseif ($type == 'date') {
            $data['date'] = AdminUserLogService::statDate($date);
        } elseif ($type == 'field') {
            $data['field'] = AdminUserLogService::statField($date, $field);
        } else {
            $num = [];
            foreach ($range as $k => $v) {
                $num[$v] = AdminUserLogService::statNum($v);
            }

            $data['num']   = $num;
            $data['date']  = AdminUserLogService::statDate($date);
            $data['field'] = AdminUserLogService::statField($date, $field);
        }

        return success($data);
    }
}
