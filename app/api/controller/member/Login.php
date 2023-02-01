<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use think\facade\Cache;
use app\api\service\LoginService;
use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\SettingService;
use app\common\service\setting\WechatService;
use app\common\service\utils\SmsUtils;
use app\common\service\utils\EmailUtils;
use app\common\service\utils\CaptchaUtils;
use app\common\cache\utils\CaptchaSmsCache;
use app\common\cache\utils\CaptchaEmailCache;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("200")
 */
class Login extends BaseController
{
    /**
     * @Apidoc\Title("登录验证码")
     * @Apidoc\Returned(ref="captchaReturn")
     */
    public function captcha()
    {
        $setting = SettingService::info();

        $data['captcha_switch'] = $setting['captcha_login'];

        if ($setting['captcha_login']) {
            $captcha = CaptchaUtils::create();
            $data    = array_merge($data, $captcha);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("acc_type", type="string", require=false, desc="账号类型：username用户名、phone手机、email邮箱")
     * @Apidoc\Param("account", type="string", require=true, desc="账号：用户名、手机、邮箱")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel", field="password")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel")
     * @Apidoc\After(event="setGlobalParam", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     */
    public function login()
    {
        $param['acc_type']     = $this->request->param('acc_type/s', '');
        $param['account']      = $this->request->param('account/s', '');
        $param['password']     = $this->request->param('password/s', '');
        $param['captcha_id']   = $this->request->param('captcha_id/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');

        if (empty($param['account'])) {
            exception('请输入账号');
        }
        if (empty($param['password'])) {
            exception('请输入密码');
        }

        $setting = SettingService::info();
        if ($setting['captcha_login']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($captcha_check)) {
                exception('验证码错误');
            }
        }

        $data = LoginService::login($param, $param['acc_type']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("手机登录验证码")
     * @Apidoc\Query("phone", type="string", require=true, desc="手机")
     */
    public function phoneCaptcha()
    {
        $param['phone'] = $this->request->param('phone/s', '');

        validate(MemberValidate::class)->scene('phoneLoginCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("手机登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("phone", type="string", require=true, desc="手机")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel")
     * @Apidoc\After(event="setGlobalParam", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     */
    public function phoneLogin()
    {
        $param['phone']        = $this->request->param('phone/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('phoneLogin')->check($param);
        if (empty($param['captcha_code'])) {
            exception('请输入验证码');
        }
        $captcha = CaptchaSmsCache::get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        $data = LoginService::login($param, 'phone');
        CaptchaSmsCache::del($param['phone']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("邮箱登录验证码")
     * @Apidoc\Query("email", type="string", require=true, desc="邮箱")
     */
    public function emailCaptcha()
    {
        $param['email'] = $this->request->param('email/s', '');

        validate(MemberValidate::class)->scene('emailLoginCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email", type="string", require=true, desc="邮箱")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel")
     * @Apidoc\After(event="setGlobalParam", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     */
    public function emailLogin()
    {
        $param['email']        = $this->request->param('email/s', '');
        $param['captcha_code'] = $this->request->param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('emailLogin')->check($param);
        if (empty($param['captcha_code'])) {
            exception('请输入验证码');
        }
        $captcha = CaptchaEmailCache::get($param['email']);
        if ($captcha != $param['captcha_code']) {
            exception('验证码错误');
        }

        $data = LoginService::login($param, 'email');
        CaptchaEmailCache::del($param['email']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("公众号登录")
     * @Apidoc\Query("offiurl", type="string", require=true, desc="登录成功后跳转地址，会携带 token 参数")
     */
    public function offi()
    {
        $setting = SettingService::info();
        $ApiToken = $this->request->param($setting['token_name'] . '/s', '');
        if ($ApiToken) {
            die('登录成功，请保存 ' . $setting['token_name']);
        }

        $offiurl = $this->request->param('offiurl/s', '');
        if (empty($offiurl)) {
            $offiurl = (string) url('', [], false, true);
            // exception('offiurl must');
        }
       
        Cache::set('offiurl', $offiurl, 30);

        $officallback = (string) url('officallback', [], false, true);
        
        $config = [
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => $officallback,
            ],
        ];

        $app = WechatService::offi($config);

        $oauth = $app->oauth;


        $oauth->redirect()->send();
    }
    // 公众号登录回调
    public function officallback()
    {
        $app  = WechatService::offi();
        $user = $app->oauth->user()->getOriginal();
        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败:' . $user['errmsg']);
        }

        $userinfo = [
            'unionid' => '',
            'openid' => '',
            'nickname' => '',
            'sex' => '',
            'city' => '',
            'province' => '',
            'country' => '',
            'headimgurl' => '',
            'language' => '',
            'privilege' => ''
        ];
        foreach ($userinfo as $k => $v) {
            if (isset($user[$k])) {
                $userinfo[$k] = $user[$k];
            }
        }
        $userinfo['login_ip']    = $this->request->ip();
        $userinfo['reg_channel'] = SettingService::REG_CHANNEL_OFFI;
        $userinfo['reg_type']    = SettingService::REG_TYPE_WECHAT;

        $data = LoginService::wechat($userinfo);

        $setting = SettingService::info();
        $offiurl = Cache::get('offiurl');
        $offiurl = $offiurl . '?' . $setting['token_name'] . '=' . $data[$setting['token_name']];

        Header('Location:' . $offiurl);
    }

    /**
     * @Apidoc\Title("小程序登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="wx.login，用户登录凭证")
     * @Apidoc\Param("user_info", type="object", require=false, desc="wx.getUserProfile，微信用户信息")
     * @Apidoc\Param("iv", type="string", require=false, desc="加密算法的初始向量")
     * @Apidoc\Param("encrypted_data", type="string", require=false, desc="包括敏感数据在内的完整用户信息的加密数据")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel")
     * @Apidoc\After(event="setGlobalParam", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     */
    public function mini()
    {
        $code           = $this->request->param('code/s', '');
        $user_info      = $this->request->param('user_info/a', []);
        $iv             = $this->request->param('iv/s', '');
        $encrypted_data = $this->request->param('encrypted_data/s', '');
        if (empty($code)) {
            exception('code must');
        }

        $app  = WechatService::mini();
        $user = $app->auth->session($code);

        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败:' . $user['errmsg']);
        }

        $userinfo = [
            'unionid' => '',
            'openid' => '',
            'nickname' => '',
            'gender' => '',
            'city' => '',
            'province' => '',
            'country' => '',
            'headimgurl' => '',
            'language' => '',
            'privilege' => ''
        ];

        if ($iv && $encrypted_data) {
            $decrypted_data = $app->encryptor->decryptData($user['session_key'], $iv, $encrypted_data);
            $user = array_merge($user, $user_info, $decrypted_data);
        }

        $user['nickname']   = isset($user['nickName']) ? $user['nickName'] : '';
        $user['gender']     = isset($user['gender']) ? $user['gender'] : 0;
        $user['headimgurl'] = isset($user['avatarUrl']) ? $user['avatarUrl'] : '';
        foreach ($userinfo as $k => $v) {
            if (isset($user[$k])) {
                $userinfo[$k] = $user[$k];
            }
        }
        $userinfo['login_ip']    = $this->request->ip();
        $userinfo['reg_channel'] = SettingService::REG_CHANNEL_MINI;
        $userinfo['reg_type']    = SettingService::REG_TYPE_WECHAT;

        $data = LoginService::wechat($userinfo);

        return success($data);
    }
}
