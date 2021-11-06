<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件分组模型
namespace app\common\model\file;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class GroupModel extends Model
{
    // 表名
    protected $name = 'file_group';
    // 表主键
    protected $pk = 'group_id';

    /**
     * @Apidoc\Field("group_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("group_name,group_desc")
     */
    public function listParam()
    {
    }

    /**
     * @Apidoc\Field("group_id,group_name,group_desc,group_sort,is_disable,create_time,update_time")
     */
    public function listReturn()
    {
    }

    /**
     * 
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("group_name,group_desc,group_sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("group_id,group_name,group_desc,group_sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }

    /**
     * @Apidoc\Field("group_name")
     */
    public function group_name()
    {
    }

    /**
     * @Apidoc\Field("group_desc")
     */
    public function group_desc()
    {
    }

    /**
     * @Apidoc\Field("group")
     * @Apidoc\AddField("group", type="array", require=true, default=" ", desc="文件分组数组")
     */
    public function group()
    {
    }
}
