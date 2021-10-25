<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 角色管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\RoleService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("角色管理")
 * @Apidoc\Group("adminAuthority")
 * @Apidoc\Sort("620")
 */
class Role
{
    /**
     * @Apidoc\Title("角色列表")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\listParam")
     * @Apidoc\Param("role_name", require=false)
     * @Apidoc\Param("role_desc", require=false)
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="角色列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\RoleModel\listReturn")
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_value = Request::param('sort_value/s', '');
        $role_name  = Request::param('role_name/s', '');
        $role_desc  = Request::param('role_desc/s', '');

        $where = [];
        if ($role_name) {
            $where[] = ['role_name', 'like', '%' . $role_name . '%'];
        }
        if ($role_desc) {
            $where[] = ['role_desc', 'like', '%' . $role_desc . '%'];
        }

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = RoleService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色信息")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\RoleModel\infoReturn")
     */
    public function info()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

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
        $param['role_name']      = Request::param('role_name/s', '');
        $param['role_desc']      = Request::param('role_desc/s', '');
        $param['role_sort']      = Request::param('role_sort/d', 200);
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

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
        $param['admin_role_id']  = Request::param('admin_role_id/d', '');
        $param['role_name']      = Request::param('role_name/s', '');
        $param['role_desc']      = Request::param('role_desc/s', '');
        $param['role_sort']      = Request::param('role_sort/d', 200);
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

        validate(RoleValidate::class)->scene('edit')->check($param);

        $data = RoleService::edit($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("角色删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\deleParam")
     */
    public function dele()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(RoleValidate::class)->scene('dele')->check($param);

        $data = RoleService::dele($param['admin_role_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("角色是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\disableParam")
     */
    public function disable()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(RoleValidate::class)->scene('disable')->check($param);

        $data = RoleService::disable($param);

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
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_value    = Request::param('sort_value/s', '');
        $admin_role_id = Request::param('admin_role_id/s', '');

        validate(RoleValidate::class)->scene('user')->check(['admin_role_id' => $admin_role_id]);

        $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = RoleService::user($where, $page, $limit, $order);

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
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(RoleValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = RoleService::userRemove($param);

        return success($data);
    }
}
