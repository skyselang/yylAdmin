<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-11-05
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminMenuValidate;
use app\admin\validate\AdminRoleValidate;
use app\admin\service\AdminMenuService;
use app\admin\service\AdminUserService;

class AdminMenu
{
    /**
     * 菜单列表
     *
     * @method GET
     * 
     * @return json
     */
    public function menuList()
    {
        $data = AdminMenuService::list();

        return success($data);
    }

    /**
     * 菜单信息
     *
     * @method GET
     * 
     * @return json
     */
    public function menuInfo()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        $param['admin_menu_id'] = $admin_menu_id;

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $data = AdminMenuService::info($admin_menu_id);

        if ($data['is_delete'] == 1) {
            exception('菜单已被删除');
        }

        return success($data);
    }

    /**
     * 菜单添加
     *
     * @method POST
     * 
     * @return json
     */
    public function menuAdd()
    {
        $param = Request::only(
            [
                'menu_pid'  => 0,
                'menu_name' => '',
                'menu_url'  => '',
                'menu_sort' => 200,
            ]
        );

        validate(AdminMenuValidate::class)->scene('menu_add')->check($param);

        $data = AdminMenuService::add($param);

        return success($data);
    }

    /**
     * 菜单修改
     *
     * @method POST
     * 
     * @return json
     */
    public function menuEdit()
    {
        if (Request::isGet()) {
            $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

            validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

            $data = AdminMenuService::edit($param);
        } else {
            $param = Request::only(
                [
                    'admin_menu_id' => '',
                    'menu_pid'      => 0,
                    'menu_name'     => '',
                    'menu_url'      => '',
                    'menu_sort'     => 200,
                ]
            );

            validate(AdminMenuValidate::class)->scene('menu_edit')->check($param);

            $data = AdminMenuService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 菜单删除
     *
     * @method POST
     * 
     * @return json
     */
    public function menuDele()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        $param['admin_menu_id'] = $admin_menu_id;

        validate(AdminMenuValidate::class)->scene('menu_dele')->check($param);

        $data = AdminMenuService::dele($admin_menu_id);

        return success($data);
    }

    /**
     * 菜单是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function menuDisable()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $is_disable   = Request::param('is_disable/s', '0');

        $param['admin_menu_id'] = $admin_menu_id;
        $param['is_disable']    = $is_disable;

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $data = AdminMenuService::disable($param);

        return success($data);
    }

    /**
     * 菜单是否无需权限
     *
     * @method POST
     * 
     * @return json
     */
    public function menuUnauth()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $is_unauth     = Request::param('is_unauth/s', '0');

        $param['admin_menu_id'] = $admin_menu_id;
        $param['is_unauth']     = $is_unauth;

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $data = AdminMenuService::unauth($param);

        return success($data);
    }

    /**
     * 菜单角色
     *
     * @method GET
     *
     * @return json
     */
    public function menuRole()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_type     = Request::param('sort_type/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        $param['admin_menu_id'] = $admin_menu_id;

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $where0 = [['admin_menu_ids', 'like', $admin_menu_id], ['is_delete', '=', 0]];
        $where1 = [['admin_menu_ids', 'like', $admin_menu_id . ',%'], ['is_delete', '=', 0]];
        $where2 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id . ',%'], ['is_delete', '=', 0]];
        $where3 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id], ['is_delete', '=', 0]];
        $where  = [$where0, $where1, $where2, $where3];
        $whereOr = true;

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminMenuService::role($where, $page, $limit, $field, $order, $whereOr);

        return success($data);
    }

    /**
     * 菜单用户
     *
     * @method GET
     *
     * @return json
     */
    public function menuUser()
    {
        $admin_role_id = Request::param('admin_role_id/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        if ($admin_menu_id) {
            $page       = Request::param('page/d', 1);
            $limit      = Request::param('limit/d', 10);
            $sort_field = Request::param('sort_field/s ', '');
            $sort_type  = Request::param('sort_type/s', '');

            $param['admin_menu_id'] = $admin_menu_id;

            validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

            $where0 = [['admin_menu_ids', 'like', $admin_menu_id], ['is_delete', '=', 0]];                                       
            $where1 = [['admin_menu_ids', 'like', $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where2 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where3 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id], ['is_delete', '=', 0]];
            $where  = [$where0, $where1, $where2, $where3];
            $whereOr = true;

            $field = '';

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminUserService::list($where, $page, $limit, $field, $order, $whereOr);

            return success($data);
        } else {
            $page       = Request::param('page/d', 1);
            $limit      = Request::param('limit/d', 10);
            $sort_field = Request::param('sort_field/s ', '');
            $sort_type  = Request::param('sort_type/s', '');

            $param['admin_role_id'] = $admin_role_id;

            validate(AdminRoleValidate::class)->scene('role_id')->check($param);

            $where0 = [['admin_role_ids', 'like', $admin_role_id], ['is_delete', '=', 0]];
            $where1 = [['admin_role_ids', 'like', $admin_role_id . ',%'], ['is_delete', '=', 0]];
            $where2 = [['admin_role_ids', 'like', '%,' . $admin_role_id . ',%'], ['is_delete', '=', 0]];
            $where3 = [['admin_role_ids', 'like', '%,' . $admin_role_id], ['is_delete', '=', 0]];
            $where  = [$where0, $where1, $where2, $where3];
            $whereOr = true;

            $field = '';

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminMenuService::user($where, $page, $limit, $field, $order, $whereOr);

            return success($data);
        }
    }
}
