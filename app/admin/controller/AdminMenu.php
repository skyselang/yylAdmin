<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-01-18
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminMenuValidate;
use app\admin\validate\AdminRoleValidate;
use app\admin\validate\AdminUserValidate;
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
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $data = AdminMenuService::info($param['admin_menu_id']);

        if ($data['is_delete'] == 1) {
            exception('菜单已被删除：' . $param['admin_menu_id']);
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
        $param['menu_pid']      = Request::param('menu_pid/d', 0);
        $param['menu_name']     = Request::param('menu_name/s', '');
        $param['menu_url']      = Request::param('menu_url/s', '');
        $param['menu_sort']     = Request::param('menu_sort/d', 200);
        $param['menu_request']  = Request::param('menu_request/s', '');
        $param['menu_response'] = Request::param('menu_response/s', '');
        $param['menu_explain']  = Request::param('menu_explain/s', '');

        validate(AdminMenuValidate::class)->scene('menu_add')->check($param);

        $data = AdminMenuService::add($param);

        return success($data);
    }

    /**
     * 菜单修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function menuEdit()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        if (Request::isGet()) {
            validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

            $data = AdminMenuService::edit($param);

            if ($data['is_delete'] == 1) {
                exception('菜单已被删除：' . $param['admin_menu_id']);
            }
        } else {
            $param['menu_pid']      = Request::param('menu_pid/d', 0);
            $param['menu_name']     = Request::param('menu_name/s', '');
            $param['menu_url']      = Request::param('menu_url/s', '');
            $param['menu_sort']     = Request::param('menu_sort/d', 200);
            $param['menu_request']  = Request::param('menu_request/s', '');
            $param['menu_response'] = Request::param('menu_response/s', '');
            $param['menu_explain']  = Request::param('menu_explain/s', '');

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
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_dele')->check($param);

        $data = AdminMenuService::dele($param['admin_menu_id']);

        return success($data);
    }

    /**
     * 菜单文档
     *
     * @method GET
     * 
     * @return json
     */
    public function menuDoc()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);

        $data = AdminMenuService::info($param['admin_menu_id']);

        if ($data['is_delete'] == 1) {
            exception('菜单已删除：' . $param['admin_menu_id']);
        }

        return success($data);
    }

    /**
     * 菜单上传图片
     *
     * @method POST
     * 
     * @return json
     */
    public function menuUpload()
    {
        $param['image_file']  = Request::file('image_file');
        $param['image_field'] = Request::param('image_field/s', '');

        validate(AdminMenuValidate::class)->scene('menu_image')->check($param);

        $data = AdminMenuService::upload($param);

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
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

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
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['is_unauth']     = Request::param('is_unauth/d', 0);

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

        validate(AdminMenuValidate::class)->scene('menu_id')->check(['admin_menu_id' => $admin_menu_id]);

        $where0 = [['admin_menu_ids', 'like', $admin_menu_id], ['is_delete', '=', 0]];
        $where1 = [['admin_menu_ids', 'like', $admin_menu_id . ',%'], ['is_delete', '=', 0]];
        $where2 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id . ',%'], ['is_delete', '=', 0]];
        $where3 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id], ['is_delete', '=', 0]];
        $where  = [$where0, $where1, $where2, $where3];

        $whereOr = true;

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = AdminMenuService::role($where, $page, $limit, $order, $field, $whereOr);

        return success($data);
    }

    /**
     * 菜单角色解除
     *
     * @method POST
     *
     * @return json
     */
    public function menuRoleRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_role_id'] = Request::param('admin_role_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);
        validate(AdminRoleValidate::class)->scene('role_id')->check($param);

        $data = AdminMenuService::roleRemove($param);

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

            validate(AdminMenuValidate::class)->scene('menu_id')->check(['admin_menu_id' => $admin_menu_id]);

            $where0 = [['admin_menu_ids', 'like', $admin_menu_id], ['is_delete', '=', 0]];
            $where1 = [['admin_menu_ids', 'like', $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where2 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id . ',%'], ['is_delete', '=', 0]];
            $where3 = [['admin_menu_ids', 'like', '%,' . $admin_menu_id], ['is_delete', '=', 0]];
            $where  = [$where0, $where1, $where2, $where3];

            $whereOr = true;

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $field = '';

            $data = AdminUserService::list($where, $page, $limit, $order, $field, $whereOr);

            return success($data);
        } else {
            $page       = Request::param('page/d', 1);
            $limit      = Request::param('limit/d', 10);
            $sort_field = Request::param('sort_field/s ', '');
            $sort_type  = Request::param('sort_type/s', '');

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

            $data = AdminMenuService::user($where, $page, $limit, $order, $field, $whereOr);

            return success($data);
        }
    }

    /**
     * 菜单用户解除
     *
     * @method POST
     *
     * @return json
     */
    public function menuUserRemove()
    {
        $param['admin_menu_id'] = Request::param('admin_menu_id/d', '');
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);
        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data = AdminMenuService::userRemove($param);

        return success($data);
    }
}
