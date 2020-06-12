<?php
/*
 * @Description  : 菜单管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminMenuService;
use app\admin\validate\AdminMenuValidate;

class AdminMenu
{
    /**
     * 菜单列表
     *
     * @method GET
     * @return json
     */
    public function menuList()
    {
        $data = AdminMenuService::list();

        return success($data);
    }

    /**
     * 菜单添加
     *
     * @method POST
     * @return json
     */
    public function menuAdd()
    {
        $param = Request::only(
            [
                'menu_pid'    => 0,
                'menu_name'   => '',
                'menu_url'    => '',
                'menu_sort'   => 200,
                'is_prohibit' => 0,
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
     * @return json
     */
    public function menuEdit()
    {
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

        $data = AdminMenuService::edit($param);

        return success($data);
    }

    /**
     * 菜单删除
     *
     * @method POST
     * @return json
     */
    public function menuDele()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('admin_menu_id')->check(['admin_menu_id' => $admin_menu_id]);

        $data = AdminMenuService::dele($admin_menu_id);

        return success($data);
    }

    /**
     * 菜单信息
     *
     * @method GET
     * @return json
     */
    public function menuInfo()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');

        validate(AdminMenuValidate::class)->scene('admin_menu_id')->check(['admin_menu_id' => $admin_menu_id]);

        $data = AdminMenuService::info($admin_menu_id);

        return success($data);
    }

    /**
     * 菜单是否禁用
     *
     * @method POST
     * @return json
     */
    public function menuProhibit()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $is_prohibit   = Request::param('is_prohibit/s', 0);

        $param['admin_menu_id'] = $admin_menu_id;
        $param['is_prohibit']   = $is_prohibit;

        validate(AdminMenuValidate::class)->scene('admin_menu_id')->check(['admin_menu_id' => $admin_menu_id]);

        $data = AdminMenuService::prohibit($param);

        return success($data);
    }

    /**
     * 菜单是否无需权限
     *
     * @method POST
     * @return json
     */
    public function menuUnauth()
    {
        $admin_menu_id = Request::param('admin_menu_id/d', '');
        $is_unauth     = Request::param('is_unauth/s', 0);

        $param['admin_menu_id'] = $admin_menu_id;
        $param['is_unauth']     = $is_unauth;

        validate(AdminMenuValidate::class)->scene('admin_menu_id')->check(['admin_menu_id' => $admin_menu_id]);

        $data = AdminMenuService::unauth($param);

        return success($data);
    }
}
