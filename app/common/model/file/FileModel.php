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
use hg\apidoc\annotation as Apidoc;

class FileModel extends Model
{
    // 表名
    protected $name = 'file';
    // 主键
    protected $pk = 'file_id';

    /**
     * @Apidoc\Field("file_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("group_id,file_type,is_disable,is_front")
     */
    public function listParam()
    {
    }

    /**
     * @Apidoc\Field("file_id,group_id,storage,file_type,file_name,sort,is_disable,create_time,update_time")
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
     * @Apidoc\Field("group_id,file_name")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("file_id,group_id,file_name,sort")
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
     * @Apidoc\Field("file_url")
     * @Apidoc\AddField("file_url", type="string", require=false, default=" ", desc="文件链接")
     */
    public function file_url()
    {
    }

    /**
     * @Apidoc\Field("file_ids")
     * @Apidoc\AddField("file_ids", type="array", require=true, default="[]", desc="文件id数组")
     */
    public function file_ids()
    {
    }
}
