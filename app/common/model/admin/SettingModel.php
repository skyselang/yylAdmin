<?php
/*
 * @Description  : 设置管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-20
 * @LastEditTime : 2021-07-14
 */

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
