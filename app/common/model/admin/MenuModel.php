<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 菜单管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation\Field;

class MenuModel extends Model
{
    protected $name = 'admin_menu';

    /**
     * @Field("admin_menu_id")
     */
    public function id()
    {
    }

    /**
     * @Field("menu_url")
     */
    public function menu_url()
    {
    }

    /**
     * @Field("admin_menu_id,menu_pid,menu_name,menu_url,menu_sort,is_disable,is_unauth,create_time,update_time")
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
     * @Field("menu_pid,menu_name,menu_url,menu_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("admin_menu_id,menu_pid,menu_name,menu_url,menu_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("admin_menu_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("admin_menu_id,is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("admin_menu_id,is_unauth")
     */
    public function unauth()
    {
    }

    /**
     * @Field("admin_menu_id,is_unlogin")
     */
    public function unlogin()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
     */
    public function role()
    {
    }

    /**
     * @Field("admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time")
     */
    public function roleRemove()
    {
    }
}
