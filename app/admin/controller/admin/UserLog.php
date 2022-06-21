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
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户日志")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("640")
 */
class UserLog
{
    /**
     * @Apidoc\Title("用户日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\listReturn")
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
        $log_type     = Request::param('log_type/d', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['admin_user_log_id', 'admin_user_id', 'username', 'admin_menu_id', 'menu_url', 'menu_name', 'response_code'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
            } else {
                $search_exp = strpos($search_value, ',') ? 'in' : 'like';
                $search_value = strpos($search_value, ',') ? $search_value : '%' . $search_value . '%';
            }
            $where[] = [$search_field, $search_exp, $search_value];
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
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(UserLogValidate::class)->scene('dele')->check($param);

        $data = UserLogService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志清除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     * @Apidoc\Param("admin_user_id", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\username")
     * @Apidoc\Param("username", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param("admin_menu_id", require=false, default="")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\menu_url")
     * @Apidoc\Param("menu_url", require=false, default="")
     * @Apidoc\Param(ref="dateParam")
     */
    public function clear()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $username      = Request::param('username/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $menu_url      = Request::param('menu_url/s', '');
        $date_value    = Request::param('date_value/a', '');

        $where = [];
        if ($admin_user_id) {
            $exp = strpos($admin_user_id, ',') ? 'in' : '=';
            $where[] = ['admin_user_id', $exp, $admin_user_id];
        }
        if ($username) {
            $exp = strpos($username, ',') ? 'in' : '=';
            $where[] = ['username', $exp, $username];
        }
        if ($admin_menu_id) {
            $exp = strpos($admin_menu_id, ',') ? 'in' : '=';
            $where[] = ['admin_menu_id', $exp, $admin_menu_id];
        }
        if ($menu_url) {
            $exp = strpos($menu_url, ',') ? 'in' : '=';
            $where[] = ['menu_url', $exp, $menu_url];
        }
        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = UserLogService::clear($where);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志清空")
     * @Apidoc\Method("POST")
     */
    public function clean()
    {
        $data = UserLogService::clear([], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志统计")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("type", type="string", default="", desc="类型")
     * @Apidoc\Param("date", type="array", default="[]", desc="日期范围eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Param("field", type="string", default="", desc="统计字段")
     */
    public function stat()
    {
        $type  = Request::param('type/s', 'day');
        $date  = Request::param('date/a', []);
        $field = Request::param('field/s', 'request_province');

        $data['count'] = UserLogService::stat($type, $date, 'count');
        $data['echart'][] = UserLogService::stat($type, $date, 'number');
        $data['field'] = UserLogService::statField($type, $date, $field);

        return success($data);
    }
}
