<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员中心控制器
namespace app\index\controller;

use think\facade\Request;
use app\common\validate\MemberValidate;
use app\common\service\MemberService;
use app\common\service\MemberLogService;
use app\common\service\UploadService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员中心")
 * @Apidoc\Sort("4")
 */
class Member
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
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
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Param(ref="app\common\model\MemberModel\editIndex")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function edit()
    {
        $param['member_id'] = member_id();
        $param['avatar']    = Request::param('avatar/s', '');
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
     * @Apidoc\Title("上传头像")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="paramFile")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnFile")
     */
    public function avatar()
    {
        $param['avatar'] = Request::file('file');

        validate(MemberValidate::class)->scene('avatar')->check($param);

        $data = UploadService::upload($param['avatar'], 'member/avatar');

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码,会员信息pwd_edit_type=0需输入原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码,会员信息pwd_edit_type=1直接设置新密码")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function pwd()
    {
        $param['member_id']    = member_id();
        $param['password_old'] = Request::param('password_old/s', '');
        $param['password_new'] = Request::param('password_new/s', '');

        $member = MemberService::info($param['member_id']);
        if ($member['pwd_edit_type']) {
            validate(MemberValidate::class)->scene('editpwd1')->check($param);
        } else {
            validate(MemberValidate::class)->scene('editpwd')->check($param);
        }

        $data = MemberService::pwd($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("我的日志")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Param(ref="paramPaging")
     * @Apidoc\Param(ref="app\common\model\MemberLogModel\log_type")
     * @Apidoc\Param(ref="paramDate")
     * @Apidoc\Returned(ref="returnCode"),
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
        $sort_field  = Request::param('sort_field/s', '');
        $sort_value   = Request::param('sort_value/s', '');
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
        if ($sort_field && $sort_value) {
            $order = [$sort_field => $sort_value];
        }

        $data = MemberLogService::list($where, $page, $limit, $order);

        return success($data);
    }
}
