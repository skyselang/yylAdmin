<?php
/*
 * @Description  : 角色模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-04-16
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class AdminRoleModel extends Model
{
    protected $name = 'admin_role';

    /**
     * @Field("admin_role_id")
     */
    public function id()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
     */
    public function list()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
     */
    public function info()
    {
    }

    /**
     * @Field("role_name,role_desc,role_sort")
     * @AddField("admin_menu_ids", type="array", default="[]", desc="菜单id，eg:[1,2]")
     */
    public function add()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort")
     * @AddField("admin_menu_ids", type="array", default="[]", desc="菜单id，eg:[1,2]")
     */
    public function edit()
    {
    }

    /**
     * @Field("admin_role_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("admin_role_id,is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("admin_role_id,is_unauth")
     */
    public function unauth()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
     */
    public function role()
    {
    }
}
