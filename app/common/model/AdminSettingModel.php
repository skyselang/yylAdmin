<?php
/*
 * @Description  : 设置管理模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-05-20
 * @LastEditTime : 2021-05-20
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class AdminSettingModel extends Model
{
    protected $name = 'admin_setting';

    /**
     * @Field("token_exp")
     */
    public function tokenInfo()
    {
    }

    /**
     * @Field("verify_switch")
     */
    public function verifyInfo()
    {
    }
}
