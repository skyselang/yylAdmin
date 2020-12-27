<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2020-12-25
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
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminMyValidate::class)->scene('user_id')->check($param);

        $data = AdminMyService::info($param['admin_user_id']);

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
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        if (Request::isGet()) {
            validate(AdminMyValidate::class)->scene('user_id')->check($param);

            $data = AdminMyService::edit($param);

            if ($data['is_delete'] == 1) {
                exception('账号信息错误，请重新登录！');
            }
        } else {
            $param['username'] = Request::param('username/s', '');
            $param['nickname'] = Request::param('nickname/s', '');
            $param['email']    = Request::param('email/s', '');

            validate(AdminMyValidate::class)->scene('my_edit')->check($param);

            $data = AdminMyService::edit($param, 'post');
        }

        return success($data);
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
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['password_old']  = Request::param('password_old/s', '');
        $param['password_new']  = Request::param('password_new/s', '');

        validate(AdminMyValidate::class)->scene('my_pwd')->check($param);

        $data = AdminMyService::pwd($param);

        return success($data);
    }

    /**
     * 更换头像
     *
     * @method POST
     * 
     * @return json
     */
    public function myAvatar()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['avatar']        = Request::file('avatar_file');

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
        $admin_log_type  = Request::param('admin_log_type/d', '');
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $request_keyword = Request::param('request_keyword/s', '');
        $menu_keyword    = Request::param('menu_keyword/s', '');
        $create_time     = Request::param('create_time/a', []);

        $admin_user_id   = admin_user_id();

        validate(AdminMyValidate::class)->scene('user_id')->check(['admin_user_id' => $admin_user_id]);

        $where   = [];
        $where[] = ['admin_user_id', '=', $admin_user_id];
        if ($admin_log_type) {
            $where[] = ['admin_log_type', '=', $admin_log_type];
        }
        if ($request_keyword) {
            $where[] = ['request_ip|request_region|request_isp', 'like', '%' . $request_keyword . '%'];
        }
        if ($menu_keyword) {
            $admin_menu     = AdminMenuService::likeQuery($menu_keyword);
            $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
            $where[]        = ['admin_menu_id', 'in', $admin_menu_ids];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminMyService::log($where, $page, $limit, $order);

        return success($data);
    }
}
