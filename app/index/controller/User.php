<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2020-12-27
 */

namespace app\index\controller;

use think\facade\Request;
use app\admin\validate\MemberValidate;
use app\admin\service\MemberService;
use app\admin\service\MemberLogService;
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
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('member_id')->check($param);

        $member = MemberService::info($param['member_id']);

        if ($member['is_delete'] == 1) {
            exception('账户已注销');
        }

        unset($member['password'], $member['remark'], $member['sort'], $member['is_disable'], $member['is_delete'], $member['delete_time']);

        $data['member_info'] = $member;
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
        $param['member_id'] = member_id();

        if (Request::isGet()) {
            validate(MemberValidate::class)->scene('member_id')->check($param);

            $data = MemberService::edit($param);
        } else {
            $param['username']  = Request::param('username/s', '');
            $param['nickname']  = Request::param('nickname/s', '');
            $param['phone']     = Request::param('phone/s', '');
            $param['email']     = Request::param('email/s', '');
            $param['region_id'] = Request::param('region_id/d', 0);

            validate(MemberValidate::class)->scene('member_edit')->check($param);

            $data = MemberService::edit($param, 'post');
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
        $param['member_id'] = member_id();
        $param['avatar']    = Request::file('avatar_file');

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
        $param['member_id']    = member_id();
        $param['password_old'] = Request::param('password_old/s', '');
        $param['password_new'] = Request::param('password_new/s', '');

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
        $member_id       = member_id();
        $page            = Request::param('page/d', 1);
        $limit           = Request::param('limit/d', 10);
        $member_log_type = Request::param('member_log_type/d', '');
        $sort_field      = Request::param('sort_field/s ', '');
        $sort_type       = Request::param('sort_type/s', '');
        $create_time     = Request::param('create_time/a', []);

        $where[] = ['member_id', '=', $member_id];
        if ($member_log_type) {
            $where[] = ['member_log_type', '=', $member_log_type];
        }
        if ($create_time) {
            $where[] = ['create_time', '>=', $create_time[0] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $create_time[1] . ' 23:59:59'];
        }

        $order = [];
        if ($sort_field && $sort_type) {
            $order = [$sort_field => $sort_type];
        }

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }
}
