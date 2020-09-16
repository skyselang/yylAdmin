<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-14
 * @LastEditTime : 2020-09-16
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\service\AdminUsersService;
use app\admin\service\AdminMenuService;
use app\admin\validate\AdminUserValidate;

class AdminUsers
{
    /**
     * 个人信息
     *
     * @method GET
     * 
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
     * 
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
     * 
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
     * 
     * @return json
     */
    public function usersAvatar()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $avatar        = Request::file('avatar_file');

        $param['admin_user_id'] = $admin_user_id;
        $param['avatar']        = $avatar;

        validate(AdminUserValidate::class)->scene('users_avatar')->check($param);

        $data = AdminUsersService::avatar($param);

        return success($data);
    }

    /**
     * 日志记录
     *
     * @method GET
     * 
     * @return json
     */
    public function usersLog()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $admin_user_id   = Request::param('admin_user_id/d', 0);
        $admin_log_type  = Request::param('type/d', 0);
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        validate(AdminUserValidate::class)->scene('admin_user_id')->check(['admin_user_id' => $admin_user_id]);

        $where = [];
        if ($admin_user_id) {
            $where[] = ['admin_user_id', '=', $admin_user_id];
        }
        if ($admin_log_type) {
            $where[] = ['admin_log_type', '=', $admin_log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($menu_keyword) {
            $admin_menu    = AdminMenuService::likeQuery($menu_keyword);
            $admin_menu_id = array_column($admin_menu, 'admin_menu_id');
            $where[] = ['admin_menu_id', 'in', $admin_menu_id];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminUsersService::log($where, $page, $limit, $field, $order);

        return success($data);
    }
}
