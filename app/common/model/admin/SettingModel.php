<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理模型
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
     * @Apidoc\Field("admin_setting_id")
     */
    public function id()
    {
    }

    /**
     * 
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("token_name,token_key,token_exp")
     */
    public function tokenInfoParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_switch")
     */
    public function captchaInfoParam()
    {
    }

    /**
     * @Apidoc\Field("log_switch,log_save_time")
     */
    public function logInfoParam()
    {
    }

    /**
     * @Apidoc\Field("api_rate_num,api_rate_time")
     */
    public function apiInfoParam()
    {
    }

    /**
     * @Apidoc\Field("logo_id,favicon_id,login_bg_id,system_name,page_title")
     * @Apidoc\AddField("logo_url", type="string", require=false, default="", desc="logo链接")
     * @Apidoc\AddField("favicon_url", type="string", require=false, default="", desc="favicon链接")
     * @Apidoc\AddField("login_bg_url", type="string", require=false, default="", desc="登录背景图链接")
     */
    public function systemInfoParam()
    {
    }
}
