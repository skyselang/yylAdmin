<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use hg\apidoc\annotation as Apidoc;
use app\common\controller\BaseController;
use app\common\validate\member\SettingValidate as Validate;
use app\common\service\member\SettingService as Service;

/**
 * @Apidoc\Title("lang(会员设置)")
 * @Apidoc\Group("setting")
 * @Apidoc\Sort("150")
 */
class Setting extends BaseController
{
    /**
     * 验证器
     */
    protected $validate = Validate::class;

    /**
     * 服务
     */
    protected $service = Service::class;

    /**
     * @Apidoc\Title("lang(会员基本设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="default_avatar_id,default_avatar_url")
     */
    public function basicInfo()
    {
        $data = $this->service::info('default_avatar_id,default_avatar_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(会员基本设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="default_avatar_id")
     */
    public function basicEdit()
    {
        $param = $this->params(['default_avatar_id/d' => 0]);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(登录注册设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login,is_auto_login")
     */
    public function logregInfo()
    {
        $data = $this->service::info('is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login,is_auto_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(登录注册设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="is_captcha_register,is_captcha_login,is_register,is_login,is_phone_register,is_phone_login,is_email_register,is_email_login,is_auto_login")
     */
    public function logregEdit()
    {
        $param = $this->params([
            'is_captcha_register/d' => 0,
            'is_captcha_login/d'    => 0,
            'is_register/d'         => 1,
            'is_login/d'            => 1,
            'is_captcha_register/d' => 1,
            'is_captcha_login/d'    => 1,
            'is_phone_register/d'   => 1,
            'is_phone_login/d'      => 1,
            'is_email_register/d'   => 1,
            'is_email_login/d'      => 1,
            'is_auto_login/d'       => 0,
        ]);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(第三方账号设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="ya_miniapp_register,ya_miniapp_login,ya_offiacc_register,ya_offiacc_login,ya_website_register,ya_website_login,ya_mobile_register,ya_mobile_login,wx_miniapp_register,wx_miniapp_login,wx_offiacc_register,wx_offiacc_login,wx_website_register,wx_website_login,wx_mobile_register,wx_mobile_login,qq_miniapp_register,qq_miniapp_login,qq_website_register,qq_website_login,qq_mobile_register,qq_mobile_login,wx_miniapp_appid,wx_miniapp_appsecret,wx_offiacc_appid,wx_offiacc_appsecret,wx_website_appid,wx_website_appsecret,wx_mobile_appid,wx_mobile_appsecret,qq_miniapp_appid,qq_miniapp_appsecret,qq_website_appid,qq_website_appsecret,qq_mobile_appid,qq_mobile_appsecret,wb_website_appid,wb_website_appsecret,wb_website_register,wb_website_login,wb_website_bind")
     * @Apidoc\Returned("platform_desc", type="string", desc="lang(平台说明)")
     */
    public function thirdInfo()
    {
        $data = $this->service::info('
        ya_miniapp_register,ya_miniapp_login,
        ya_offiacc_register,ya_offiacc_login,
        ya_website_register,ya_website_login,
        ya_mobile_register,ya_mobile_login,
        wx_miniapp_appid,wx_miniapp_appsecret,wx_miniapp_register,wx_miniapp_login,wx_miniapp_bind,
        wx_offiacc_appid,wx_offiacc_appsecret,wx_offiacc_register,wx_offiacc_login,wx_offiacc_bind,
        wx_website_appid,wx_website_appsecret,wx_website_register,wx_website_login,wx_website_bind,
        wx_mobile_appid,wx_mobile_appsecret,wx_mobile_register,wx_mobile_login,wx_mobile_bind,
        qq_miniapp_appid,qq_miniapp_appsecret,qq_miniapp_register,qq_miniapp_login,qq_miniapp_bind,
        qq_website_appid,qq_website_appsecret,qq_website_register,qq_website_login,qq_website_bind,
        qq_mobile_appid,qq_mobile_appsecret,qq_mobile_register,qq_mobile_login,qq_mobile_bind,
        wb_website_appid,wb_website_appsecret,wb_website_register,wb_website_login,wb_website_bind
       ');
        $data['platform_desc'] = $this->service::PLATFORM_DESC;

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(第三方账号设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="ya_miniapp_register,ya_miniapp_login,ya_offiacc_register,ya_offiacc_login,ya_website_register,ya_website_login,ya_mobile_register,ya_mobile_login,wx_miniapp_register,wx_miniapp_login,wx_offiacc_register,wx_offiacc_login,wx_website_register,wx_website_login,wx_mobile_register,wx_mobile_login,qq_miniapp_register,qq_miniapp_login,qq_website_register,qq_website_login,qq_mobile_register,qq_mobile_login,wx_miniapp_appid,wx_miniapp_appsecret,wx_offiacc_appid,wx_offiacc_appsecret,wx_website_appid,wx_website_appsecret,wx_mobile_appid,wx_mobile_appsecret,qq_miniapp_appid,qq_miniapp_appsecret,qq_website_appid,qq_website_appsecret,qq_mobile_appid,qq_mobile_appsecret,wb_website_appid,wb_website_appsecret,wb_website_register,wb_website_login,wb_website_bind")
     */
    public function thirdEdit()
    {
        $param = $this->params([
            'ya_miniapp_register/d'  => 1,
            'ya_miniapp_login/d'     => 1,
            'ya_offiacc_register/d'  => 1,
            'ya_offiacc_login/d'     => 1,
            'ya_website_register/d'  => 1,
            'ya_website_login/d'     => 1,
            'ya_mobile_register/d'   => 1,
            'ya_mobile_login/d'      => 1,
            'wx_miniapp_appid/s'     => '',
            'wx_miniapp_appsecret/s' => '',
            'wx_miniapp_register/d'  => 1,
            'wx_miniapp_login/d'     => 1,
            'wx_miniapp_bind/d'      => 1,
            'wx_offiacc_appid/s'     => '',
            'wx_offiacc_appsecret/s' => '',
            'wx_offiacc_register/d'  => 1,
            'wx_offiacc_login/d'     => 1,
            'wx_offiacc_bind/d'      => 1,
            'wx_website_appid/s'     => '',
            'wx_website_appsecret/s' => '',
            'wx_website_register/d'  => 1,
            'wx_website_login/d'     => 1,
            'wx_website_bind/d'      => 1,
            'wx_mobile_appid/s'      => '',
            'wx_mobile_appsecret/s'  => '',
            'wx_mobile_register/d'   => 1,
            'wx_mobile_login/d'      => 1,
            'wx_mobile_bind/d'       => 1,
            'qq_miniapp_appid/s'     => '',
            'qq_miniapp_appsecret/s' => '',
            'qq_miniapp_register/d'  => 1,
            'qq_miniapp_login/d'     => 1,
            'qq_miniapp_bind/d'      => 1,
            'qq_website_appid/s'     => '',
            'qq_website_appsecret/s' => '',
            'qq_website_register/d'  => 1,
            'qq_website_login/d'     => 1,
            'qq_website_bind/d'      => 1,
            'qq_mobile_appid/s'      => '',
            'qq_mobile_appsecre/s'   => '',
            'qq_mobile_register/d'   => 1,
            'qq_mobile_login/d'      => 1,
            'qq_mobile_bind/d'       => 1,
            'wb_website_appid/s'     => '',
            'wb_website_appsecret/s' => '',
            'wb_website_register/d'  => 1,
            'wb_website_login/d'     => 1,
            'wb_website_bind/d'      => 1,
        ]);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(验证码设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="captcha_mode,captcha_type,captcha_transparent")
     * @Apidoc\Returned(ref={Service::class,"basedata"})
     */
    public function captchaInfo()
    {
        $data = $this->service::info('captcha_mode,captcha_type,captcha_transparent');
        $data['basedata'] = $this->service::basedata();

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(验证码设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="captcha_mode,captcha_type,captcha_transparent")
     */
    public function captchaEdit()
    {
        $param = $this->params(['captcha_mode/d' => 1, 'captcha_type/d' => 1, 'captcha_transparent/d' => 1]);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(日志设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="log_switch,log_save_time,log_unlogin,log_param_without")
     */
    public function logInfo()
    {
        $data = $this->service::info('log_switch,log_save_time,log_unlogin,log_param_without');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(日志设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="log_switch,log_save_time,log_unlogin,log_param_without")
     */
    public function logEdit()
    {
        $param = $this->params(['log_switch/d' => 0, 'log_save_time/d' => 0, 'log_unlogin/d' => 0, 'log_param_without/s' => '']);

        validate($this->validate)->scene('log_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(接口设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
        $data = $this->service::info('is_member_api,api_rate_num,api_rate_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(接口设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param = $this->params(['is_member_api/d' => 0, 'api_rate_num/d' => 3, 'api_rate_time/d' => 1]);

        validate($this->validate)->scene('api_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(Token设置信息)")
     * @Apidoc\Returned(ref={Service::class,"info"}, field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $data = $this->service::info('token_key,token_exp,is_multi_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("lang(Token设置修改)")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref={Service::class,"info"}, field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param = $this->params(['token_key/s' => '', 'token_exp/f' => 720, 'is_multi_login/d' => 0]);

        validate($this->validate)->scene('token_edit')->check($param);

        $data = $this->service::edit($param);

        return success($data);
    }
}
