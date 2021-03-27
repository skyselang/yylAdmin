<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminMenuValidate;
use app\admin\validate\AdminRoleValidate;
use app\admin\validate\AdminAdminValidate;
use app\admin\service\AdminMenuService;
use app\admin\service\AdminAdminService;

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

        $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminMenuService::role($where, $page, $limit, $order);

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
     * 菜单管理员
     *
     * @method GET
     *
     * @return json
     */
    public function menuAdmin()
    {
        $admin_role_id = Request::param('admin_role_id/d', '');
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        if ($admin_menu_id) {
            $page       = Request::param('page/d', 1);
            $limit      = Request::param('limit/d', 10);
            $sort_field = Request::param('sort_field/s ', '');
            $sort_type  = Request::param('sort_type/s', '');

            validate(AdminMenuValidate::class)->scene('menu_id')->check(['admin_menu_id' => $admin_menu_id]);

            $where[] = ['admin_menu_ids', 'like', '%' . str_join($admin_menu_id) . '%'];

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminAdminService::list($where, $page, $limit, $order);

            return success($data);
        } else {
            $page       = Request::param('page/d', 1);
            $limit      = Request::param('limit/d', 10);
            $sort_field = Request::param('sort_field/s ', '');
            $sort_type  = Request::param('sort_type/s', '');

            validate(AdminRoleValidate::class)->scene('role_id')->check(['admin_role_id' => $admin_role_id]);

            $where[] = ['admin_role_ids', 'like', '%' . str_join($admin_role_id) . '%'];

            $order = [];
            if ($sort_field && $sort_type) {
                $order = [$sort_field => $sort_type];
            }

            $data = AdminMenuService::admin($where, $page, $limit, $order);

            return success($data);
        }
    }

    /**
     * 菜单管理员解除
     *
     * @method POST
     *
     * @return json
     */
    public function menuAdminRemove()
    {
        $param['admin_menu_id']  = Request::param('admin_menu_id/d', '');
        $param['admin_admin_id'] = Request::param('admin_admin_id/d', '');

        validate(AdminMenuValidate::class)->scene('menu_id')->check($param);
        validate(AdminAdminValidate::class)->scene('admin_id')->check($param);

        $data = AdminMenuService::adminRemove($param);

        return success($data);
    }
}
