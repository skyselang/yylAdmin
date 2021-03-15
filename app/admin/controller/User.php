<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-03-08
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\UserValidate;
use app\admin\service\UserService;

class User
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
        $user_id    = Request::param('user_id/d', '');
        $username   = Request::param('username/s', '');
        $phone      = Request::param('phone/s', '');
        $email      = Request::param('email/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        $where = [];
        if ($user_id) {
            $where[] = ['user_id', '=', $user_id];
        }
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($phone) {
            $where[] = ['phone', 'like', '%' . $phone . '%'];
        }
        if ($email) {
            $where[] = ['email', 'like', '%' . $email . '%'];
        }
        if ($date_type && $date_range) {
            $where[] = [$date_type, '>=', $date_range[0] . ' 00:00:00'];
            $where[] = [$date_type, '<=', $date_range[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $field = '';

        $data = UserService::list($where, $page, $limit, $order, $field);

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
        $param['user_id'] = Request::param('user_id/d', '');

        validate(UserValidate::class)->scene('user_id')->check($param);

        $data = UserService::info($param['user_id']);

        if ($data['is_delete'] == 1) {
            exception('用户已被删除：' . $param['user_id']);
        }

        unset($data['password'], $data['token']);

        return success($data);
    }

    /**
     * 用户添加
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function userAdd()
    {
        if (Request::isGet()) {
            $data = UserService::add();
        } else {
            $param['username']  = Request::param('username/s', '');
            $param['nickname']  = Request::param('nickname/s', '');
            $param['password']  = Request::param('password/s', '');
            $param['phone']     = Request::param('phone/s', '');
            $param['email']     = Request::param('email/s', '');
            $param['region_id'] = Request::param('region_id/d', 0);
            $param['remark']    = Request::param('remark/s', '');
            $param['sort']      = Request::param('sort/d', 10000);

            validate(UserValidate::class)->scene('user_add')->check($param);

            $data = UserService::add($param, 'post');
        }

        return success($data);
    }

    /**
     * 用户修改
     *
     * @method GET|POST
     * 
     * @return json
     */
    public function userEdit()
    {
        $param['user_id'] = Request::param('user_id/d', '');

        if (Request::isGet()) {
            validate(UserValidate::class)->scene('user_id')->check($param);

            $data = UserService::edit($param);
        } else {
            $param['username']  = Request::param('username/s', '');
            $param['nickname']  = Request::param('nickname/s', '');
            $param['phone']     = Request::param('phone/s', '');
            $param['email']     = Request::param('email/s', '');
            $param['region_id'] = Request::param('region_id/d', 0);
            $param['remark']    = Request::param('remark/s', '');
            $param['sort']      = Request::param('sort/d', 10000);

            validate(UserValidate::class)->scene('user_edit')->check($param);

            $data = UserService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 用户更换头像
     *
     * @method POST
     * 
     * @return json
     */
    public function userAvatar()
    {
        $param['user_id'] = Request::param('user_id/d', '');
        $param['avatar']  = Request::file('avatar_file');

        validate(UserValidate::class)->scene('user_avatar')->check($param);

        $data = UserService::avatar($param);

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
        $param['user_id'] = Request::param('user_id/d', '');

        validate(UserValidate::class)->scene('user_dele')->check($param);

        $data = UserService::dele($param['user_id']);

        return success($data);
    }

    /**
     * 用户密码重置
     *
     * @method POST
     * 
     * @return json
     */
    public function userPassword()
    {
        $param['user_id']  = Request::param('user_id/d', '');
        $param['password'] = Request::param('password/s', '');

        validate(UserValidate::class)->scene('user_password')->check($param);

        $data = UserService::password($param);

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
        $param['user_id']    = Request::param('user_id/d', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(UserValidate::class)->scene('user_disable')->check($param);

        $data = UserService::disable($param);

        return success($data);
    }
}
