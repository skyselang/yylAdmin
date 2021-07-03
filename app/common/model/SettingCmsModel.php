<?php
/*
 * @Description  : 内容设置模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-06-17
 * @LastEditTime : 2021-07-03
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;

class SettingCmsModel extends Model
{
    protected $name = 'setting_cms';
    protected $pk = 'setting_cms_id';

    /**
     * 内容设置信息
     * @WithoutField("setting_cms_id")
     */
    public function info()
    {
    }

    /**
     * 内容设置修改
     * @WithoutField("setting_cms_id,create_time,update_time")
     */
    public function edit()
    {
    }
}
