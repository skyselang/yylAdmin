<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-05-06
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
     * @Apidoc\Title("登录")
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
     * @Apidoc\Desc("拼接callback参数后打开")
     * @Apidoc\Method("GET")
     * @Apidoc\Param("callback", type="string", require=true, desc="登录成功后跳转的页面地址,会携带member_token")
     */
    public function offi()
    {
        $callback = Request::param('callback/s', '');

        if (empty($callback)) {
            die('callback must');
        }

        Cache::set('offiLoginCallback', $callback, 15);

        $offi_info = SettingWechatService::offiInfo();

        $config = [
            'app_id' => $offi_info['appid'],
            'secret' => $offi_info['appsecret'],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => url('officallback', [], false),
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

        $app   = Factory::officialAccount($config);
        $oauth = $app->oauth;
        $user  = $oauth->user()->getOriginal();

        if (empty($user) || !isset($user['openid'])) {
            exception('微信登录失败，请重试！offi');
        }

        $user['login_ip'] = Request::ip();

        $data = LoginService::offiLogin($user);

        $callback = Cache::get('offiLoginCallback');
        $callback = $callback . '?member_token=' . $data['member_token'];

        Header("Location:" . $callback);
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
