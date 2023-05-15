<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\validate\system\UserLogValidate;
use app\common\service\system\UserLogService;
use app\common\service\system\UserService;
use app\common\service\system\MenuService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("用户日志")
 * @Apidoc\Group("system")
 * @Apidoc\Sort("600")
 */
class UserLog extends BaseController
{
    /**
     * @Apidoc\Title("用户日志列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", children={
     *   @Apidoc\Returned(ref="app\common\model\system\UserLogModel", field="log_id,user_id,menu_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time"),
     *   @Apidoc\Returned(ref="app\common\model\system\UserModel", field="nickname,username"),
     *   @Apidoc\Returned(ref="app\common\model\system\MenuModel", field="menu_name,menu_url"),
     * })
     * @Apidoc\Returned("user", ref="app\common\model\system\UserModel", type="array", desc="用户列表", field="user_id,nickname,username")
     * @Apidoc\Returned("menu", ref="app\common\model\system\MenuModel", type="tree", desc="菜单树形", field="menu_id,menu_pid,menu_name")
     * @Apidoc\Returned("log_types", type="array", desc="日志类型")
     */
    public function list()
    {
        $where = $this->where(where_delete());

        $data = UserLogService::list($where, $this->page(), $this->limit(), $this->order());

        $data['user']  = UserService::list([where_delete()], 0, 0, [], 'user_id,nickname,username');
        $data['menu']  = MenuService::list('tree', [where_delete()], [], 'menu_id,menu_pid,menu_name');
        $data['exps']  = where_exps();
        $data['where'] = $where;

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志信息")
     * @Apidoc\Query(ref="app\common\model\system\UserLogModel", field="log_id")
     * @Apidoc\Returned(ref="app\common\model\system\UserLogModel")
     * @Apidoc\Returned(ref="app\common\model\system\UserModel", field="nickname,username")
     * @Apidoc\Returned(ref="app\common\model\system\MenuModel", field="menu_name,menu_url")
     */
    public function info()
    {
        $param = $this->params(['log_id/d' => 0]);

        validate(UserLogValidate::class)->scene('info')->check($param);

        $data = UserLogService::info($param['log_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(UserLogValidate::class)->scene('dele')->check($param);

        $data = UserLogService::dele($param['ids'], true);

        return success($data);
    }

    /**
     * @Apidoc\Title("用户日志清空")
     * @Apidoc\Method("POST")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     */
    public function clear()
    {
        $where = $this->where();

        $data = UserLogService::clear($where);

        return success($data);
    }
}
