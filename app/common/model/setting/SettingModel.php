<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 设置管理模型
namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingModel extends Model
{
    // 表名
    protected $name = 'setting';
    // 表主键
    protected $pk = 'setting_id';

    /**
     * @Apidoc\Field("token_name,token_key,token_exp")
     */
    public function tokenInfoParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_register,captcha_login")
     */
    public function captchaInfoParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_register")
     */
    public function captchaRegisterParam()
    {
    }

    /**
     * @Apidoc\Field("captcha_login")
     */
    public function captchaLoginParam()
    {
    }

    /**
     * @Apidoc\Field("log_switch")
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
}
