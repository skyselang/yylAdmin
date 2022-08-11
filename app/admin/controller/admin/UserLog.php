<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\admin;

use app\common\BaseController;
use app\common\validate\admin\UserLogValidate;
use app\common\service\admin\UserLogService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户日志")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("640")
 */
class UserLog extends BaseController
{
    /**
     * @Apidoc\Title("用户日志列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\UserLogModel\listReturn", type="array", desc="日志列表")
     */
    public function list()
    {
        $log_type = $this->param('log_type/d', '');

        $where = [];
        if ($log_type !== '') {
            $where[] = ['log_type', '=', $log_type];
        }
        $where = $this->where($where, 'admin_user_log_id,admin_user_id,username,admin_menu_id,menu_url,menu_name,response_code');

        $data = UserLogService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志信息")
     * @Apidoc\Param(ref="app\common\model\admin\UserLogModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\UserLogModel\infoReturn")
     */
    public function info()
    {
        $param['admin_user_log_id'] = $this->param('admin_user_log_id/d', '');

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
        $param['ids'] = $this->param('ids/a', '');

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
        $admin_user_id = $this->param('admin_user_id/d', '');
        $username      = $this->param('username/s', '');
        $admin_menu_id = $this->param('admin_menu_id/d', '');
        $menu_url      = $this->param('menu_url/s', '');
        $date_value    = $this->param('date_value/a', '');

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
        $type  = $this->param('type/s', 'day');
        $date  = $this->param('date/a', []);
        $field = $this->param('field/s', 'request_province');

        $data['count'] = UserLogService::stat($type, $date, 'count');
        $data['echart'][] = UserLogService::stat($type, $date, 'number');
        $data['field'] = UserLogService::statField($type, $date, $field);

        return success($data);
    }
}
