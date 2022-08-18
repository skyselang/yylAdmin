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
class Menu extends BaseController
{
    /**
     * @Apidoc\Title("菜单列表")
     * @Apidoc\Param(ref="searchParam")
     * @Apidoc\Returned("list", ref="app\common\model\admin\MenuModel\listReturn", type="array", desc="菜单列表")
     * @Apidoc\Returned("tree", ref="app\common\model\admin\MenuModel\treeReturn", type="tree", childrenField="children", desc="菜单树形")
     */
    public function list()
    {
        $search_field = $this->param('search_field/s', '');
        $search_value = $this->param('search_value/s', '');
        $date_field   = $this->param('date_field/s', '');
        $date_value   = $this->param('date_value/a', '');

        $where = $order = [];
        if ($search_field && $search_value !== '') {
            if ($search_field == 'show_lv') {
                if ($search_value == 1) {
                    $where[] = ['menu_pid', '=', 0];
                } elseif ($search_value == 2) {
                    $admin_menu_ids = MenuService::list('list', [['menu_pid', '=', 0]], [], 'admin_menu_id');
                    $admin_menu_ids = array_column($admin_menu_ids, 'admin_menu_id');
                    $where[] = ['menu_pid', 'in', $admin_menu_ids];
                    $order = ['menu_pid' => 'asc', 'menu_sort' => 'desc'];
                } elseif ($search_value == 3) {
                    $admin_menu_ids = MenuService::list('list', [['menu_pid', '=', 0]], [], 'admin_menu_id');
                    $admin_menu_ids = array_column($admin_menu_ids, 'admin_menu_id');
                    $admin_menu_ids = MenuService::list('list', [['menu_pid', 'in', $admin_menu_ids]], [], 'admin_menu_id');
                    $admin_menu_ids = array_column($admin_menu_ids, 'admin_menu_id');
                    $where[] = ['menu_pid', 'in', $admin_menu_ids];
                    $order = ['menu_pid' => 'asc', 'menu_sort' => 'desc'];
                }
            } elseif (in_array($search_field, ['admin_menu_id', 'menu_pid', 'menu_type', 'is_unlogin', 'is_unauth', 'is_disable', 'hidden'])) {
                $search_exp = strpos($search_value, ',') ? 'in' : '=';
                $where[] = [$search_field, $search_exp, $search_value];
            } else {
                $where[] = [$search_field, 'like', '%' . $search_value . '%'];
            }
        }
        if ($date_field && $date_value) {
            $where[] = [$date_field, '>=', $date_value[0] . ' 00:00:00'];
            $where[] = [$date_field, '<=', $date_value[1] . ' 23:59:59'];
        }

        if ($where) {
            $data['list'] = MenuService::list('list', $where, $order);
        } else {
            $data['list'] = MenuService::list('tree', $where, $order);
        }
        $data['tree'] = MenuService::list('tree', [], [], 'admin_menu_id,menu_pid,menu_name');

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单信息")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Returned(ref="app\common\model\admin\MenuModel\infoReturn")
     */
    public function info()
    {
        $param['admin_menu_id'] = $this->param('admin_menu_id/d', '');

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
        $param['menu_pid']   = $this->param('menu_pid/d', 0);
        $param['menu_type']  = $this->param('menu_type/d', 1);
        $param['meta_icon']  = $this->param('meta_icon/s', '');
        $param['menu_name']  = $this->param('menu_name/s', '');
        $param['menu_url']   = $this->param('menu_url/s', '');
        $param['path']       = $this->param('path/s', '');
        $param['component']  = $this->param('component/s', '');
        $param['name']       = $this->param('name/s', '');
        $param['meta_query'] = $this->param('meta_query/s', '');
        $param['hidden']     = $this->param('hidden/d', 0);
        $param['menu_sort']  = $this->param('menu_sort/d', 250);
        $param['add_info']   = $this->param('add_info/b', false);
        $param['add_add']    = $this->param('add_add/b', false);
        $param['add_edit']   = $this->param('add_edit/b', false);
        $param['add_dele']   = $this->param('add_dele/b', false);

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
        $param['admin_menu_id'] = $this->param('admin_menu_id/d', '');
        $param['menu_pid']      = $this->param('menu_pid/d', 0);
        $param['menu_type']     = $this->param('menu_type/d', 1);
        $param['meta_icon']     = $this->param('meta_icon/s', '');
        $param['menu_name']     = $this->param('menu_name/s', '');
        $param['menu_url']      = $this->param('menu_url/s', '');
        $param['path']          = $this->param('path/s', '');
        $param['component']     = $this->param('component/s', '');
        $param['name']          = $this->param('name/s', '');
        $param['meta_query']    = $this->param('meta_query/s', '');
        $param['hidden']        = $this->param('hidden/d', 0);
        $param['menu_sort']     = $this->param('menu_sort/d', 250);
        $param['add_info']      = $this->param('add_info/b', false);
        $param['add_add']       = $this->param('add_add/b', false);
        $param['add_edit']      = $this->param('add_edit/b', false);
        $param['add_dele']      = $this->param('add_dele/b', false);
        $param['edit_info']     = $this->param('edit_info/b', false);
        $param['edit_add']      = $this->param('edit_add/b', false);
        $param['edit_edit']     = $this->param('edit_edit/b', false);
        $param['edit_dele']     = $this->param('edit_dele/b', false);

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
        $param['ids'] = $this->param('ids/a', '');

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
        $param['ids']      = $this->param('ids/a', '');
        $param['menu_pid'] = $this->param('menu_pid/d', 0);

        validate(MenuValidate::class)->scene('pid')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免登")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_unlogin")
     */
    public function unlogin()
    {
        $param['ids']        = $this->param('ids/a', '');
        $param['is_unlogin'] = $this->param('is_unlogin/d', 0);

        validate(MenuValidate::class)->scene('unlogin')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免权")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_unauth")
     */
    public function unauth()
    {
        $param['ids']       = $this->param('ids/a', '');
        $param['is_unauth'] = $this->param('is_unauth/d', 0);

        validate(MenuValidate::class)->scene('unauth')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否免限")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_unrate")
     */
    public function unrate()
    {
        $param['ids']       = $this->param('ids/a', '');
        $param['is_unrate'] = $this->param('is_unrate/d', 0);

        validate(MenuValidate::class)->scene('unrate')->check($param);

        $data = MenuService::update($param['ids'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单是否隐藏")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\is_hidden")
     */
    public function hidden()
    {
        $param['ids']    = $this->param('ids/a', '');
        $param['hidden'] = $this->param('hidden/d', 0);

        validate(MenuValidate::class)->scene('hidden')->check($param);

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
        $param['ids']        = $this->param('ids/a', '');
        $param['is_disable'] = $this->param('is_disable/d', 0);

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
     * @Apidoc\Returned("list", ref="app\common\model\admin\RoleModel\listReturn", type="array", desc="角色列表")
     */
    public function role()
    {
        $admin_menu_id = $this->param('admin_menu_id/d', '');

        validate(MenuValidate::class)->scene('role')->check(['admin_menu_id' => $admin_menu_id]);

        $where = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
        $where = $this->where($where);

        $data = MenuService::role($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("菜单角色解除")
     * @Apidoc\Param(ref="app\common\model\admin\MenuModel\id")
     * @Apidoc\Param(ref="app\common\model\admin\RoleModel\id")
     */
    public function roleRemove()
    {
        $param['admin_menu_id'] = $this->param('admin_menu_id/d', '');
        $param['admin_role_id'] = $this->param('admin_role_id/d', '');

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
     * @Apidoc\Returned("list", ref="app\common\model\admin\UserModel\listReturn", type="array", desc="用户列表")
     */
    public function user()
    {
        $admin_role_id = $this->param('admin_role_id/d', '');
        $admin_menu_id = $this->param('admin_menu_id/d', '');

        if ($admin_menu_id) {
            validate(MenuValidate::class)->scene('user')->check(['admin_menu_id' => $admin_menu_id]);

            $where = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];
            $where = $this->where($where);

            $data = UserService::list($where, $this->page(), $this->limit(), $this->order());

            return success($data);
        } else {
            validate(RoleValidate::class)->scene('id')->check(['admin_role_id' => $admin_role_id]);

            $where = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];
            $where = $this->where($where);

            $data = MenuService::user($where, $this->page(), $this->limit(), $this->order());

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
        $param['admin_menu_id'] = $this->param('admin_menu_id/d', '');
        $param['admin_user_id'] = $this->param('admin_user_id/d', '');

        validate(MenuValidate::class)->scene('id')->check($param);
        validate(UserValidate::class)->scene('id')->check($param);

        $data = MenuService::userRemove($param);

        return success($data);
    }
}
