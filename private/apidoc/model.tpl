<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// {$form.controller_title}模型
namespace {$tables[0].namespace};

use think\Model;
use hg\apidoc\annotation as Apidoc;

class {$tables[0].model_name} extends Model
{
    protected $name = '{$tables[0].table_name}';
    protected $pk = 'id';

    /**
     * id
     * @Apidoc\Field("id")
     */
    public function id()
    {
    }

    /**
     * 列表
     */
    public function listReturn()
    {
    }

    /**
     * 信息
     */
    public function infoReturn()
    {
    }

    /**
     * 添加
     * @Apidoc\WithoutField("id,is_delete,create_time,update_time,delete_time")
     */
    public function addParam()
    {
    }

    /**
     * 修改
     * @Apidoc\WithoutField("is_delete,create_time,update_time,delete_time")
     */
    public function editParam()
    {
    }
}
