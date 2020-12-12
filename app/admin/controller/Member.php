<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2020-12-11
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
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

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
        if ($date_type && $date_range) {
            $where[] = [$date_type, '>=', $date_range[0] . ' 00:00:00'];
            $where[] = [$date_type, '<=', $date_range[1] . ' 23:59:59'];
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
        $param['member_id'] = Request::param('member_id/d', '');

        validate(MemberValidate::class)->scene('member_id')->check($param);

        $data = MemberService::info($param['member_id']);

        if ($data['is_delete'] == 1) {
            exception('会员已被删除');
        }

        unset($data['password'], $data['token']);

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
        $param['username'] = Request::param('username/s', '');
        $param['nickname'] = Request::param('nickname/s', '');
        $param['password'] = Request::param('password/s', '');
        $param['phone']    = Request::param('phone/s', '');
        $param['email']    = Request::param('email/s', '');
        $param['remark']   = Request::param('remark/s', '');
        $param['sort']     = Request::param('sort/d', 10000);

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
        $param['member_id'] = Request::param('member_id/d', '');
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['remark']    = Request::param('remark/s', '');
        $param['sort']      = Request::param('sort/d', 10000);

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
        $param['member_id'] = Request::param('member_id/d', '');
        $param['avatar']    = Request::file('avatar_file');

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
        $param['member_id'] = Request::param('member_id/d', '');

        validate(MemberValidate::class)->scene('member_dele')->check($param);

        $data = MemberService::dele($param['member_id']);

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
        $param['member_id'] = Request::param('member_id/d', '');
        $param['password']  = Request::param('password/s', '');

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
        $param['member_id']  = Request::param('member_id/d', '');
        $param['is_disable'] = Request::param('is_disable/s', '0');

        validate(MemberValidate::class)->scene('member_disable')->check($param);

        $data = MemberService::disable($param);

        return success($data);
    }
}
