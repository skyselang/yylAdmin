<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 文件管理模型
namespace app\common\model\file;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class FileModel extends Model
{
    // 表名
    protected $name = 'file';
    // 主键
    protected $pk = 'file_id';

    /**
     * @Field("file_id,group_id,storage,file_type,file_name,sort,is_disable,create_time,update_time")
     */
    public function list()
    {
    }

    /**
     * 
     */
    public function info()
    {
    }

    /**
     * @Field("group_id,file_name")
     */
    public function add()
    {
    }

    /**
     * @Field("file_id,group_id,file_name,sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("file_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("file_id,is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("file_id")
     */
    public function id()
    {
    }

    /**
     * @Field("file_url")
     * @AddField("file_url", type="string", require=false, default="", desc="文件链接")
     */
    public function file_url()
    {
    }

    /**
     * @Field("file_ids")
     * @AddField("file_ids", type="array", require=true, default="[]", desc="文件id数组")
     */
    public function file_ids()
    {
    }
}
