<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-06-02
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\AdminMenuValidate;
use app\common\validate\AdminRoleValidate;
use app\common\validate\AdminUserValidate;
use app\common\service\AdminMenuService;
use app\common\service\AdminUserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("菜单管理")
 * @Apidoc\Group("admin")
 * @Apidoc\Sort("10")
 */
class AdminMenu
{
    /**
     * @Apidoc\Title("菜单列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="树形列表",
     *          @Apidoc\Returned(ref="app\common\model\AdminMenuModel\list")
     *      )
     * )
     */
    public function list()
    {
        $data['list'] = AdminMenuService::tree();

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\AdminMenuModel\info")
     * )
     */
    public function info()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('info')->check($param);

        $data = AdminMenuService::info($param['admin_menu_id']);

        if ($data['is_delete'] == 1) {
            exception('菜单已被删除：' . $param['admin_menu_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\add")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function add()
    {
        $param['menu_pid']  = Request::param('menu_pid/d', 0);
        $param['menu_name'] = Request::param('menu_name/s', '');
        $param['menu_url']  = Request::param('menu_url/s', '');
        $param['menu_sort'] = Request::param('menu_sort/d', 200);
        $param['add_list'] = Request::param('add_list/b', false);
        $param['add_info'] = Request::param('add_info/b', false);
        $param['add_add']  = Request::param('add_add/b', false);
        $param['add_edit'] = Request::param('add_edit/b', false);
        $param['add_dele'] = Request::param('add_dele/b', false);

        validate(AdminMenuValidate::class)->scene('add')->check($param);

        $data = AdminMenuService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\edit")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function edit()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['menu_pid']      = Request::param('menu_pid/d', 0);
        $param['menu_name']     = Request::param('menu_name/s', '');
        $param['menu_url']      = Request::param('menu_url/s', '');
        $param['menu_sort']     = Request::param('menu_sort/d', 200);
        $param['add_list']      = Request::param('add_list/b', false);
        $param['add_info']      = Request::param('add_info/b', false);
        $param['add_add']       = Request::param('add_add/b', false);
        $param['add_edit']      = Request::param('add_edit/b', false);
        $param['add_dele']      = Request::param('add_dele/b', false);
        $param['edit_list']     = Request::param('edit_list/b', false);
        $param['edit_info']     = Request::param('edit_info/b', false);
        $param['edit_add']      = Request::param('edit_add/b', false);
        $param['edit_edit']     = Request::param('edit_edit/b', false);
        $param['edit_dele']     = Request::param('edit_dele/b', false);

        validate(AdminMenuValidate::class)->scene('edit')->check($param);

        $data = AdminMenuService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\dele")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function dele()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('dele')->check($param);

        $data = AdminMenuService::dele($param['admin_menu_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\disable")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function disable()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(AdminMenuValidate::class)->scene('disable')->check($param);

        $data = AdminMenuService::disable($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否无需权限")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\unauth")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function unauth()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['is_unauth']     = Request::param('is_unauth/d', 0);

        validate(AdminMenuValidate::class)->scene('unauth')->check($param);

        $data = AdminMenuService::unauth($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否无需登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\unlogin")
     * @Apidoc\Returned(ref="returnCode")
     */
    public function unlogin()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['is_unlogin']    = Request::param('is_unlogin/d', 0);

        validate(AdminMenuValidate::class)->scene('unlogin')->check($param);

        $data = AdminMenuService::unlogin($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\AdminRoleModel\role")
     *      )
     * )
     */
    public function role()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_type     = Request::param('sort_type/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('role')->check(['admin_menu_id' => $admin_menu_id]);

        $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminMenuService::role($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色解除")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id")
     * @Apidoc\Param(ref="app\common\model\AdminRoleModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function roleRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(AdminMenuValidate::class)->scene('id')->check($param);
        validate(AdminRoleValidate::class)->scene('id')->check($param);

        $data = AdminMenuService::roleRemove($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单用户")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id")
     * @Apidoc\Param(ref="app\common\model\AdminRoleModel\id")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表",
     *          @Apidoc\Returned(ref="app\common\model\AdminUserModel\user")
     *      )
     * )
     */
    public function user()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s ', '');
        $sort_type     = Request::param('sort_type/s', '');
        $admin_role_id = Request::param('admin_role_id/d', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        if ($admin_menu_id) {
            validate(AdminMenuValidate::class)->scene('user')->check(['admin_menu_id' => $admin_menu_id]);

            $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminUserService::list($where, $page, $limit, $order);

            return success($data);
        } else {
            validate(AdminRoleValidate::class)->scene('id')->check(['admin_role_id' => $admin_role_id]);

            $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminMenuService::user($where, $page, $limit, $order);

            return success($data);
        }
    }

    /**
     * @Apidoc\Title("菜单用户解除")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\AdminMenuModel\id")
     * @Apidoc\Param(ref="app\common\model\AdminUserModel\id")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnData")
     */
    public function userRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminMenuValidate::class)->scene('id')->check($param);
        validate(AdminUserValidate::class)->scene('id')->check($param);

        $data = AdminMenuService::userRemove($param);

        return success($data);
    }
}
