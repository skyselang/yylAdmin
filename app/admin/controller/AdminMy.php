<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-11-01
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminMyValidate;
use app\admin\service\AdminMyService;
use app\admin\service\AdminMenuService;

class AdminMy
{
    /**
     * 我的信息
     *
     * @method GET
     * 
     * @return json
     */
    public function myInfo()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        $param['admin_user_id'] = $admin_user_id;

        validate(AdminMyValidate::class)->scene('user_id')->check($param);

        $data = AdminMyService::info($admin_user_id);

        if ($data['is_delete'] == 1) {
            exception('账号信息错误，请重新登录！');
        }

        return success($data);
    }

    /**
     * 修改信息
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function myEdit()
    {
        if (Request::isGet()) {
            $param['admin_user_id'] = Request::param('admin_user_id/d', '');

            validate(AdminMyValidate::class)->scene('user_id')->check($param);

            $data = AdminMyService::edit($param);

            return success($data);
        } else {
            $param = Request::only(
                [
                    'admin_user_id' => '',
                    'username'      => '',
                    'nickname'      => '',
                    'email'         => '',
                ]
            );

            validate(AdminMyValidate::class)->scene('my_edit')->check($param);

            $data = AdminMyService::edit($param, 'post');

            return success($data);
        }
    }

    /**
     * 修改密码
     *
     * @method POST
     * 
     * @return json
     */
    public function myPwd()
    {

        $param = Request::only(
            [
                'admin_user_id' => '',
                'password_old'  => '',
                'password_new'  => '',
            ]
        );

        validate(AdminMyValidate::class)->scene('my_pwd')->check($param);

        $data = AdminMyService::pwd($param);

        return success($data);
    }

    /**
     * 修改头像
     *
     * @method POST
     * 
     * @return json
     */
    public function myAvatar()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $avatar        = Request::file('avatar_file');

        $param['admin_user_id'] = $admin_user_id;
        $param['avatar']        = $avatar;

        validate(AdminMyValidate::class)->scene('my_avatar')->check($param);

        $data = AdminMyService::avatar($param);

        return success($data);
    }

    /**
     * 我的日志
     *
     * @method GET
     * 
     * @return json
     */
    public function myLog()
    {
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $admin_user_id   = Request::param('admin_user_id/d', '');
        $admin_log_type  = Request::param('type/d', '');
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $param['admin_user_id'] = $admin_user_id;

        validate(AdminMyValidate::class)->scene('user_id')->check($param);

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

        $data = AdminMyService::log($where, $page, $limit, $field, $order);

        return success($data);
    }
}
