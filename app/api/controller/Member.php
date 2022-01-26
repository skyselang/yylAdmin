<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员中心控制器
namespace app\api\controller;

use think\facade\Request;
use app\common\validate\member\MemberValidate;
use app\common\validate\file\FileValidate;
use app\common\service\member\MemberService;
use app\common\service\member\LogService;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员中心")
 * @Apidoc\Sort("310")
 * @Apidoc\Group("member")
 */
class Member
{
    /**
     * @Apidoc\Title("我的信息")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\indexInfoReturn")
     */
    public function info()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id']);
        if ($data['is_delete'] == 1) {
            exception('会员已被注销');
        }
        unset($data['password'], $data['remark'], $data['sort'], $data['is_disable'], $data['is_delete'], $data['delete_time']);

        return success($data);
    }

    /**
     * @Apidoc\Title("修改信息")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\indexEditParam")
     */
    public function edit()
    {
        $param['member_id'] = member_id();
        $param['avatar_id'] = Request::param('avatar_id/d', 0);
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
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\addParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function avatar()
    {
        $param['file']      = Request::file('file');
        $param['group_id']  = Request::param('group_id/d', 0);
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front']  = 1;

        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);

        return success($data, '上传成功');
    }

    /**
     * @Apidoc\Title("修改密码")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码,会员信息pwd_edit_type=0需输入原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码,会员信息pwd_edit_type=1直接设置新密码")
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
            validate(MemberValidate::class)->scene('editpwd0')->check($param);
        }

        $data = MemberService::repwd([$param['member_id']], $param['password_new']);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志记录")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\log_type")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", 
     *     @Apidoc\Returned(ref="app\common\model\member\LogModel\listReturn")
     * )
     */
    public function log()
    {
        $member_id   = member_id();
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $log_type    = Request::param('log_type/d', '');
        $sort_field  = Request::param('sort_field/s', '');
        $sort_value  = Request::param('sort_value/s', '');
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

        $data = LogService::list($where, $page, $limit, $order);

        return success($data);
    }

    /**
     * @Apidoc\Title("绑定手机（小程序）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="wx.login，用户登录凭证")
     * @Apidoc\Param("iv", type="string", require=true, desc="加密算法的初始向量")
     * @Apidoc\Param("encrypted_data", type="string", require=true, desc="包括敏感数据在内的完整用户信息的加密数据")
     */
    public function bindPhoneMini()
    {
        $param['code']           = Request::param('code/s', '');
        $param['iv']             = Request::param('iv/s', '');
        $param['encrypted_data'] = Request::param('encrypted_data/s', '');

        foreach ($param as $k => $v) {
            if (empty($v)) {
                exception($k . ' must');
            }
        }

        $data = MemberService::bindPhoneMini($param['code'], $param['iv'], $param['encrypted_data']);

        return success($data);
    }
}
