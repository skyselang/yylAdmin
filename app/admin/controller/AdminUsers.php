<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-14
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminUsersService;
use app\admin\validate\AdminUserValidate;

class AdminUsers
{
    /**
     * 个人信息
     *
     * @method GET
     * @return json
     */
    public function usersInfo()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $data = AdminUsersService::info($admin_user_id);

        return success($data);
    }

    /**
     * 修改信息
     *
     * @method POST
     * @return json
     */
    public function usersEdit()
    {
        $param = Request::only(
            [
                'admin_user_id' => '',
                'username'      => '',
                'nickname'      => '',
                'email'         => '',
            ]
        );

        validate(AdminUserValidate::class)->scene('users_edit')->check($param);

        $data = AdminUsersService::edit($param);

        return success($data);
    }

    /**
     * 修改密码
     *
     * @method POST
     * @return json
     */
    public function usersPwd()
    {

        $param = Request::only(
            [
                'admin_user_id' => '',
                'password'      => '',
                'passwords'     => '',
            ]
        );

        validate(AdminUserValidate::class)->scene('users_pwd')->check($param);

        $data = AdminUsersService::pwd($param);

        return success($data);
    }

    /**
     * 更换头像
     *
     * @method POST
     * @return json
     */
    public function usersAvatar()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $avatar        = Request::file('avatar_file');

        $param['admin_user_id'] = $admin_user_id;
        $param['avatar'] = $avatar;

        validate(AdminUserValidate::class)->scene('users_avatar')->check($param);

        $data = AdminUsersService::avatar($param);

        return success($data);
    }

    /**
     * 日志记录
     *
     * @method GET
     * @return json
     */
    public function usersLog()
    {
        $page          = Request::param('page/d', 1);
        $limit         = Request::param('limit/d', 10);
        $order_field   = Request::param('order_field/s ', '');
        $order_type    = Request::param('order_type/s', '');
        $admin_user_id = Request::param('admin_user_id/d', '');
        $menu_url      = Request::param('menu_url/s', '');
        $create_time   = Request::param('create_time/a', '');

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $where = [];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '=', $admin_user_id];
        }
        if ($menu_url) {
            $where[] = ['menu_url', 'like', '%' . $menu_url . '%'];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $field = '';

        $order = [];
        if ($order_field && $order_type) {
            $order = [$order_field => $order_type];
        }

        $data = AdminUsersService::log($where, $page, $limit, $field, $order);

        return success($data);
    }
}
