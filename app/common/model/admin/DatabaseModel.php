<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 数据库管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class DatabaseModel extends Model
{
    // 表名
    protected $name = 'admin_database';
    // 表主键
    protected $pk = 'admin_database_id';

    /**
     * @Apidoc\Field("admin_database_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\WithoutField("path,table,is_delete,delete_time")
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
     * @Apidoc\Field("table")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("admin_database_id,remark")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("")
     * @Apidoc\AddField("table", type="array", require=true, desc="表名")
     */
    public function table()
    {
    }
}
