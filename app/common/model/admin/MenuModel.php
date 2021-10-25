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
use hg\apidoc\annotation as Apidoc;

class MenuModel extends Model
{
    // 表名
    protected $name = 'admin_menu';
    // 主键
    protected $pk = 'admin_menu_id';

    /**
     * @Apidoc\Field("admin_menu_id")
     */
    public function id()
    {
    }
    /**
     * @Apidoc\Field("menu_url")
     */
    public function menu_url()
    {
    }

    /**
     * @Apidoc\Field("admin_menu_id,menu_pid,menu_name,menu_url,menu_sort,is_disable,is_unauth,create_time,update_time")
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
     * @Apidoc\Field("menu_pid,menu_name,menu_url,menu_sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("admin_menu_id,menu_pid,menu_name,menu_url,menu_sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("admin_menu_id,is_disable")
     */
    public function disableParam()
    {
    }

    /**
     * @Apidoc\Field("admin_menu_id,is_unauth")
     */
    public function unauthParam()
    {
    }

    /**
     * @Apidoc\Field("admin_menu_id,is_unlogin")
     */
    public function unloginParam()
    {
    }
}
