<?php
/*
 * @Description  : 管理员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\AdminUserValidate;
use app\admin\service\AdminUserService;

class AdminUser
{
    /**
     * 管理员列表
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

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = AdminUserService::list($where, $page, $limit, $order, $field);

        return success($data);
    }

    /**
     * 管理员信息
     *
     * @method GET
     * 
     * @return json
     */
    public function userInfo()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('user_id')->check($param);

        $data = AdminUserService::info($param['admin_user_id']);

        if ($data['is_delete'] == 1) {
            exception('管理员已被删除：' . $param['admin_user_id']);
        }

        return success($data);
    }

    /**
     * 管理员添加
     *
     * @method POST
     * 
     * @return json
     */
    public function userAdd()
    {
        $param['username'] = Request::param('username/s', '');
        $param['nickname'] = Request::param('nickname/s', '');
        $param['password'] = Request::param('password/s', '');
        $param['email']    = Request::param('email/s', '');
        $param['remark']   = Request::param('remark/s', '');
        $param['sort']     = Request::param('sort/d', 200);

        validate(AdminUserValidate::class)->scene('user_add')->check($param);

        $data = AdminUserService::add($param);

        return success($data);
    }

    /**
     * 管理员修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function userEdit()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        if (Request::isGet()) {
            validate(AdminUserValidate::class)->scene('user_id')->check($param);

            $data = AdminUserService::edit($param);

            if ($data['admin_user']['is_delete'] == 1) {
                exception('管理员已被删除：' . $param['admin_user_id']);
            }
        } else {
            $param['username'] = Request::param('username/s', '');
            $param['nickname'] = Request::param('nickname/s', '');
            $param['email']    = Request::param('email/s', '');
            $param['remark']   = Request::param('remark/s', '');
            $param['sort']     = Request::param('sort/d', 200);

            validate(AdminUserValidate::class)->scene('user_edit')->check($param);

            $data = AdminUserService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 管理员删除
     *
     * @method POST
     * 
     * @return json
     */
    public function userDele()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        validate(AdminUserValidate::class)->scene('user_dele')->check($param);

        $data = AdminUserService::dele($param['admin_user_id']);

        return success($data);
    }

    /**
     * 管理员更换头像
     *
     * @method POST
     * 
     * @return json
     */
    public function userAvatar()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['avatar']        = Request::file('avatar_file');

        validate(AdminUserValidate::class)->scene('user_avatar')->check($param);

        $data = AdminUserService::avatar($param);

        return success($data);
    }

    /**
     * 管理员密码重置
     *
     * @method POST
     * 
     * @return json
     */
    public function userPwd()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['password']      = Request::param('password/s', '');

        validate(AdminUserValidate::class)->scene('user_pwd')->check($param);

        $data = AdminUserService::pwd($param);

        return success($data);
    }

    /**
     * 管理员权限分配
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function userRule()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');

        if (Request::isGet()) {
            validate(AdminUserValidate::class)->scene('user_id')->check($param);

            $data = AdminUserService::rule($param);
        } else {
            $param['admin_role_ids'] = Request::param('admin_role_ids/a', []);
            $param['admin_menu_ids'] = Request::param('admin_menu_ids/a', []);

            validate(AdminUserValidate::class)->scene('user_rule')->check($param);

            $data = AdminUserService::rule($param, 'post');
        }

        return success($data);
    }

    /**
     * 管理员是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function userDisable()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['is_disable']    = Request::param('is_disable/d', 0);

        validate(AdminUserValidate::class)->scene('user_disable')->check($param);

        $data = AdminUserService::disable($param);

        return success($data);
    }

    /**
     * 管理员是否超管
     *
     * @method POST
     * 
     * @return json
     */
    public function userAdmin()
    {
        $param['admin_user_id'] = Request::param('admin_user_id/d', '');
        $param['is_admin']      = Request::param('is_admin/d', 0);

        validate(AdminUserValidate::class)->scene('user_admin')->check($param);

        $data = AdminUserService::admin($param);

        return success($data);
    }
}
