<?php
/*
 * @Description  : 会员中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-06
 */

namespace app\index\controller;

use think\facade\Request;
use app\common\validate\MemberValidate;
use app\common\service\MemberService;
use app\common\service\MemberLogService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员中心")
 */
class Member
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\id")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", 
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\infoIndex")
     * )
     */ 
    public function info()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('info')->check($param);

        $member = MemberService::info($param['member_id']);

        if ($member['is_delete'] == 1) {
            exception('会员已被注销');
        }

        unset($member['password'], $member['remark'], $member['sort'], $member['is_disable'], $member['is_delete'], $member['delete_time']);

        $data = $member;

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="app\common\model\MemberModel\editIndex")
     * @Apidoc\Returned(ref="return")
     */ 
    public function edit()
    {
        $param['member_id'] = member_id();
        $param['username']  = Request::param('username/s', '');
        $param['nickname']  = Request::param('nickname/s', '');
        $param['phone']     = Request::param('phone/s', '');
        $param['email']     = Request::param('email/s', '');
        $param['region_id'] = Request::param('region_id/d', 0);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("更换头像")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="app\common\model\MemberModel\avatar")
     * @Apidoc\Returned(ref="return")
     */ 
    public function avatar()
    {
        $param['member_id'] = member_id();
        $param['avatar']    = Request::file('avatar_file');

        validate(MemberValidate::class)->scene('avatar')->check($param);

        $data = MemberService::avatar($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码")
     * @Apidoc\Returned(ref="return")
     */ 
    public function pwd()
    {
        $param['member_id']    = member_id();
        $param['password_old'] = Request::param('password_old/s', '');
        $param['password_new'] = Request::param('password_new/s', '');

        validate(MemberValidate::class)->scene('editpwd')->check($param);

        $data = MemberService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Header(ref="headerAdmin")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\log")
     * @Apidoc\Param("create_time", type="array", default="[]", desc="开始与结束日期eg:['2022-02-22','2022-02-28']")
     * @Apidoc\Returned(ref="return"),
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="returnPaging"),
     *      @Apidoc\Returned("list", type="array", desc="数据列表", 
     *          @Apidoc\Returned(ref="app\common\model\MemberLogModel\list")
     *      )
     * )
     */ 
    public function log()
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

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }
}
