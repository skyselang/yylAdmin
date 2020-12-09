<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-11-30
 */

namespace app\admin\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\admin\service\MemberService;

class Member
{
    /**
     * 会员列表
     *
     * @method GET
     * 
     * @return json
     */
    public function memberList()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $username   = Request::param('username/s', '');
        $nickname   = Request::param('nickname/s', '');
        $phone      = Request::param('phone/s', '');
        $email      = Request::param('email/s', '');

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($nickname) {
            $where[] = ['nickname', 'like', '%' . $nickname . '%'];
        }
        if ($phone) {
            $where[] = ['phone', 'like', '%' . $phone . '%'];
        }
        if ($email) {
            $where[] = ['email', 'like', '%' . $email . '%'];
        }

        $field = '';

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = MemberService::list($where, $page, $limit, $field, $order);

        return success($data);
    }

    /**
     * 会员信息
     *
     * @method GET
     * 
     * @return json
     */
    public function memberInfo()
    {
        $member_id = Request::param('member_id/d', '');

        $param['member_id'] = $member_id;

        validate(MemberValidate::class)->scene('member_id')->check($param);

        $data = MemberService::info($member_id);

        if ($data['is_delete'] == 1) {
            exception('会员已被删除');
        }

        return success($data);
    }

    /**
     * 会员添加
     *
     * @method POST
     * 
     * @return json
     */
    public function memberAdd()
    {
        $param = Request::only(
            [
                'username' => '',
                'nickname' => '',
                'password' => '',
                'phone'    => '',
                'email'    => '',
                'remark'   => '',
                'sort'     => 10000,
            ]
        );

        validate(MemberValidate::class)->scene('member_add')->check($param);

        $data = MemberService::add($param);

        return success($data);
    }

    /**
     * 会员修改
     *
     * @method POST
     * 
     * @return json
     */
    public function memberEdit()
    {
        $param = Request::only(
            [
                'member_id' => '',
                'username'  => '',
                'nickname'  => '',
                'phone'     => '',
                'email'     => '',
                'remark'    => '',
                'sort'      => 10000,
            ]
        );

        validate(MemberValidate::class)->scene('member_edit')->check($param);

        $data = MemberService::edit($param);

        return success($data);
    }

    /**
     * 会员修改头像
     *
     * @method POST
     * 
     * @return json
     */
    public function memberAvatar()
    {
        $member_id   = Request::param('member_id/d', '');
        $avatar_file = Request::file('avatar_file');

        $param['member_id'] = $member_id;
        $param['avatar']    = $avatar_file;

        validate(MemberValidate::class)->scene('member_avatar')->check($param);

        $data = MemberService::avatar($param);

        return success($data);
    }

    /**
     * 会员删除
     *
     * @method POST
     * 
     * @return json
     */
    public function memberDele()
    {
        $member_id = Request::param('member_id/d', '');

        $param['member_id'] = $member_id;

        validate(MemberValidate::class)->scene('member_dele')->check($param);

        $data = MemberService::dele($member_id);

        return success($data);
    }

    /**
     * 会员密码重置
     *
     * @method POST
     * 
     * @return json
     */
    public function memberPassword()
    {
        $param = Request::only(
            [
                'member_id' => '',
                'password'  => '',
            ]
        );

        validate(MemberValidate::class)->scene('member_password')->check($param);

        $data = MemberService::password($param);

        return success($data);
    }

    /**
     * 会员是否禁用
     *
     * @method POST
     * 
     * @return json
     */
    public function memberDisable()
    {
        $param = Request::only(
            [
                'member_id'  => '',
                'is_disable' => '0',
            ]
        );

        validate(MemberValidate::class)->scene('member_disable')->check($param);

        $data = MemberService::disable($param);

        return success($data);
    }
}
