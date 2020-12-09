<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-09
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\admin\service\MemberService;
use app\admin\service\LogService;

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
        $member_id = member_id();

        $param['member_id'] = $member_id;

        validate(MemberValidate::class)->scene('member_id')->check($param);

        $data = MemberService::info($member_id);

        if ($data['is_delete'] == 1) {
            exception('账户已注销');
        }

        unset($data['password'], $data['remark'], $data['sort'], $data['is_disable'], $data['is_delete'], $data['delete_time']);

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
        $param = Request::only(
            [
                'username'  => '',
                'nickname'  => '',
                'phone'     => '',
                'email'     => '',
            ]
        );
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('member_edit')->check($param);

        $data = MemberService::edit($param);

        return success($data);
    }

    /**
     * 修改头像
     *
     * @method POST
     * 
     * @return json
     */
    public function userAvatar()
    {
        $member_id   = member_id();
        $avatar_file = Request::file('avatar_file');

        $param['member_id'] = $member_id;
        $param['avatar']    = $avatar_file;

        validate(MemberValidate::class)->scene('member_avatar')->check($param);

        $data = MemberService::avatar($param);

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
        $param = Request::only(
            [
                'password_old' => '',
                'password_new' => '',
            ]
        );
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('member_pwdedit')->check($param);

        $data = MemberService::pwdedit($param);

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
        $member_id   = member_id();
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $log_type    = Request::param('log_type/d', '');
        $sort_field  = Request::param('sort_field/s ', '');
        $sort_type   = Request::param('sort_type/s', '');
        $create_time = Request::param('create_time/a', []);

        $where[] = ['member_id', '=', $member_id];
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

        $data = LogService::list($where, $page, $limit, $order);

        return success($data);
    }
}
