<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2020-11-19
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminUserValidate;
use app\admin\service\AdminUserService;

class AdminUser
{
    /**
     * 用户列表
     *
     * @method GET
     * 
     * @return json
     */
    public function userList()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $username   = Request::param('username/s', '');
        $nickname   = Request::param('nickname/s', '');
        $email      = Request::param('email/s', '');

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($nickname) {
            $where[] = ['nickname', 'like', '%' . $nickname . '%'];
        }
        if ($email) {
            $where[] = ['email', 'like', '%' . $email . '%'];
        }

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = AdminUserService::list($where, $page, $limit, $field, $order);

        return success($data);
    }

    /**
     * 用户信息
     *
     * @method GET
     * 
     * @return json
     */
    public function userInfo()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        $param['admin_user_id'] = $admin_user_id;

        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data = AdminUserService::info($admin_user_id);

        if ($data['is_delete'] == 1) {
            exception('用户已被删除');
        }

        return success($data);
    }

    /**
     * 用户添加
     *
     * @method POST
     * 
     * @return json
     */
    public function userAdd()
    {
        $param = Request::only(
            [
                'username' => '',
                'nickname' => '',
                'password' => '',
                'email'    => '',
                'remark'   => '',
                'sort'     => 200,
            ]
        );

        validate(AdminUserValidate::class)->scene('user_add')->check($param);

        $data = AdminUserService::add($param);

        return success($data);
    }

    /**
     * 用户修改
     *
     * @method POST
     * 
     * @return json
     */
    public function userEdit()
    {
        $param = Request::only(
            [
                'admin_user_id' => '',
                'username'      => '',
                'nickname'      => '',
                'email'         => '',
                'remark'        => '',
                'sort'          => 200,
            ]
        );

        validate(AdminUserValidate::class)->scene('user_edit')->check($param);

        $data = AdminUserService::edit($param);

        return success($data);
    }

    /**
     * 用户修改头像
     *
     * @method POST
     * 
     * @return json
     */
    public function userAvatar()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');
        $avatar_file   = Request::file('avatar_file');

        $param['admin_user_id'] = $admin_user_id;
        $param['avatar']        = $avatar_file;

        validate(AdminUserValidate::class)->scene('user_avatar')->check($param);

        $data = AdminUserService::avatar($param);

        return success($data);
    }

    /**
     * 用户删除
     *
     * @method POST
     * 
     * @return json
     */
    public function userDele()
    {
        $admin_user_id = Request::param('admin_user_id/d', '');

        $param['admin_user_id'] = $admin_user_id;

        validate(AdminUserValidate::class)->scene('user_dele')->check($param);

        $data = AdminUserService::dele($admin_user_id);

        return success($data);
    }

    /**
     * 用户密码重置
     *
     * @method POST
     * 
     * @return json
     */
    public function userPwd()
    {
        $param = Request::only(
            [
                'admin_user_id' => '',
                'password'      => '',
            ]
        );

        validate(AdminUserValidate::class)->scene('user_pwd')->check($param);

        $data = AdminUserService::pwd($param);

        return success($data);
    }

    /**
     * 用户权限分配
     *
     * @method POST
     * 
     * @return json
     */
    public function userRule()
    {
        if (Request::isGet()) {
            $param['admin_user_id'] = Request::param('admin_user_id/d', '');

            validate(AdminUserValidate::class)->scene('user_id')->check($param);

            $data = AdminUserService::rule($param);
        } else {
            $param['admin_user_id']  = Request::param('admin_user_id/d', '');
            $param['admin_role_ids'] = Request::param('admin_role_ids/a', []);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            validate(AdminUserValidate::class)->scene('user_rule')->check($param);

            $data = AdminUserService::rule($param, 'post');
        }

        return success($data);
    }

    /**
     * 用户是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function userDisable()
    {
        $param = Request::only(
            [
                'admin_user_id' => '',
                'is_disable'    => '0',
            ]
        );

        validate(AdminUserValidate::class)->scene('user_disable')->check($param);

        $data = AdminUserService::disable($param);

        return success($data);
    }

    /**
     * 用户是否管理员
     *
     * @method POST
     * 
     * @return json
     */
    public function userAdmin()
    {
        $param = Request::only(
            [
                'admin_user_id' => '',
                'is_admin'      => '0',
            ]
        );

        validate(AdminUserValidate::class)->scene('user_admin')->check($param);

        $data = AdminUserService::admin($param);

        return success($data);
    }
}
