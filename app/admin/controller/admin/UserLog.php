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
use app\common\model\admin\MenuModel;
use app\common\model\admin\UserModel;
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
        $page         = Request::param('page/d', 1);
        $limit        = Request::param('limit/d', 10);
        $sort_field   = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
        $date_field   = Request::param('date_field/s', '');
        $date_value   = Request::param('date_value/a', '');
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');
        $log_type     = Request::param('log_type/d', '');

        $where = [];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($search_field && $search_value) {
            if (in_array($search_field, ['admin_user_log_id', 'admin_user_id', 'admin_menu_id'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } elseif (in_array($search_field, ['username'])) {
                $user_exp = strpos($search_value, ',') ? 'in' : '=';
                $user_where[] = [$search_field, $user_exp, $search_value];
                $UserModel = new UserModel();
                $admin_user_ids = $UserModel
                    ->field($UserModel->getPk())
                    ->where($user_where)
                    ->column($UserModel->getPk());
                $where[] = ['admin_user_id', 'in', $admin_user_ids];
            } elseif (in_array($search_field, ['menu_url', 'menu_name'])) {
                $menu_exp = strpos($search_value, ',') ? 'in' : '=';
                $menu_where[] = [$search_field, $menu_exp, $search_value];
                $MenuModel = new MenuModel();
                $admin_menu_ids = $MenuModel
                    ->field($MenuModel->getPk())
                    ->where($menu_where)
                    ->column($MenuModel->getPk());
                $where[] = ['admin_menu_id', 'in', $admin_menu_ids];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
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

        $data = UserLogService::list($where, $page, $limit, $order);

        return success($data, $where);
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
        $admin_user_id = Request::param('admin_user_id/d', '');
        $username      = Request::param('username/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $menu_url      = Request::param('menu_url/s', '');
        $date_value    = Request::param('date_value/a', '');
        $clean         = Request::param('clean/b', false);

        $where = [];
        $admin_user_ids = [];
        if ($admin_user_id) {
            $admin_user_ids = array_merge(explode(',', $admin_user_id), $admin_user_ids);
        }
        if ($username) {
            $user_exp = strstr($username, ',') ? 'in' : '=';
            $UserModel = new UserModel();
            $user_ids = $UserModel
                ->field($UserModel->getPk())
                ->where('username', $user_exp, $username)
                ->column($UserModel->getPk());
            if ($user_ids) {
                $admin_user_ids = array_merge($user_ids, $admin_user_ids);
            }
        }
        if ($admin_user_ids) {
            $where[] = ['admin_user_id', 'in', $admin_user_ids];
        }

        $admin_menu_ids = [];
        if ($admin_menu_id) {
            $admin_menu_ids = array_merge(explode(',', $admin_menu_id), $admin_menu_ids);
        }
        if ($menu_url) {
            $menu_exp = strstr($menu_url, ',') ? 'in' : '=';
            $MenuModel = new MenuModel();
            $menu_ids = $MenuModel
                ->field($MenuModel->getPk())
                ->where('menu_url', $menu_exp, $menu_url)
                ->column($MenuModel->getPk());
            if ($menu_ids) {
                $admin_menu_ids = array_merge($menu_ids, $admin_menu_ids);
            }
        }
        if ($admin_menu_ids) {
            $where[] = ['admin_menu_id', 'in', $admin_menu_ids];
        }

        if ($date_value) {
            $where[] = ['create_time', '>=', $date_value[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $date_value[1] . ' 23:59:59'];
        }

        $data = UserLogService::clear($where, $clean);

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
            foreach ($range as $v) {
                $num[$v] = UserLogService::statNum($v);
            }
            $data['num'] = $num;
        } elseif ($type == 'date') {
            $data['date'] = UserLogService::statDate($date);
        } elseif ($type == 'field') {
            $data['field'] = UserLogService::statField($date, $field);
        } else {
            $num = [];
            foreach ($range as $v) {
                $num[$v] = UserLogService::statNum($v);
            }

            $data['num']   = $num;
            $data['date']  = UserLogService::statDate($date);
            $data['field'] = UserLogService::statField($date, $field);
        }

        return success($data);
    }
}
