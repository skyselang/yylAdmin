<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件设置模型
namespace app\common\model\file;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingModel extends Model
{
    // 表名
    protected $name = 'file_setting';
    // 表主键
    protected $pk = 'setting_id';

    /**
     * @Apidoc\Field("setting_id")
     */
    public function id()
    {
    }

    /**
     * 设置信息参数
     */
    public function infoReturn()
    {
    }

    /**
     * 设置修改参数
     * @Apidoc\WithoutField("setting_id,create_time,update_time")
     */
    public function editParam()
    {
    }
}
