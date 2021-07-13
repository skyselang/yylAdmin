<?php
/*
 * @Description  : 内容设置模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-13
 */

namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;
use hg\apidoc\annotation\Param;

class SettingModel extends Model
{
    protected $name = 'cms_setting';
    protected $pk = 'setting_id';

    /**
     * @WithoutField("setting_id")
     */
    public function info()
    {
    }

    /**
     * @WithoutField("setting_id,create_time,update_time")
     */
    public function edit()
    {
    }
}
