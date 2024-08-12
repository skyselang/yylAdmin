<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use think\facade\Validate;
use app\api\service\LoginService;
use app\common\controller\BaseController;
use app\common\validate\member\MemberValidate;
use app\common\service\member\MemberService;
use app\common\service\member\SettingService;
use app\common\service\member\TokenService;
use app\common\service\utils\SmsUtils;
use app\common\service\utils\EmailUtils;
use app\common\service\utils\CaptchaUtils;
use app\common\service\utils\AjCaptchaUtils;
use app\common\cache\Cache;
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
     * @Apidoc\Desc("get获取验证码，post验证行为验证码")
     * @Apidoc\Method("GET,POST")
     * @Apidoc\Param("captchaType", type="string", require=true, desc="行为，验证码方式：blockPuzzle、clickWord")
     * @Apidoc\Param("pointJson", type="string", default="", desc="行为，pointJson")
     * @Apidoc\Param("token", type="string", default="", desc="行为，token")
     * @Apidoc\Returned(ref="captchaReturn")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function captcha()
    {
        $data = SettingService::info('is_captcha_login,captcha_mode,captcha_type');

        $data['captcha_switch'] = $data['is_captcha_login'];
        if ($this->request->isGet()) {
            if ($data['captcha_switch']) {
                if ($data['captcha_mode'] == 2) {
                    $AjCaptchaUtils = new AjCaptchaUtils();
                    $captcha = $AjCaptchaUtils->get($data['captcha_type']);
                    $data = array_merge($data, $captcha);
                } else {
                    $captcha = CaptchaUtils::create($data['captcha_type']);
                    $data = array_merge($data, $captcha);
                }
            }
        } else {
            $captchaData = $this->param('');
            $AjCaptchaUtils = new AjCaptchaUtils();
            $data = $AjCaptchaUtils->check($data['captcha_type'], $captchaData);
        }

        return success($data);
    }

    /**
     * @Apidoc\Title("登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("acc_type", type="string", require=false, desc="账号类型：username用户名、phone手机、email邮箱")
     * @Apidoc\Param("account", type="string", require=true, desc="账号：用户名、手机、邮箱")
     * @Apidoc\Param("password", type="string", require=true, default="123456", desc="密码")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\platforms")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\applications")
     * @Apidoc\Param(ref="captchaParam")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr", field="avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,nickname,username,login_ip,login_time,login_num")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function login()
    {
        $setting = SettingService::info();
        if (!$setting['is_login']) {
            return error('系统维护，无法登录');
        }

        $param = $this->params([
            'acc_type/s'     => '',
            'account/s'      => '',
            'password/s'     => '',
            'captcha_id/s'   => '',
            'captcha_code/s' => '',
            'ajcaptcha',
        ]);
        if (empty($param['account'])) {
            return error('请输入账号');
        }
        if (empty($param['password'])) {
            return error('请输入密码');
        }

        $setting = SettingService::info();
        if ($setting['is_captcha_login']) {
            if ($setting['captcha_mode'] == 2) {
                $AjCaptchaUtils = new AjCaptchaUtils();
                $captcha_check = $AjCaptchaUtils->checkTwo($setting['captcha_type'], $param['ajcaptcha']);
                if ($captcha_check['error']) {
                    exception('验证码错误');
                }
            } else {
                if (empty($param['captcha_id'])) {
                    return error('captcha_id must');
                }
                if (empty($param['captcha_code'])) {
                    return error('请输入验证码');
                }
                $captcha_check = CaptchaUtils::check($param['captcha_id'], $param['captcha_code']);
                if (empty($captcha_check)) {
                    return error('验证码错误');
                }
            }
        }

        $data = LoginService::login($param, $param['acc_type']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("手机登录验证码")
     * @Apidoc\Query("phone", type="string", require=true, desc="手机")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function phoneCaptcha()
    {
        $setting = SettingService::info();
        if (!$setting['is_phone_login']) {
            return error('系统维护，无法登录');
        }

        $param = $this->params(['phone/s' => '']);

        validate(MemberValidate::class)->scene('phoneLoginCaptcha')->check($param);

        SmsUtils::captcha($param['phone']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("手机登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("phone", type="string", require=true, desc="手机")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="手机验证码")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\platforms")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\applications")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr", field="avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,nickname,username,login_ip,login_time,login_num")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function phoneLogin()
    {
        $setting = SettingService::info();
        if (!$setting['is_phone_login']) {
            return error('系统维护，无法登录');
        }

        $param = $this->params(['phone/s' => '', 'captcha_code/s' => '']);

        validate(MemberValidate::class)->scene('phoneLogin')->check($param);
        if (empty($param['captcha_code'])) {
            return error('请输入验证码');
        }
        $captcha = CaptchaSmsCache::get($param['phone']);
        if ($captcha != $param['captcha_code']) {
            return error('验证码错误');
        }

        $data = LoginService::login($param, 'phone');
        CaptchaSmsCache::del($param['phone']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("邮箱登录验证码")
     * @Apidoc\Query("email", type="string", require=true, desc="邮箱")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function emailCaptcha()
    {
        $setting = SettingService::info();
        if (!$setting['is_email_login']) {
            return error('系统维护，无法登录');
        }

        $param = $this->params(['email/s' => '']);

        validate(MemberValidate::class)->scene('emailLoginCaptcha')->check($param);

        EmailUtils::captcha($param['email']);

        return success([], '发送成功');
    }

    /**
     * @Apidoc\Title("邮箱登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("email", type="string", require=true, desc="邮箱")
     * @Apidoc\Param("captcha_code", type="string", require=true, desc="邮箱验证码")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\platforms")
     * @Apidoc\Param(ref="app\common\service\member\SettingService\applications")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,nickname,username,login_ip,login_time,login_num")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function emailLogin()
    {
        $setting = SettingService::info();
        if (!$setting['is_email_login']) {
            return error('系统维护，无法登录');
        }

        $param = $this->params(['email/s' => '', 'captcha_code/s' => '']);

        validate(MemberValidate::class)->scene('emailLogin')->check($param);
        if (empty($param['captcha_code'])) {
            return error('请输入验证码');
        }
        $captcha = CaptchaEmailCache::get($param['email']);
        if ($captcha != $param['captcha_code']) {
            return error('验证码错误');
        }

        $data = LoginService::login($param, 'email');
        CaptchaEmailCache::del($param['email']);

        return success($data, '登录成功');
    }

    /**
     * @Apidoc\Title("小程序登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Desc("参数register=0静默登录，如果未注册（返回码code=402），则拉起授权，获取用户信息、手机号，传register=1进行注册")
     * @Apidoc\Param("app", type="string", default="wx", desc="应用：wx 微信小程序，qq QQ小程序")
     * @Apidoc\Param("code", type="string", require=true, desc="code 用户登录凭证")
     * @Apidoc\Param("phone_code", type="string", require=false, desc="code 手机号获取凭证")
     * @Apidoc\Param("register", type="int", default="0", desc="是否注册1是0否")
     * @Apidoc\Param("headimgurl", type="string", require=false, desc="头像")
     * @Apidoc\Param("nickname", type="string", require=false, desc="昵称")
     * @Apidoc\Param("avatar_id", type="int", require=false, desc="上传头像返回的file_id")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr", field="avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,nickname,username,login_ip,login_time,login_num")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function miniapp()
    {
        $param = $this->params([
            'app/s'        => 'wx',
            'code/s'       => '',
            'phone_code/s' => '',
            'register/d'   => 0,
            'headimgurl/s' => '',
            'nickname/s'   => '',
            'avatar_id/d'  => 0,
        ]);
        $validate = Validate::rule(['app' => 'require', 'code' => 'require']);
        if (!$validate->check($param)) {
            return error($validate->getError());
        }

        $setting = SettingService::info();
        if ($param['app'] == 'wx') {
            $platform    = SettingService::PLATFORM_WX;
            $application = SettingService::APP_WX_MINIAPP;
            $miniapp     = new \thirdsdk\WxMiniapp($setting['wx_miniapp_appid'], $setting['wx_miniapp_appsecret']);
            if ($param['phone_code'] ?? '') {
                $phone = $miniapp->getPhoneNumber($param['phone_code']);
            }
        } elseif ($param['app'] == 'qq') {
            $platform    = SettingService::PLATFORM_QQ;
            $application = SettingService::APP_QQ_MINIAPP;
            $miniapp     = new \thirdsdk\QqMiniapp($setting['qq_miniapp_appid'], $setting['qq_miniapp_appsecret']);
        } else {
            return error('app value error');
        }

        $user_info                = $miniapp->login($param['code']);
        $user_info['register']    = $param['register'];
        $user_info['avatar_id']   = $param['avatar_id'];
        $user_info['platform']    = $platform;
        $user_info['application'] = $application;
        if (isset($param['headimgurl'])) {
            $user_info['headimgurl'] = $param['headimgurl'];
        }
        if (isset($param['nickname'])) {
            $user_info['nickname'] = $param['nickname'];
        }
        if ($phone ?? '') {
            $user_info['phone'] = $phone;
        }

        $data = LoginService::thirdLogin($user_info);
        $data['phone'] = $phone ?? '';

        return success($data);
    }

    /**
     * @Apidoc\Title("公众号登录")
     * @Apidoc\Desc("拼接参数后打开链接")
     * @Apidoc\Query("app", type="string", default="wx", desc="应用：wx 微信公众号")
     * @Apidoc\Query("jump_url", type="string", require=true, desc="登录成功后跳转地址，会携带 token 参数")
     * @Apidoc\Query("redirect_uri", type="string", require=false, desc="redirect_uri，调试使用")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function offiacc()
    {
        $param    = $this->params(['app/s' => 'wx', 'jump_url/s' => '', 'redirect_uri/s' => '']);
        $validate = Validate::rule(['app' => 'require', 'jump_url' => 'require|url', 'redirect_uri' => 'url']);
        if (!$validate->check($param)) {
            echo $validate->getError();
            return;
        }

        $setting = SettingService::info();
        $app['app']  = $param['app'];
        if ($app['app'] == 'wx') {
            $app['platform']    = SettingService::PLATFORM_WX;
            $app['application'] = SettingService::APP_WX_OFFIACC;
            $offiacc            = new \thirdsdk\WxOffiacc($setting['wx_offiacc_appid'], $setting['wx_offiacc_appsecret']);
        } else {
            echo 'app value error';
            return;
        }

        $redirect_uri = $param['redirect_uri'] ?: (string) url('redirectUri', [], false, true);
        $state        = md5(uniqid('offiacc', true));

        $cache['type']         = 'offiacc';
        $cache['app']          = $app;
        $cache['jump_url']     = $param['jump_url'];
        $cache['redirect_uri'] = $redirect_uri;
        Cache::set(SettingService::OFFIACC_WEBSITE_KEY . $state, $cache, 600);

        $offiacc->login($redirect_uri, $state);
    }

    /**
     * @Apidoc\Title("网站应用登录")
     * @Apidoc\Desc("拼接参数后打开链接")
     * @Apidoc\Query("app", type="string", default="wx", desc="应用：wx 微信网站应用，qq QQ网站应用，wb 微博网站应用")
     * @Apidoc\Query("jump_url", type="string", require=true, desc="登录成功后跳转地址，会携带 token 参数")
     * @Apidoc\Query("redirect_uri", type="string", require=false, desc="redirect_uri，调试使用")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function website()
    {
        $param    = $this->params(['app/s' => 'wx', 'jump_url/s' => '', 'redirect_uri/s' => '']);
        $validate = Validate::rule(['app' => 'require', 'jump_url' => 'require|url', 'redirect_uri' => 'url']);
        if (!$validate->check($param)) {
            echo $validate->getError();
            return;
        }

        $setting = SettingService::info();
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
            echo 'app value error';
            return;
        }

        $redirect_uri = $param['redirect_uri'] ?: (string) url('redirectUri', [], false, true);
        $state        = md5(uniqid('website', true));

        $cache['type']         = 'website';
        $cache['app']          = $app;
        $cache['jump_url']     = $param['jump_url'];
        $cache['redirect_uri'] = $redirect_uri;
        Cache::set(SettingService::OFFIACC_WEBSITE_KEY . $state, $cache, 600);

        $website->login($redirect_uri, $state);
    }

    /**
     * @Apidoc\Title("公众号/网站登录/绑定回调")
     * @Apidoc\Query("code", type="string", require=true, desc="code")
     * @Apidoc\Query("state", type="string", require=true, desc="state")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function redirectUri()
    {
        $param    = $this->params(['code/s' => '', 'state/s' => '']);
        $validate = Validate::rule(['code' => 'require', 'state' => 'require']);
        if (!$validate->check($param)) {
            echo $validate->getError();
            return;
        }
        $cache        = Cache::get(SettingService::OFFIACC_WEBSITE_KEY . $param['state']);
        $type         = $cache['type'];
        $app          = $cache['app'];
        $jump_url     = $cache['jump_url'];
        $token        = $cache['token'] ?? '';
        $redirect_uri = $cache['redirect_uri'] ?? '';
        if (empty($app) || empty($jump_url)) {
            if ($token) {
                echo '绑定失败，请重试！';
            } else {
                echo '登录失败，请重试！';
            }
            return;
        }

        $setting = SettingService::info();
        if ($type == 'offiacc') {
            if ($app['app'] == 'wx') {
                $offiacc   = new \thirdsdk\WxOffiacc($setting['wx_offiacc_appid'], $setting['wx_offiacc_appsecret']);
                $user_info = $offiacc->getUserinfo($param['code']);
            } else {
                echo 'app value error';
                return;
            }
        } elseif ($type == 'website') {
            if ($app['app'] == 'wx') {
                $website   = new \thirdsdk\WxWebsite($setting['wx_website_appid'], $setting['wx_website_appsecret']);
                $user_info = $website->getUserinfo($param['code']);
            } elseif ($app['app'] == 'qq') {
                $website   = new \thirdsdk\QqWebsite($setting['qq_website_appid'], $setting['qq_website_appsecret']);
                $user_info = $website->getUserinfo($redirect_uri, $param['code']);
            } elseif ($app['app'] == 'wb') {
                $website   = new \thirdsdk\WbWebsite($setting['wb_website_appid'], $setting['wb_website_appsecret']);
                $user_info = $website->getUserinfo($redirect_uri, $param['code']);
            } else {
                echo 'app value error';
                return;
            }
        } else {
            echo 'type value error';
            return;
        }

        $user_info['register']    = 1;
        $user_info['platform']    = $app['platform'];
        $user_info['application'] = $app['application'];
        if ($token) {
            $member_id = TokenService::memberId($token, true);
            $user_info = array_merge($user_info, ['member_id' => $member_id]);
            $member    = MemberService::thirdBind($user_info);
        } else {
            $member = LoginService::thirdLogin($user_info);
        }

        Cache::del(SettingService::OFFIACC_WEBSITE_KEY . $param['state']);
        $location_url = $jump_url . '?' . $setting['token_name'] . '=' . $member[$setting['token_name']];
        header('Location:' . $location_url);
    }

    /**
     * @Apidoc\Title("移动应用登录")
     * @Apidoc\Method("POST")
     * @Apidoc\Param("app", type="string", default="wx", desc="应用：wx 微信移动应用，qq QQ移动应用")
     * @Apidoc\Param("code", type="string", require=true, desc="wx，code")
     * @Apidoc\Param("access_token", type="string", require=true, desc="qq，access_token")
     * @Apidoc\Param("openid", type="string", require=true, desc="qq，openid")
     * @Apidoc\Param("register", type="int", default="0", desc="是否注册1是0否")
     * @Apidoc\Param("headimgurl", type="string", require=false, desc="头像")
     * @Apidoc\Param("nickname", type="string", require=false, desc="昵称")
     * @Apidoc\Param("avatar_id", type="int", require=false, desc="上传头像返回的file_id")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel\getAvatarUrlAttr", field="avatar_url")
     * @Apidoc\Returned(ref="app\common\model\member\MemberModel", field="member_id,nickname,username,login_ip,login_time,login_num")
     * @Apidoc\Returned("ApiToken", type="string", desc="token")
     * @Apidoc\After(event="setGlobalBody", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalQuery", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\After(event="setGlobalHeader", key="ApiToken", value="res.data.data.ApiToken", desc="ApiToken")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function mobile()
    {
        $param = $this->params([
            'app/s'          => 'wx',
            'code/s'         => '',
            'access_token/s' => '',
            'openid/s'       => '',
            'register/d'     => 0,
            'headimgurl/s'   => '',
            'nickname/s'     => '',
            'avatar_id/d'    => 0,
        ]);
        $rule = ['app' => 'require'];
        if ($param['app'] == 'wx') {
            $rule['code'] = 'require';
        } elseif ($param['app'] == 'qq') {
            $rule['access_token'] = 'require';
            $rule['openid']       = 'require';
        } else {
            return error('app value error');
        }

        $validate = Validate::rule($rule);
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
            return error('app value error');
        }

        $user_info['register']    = $param['register'];
        $user_info['platform']    = $platform;
        $user_info['application'] = $application;
        if (isset($param['headimgurl'])) {
            $user_info['headimgurl'] = $param['headimgurl'];
        }
        if (isset($param['nickname'])) {
            $user_info['nickname'] = $param['nickname'];
        }

        $data = LoginService::thirdLogin($user_info);

        return success($data);
    }
}
