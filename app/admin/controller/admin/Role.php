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
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\RoleService;
use app\common\service\admin\MenuService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("角色管理")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("620")
 */
class Role extends BaseController
{
    /**
     * @Apidoc\Title("角色列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\admin\RoleModel\listReturn", type="array", desc="角色列表")
     */
    public function list()
    {
        $where = $this->where([], 'admin_role_id,is_disable');

        $data = RoleService::list($where, $this->page(), $this->limit(), $this->order());
        $data['menu'] = MenuService::list('tree', [], [], 'admin_menu_id,menu_pid,menu_name,menu_url,is_unlogin,is_unauth');

        return success($data);
    }

    /**
     * @Apidoc\Title("角色信息")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\RoleModel\infoReturn")
     */
    public function info()
    {
        $param['admin_role_id'] = $this->param('admin_role_id/d', '');

        validate(RoleValidate::class)->scene('info')->check($param);

        $data = RoleService::info($param['admin_role_id']);
        if ($data['is_delete'] == 1) {
            exception('角色已被删除：' . $param['admin_role_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("角色添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\addParam")
     */
    public function add()
    {
        $param['admin_menu_ids']  = $this->param('admin_menu_ids/a', '');
        $param['admin_menu_pids'] = $this->param('admin_menu_pids/a', '');
        $param['role_name']       = $this->param('role_name/s', '');
        $param['role_desc']       = $this->param('role_desc/s', '');
        $param['role_sort']       = $this->param('role_sort/d', 250);

        validate(RoleValidate::class)->scene('add')->check($param);

        $data = RoleService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\editParam")
     */
    public function edit()
    {
        $param['admin_role_id']   = $this->param('admin_role_id/d', '');
        $param['admin_menu_ids']  = $this->param('admin_menu_ids/a', '');
        $param['admin_menu_pids'] = $this->param('admin_menu_pids/a', '');
        $param['role_name']       = $this->param('role_name/s', '');
        $param['role_desc']       = $this->param('role_desc/s', '');
        $param['role_sort']       = $this->param('role_sort/d', 250);

        validate(RoleValidate::class)->scene('edit')->check($param);

        $data = RoleService::edit($param['admin_role_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = $this->param('ids/a', '');

        validate(RoleValidate::class)->scene('dele')->check($param);

        $data = RoleService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

        validate(RoleValidate::class)->scene('disable')->check($param);

        $data = RoleService::edit($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色用户")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\UserModel\listReturn")
     */
    public function user()
    {
        $admin_role_id = $this->param('admin_role_id/s', '');

        validate(RoleValidate::class)->scene('user')->check(['admin_role_id' => $admin_role_id]);

        $where = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];
        $where = $this->where($where);

        $data = RoleService::user($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("角色用户解除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     */
    public function userRemove()
    {
        $param['admin_role_id'] = $this->param('admin_role_id/d', '');
        $param['admin_user_id'] = $this->param('admin_user_id/d', '');

        validate(RoleValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = RoleService::userRemove($param);

        return success($data);
    }
}
