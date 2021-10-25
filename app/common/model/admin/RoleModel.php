<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 角色管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class RoleModel extends Model
{
    // 表名
    protected $name = 'admin_role';
    // 主键
    protected $pk = 'admin_role_id';

    /**
     * @Apidoc\Field("admin_role_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("role_name,role_desc")
     */
    public function listParam()
    {
    }

    /**
     * @Apidoc\Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
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
     * @Apidoc\Field("role_name,role_desc,role_sort")
     * @Apidoc\AddField("admin_menu_ids", type="array", default="[]", desc="菜单id，eg:[1,2]")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("admin_role_id,role_name,role_desc,role_sort")
     * @Apidoc\AddField("admin_menu_ids", type="array", default="[]", desc="菜单id，eg:[1,2]")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("admin_role_id")
     */
    public function deleParam()
    {
    }

    /**
     * @Apidoc\Field("admin_role_id,is_disable")
     */
    public function disableParam()
    {
    }

    /**
     * @Apidoc\Field("admin_role_id,is_unauth")
     */
    public function unauthParam()
    {
    }
}
