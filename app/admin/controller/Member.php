<?php
/*
 * @Description  : 会员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-23
 * @LastEditTime : 2021-05-06
 */

namespace app\admin\controller;

use think\facade\Request;
use app\common\validate\MemberValidate;
use app\common\service\MemberService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员管理")
 * @Apidoc\Group("index")
 */
class Member
{
    /**
     * @Apidoc\Title("会员列表")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param("member_id", type="int", default="", desc="会员ID")
     * @Apidoc\Param("username", type="string", default="", desc="账号")
     * @Apidoc\Param("phone", type="string", default="", desc="手机")
     * @Apidoc\Param("email", type="string", default="", desc="邮箱")
     * @Apidoc\Param("date_type", type="string", default="", desc="日期类型字段")
     * @Apidoc\Param("date_range", type="array", default="[]", desc="日期范围")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\MemberModel\list")
     *      )
     * )
     */
    public function list()
    {
        $page       = Request::param('page/d', 1);
        $limit      = Request::param('limit/d', 10);
        $sort_field = Request::param('sort_field/s ', '');
        $sort_type  = Request::param('sort_type/s', '');
        $member_id  = Request::param('member_id/d', '');
        $username   = Request::param('username/s', '');
        $phone      = Request::param('phone/s', '');
        $email      = Request::param('email/s', '');
        $date_type  = Request::param('date_type/s', '');
        $date_range = Request::param('date_range/a', []);

        $where = [];
        if ($member_id) {
            $where[] = ['member_id', '=', $member_id];
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

        $data = MemberService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\id")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\info")
     * )
     */
    public function info()
    {
        $param['member_id'] = Request::param('member_id/d', '');

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id']);

        if ($data['is_delete'] == 1) {
            exception('会员已被删除：' . $param['member_id']);
        }

        unset($data['password'], $data['token']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\add")
     * @Apidoc\Returned(ref="return")
     */
    public function add()
    {
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['password']  = Request::param('password/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['region_id'] = Request::param('region_id/d', 0);
        $param['remark']    = Request::param('remark/s', '');
        $param['sort']      = Request::param('sort/d', 10000);

        validate(MemberValidate::class)->scene('add')->check($param);

        $data = MemberService::add($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("会员修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\edit")
     * @Apidoc\Returned(ref="return")
     */
    public function edit()
    {
        $param['member_id'] = Request::param('member_id/d', '');
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['region_id'] = Request::param('region_id/d', 0);
        $param['remark']    = Request::param('remark/s', '');
        $param['sort']      = Request::param('sort/d', 10000);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param, 'post');

        return success($data);
    }

    /**
     * @Apidoc\Title("会员删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\dele")
     * @Apidoc\Returned(ref="return")
     */
    public function dele()
    {
        $param['member_id'] = Request::param('member_id/d', '');

        validate(MemberValidate::class)->scene('dele')->check($param);

        $data = MemberService::dele($param['member_id']);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员更换头像")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="app\common\model\MemberModel\avatar")
     * @Apidoc\Returned(ref="return")
     */
    public function avatar()
    {
        $param['member_id'] = Request::param('member_id/d', '');
        $param['avatar']    = Request::file('avatar_file');

        validate(MemberValidate::class)->scene('avatar')->check($param);

        $data = MemberService::avatar($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员重置密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\pwd")
     * @Apidoc\Returned(ref="return")
     */
    public function pwd()
    {
        $param['member_id'] = Request::param('member_id/d', '');
        $param['password']  = Request::param('password/s', '');

        validate(MemberValidate::class)->scene('pwd')->check($param);

        $data = MemberService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\disable")
     * @Apidoc\Returned(ref="return")
     */
    public function disable()
    {
        $param['member_id']  = Request::param('member_id/d', '');
        $param['is_disable'] = Request::param('is_disable/d', 0);

        validate(MemberValidate::class)->scene('disable')->check($param);

        $data = MemberService::disable($param);

        return success($data);
    }
}
