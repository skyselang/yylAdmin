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
use app\common\cache\utils\CaptchaSmsCache;
use app\common\cache\utils\CaptchaEmailCache;
use app\common\utils\SmsUtils;
use app\common\utils\EmailUtils;
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
        if ($data['is_disable'] == 1) {
            exception('会员已被禁用');
        } else if ($data['is_delete'] == 1) {
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
        $param['region_id'] = Request::param('region_id/d', 0);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param['member_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("更换头像")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Param(ref="app\common\model\file\FileModel\addParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function avatar()
    {
        $member_id          = member_id();
        $param['file']      = Request::file('file');
        $param['group_id']  = Request::param('group_id/d', 0);
        $param['file_type'] = Request::param('file_type/s', 'image');
        $param['file_name'] = Request::param('file_name/s', '');
        $param['is_front']  = 1;

        validate(MemberValidate::class)->scene('id')->check(['member_id' => $member_id]);
        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);
        MemberService::edit($member_id, ['avatar_id' => $data['file_id']]);

        return success($data, '更换成功');
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

        $data = MemberService::edit($param['member_id'], ['password' => md5($param['password_new'])]);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志记录")
     * @Apidoc\Param(ref="pagingParam")
     * @Apidoc\Param(ref="app\common\model\member\LogModel\log_type")
     * @Apidoc\Param("log_type", require=false, default="")
     * @Apidoc\Param(ref="dateParam")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="列表", 
     *     @Apidoc\Returned(ref="app\common\model\member\LogModel\listReturn")
     * )
     */
    public function log()
    {
        $page        = Request::param('page/d', 1);
        $limit       = Request::param('limit/d', 10);
        $log_type    = Request::param('log_type/d', '');
        $sort_field  = Request::param('sort_field/s', '');
        $sort_value  = Request::param('sort_value/s', '');
        $create_time = Request::param('create_time/a', []);

        $where[] = ['member_id', '=', member_id()];
        if ($log_type) {
            $where[] = ['log_type', '=', $log_type];
        }
        $where[] = ['is_delete', '=', 0];
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
     * @Apidoc\Title("手机绑定验证码")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\phone")
     */
    public function phoneCaptcha()
    {
        $param['phone'] = Request::param('phone/s', '');

        validate(MemberValidate::class)->scene('phoneBindCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("手机绑定")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\phone")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     */
    public function phoneBind()
    {
        $param['member_id']    = member_id();
        $param['phone']        = Request::param('phone/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('phoneBind')->check($param);
        $captcha = CaptchaSmsCache::get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        $data = MemberService::edit($param['member_id'], ['phone' => $param['phone']]);
        CaptchaSmsCache::del($param['phone']);

        return success($data, '绑定成功');
    }

    /**
     * @Apidoc\Title("手机绑定（小程序）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="button 组件 open-type 的值为 getPhoneNumber，bindgetphonenumber 事件回调获取到的动态令牌code")
     */
    public function bindPhoneMini()
    {
        $param['code'] = Request::param('code/s', '');

        foreach ($param as $k => $v) {
            if (empty($v)) {
                exception($k . ' must');
            }
        }

        $data = MemberService::bindPhoneMini($param['code']);

        return success($data);
    }

    /**
     * @Apidoc\Title("邮箱绑定验证码")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\email", require=true)
     */
    public function emailCaptcha()
    {
        $param['email'] = Request::param('email/s', '');

        validate(MemberValidate::class)->scene('emailBindCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱绑定")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\email", require=true)
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     */
    public function emailBind()
    {
        $param['member_id']    = member_id();
        $param['email']        = Request::param('email/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('emailBind')->check($param);
        $captcha = CaptchaEmailCache::get($param['email']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        $data = MemberService::edit($param['member_id'], ['email' => $param['email']]);
        CaptchaEmailCache::del($param['email']);

        return success($data, '绑定成功');
    }
}
