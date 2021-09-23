<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 登录退出控制器
namespace app\index\controller;

use think\facade\Request;
use think\facade\Cache;
use app\index\service\LoginService;
use app\common\utils\CaptchaUtils;
use app\common\validate\MemberValidate;
use app\common\service\WechatService;
use app\common\service\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("登录退出")
 * @Apidoc\Sort("3")
 * @Apidoc\Group("login")
 */
class Login
{
    /**
     * @Apidoc\Title("验证码")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned(ref="returnCaptcha")
     */
    public function captcha()
    {
        $setting = SettingService::captchaInfo();
        if ($setting['captcha_login']) {
            $data = CaptchaUtils::create();
        } else {
            $data['captcha_switch'] = $setting['captcha_login'];
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录(账号)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\MemberModel\username")
     * @Apidoc\Param(ref="app\common\model\MemberModel\password")
     * @Apidoc\Param(ref="paramCaptcha")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\login")
     * )
     */
    public function login()
    {
        $param['username']     = Request::param('username/s', '');
        $param['password']     = Request::param('password/s', '');
        $param['captcha_id']   = Request::param('captcha_id/s', '');
        $param['captcha_code'] = Request::param('captcha_code/s', '');

        validate(MemberValidate::class)->scene('login')->check($param);

        $setting = SettingService::captchaInfo();
        if ($setting['captcha_login']) {
            if (empty($param['captcha_code'])) {
                exception('请输入验证码');
            }
            $check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
            if (empty($check)) {
                exception('验证码错误');
            }
        }

        $data = LoginService::login($param);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("登录(公众号)")
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
    // 登录(公众号)回调
    public function officallback()
    {
        $app  = WechatService::offi();
        $user = $app->oauth->user()->getOriginal();
        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败请重试！'.$user['errmsg']);
        }

        $userinfo['unionid'] = isset($user['unionid']) ? $user['unionid'] : '';
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
     * @Apidoc\Title("登录(小程序)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("code", type="string", require=true, desc="wx.login，用户登录凭证")
     * @Apidoc\Param("user_info", type="object", require=false, desc="wx.getUserProfile，微信用户信息")
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据",
     *      @Apidoc\Returned(ref="app\common\model\MemberModel\login")
     * )
     */
    public function mini()
    {
        $code      = Request::param('code/s', '');
        $user_info = Request::param('user_info/a', []);
        if (empty($code)) {
            exception('code must');
        }

        $app  = WechatService::mini();
        $user = $app->auth->session($code);
        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败请重试！'.$user['errmsg']);
        }

        $userinfo['unionid'] = isset($user['unionid']) ? $user['unionid'] : '';
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
     * @Apidoc\Returned(ref="returnCode")
     * @Apidoc\Returned("data", type="object", desc="返回数据")
     */
    public function logout()
    {
        $param['member_id'] = member_id();

        validate(MemberValidate::class)->scene('logout')->check($param);

        $data = LoginService::logout($param['member_id']);

        return success($data, '退出成功');
    }
}
