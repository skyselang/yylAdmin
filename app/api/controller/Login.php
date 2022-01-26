<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 登录退出控制器
namespace app\api\controller;

use think\facade\Request;
use think\facade\Cache;
use app\api\service\LoginService;
use app\common\validate\member\MemberValidate;
use app\common\service\setting\WechatService;
use app\common\service\setting\SettingService;
use app\common\utils\CaptchaUtils;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Sort("210")
 * @Apidoc\Group("login")
 */
class Login
{
    /**
     * @Apidoc\Title("验证码")
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
     * @Apidoc\Title("登录（账号）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\member\MemberModel\password")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\loginReturn")
     */
    public function login()
    {
        $param['username']     = Request::param('username/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('login')->check($param);

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

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("登录（公众号）")
     * @Apidoc\Param("offiurl", type="string", require=true, desc="登录成功后跳转地址，会携带 api_token 参数")
     */
    public function offi()
    {
        $api_token = Request::param('api_token/s', '');
        if ($api_token) {
            die('Please save member_id and api_token');
        }

        $offiurl = Request::param('offiurl/s', '');
        if (empty($offiurl)) {
            $offiurl = (string) url('', [], false);
            // exception('offiurl must');
        }

        Cache::set('offiurl', $offiurl, 30);

        $config = [
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => (string) url('officallback', [], false),
            ],
        ];

        $app = WechatService::offi($config);

        $oauth = $app->oauth;

        $oauth->redirect()->send();
    }
    // 登录（公众号）回调
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
        $userinfo['login_ip']    = Request::ip();
        $userinfo['reg_channel'] = 2;

        $data = LoginService::wechat($userinfo);

        $offiurl = Cache::get('offiurl');
        $offiurl = $offiurl . '?api_token=' . $data['api_token'];

        Header("Location:" . $offiurl);
    }

    /**
     * @Apidoc\Title("登录（小程序）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="wx.login，用户登录凭证")
     * @Apidoc\Param("user_info", type="object", require=false, desc="wx.getUserProfile，微信用户信息")
     * @Apidoc\Param("iv", type="string", require=false, desc="加密算法的初始向量")
     * @Apidoc\Param("encrypted_data", type="string", require=false, desc="包括敏感数据在内的完整用户信息的加密数据")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\loginReturn")
     */
    public function mini()
    {
        $code           = Request::param('code/s', '');
        $user_info      = Request::param('user_info/a', []);
        $iv             = Request::param('iv/s', '');
        $encrypted_data = Request::param('encrypted_data/s', '');
        if (empty($code)) {
            exception('code must');
        }

        $app = WechatService::mini();
        $user = $app->auth->session($code);

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

        if ($iv && $encrypted_data) {
            $decrypted_data = $app->encryptor->decryptData($user['session_key'], $iv, $encrypted_data);
        }

        $user = array_merge($user, $user_info, $decrypted_data);
        $user['nickname']   = isset($user['nickName']) ? $user['nickName'] : '';
        $user['sex']        = isset($user['gender']) ? $user['gender'] : 0;
        $user['headimgurl'] = isset($user['avatarUrl']) ? $user['avatarUrl'] : '';
        foreach ($userinfo as $k => $v) {
            if (isset($user[$k])) {
                $userinfo[$k] = $user[$k];
            }
        }
        $userinfo['login_ip']    = Request::ip();
        $userinfo['reg_channel'] = 3;

        $data = LoginService::wechat($userinfo);

        return success($data);
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     */
    public function logout()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('logout')->check($param);

        $data = LoginService::logout($param['member_id']);

        return success($data, '退出成功');
    }
}
