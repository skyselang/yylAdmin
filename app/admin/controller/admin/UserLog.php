<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户日志控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\UserLogValidate;
use app\common\service\admin\UserLogService;
use app\common\service\admin\MenuService;
use app\common\service\admin\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户日志")
 * @Apidoc\Group("adminAuthority")
 * @Apidoc\Sort("640")
 */
class UserLog
{
    /**
     * @Apidoc\Title("用户日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\listParam")
     * @Apidoc\Param("log_type", require=false, default=" ")
     * @Apidoc\Param("response_code", require=false, default=" ")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\listReturn")
     * )
     */
    public function list()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $sort_field      = Request::param('sort_field/s', '');
        $sort_value      = Request::param('sort_value/s', '');
        $date_field      = Request::param('date_field/s', 'create_time');
        $date_value      = Request::param('date_value/a', '');
        $log_type        = Request::param('log_type/d', '');
        $response_code   = Request::param('response_code/s', '');
        $user_keyword    = Request::param('user_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $request_keyword = Request::param('request_keyword/s', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($response_code) {
            $where[] = ['response_code', '=', $response_code];
        }
        if ($user_keyword) {
            $admin_user     = UserService::equQuery($user_keyword);
            $admin_user_ids = array_column($admin_user, 'admin_user_id');
            $where[]        = ['admin_user_id', 'in', $admin_user_ids];
        }
        if ($menu_keyword) {
            $admin_menu     = MenuService::equQuery($menu_keyword);
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            $where[]        = ['admin_menu_id', 'in', $admin_menu_ids];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = UserLogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\infoReturn")
     */
    public function info()
    {
        $param['admin_user_log_id'] = Request::param('admin_user_log_id/d', '');

        validate(UserLogValidate::class)->scene('info')->check($param);

        $data = UserLogService::info($param['admin_user_log_id']);
        if ($data['is_delete'] == 1) {
            exception('日志已被删除：' . $param['admin_user_log_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\deleParam")
     */
    public function dele()
    {
        $param['admin_user_log_id'] = Request::param('admin_user_log_id/d', '');

        validate(UserLogValidate::class)->scene('dele')->check($param);

        $data = UserLogService::dele($param['admin_user_log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Param("admin_user_id", require=false,default=" ")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\username")
     * @Apidoc\Param("username", require=false,default=" ")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param("admin_menu_id", require=false,default=" ")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\menu_url")
     * @Apidoc\Param("menu_url", require=false,default=" ")
     * @Apidoc\Param("date_value", type="array", default=" ", desc="日期范围eg:['2022-02-22','2022-02-28']")
     */
    public function clear()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['username']      = Request::param('username/s', '');
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['menu_url']      = Request::param('menu_url/s', '');
        $param['date_value']    = Request::param('date_value/a', '');

        $data = UserLogService::clear($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志统计")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("type", type="string", default=" ", desc="类型")
     * @Apidoc\Param("date", type="array", default="[]", desc="日期范围eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Param("field", type="string", default=" ", desc="统计字段")
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
                $num[$v] = UserLogService::statNum($v);
            }
            $data['num'] = $num;
        } elseif ($type == 'date') {
            $data['date'] = UserLogService::statDate($date);
        } elseif ($type == 'field') {
            $data['field'] = UserLogService::statField($date, $field);
        } else {
            $num = [];
            foreach ($range as $k => $v) {
                $num[$v] = UserLogService::statNum($v);
            }

            $data['num']   = $num;
            $data['date']  = UserLogService::statDate($date);
            $data['field'] = UserLogService::statField($date, $field);
        }

        return success($data);
    }
}
