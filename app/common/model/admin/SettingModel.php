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
use hg\apidoc\annotation\Field;

class SettingModel extends Model
{
    protected $name = 'admin_setting';

    /**
     * @Field("token_name,token_key,token_exp")
     */
    public function tokenInfo()
    {
    }

    /**
     * @Field("captcha_switch")
     */
    public function captchaInfo()
    {
    }

    /**
     * @Field("log_switch")
     */
    public function logInfo()
    {
    }

    /**
     * @Field("api_rate_num,api_rate_time")
     */
    public function apiInfo()
    {
    }
}
