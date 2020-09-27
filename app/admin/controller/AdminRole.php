<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30
 * @LastEditTime : 2020-09-27
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminRoleService;
use app\admin\validate\AdminRoleValidate;

class AdminRole
{
    /**
     * 角色列表
     *
     * @method GET
     * 
     * @return json
     */
    public function roleList()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s', '');
        $sort_type     = Request::param('sort_type/s', '');
        $role_name     = Request::param('role_name/s', '');
        $role_desc     = Request::param('role_desc/s', '');
        $admin_menu_id = Request::param('admin_menu_id/d', 0);

        $where = [];
        if ($role_name) {
            $where[] = ['role_name', 'like', '%' . $role_name . '%'];
        }
        if ($role_desc) {
            $where[] = ['role_desc', 'like', '%' . $role_desc . '%'];
        }
        $whereOr = false;
        if ($admin_menu_id) {
            $whereOr = true;
            $where0 = [['admin_menu_ids', 'like', $admin_menu_id], ['is_delete', '=', 0]];
            $where1 = [['admin_menu_ids', 'like', $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where2 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where3 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id], ['is_delete', '=', 0]];
            $where = [$where0, $where1, $where2, $where3];
        }

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminRoleService::list($where, $page, $limit, $field, $order, $whereOr);

        return success($data);
    }

    /**
     * 角色信息
     *
     * @method GET
     * 
     * @return json
     */
    public function roleInfo()
    {
        $admin_role_id = Request::param('admin_role_id/d', 0);

        validate(AdminRoleValidate::class)->scene('admin_role_id')->check(['admin_role_id' => $admin_role_id]);

        $data = AdminRoleService::info($admin_role_id);

        return success($data);
    }

    /**
     * 角色添加
     *
     * @method POST
     * 
     * @return json
     */
    public function roleAdd()
    {
        $param = Request::only(
            [
                'role_name'   => '',
                'role_desc'   => '',
                'role_sort'   => 200,
                'is_prohibit' => '0',
            ]
        );
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

        validate(AdminRoleValidate::class)->scene('role_add')->check($param);

        $data = AdminRoleService::add($param);

        return success($data);
    }

    /**
     * 角色修改
     *
     * @method POST
     * 
     * @return json
     */
    public function roleEdit()
    {
        $param = Request::only(
            [
                'admin_role_id' => 0,
                'role_name'     => '',
                'role_desc'     => '',
                'role_sort'     => 200,
                'is_prohibit'   => '0',
            ]
        );
        $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

        validate(AdminRoleValidate::class)->scene('role_edit')->check($param);

        $data = AdminRoleService::edit($param);

        return success($data);
    }

    /**
     * 角色删除
     *
     * @method POST
     * 
     * @return json
     */
    public function roleDele()
    {
        $admin_role_id = Request::param('admin_role_id/d', 0);

        validate(AdminRoleValidate::class)->scene('admin_role_id')->check(['admin_role_id' => $admin_role_id]);

        $data = AdminRoleService::dele($admin_role_id);

        return success($data);
    }

    /**
     * 角色是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function roleProhibit()
    {
        $admin_role_id = Request::param('admin_role_id/d', '');
        $is_prohibit   = Request::param('is_prohibit/s', '0');

        $param['admin_role_id'] = $admin_role_id;
        $param['is_prohibit']   = $is_prohibit;

        validate(AdminRoleValidate::class)->scene('admin_role_id')->check(['admin_role_id' => $admin_role_id]);

        $data = AdminRoleService::prohibit($param);

        return success($data);
    }
}
