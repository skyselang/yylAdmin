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
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class GroupModel extends Model
{
    // 表名
    protected $name = 'file_group';
    // 主键
    protected $pk = 'group_id';

    /**
     * @Field("group_id")
     */
    public function id()
    {
    }

    /**
     * @Field("group_id,group_name,group_desc,group_sort,is_disable,create_time,update_time")
     */
    public function list()
    {
    }

    /**
     * @Field("group_id,group_name,group_desc,group_sort,is_disable,create_time,update_time")
     */
    public function info()
    {
    }

    /**
     * @Field("group_name,group_desc,group_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("group_id,group_name,group_desc,group_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("group")
     * @AddField("group", type="array", require=true, default="", desc="文件分组数组")
     */
    public function group()
    {
    }

    /**
     * 文件管理-文件分组列表
     * @Field("group_id,group_name")
     */
    public function fileGroup()
    {
    }
}
