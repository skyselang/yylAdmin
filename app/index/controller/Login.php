<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-14
 */

namespace app\index\controller;

use think\facade\Cache;
use think\facade\Request;
use app\index\service\LoginService;
use app\common\service\SettingService;
use app\common\validate\MemberValidate;
use app\common\service\SettingWechatService;
use app\common\utils\VerifyUtils;
use hg\apidoc\annotation as Apidoc;
use EasyWeChat\Factory;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Sort("3")
 */
class Login
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned(ref="returnVerify")
     */
    public function verify()
    {
        $setting = SettingService::verifyInfo();

        if ($setting['verify_login']) {
            $data = VerifyUtils::create();
        } else {
            $data['verify_switch'] = $setting['verify_login'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录（账号密码）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\MemberModel\password")
     * @Apidoc\Param(ref="paramVerify")
     * @Apidoc\Returned(ref="return")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\login")
     * )
     */
    public function login()
    {
        $param['username']    = Request::param('username/s', '');
        $param['password']    = Request::param('password/s', '');
        $param['verify_id']   = Request::param('verify_id/s', '');
        $param['verify_code'] = Request::param('verify_code/s', '');

        $setting = SettingService::verifyInfo();
        if ($setting['verify_login']) {
            $check = VerifyUtils::check($param['verify_id'], $param['verify_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        validate(MemberValidate::class)->scene('login')->check($param);

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("登录（公众号）")
     * @Apidoc\Method("GET")
     * @Apidoc\Param("offiurl", type="string", require=true, desc="登录成功后跳转的页面地址，会携带member_id,member_token")
     */
    public function offi()
    {
        $offiurl = Request::param('offiurl/s', '');

        if (empty($offiurl)) {
            exception('offiurl must');
        }

        Cache::set('offiurl', $offiurl, 15);

        $offi_info = SettingWechatService::offiInfo();

        $config = [
            'app_id' => $offi_info['appid'],
            'secret' => $offi_info['appsecret'],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => (string) url('officallback', [], false),
            ],
        ];

        $app = Factory::officialAccount($config);

        $oauth = $app->oauth;

        $oauth->redirect()->send();
    }
    // 登录（公众号）回调
    public function officallback()
    {
        $offi_info = SettingWechatService::offiInfo();

        $config = [
            'app_id' => $offi_info['appid'],
            'secret' => $offi_info['appsecret'],
        ];

        $app  = Factory::officialAccount($config);
        $user = $app->oauth->user()->getOriginal();

        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败，请重试！offi');
        }

        $userinfo['openid'] = $user['openid'];
        if (isset($user['nickname'])) {
            $userinfo['nickname'] = $user['nickname'];
        }
        if (isset($user['sex'])) {
            $userinfo['sex'] = $user['sex'];
        }
        if (isset($user['city'])) {
            $userinfo['city'] = $user['city'];
        }
        if (isset($user['province'])) {
            $userinfo['province'] = $user['province'];
        }
        if (isset($user['country'])) {
            $userinfo['country'] = $user['country'];
        }
        if (isset($user['headimgurl'])) {
            $userinfo['headimgurl'] = $user['headimgurl'];
        }
        if (isset($user['language'])) {
            $userinfo['language'] = $user['language'];
        }
        if (isset($user['privilege'])) {
            $userinfo['privilege'] = serialize($user['privilege']);
        }
        $userinfo['login_ip']  = Request::ip();

        $data = LoginService::offiLogin($userinfo);

        $offiurl = Cache::get('offiurl');
        $offiurl = $offiurl . '?member_id=' . $data['member_id'] . '&member_token=' . $data['member_token'];

        Header("Location:" . $offiurl);
    }

    /**
     * @Apidoc\Title("登录（小程序）")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="wx.login()，用户登录凭证")
     * @Apidoc\Param("user_info", type="object", require=false, desc="wx.getUserProfile，微信用户信息")
     */
    public function mini()
    {
        $code = Request::param('code/s', '');
        $user_info = Request::param('user_info/a', []);

        if (empty($code)) {
            die('code must');
        }

        $mini_info = SettingWechatService::miniInfo();

        $config = [
            'app_id' => $mini_info['appid'],
            'secret' => $mini_info['appsecret']
        ];

        $app  = Factory::miniProgram($config);
        $user = $app->auth->session($code);

        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败，请重试！mini');
        }

        $userinfo['openid'] = $user['openid'];
        if (isset($user_info['nickName'])) {
            $userinfo['nickname'] = $user_info['nickName'];
        }
        if (isset($user_info['gender'])) {
            $userinfo['sex'] = $user_info['gender'];
        }
        if (isset($user_info['city'])) {
            $userinfo['city'] = $user_info['city'];
        }
        if (isset($user_info['province'])) {
            $userinfo['province'] = $user_info['province'];
        }
        if (isset($user_info['country'])) {
            $userinfo['country'] = $user_info['country'];
        }
        if (isset($user_info['avatarUrl'])) {
            $userinfo['headimgurl'] = $user_info['avatarUrl'];
        }
        if (isset($user_info['language'])) {
            $userinfo['language'] = $user_info['language'];
        }
        $userinfo['login_ip'] = Request::ip();

        $data = LoginService::miniLogin($userinfo);

        return success($data);
    }

    /**
     * @Apidoc\Title("退出")
     * @Apidoc\Method("POST")
     * @Apidoc\Header(ref="headerIndex")
     * @Apidoc\Returned(ref="return")
     */
    public function logout()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('logout')->check($param);

        $data = LoginService::logout($param['member_id']);

        return success($data, '退出成功');
    }
}
