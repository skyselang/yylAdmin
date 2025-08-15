<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\validate\file\FileValidate;
use app\common\service\member\SettingService;
use app\common\service\member\MemberService;
use app\common\service\member\ThirdService;
use app\common\service\member\LogService;
use app\common\service\file\FileService;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\cache\utils\CaptchaEmailCache;
use app\common\cache\Cache;
use app\common\utils\SmsUtils;
use app\common\utils\EmailUtils;

/**
 * @Apidoc\Title("lang(会员中心)")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Member extends BaseController
{
    /**
     * @Apidoc\Title("lang(我的信息)")
     * @Apidoc\Returned(ref={MemberService::class,"info"}, withoutField="password,remark,sort,is_disable,is_delete,delete_time")
     */
    public function info()
    {
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('info')->check($param);

        $data = MemberService::info($param['member_id'], true, true, true);
        if ($data['is_disable'] == 1) {
            return error(lang('会员已被禁用'));
        } else if ($data['is_delete'] == 1) {
            return error(lang('会员已被注销'));
        }

        unset($data['password'], $data['remark'], $data['sort'], $data['is_disable'], $data['is_delete'], $data['delete_time'], $data['api_ids'], $data['api_urls'], $data['api_list'], $data['api_tree']);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(修改信息)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={MemberService::class,"edit"}, field="nickname,username,phone,email,name,gender,region_id")
     */
    public function edit()
    {
        $param = $this->params([
            'avatar_id/d'   => 0,
            'nickname/s'    => '',
            'username/s'    => '',
            'phone/s'       => '',
            'email/s'       => '',
            'name/s'        => '',
            'gender/d'      => 0,
            'birthday'      => NULL,
            'hometown_id/d' => 0,
            'region_id/d'   => 0,
        ]);
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('edit')->check($param);

        $data = MemberService::edit($param['member_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(更换头像)")
     * @Apidoc\Method("POST")
     * @Apidoc\ParamType("formdata")
     * @Apidoc\Param(ref="fileParam")
     * @Apidoc\Returned(ref="fileReturn")
     */
    public function avatar()
    {
        $param = $this->params([
            'group_id/d'  => 0,
            'file_type/s' => 'image',
            'file_name/s' => '',
        ]);
        $param['member_id'] = member_id(true);
        $param['file']      = $this->request->file('file');
        $param['is_front']  = 1;

        validate(MemberValidate::class)->scene('info')->check($param);
        validate(FileValidate::class)->scene('add')->check($param);

        $data = FileService::add($param);
        MemberService::edit($param['member_id'], ['avatar_id' => $data['file_id']]);

        return success($data, lang('更换成功'));
    }

    /**
     * @Apidoc\Title("lang(修改密码)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("password_old", type="string", require=true, desc="原密码,会员信息pwd_edit_type=0需输入原密码")
     * @Apidoc\Param("password_new", type="string", require=true, desc="新密码,会员信息pwd_edit_type=1直接设置新密码")
     */
    public function pwd()
    {
        $param = $this->params(['password_old/s' => '', 'password_new/s' => '']);
        $param['member_id'] = member_id(true);

        $member = MemberService::info($param['member_id']);
        if ($member['pwd_edit_type']) {
            validate(MemberValidate::class)->scene('editpwd1')->check($param);
        } else {
            validate(MemberValidate::class)->scene('editpwd0')->check($param);
        }

        MemberService::edit($param['member_id'], ['password' => $param['password_new']]);

        return success();
    }

    /**
     * @Apidoc\Title("lang(日志记录)")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref={LogService::class,"edit"}, field="log_type,create_time")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="日志列表", children={
     *   @Apidoc\Returned(ref={LogService::class,"info"}, field="log_id,request_ip,request_region,request_isp,response_code,response_msg,create_time,api_url,api_name"),
     * })
     */
    public function log()
    {
        $log_type    = $this->param('log_type/s', '');
        $create_time = $this->param('create_time/a', []);

        $where = [['member_id', '=', member_id(true)]];
        if ($log_type !== '') {
            $where[] = ['log_type', '=', $log_type];
        }
        if ($create_time) {
            $start_date = $create_time[0] ?? '';
            $end_date   = $create_time[1] ?? '';
            if ($start_date) {
                $where[] = ['create_time', '>=', $start_date . ' 00:00:00'];
            }
            if ($end_date) {
                $where[] = ['create_time', '<=', $end_date . ' 23:59:59'];
            }
        }
        $where = where_disdel($where);

        $data = LogService::list($where, $this->page(), $this->limit(), $this->order());

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(手机绑定验证码)")
     * @Apidoc\Query("phone", type="string", require=true, desc="手机")
     */
    public function phoneCaptcha()
    {
        $param = $this->params(['phone/s' => '']);
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('phoneBindCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success([], lang('发送成功'));
    }

    /**
     * @Apidoc\Title("lang(手机绑定)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("phone", type="string", require=true, desc="手机")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     */
    public function phoneBind()
    {
        $param = $this->params(['phone/s' => '', 'captcha_code/s' => '']);
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('phoneBind')->check($param);

        $cache = new CaptchaSmsCache();
        $captcha = $cache->get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            return error(lang('验证码错误'));
        }

        $data = MemberService::edit($param['member_id'], ['phone' => $param['phone']]);
        $cache->del($param['phone']);

        return success($data, lang('绑定成功'));
    }

    /**
     * @Apidoc\Title("lang(手机绑定(小程序))")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("app", type="string", default="wx", desc="wx 微信小程序，qq QQ小程序（暂不支持）")
     * @Apidoc\Param("phone_code", type="string", require=true, desc="手机号获取凭证 code")
     */
    public function phoneBindMiniapp()
    {
        $param = $this->params(['app/s' => 'wx', 'phone_code/s' => '']);
        $param['member_id'] = member_id(true);
        $validate = validate(['app' => 'require', 'phone_code' => 'require'], [], false, false);
        if (!$validate->check($param)) {
            return error($validate->getError());
        }
        $param['captcha_code'] = $param['phone_code'];

        $setting = SettingService::info();
        if ($param['app'] == 'wx') {
            $miniapp = new \thirdsdk\WxMiniapp($setting['wx_miniapp_appid'], $setting['wx_miniapp_appsecret']);
            $phone   = $miniapp->getPhoneNumber($param['phone_code']);
        } else {
            return error(lang('app错误'));
        }
        if (empty($phone)) {
            return error(lang('获取手机号失败'));
        }
        $param['phone'] = $phone;
        validate(MemberValidate::class)->scene('phoneBind')->check($param);

        $data = MemberService::edit($param['member_id'], ['phone' => $phone]);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(邮箱绑定验证码)")
     * @Apidoc\Query("email", type="string", require=true, desc="邮箱")
     */
    public function emailCaptcha()
    {
        $param = $this->params(['email/s' => '']);
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('emailBindCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success([], lang('发送成功'));
    }

    /**
     * @Apidoc\Title("lang(邮箱绑定)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email", type="string", require=true, desc="邮箱")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     */
    public function emailBind()
    {
        $param = $this->params(['email/s' => '', 'captcha_code/s' => '']);
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('emailBind')->check($param);
        $cache = new CaptchaEmailCache();
        $captcha = $cache->get($param['email']);
        if ($captcha != $param['captcha_code']) {
            return error(lang('验证码错误'));
        }

        $data = MemberService::edit($param['member_id'], ['email' => $param['email']]);
        $cache->del($param['email']);

        return success($data, lang('绑定成功'));
    }

    /**
     * @Apidoc\Title("lang(绑定小程序)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("app", type="string", default="wx", desc="wx 微信小程序，qq QQ小程序")
     * @Apidoc\Param("code", type="string", require=true, desc="code 用户绑定凭证")
     * @Apidoc\Param("userinfo", type="object", require=false, desc="用户信息：headimgurl 头像 ，nickname 昵称")
     */
    public function bindMiniapp()
    {
        $member_id = member_id(true);
        $param     = $this->params(['app/s' => 'wx', 'code/s' => '', 'userinfo/a' => []]);
        $validate  = validate(['app' => 'require', 'code' => 'require', 'userinfo' => 'array'], [], false, false);
        if (!$validate->check($param)) {
            return error($validate->getError());
        }

        $setting = SettingService::info();
        if ($param['app'] == 'wx') {
            $platform    = SettingService::PLATFORM_WX;
            $application = SettingService::APP_WX_MINIAPP;
            $miniapp     = new \thirdsdk\WxMiniapp($setting['wx_miniapp_appid'], $setting['wx_miniapp_appsecret']);
        } elseif ($param['app'] == 'qq') {
            $platform    = SettingService::PLATFORM_QQ;
            $application = SettingService::APP_QQ_MINIAPP;
            $miniapp     = new \thirdsdk\QqMiniapp($setting['qq_miniapp_appid'], $setting['qq_miniapp_appsecret']);
        } else {
            return error(lang('app错误'));
        }

        $user_info                = $miniapp->login($param['code']);
        $user_info['member_id']   = $member_id;
        $user_info['platform']    = $platform;
        $user_info['application'] = $application;
        if (isset($param['userinfo']['headimgurl'])) {
            $user_info['headimgurl'] = $param['userinfo']['headimgurl'];
        }
        if (isset($param['userinfo']['nickname'])) {
            $user_info['nickname'] = $param['userinfo']['nickname'];
        }

        $data = MemberService::thirdBind($user_info);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(绑定公众号)")
     * @Apidoc\Desc("拼接参数后打开链接")
     * @Apidoc\Query("app", type="string", default="wx", desc="应用：wx 微信公众号")
     * @Apidoc\Query("jump_url", type="string", require=true, desc="绑定成功后跳转地址，会携带 token 参数")
     * @Apidoc\Query("redirect_uri", type="string", require=false, desc="redirect_uri，调试使用")
     */
    public function bindOffiacc()
    {
        $setting  = SettingService::info();
        $param    = $this->params(['app/s' => 'wx', 'jump_url/s' => '', 'redirect_uri/s' => '', $setting['token_name'] => '']);
        $rule     = ['app' => 'require', 'jump_url' => 'require|url', 'redirect_uri' => 'url', $setting['token_name'] => 'require'];
        $validate = validate($rule, [], false, false);
        if (!$validate->check($param)) {
            echo $validate->getError();
            return;
        }

        $member_id    = member_id(true);
        $member_token = member_token();
        if (empty($member_token)) {
            echo lang('绑定失败，请重试');
            return;
        }

        $app['app'] = $param['app'];
        if ($app['app'] == 'wx') {
            $app['platform']    = SettingService::PLATFORM_WX;
            $app['application'] = SettingService::APP_WX_OFFIACC;
            $offiacc            = new \thirdsdk\WxOffiacc($setting['wx_offiacc_appid'], $setting['wx_offiacc_appsecret']);
        } else {
            echo lang('app错误');
            return;
        }

        $redirect_uri = $param['redirect_uri'] ?: (string) url('api/member.Login/redirectUri', [], false, true);
        $state        = md5(uniqid('offiacc' . $member_id, true));

        $cache = new Cache(false);
        $cache_key = SettingService::OFFIACC_WEBSITE_KEY . $state;
        $cache_val['type']         = 'offiacc';
        $cache_val['app']          = $app;
        $cache_val['jump_url']     = $param['jump_url'];
        $cache_val['token']        = $member_token;
        $cache_val['redirect_uri'] = $redirect_uri;
        $cache->set($cache_key, $cache_val, 1800);

        $offiacc->login($redirect_uri, $state);
    }

    /**
     * @Apidoc\Title("lang(绑定网站应用)")
     * @Apidoc\Desc("拼接参数后打开链接")
     * @Apidoc\Query("app", type="string", default="wx", desc="应用：wx 微信网站应用，qq QQ网站应用，wb 微博网站应用")
     * @Apidoc\Query("jump_url", type="string", require=true, desc="绑定成功后跳转地址，会携带 token 参数")
     * @Apidoc\Query("redirect_uri", type="string", require=false, desc="redirect_uri，调试使用")
     */
    public function bindWebsite()
    {
        $setting  = SettingService::info();
        $param    = $this->params(['app/s' => 'wx', 'jump_url/s' => '', 'redirect_uri/s' => '', $setting['token_name'] => '']);
        $rule     = ['app' => 'require', 'jump_url' => 'require|url', 'redirect_uri' => 'url', $setting['token_name'] => 'require'];
        $validate = validate($rule, [], false, false);
        if (!$validate->check($param)) {
            echo $validate->getError();
            return;
        }

        $member_id    = member_id(true);
        $member_token = member_token();
        if (empty($member_token)) {
            echo lang('绑定失败，请重试');
            return;
        }

        $app['app'] = $param['app'];
        if ($app['app'] == 'wx') {
            $app['platform']    = SettingService::PLATFORM_WX;
            $app['application'] = SettingService::APP_WX_WEBSITE;
            $website            = new \thirdsdk\WxWebsite($setting['wx_website_appid'], $setting['wx_website_appsecret']);
        } elseif ($app['app'] == 'qq') {
            $app['platform']    = SettingService::PLATFORM_QQ;
            $app['application'] = SettingService::APP_QQ_WEBSITE;
            $website            = new \thirdsdk\QqWebsite($setting['qq_website_appid'], $setting['qq_website_appsecret']);
        } elseif ($app['app'] == 'wb') {
            $app['platform']    = SettingService::PLATFORM_WB;
            $app['application'] = SettingService::APP_WB_WEBSITE;
            $website            = new \thirdsdk\WbWebsite($setting['wb_website_appid'], $setting['wb_website_appsecret']);
        } else {
            echo lang('app错误');
            return;
        }

        $redirect_uri = $param['redirect_uri'] ?: (string) url('api/member.Login/redirectUri', [], false, true);
        $state        = md5(uniqid('website' . $member_id, true));

        $cache = new Cache(false);
        $cache_key = SettingService::OFFIACC_WEBSITE_KEY . $state;
        $cache_val['type']         = 'website';
        $cache_val['app']          = $app;
        $cache_val['jump_url']     = $param['jump_url'];
        $cache_val['redirect_uri'] = $redirect_uri;
        $cache_val['token']        = $member_token;
        $cache->set($cache_key, $cache_val, 1800);

        $website->login($redirect_uri, $state);
    }

    /**
     * @Apidoc\Title("lang(绑定移动应用)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("app", type="string", default="wx", desc="应用：wx 微信移动应用，qq QQ移动应用")
     * @Apidoc\Param("code", type="string", require=true, desc="wx，code")
     * @Apidoc\Param("access_token", type="string", require=true, desc="qq，access_token")
     * @Apidoc\Param("openid", type="string", require=true, desc="qq，openid")
     * @Apidoc\Param("userinfo", type="object", require=false, desc="用户信息：headimgurl头像，nickname昵称")
     * @Apidoc\Returned(ref={MemberService::class,"info"}, field="member_id,nickname,username,login_ip,login_time,login_num,avatar_url")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     */
    public function bindMobile()
    {
        $member_id = member_id(true);
        $param     = $this->params(['app/s' => 'wx', 'code/s' => '', 'access_token/s' => '', 'openid/s' => '', 'userinfo/a' => []]);
        $rule      = ['app' => 'require', 'userinfo' => 'array'];
        if ($param['app'] == 'wx') {
            $rule['code'] = 'require';
        } elseif ($param['app'] == 'qq') {
            $rule['access_token'] = 'require';
            $rule['openid'] = 'require';
        } else {
            return error(lang('app错误'));
        }
        $validate = validate($rule, [], false, false);
        if (!$validate->check($param)) {
            return error($validate->getError());
        }

        $setting = SettingService::info();
        if ($param['app'] == 'wx') {
            $platform    = SettingService::PLATFORM_WX;
            $application = SettingService::APP_WX_MOBILE;
            $mobile      = new \thirdsdk\WxMobile($setting['wx_mobile_appid'], $setting['wx_mobile_appsecret']);
            $user_info   = $mobile->login($param['code']);
        } elseif ($param['app'] == 'qq') {
            $platform    = SettingService::PLATFORM_QQ;
            $application = SettingService::APP_QQ_MOBILE;
            $mobile      = new \thirdsdk\QqMobile($setting['qq_mobile_appid'], $setting['qq_mobile_appsecret']);
            $user_info   = $mobile->login($param['access_token'], $param['openid']);
        } else {
            return error(lang('app错误'));
        }

        $user_info['member_id']   = $member_id;
        $user_info['platform']    = $platform;
        $user_info['application'] = $application;
        if (isset($param['userinfo']['headimgurl'])) {
            $user_info['headimgurl'] = $param['userinfo']['headimgurl'];
        }
        if (isset($param['userinfo']['nickname'])) {
            $user_info['nickname'] = $param['userinfo']['nickname'];
        }

        $data = MemberService::thirdBind($user_info);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(第三方账号列表)")
     * @Apidoc\Returned("list", type="array", desc="第三方账号列表", children={
     *   @Apidoc\Returned(ref={ThirdService::class,"info"}, field="third_id,member_id,platform,application,headimgurl,nickname,create_time,login_time,platform_name,application_name"),
     * })
     */
    public function thirdList()
    {
        $param['member_id'] = member_id(true);

        validate(MemberValidate::class)->scene('info')->check($param);

        $where = where_disdel([['member_id', '=', $param['member_id']]]);

        $data = ThirdService::list($where, 0, 0, [], '', false);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(第三方账号解绑)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("third_id", type="int", require=true, desc="第三方账号id")
     */
    public function thirdUnbind()
    {
        $member_id = member_id(true);
        $third_id  = $this->param('third_id', '');
        if (empty($third_id)) {
            return error(lang('third_id必须'));
        }

        $data = MemberService::thirdUnbind($third_id, $member_id);

        return success($data);
    }
}
