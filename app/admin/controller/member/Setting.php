<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\member;

use app\common\controller\BaseController;
use app\common\validate\member\SettingValidate;
use app\common\service\member\SettingService;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("会员设置")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("700")
 */
class Setting extends BaseController
{
    /**
     * @Apidoc\Title("会员设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="default_avatar_id")
     * @Apidoc\Returned(ref="app\common\service\member\SettingService\info", field="default_avatar_url")
     */
    public function memberInfo()
    {
        $data = SettingService::info('default_avatar_id,default_avatar_url');

        return success($data);
    }

    /**
     * @Apidoc\Title("会员设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="default_avatar_id")
     */
    public function memberEdit()
    {
        $param = $this->params(['default_avatar_id/d' => 0]);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="captcha_mode,captcha_type")
     */
    public function captchaInfo()
    {
        $data = SettingService::info('captcha_mode,captcha_type');

        return success($data);
    }

    /**
     * @Apidoc\Title("验证码设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="captcha_mode,captcha_type")
     */
    public function captchaEdit()
    {
        $param = $this->params(['captcha_mode/d' => 1, 'captcha_type/d' => 1]);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenInfo()
    {
        $data = SettingService::info('token_key,token_exp,is_multi_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("Token设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="token_key,token_exp,is_multi_login")
     */
    public function tokenEdit()
    {
        $param = $this->params(['token_key/s' => '', 'token_exp/d' => 720, 'is_multi_login/d' => 0]);

        validate(SettingValidate::class)->scene('token_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logInfo()
    {
        $data = SettingService::info('log_switch,log_save_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("日志设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="log_switch,log_save_time")
     */
    public function logEdit()
    {
        $param = $this->params(['log_switch/d' => 0, 'log_save_time/d' => 0]);

        validate(SettingValidate::class)->scene('log_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
        $data = SettingService::info('is_member_api,api_rate_num,api_rate_time');

        return success($data);
    }

    /**
     * @Apidoc\Title("接口设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_member_api,api_rate_num,api_rate_time")
     */
    public function apiEdit()
    {
        $param = $this->params(['is_member_api/d' => 0, 'api_rate_num/d' => 3, 'api_rate_time/d' => 1]);

        validate(SettingValidate::class)->scene('api_edit')->check($param);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login")
     */
    public function logregInfo()
    {
        $data = SettingService::info('is_captcha_register,is_captcha_login,is_register,is_login,is_captcha_register,is_captcha_login,is_phone_register,is_phone_login,is_email_register,is_email_login');

        return success($data);
    }

    /**
     * @Apidoc\Title("登录注册设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="is_captcha_register,is_captcha_login,is_register,is_login,is_phone_register,is_phone_login,is_email_register,is_email_login")
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
        ]);

        $data = SettingService::edit($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("第三方账号设置信息")
     * @Apidoc\Returned(ref="app\common\model\member\SettingModel", field="ya_miniapp_register,ya_miniapp_login,ya_offiacc_register,ya_offiacc_login,ya_website_register,ya_website_login,ya_mobile_register,ya_mobile_login,wx_miniapp_register,wx_miniapp_login,wx_offiacc_register,wx_offiacc_login,wx_website_register,wx_website_login,wx_mobile_register,wx_mobile_login,qq_miniapp_register,qq_miniapp_login,qq_website_register,qq_website_login,qq_mobile_register,qq_mobile_login,wx_miniapp_appid,wx_miniapp_appsecret,wx_offiacc_appid,wx_offiacc_appsecret,wx_website_appid,wx_website_appsecret,wx_mobile_appid,wx_mobile_appsecret,qq_miniapp_appid,qq_miniapp_appsecret,qq_website_appid,qq_website_appsecret,qq_mobile_appid,qq_mobile_appsecret,wb_website_appid,wb_website_appsecret,wb_website_register,wb_website_login,wb_website_bind")
     * @Apidoc\Returned("platform_desc", type="string", desc="平台说明")
     */
    public function thirdInfo()
    {
        $data = SettingService::info('
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
        $data['platform_desc'] = SettingService::PLATFORM_DESC;

        return success($data);
    }

    /**
     * @Apidoc\Title("第三方账号设置修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\SettingModel", field="ya_miniapp_register,ya_miniapp_login,ya_offiacc_register,ya_offiacc_login,ya_website_register,ya_website_login,ya_mobile_register,ya_mobile_login,wx_miniapp_register,wx_miniapp_login,wx_offiacc_register,wx_offiacc_login,wx_website_register,wx_website_login,wx_mobile_register,wx_mobile_login,qq_miniapp_register,qq_miniapp_login,qq_website_register,qq_website_login,qq_mobile_register,qq_mobile_login,wx_miniapp_appid,wx_miniapp_appsecret,wx_offiacc_appid,wx_offiacc_appsecret,wx_website_appid,wx_website_appsecret,wx_mobile_appid,wx_mobile_appsecret,qq_miniapp_appid,qq_miniapp_appsecret,qq_website_appid,qq_website_appsecret,qq_mobile_appid,qq_mobile_appsecret,wb_website_appid,wb_website_appsecret,wb_website_register,wb_website_login,wb_website_bind")
     */
    public function thirdEdit()
    {
        $param = $this->params([
            'ya_miniapp_register/d' => 1, 'ya_miniapp_login/d'      => 1,
            'ya_offiacc_register/d' => 1, 'ya_offiacc_login/d'      => 1,
            'ya_website_register/d' => 1, 'ya_website_login/d'      => 1,
            'ya_mobile_register/d'  => 1, 'ya_mobile_login/d'       => 1,
            'wx_miniapp_appid/s'    => '', 'wx_miniapp_appsecret/s' => '', 'wx_miniapp_register/d' => 1, 'wx_miniapp_login/d' => 1, 'wx_miniapp_bind/d' => 1,
            'wx_offiacc_appid/s'    => '', 'wx_offiacc_appsecret/s' => '', 'wx_offiacc_register/d' => 1, 'wx_offiacc_login/d' => 1, 'wx_offiacc_bind/d' => 1,
            'wx_website_appid/s'    => '', 'wx_website_appsecret/s' => '', 'wx_website_register/d' => 1, 'wx_website_login/d' => 1, 'wx_website_bind/d' => 1,
            'wx_mobile_appid/s'     => '', 'wx_mobile_appsecret/s'  => '', 'wx_mobile_register/d'  => 1, 'wx_mobile_login/d'  => 1, 'wx_mobile_bind/d'  => 1,
            'qq_miniapp_appid/s'    => '', 'qq_miniapp_appsecret/s' => '', 'qq_miniapp_register/d' => 1, 'qq_miniapp_login/d' => 1, 'qq_miniapp_bind/d' => 1,
            'qq_website_appid/s'    => '', 'qq_website_appsecret/s' => '', 'qq_website_register/d' => 1, 'qq_website_login/d' => 1, 'qq_website_bind/d' => 1,
            'qq_mobile_appid/s'     => '', 'qq_mobile_appsecre/s'   => '', 'qq_mobile_register/d'  => 1, 'qq_mobile_login/d'  => 1, 'qq_mobile_bind/d'  => 1,
            'wb_website_appid/s'    => '', 'wb_website_appsecret/s' => '', 'wb_website_register/d' => 1, 'wb_website_login/d' => 1, 'wb_website_bind/d' => 1,
        ]);

        $data = SettingService::edit($param);

        return success($data);
    }
}
