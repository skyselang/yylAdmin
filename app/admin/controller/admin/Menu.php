<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 菜单管理控制器
namespace app\admin\controller\admin;

use think\facade\Request;
use app\common\validate\admin\MenuValidate;
use app\common\validate\admin\RoleValidate;
use app\common\validate\admin\UserValidate;
use app\common\service\admin\MenuService;
use app\common\service\admin\UserService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("菜单管理")
 * @Apidoc\Group("adminAuth")
 * @Apidoc\Sort("610")
 */
class Menu
{
    /**
     * @Apidoc\Title("菜单列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Returned("list", type="array", desc="列表",
     *     @Apidoc\Returned(ref="app\common\model\admin\MenuModel\listReturn")
     * )
     */
    public function list()
    {
        $search_field = Request::param('search_field/s', '');
        $search_value = Request::param('search_value/s', '');

        $where = [];
        if ($search_field && $search_value !== '') {
            if (in_array($search_field, ['admin_menu_id', 'menu_pid', 'is_unlogin', 'is_unauth', 'is_disable'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }

        if ($where) {
            $data['list'] = MenuService::list('list', $where);
        } else {
            $data['list'] = MenuService::list('tree', $where);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单信息")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\MenuModel\infoReturn")
     */
    public function info()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(MenuValidate::class)->scene('info')->check($param);

        $data = MenuService::info($param['admin_menu_id']);
        if ($data['is_delete'] == 1) {
            exception('菜单已被删除：' . $param['admin_menu_id']);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\addParam")
     */
    public function add()
    {
        $param['menu_pid']  = Request::param('menu_pid/d', 0);
        $param['menu_name'] = Request::param('menu_name/s', '');
        $param['menu_url']  = Request::param('menu_url/s', '');
        $param['menu_sort'] = Request::param('menu_sort/d', 250);
        $param['add_list']  = Request::param('add_list/b', false);
        $param['add_info']  = Request::param('add_info/b', false);
        $param['add_add']   = Request::param('add_add/b', false);
        $param['add_edit']  = Request::param('add_edit/b', false);
        $param['add_dele']  = Request::param('add_dele/b', false);

        validate(MenuValidate::class)->scene('add')->check($param);

        $data = MenuService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\editParam")
     */
    public function edit()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['menu_pid']      = Request::param('menu_pid/d', 0);
        $param['menu_name']     = Request::param('menu_name/s', '');
        $param['menu_url']      = Request::param('menu_url/s', '');
        $param['menu_sort']     = Request::param('menu_sort/d', 250);
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

        validate(MenuValidate::class)->scene('edit')->check($param);

        $data = MenuService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param['ids'] = Request::param('ids/a', '');

        validate(MenuValidate::class)->scene('dele')->check($param);

        $data = MenuService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单修改上级")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\menu_pid")
     */
    public function pid()
    {
        $param['ids']      = Request::param('ids/a', '');
        $param['menu_pid'] = Request::param('menu_pid/d', 0);

        validate(MenuValidate::class)->scene('pid')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否无需登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_unlogin'] = Request::param('is_unlogin/d', 0);

        validate(MenuValidate::class)->scene('unlogin')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否无需权限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_unauth")
     */
    public function unauth()
    {
        $param['ids']       = Request::param('ids/a', '');
        $param['is_unauth'] = Request::param('is_unauth/d', 0);

        validate(MenuValidate::class)->scene('unauth')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_disable")
     */
    public function disable()
    {
        $param['ids']        = Request::param('ids/a', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(MenuValidate::class)->scene('disable')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="角色列表", 
     *     @Apidoc\Returned(ref="app\common\model\admin\RoleModel\listReturn")
     * )
     */
    public function role()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_value    = Request::param('sort_value/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        validate(MenuValidate::class)->scene('role')->check(['admin_menu_id' => $admin_menu_id]);

        $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

        $order = [];
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = MenuService::role($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色解除")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     */
    public function roleRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(RoleValidate::class)->scene('id')->check($param);

        $data = MenuService::roleRemove($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单用户")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="sortParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="用户列表",
     *     @Apidoc\Returned(ref="app\common\model\admin\UserModel\listReturn")
     * )
     */
    public function user()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_value    = Request::param('sort_value/s', '');
        $admin_role_id = Request::param('admin_role_id/d', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        if ($admin_menu_id) {
            validate(MenuValidate::class)->scene('user')->check(['admin_menu_id' => $admin_menu_id]);

            $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

            $order = [];
            if ($sort_field && $sort_value) {
                $order = [$sort_field => $sort_value];
            }

            $data = UserService::list($where, $page, $limit, $order);

            return success($data);
        } else {
            validate(RoleValidate::class)->scene('id')->check(['admin_role_id' => $admin_role_id]);

            $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

            $order = [];
            if ($sort_field && $sort_value) {
                $order = [$sort_field => $sort_value];
            }

            $data = MenuService::user($where, $page, $limit, $order);

            return success($data);
        }
    }

    /**
     * @Apidoc\Title("菜单用户解除")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\UserModel\id")
     */
    public function userRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = MenuService::userRemove($param);

        return success($data);
    }
}
