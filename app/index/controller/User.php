<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-03-25
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\UserValidate;
use app\admin\service\UserService;
use app\admin\service\UserLogService;
use app\admin\service\RegionService;

class User
{
    /**
     * 我的信息
     *
     * @method GET
     * 
     * @return json
     */
    public function userInfo()
    {
        $param['user_id'] = user_id();

        validate(UserValidate::class)->scene('user_id')->check($param);

        $user = UserService::info($param['user_id']);

        if ($user['is_delete'] == 1) {
            exception('用户已被注销');
        }

        unset($user['password'], $user['remark'], $user['sort'], $user['is_disable'], $user['is_delete'], $user['delete_time']);

        $data['user_info']   = $user;
        $data['region_tree'] = RegionService::info('tree');

        return success($data);
    }

    /**
     * 修改信息
     *
     * @method POST
     * 
     * @return json
     */
    public function userEdit()
    {
        $param['user_id'] = user_id();

        if (Request::isGet()) {
            validate(UserValidate::class)->scene('user_id')->check($param);

            $data = UserService::edit($param);
        } else {
            $param['username']  = Request::param('username/s', '');
            $param['nickname']  = Request::param('nickname/s', '');
            $param['phone']     = Request::param('phone/s', '');
            $param['email']     = Request::param('email/s', '');
            $param['region_id'] = Request::param('region_id/d', 0);

            validate(UserValidate::class)->scene('user_edit')->check($param);

            $data = UserService::edit($param, 'post');
        }

        return success($data);
    }

    /**
     * 更换头像
     *
     * @method POST
     * 
     * @return json
     */
    public function userAvatar()
    {
        $param['user_id'] = user_id();
        $param['avatar']  = Request::file('avatar_file');

        validate(UserValidate::class)->scene('user_avatar')->check($param);

        $data = UserService::avatar($param);

        return success($data);
    }

    /**
     * 修改密码
     *
     * @method POST
     * 
     * @return json
     */
    public function userPwd()
    {
        $param['user_id']      = user_id();
        $param['password_old'] = Request::param('password_old/s', '');
        $param['password_new'] = Request::param('password_new/s', '');

        validate(UserValidate::class)->scene('user_pwdedit')->check($param);

        $data = UserService::pwdedit($param);

        return success($data);
    }

    /**
     * 我的日志
     *
     * @method GET
     * 
     * @return json
     */
    public function userLog()
    {
        $user_id     = user_id();
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $log_type    = Request::param('log_type/d', '');
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $create_time = Request::param('create_time/a', []);

        $where[] = ['user_id', '=', $user_id];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = UserLogService::list($where, $page, $limit, $order);

        return success($data);
    }
}
