<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30
 * @LastEditTime : 2020-12-25
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminRoleValidate;
use app\admin\validate\AdminUserValidate;
use app\admin\service\AdminRoleService;

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
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s', '');
        $sort_type  = Request::param('sort_type/s', '');
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
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminRoleService::list($where, $page, $limit, $order);

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
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(AdminRoleValidate::class)->scene('role_id')->check($param);

        $data = AdminRoleService::info($param['admin_role_id']);

        if ($data['is_delete'] == 1) {
            exception('角色已被删除：' . $param['admin_role_id']);
        }

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
        if (Request::isGet()) {
            $data = AdminRoleService::add();
        } else {
            $param['role_name']      = Request::param('role_name/s', '');
            $param['role_desc']      = Request::param('role_desc/s', '');
            $param['role_sort']      = Request::param('role_sort/d', 200);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            validate(AdminRoleValidate::class)->scene('role_add')->check($param);

            $data = AdminRoleService::add($param, 'post');
        }

        return success($data);
    }

    /**
     * 角色修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function roleEdit()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        if (Request::isGet()) {
            validate(AdminRoleValidate::class)->scene('role_id')->check($param);

            $data = AdminRoleService::edit($param);

            if ($data['admin_role']['is_delete'] == 1) {
                exception('角色已被删除：' . $param['admin_role_id']);
            }
        } else {
            $param['role_name']      = Request::param('role_name/s', '');
            $param['role_desc']      = Request::param('role_desc/s', '');
            $param['role_sort']      = Request::param('role_sort/d', 200);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            validate(AdminRoleValidate::class)->scene('role_edit')->check($param);

            $data = AdminRoleService::edit($param, 'post');
        }

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
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(AdminRoleValidate::class)->scene('role_dele')->check($param);

        $data = AdminRoleService::dele($param['admin_role_id']);

        return success($data);
    }

    /**
     * 角色禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function roleDisable()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(AdminRoleValidate::class)->scene('role_id')->check($param);

        $data = AdminRoleService::disable($param);

        return success($data);
    }

    /**
     * 角色用户
     *
     * @method GET
     *
     * @return json
     */
    public function roleUser()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $sort_field    = Request::param('sort_field/s ', '');
        $sort_type     = Request::param('sort_type/s', '');
        $admin_role_id = Request::param('admin_role_id/s', '');

        validate(AdminRoleValidate::class)->scene('role_id')->check(['admin_role_id' => $admin_role_id]);

        $where0 = [['admin_role_ids', 'like', $admin_role_id], ['is_delete', '=', 0]];
        $where1 = [['admin_role_ids', 'like', $admin_role_id . ',%'], ['is_delete', '=', 0]];
        $where2 = [['admin_role_ids', 'like', '%,' . $admin_role_id . ',%'], ['is_delete', '=', 0]];
        $where3 = [['admin_role_ids', 'like', '%,' . $admin_role_id], ['is_delete', '=', 0]];
        $where  = [$where0, $where1, $where2, $where3];
        $whereOr = true;

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = AdminRoleService::user($where, $page, $limit, $order, $field, $whereOr);

        return success($data);
    }

    /**
     * 角色用户解除
     *
     * @method POST
     *
     * @return json
     */
    public function roleUserRemove()
    {
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminRoleValidate::class)->scene('role_id')->check($param);
        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data = AdminRoleService::userRemove($param);

        return success($data);
    }
}
