<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 内容设置模型
namespace app\common\model\cms;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class SettingModel extends Model
{
    // 表名
    protected $name = 'cms_setting';
    // 表主键
    protected $pk = 'setting_id';

    /**
     * @Apidoc\Field("setting_id")
     */
    public function id()
    {
    }

    /**
     * 设置信息
     * @Apidoc\WithoutField("setting_id,logo_id,off_acc_id,diy_config")
     * @Apidoc\AddField("logo_url", type="string", default="", desc="logo链接")
     * @Apidoc\AddField("off_acc_url", type="string", default="", desc="公众号链接")
     */
    public function infoReturn()
    {
    }

    /**
     * 设置修改
     * @Apidoc\WithoutField("setting_id,diy_config,create_time,update_time")
     */
    public function editParam()
    {
    }
}
