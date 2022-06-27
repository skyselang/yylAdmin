<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 系统管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingModel extends Model
{
    // 表名
    protected $name = 'admin_setting';
    // 表主键
    protected $pk = 'admin_setting_id';

    /**
     * 设置id
     * @Apidoc\Field("admin_setting_id")
     */
    public function id()
    {
    }

    /**
     * 设置信息
     */
    public function infoReturn()
    {
    }

    /**
     * token设置信息
     * @Apidoc\Field("token_name,token_key,token_exp")
     */
    public function tokenInfoParam()
    {
    }

    /**
     * 验证码设置信息
     * @Apidoc\Field("captcha_switch")
     */
    public function captchaInfoParam()
    {
    }

    /**
     * 日志设置信息
     * @Apidoc\Field("log_switch,log_save_time")
     */
    public function logInfoParam()
    {
    }

    /**
     * 接口设置信息
     * @Apidoc\Field("api_rate_num,api_rate_time")
     */
    public function apiInfoParam()
    {
    }

    /**
     * 系统设置信息
     * @Apidoc\Field("logo_id,favicon_id,login_bg_id,system_name,page_title")
     * @Apidoc\AddField("logo_url", type="string", require=false, default="", desc="logo链接")
     * @Apidoc\AddField("favicon_url", type="string", require=false, default="", desc="favicon链接")
     * @Apidoc\AddField("login_bg_url", type="string", require=false, default="", desc="登录背景图链接")
     */
    public function systemInfoParam()
    {
    }

    /**
     * 邮箱设置信息
     * @Apidoc\Field("email_host,email_port,email_secure,email_username,email_password,email_setfrom")
     */
    public function emailInfoParam()
    {
    }

    /**
     * 邮箱设置测试
     * @Apidoc\Field("email_test")
     */
    public function emailTestParam()
    {
    }

    /**
     * 短信设置信息
     * @Apidoc\Field("sms_name,sms_type,sms_param,sms_test")
     */
    public function smsInfoParam()
    {
    }

    /**
     * 短信设置测试
     * @Apidoc\Field("sms_test")
     */
    public function smsTestParam()
    {
    }

    /**
     * 系统信息
     * @Apidoc\Field("system_name,page_title")
     * @Apidoc\AddField("logo_url", type="string", require=false, default="", desc="logo链接")
     * @Apidoc\AddField("favicon_url", type="string", require=false, default="", desc="favicon链接")
     * @Apidoc\AddField("login_bg_url", type="string", require=false, default="", desc="登录背景图链接")
     */
    public function loginSettingParam()
    {
    }
}
