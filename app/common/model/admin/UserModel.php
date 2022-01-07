<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class UserModel extends Model
{
    // 表名
    protected $name = 'admin_user';
    // 表主键
    protected $pk = 'admin_user_id';

    /**
     * @Apidoc\Field("admin_user_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("username,nickname,email")
     */
    public function listParam()
    {
    }

    /**
     * @Apidoc\Field("admin_user_id,username,nickname,phone,email,sort,is_disable,is_super,login_num,login_ip,login_time")
     */
    public function listReturn()
    {
    }

    /**
     * @Apidoc\AddField("admin_token", type="string", require=true, desc="AdminToken")
     * @Apidoc\AddField("avatar_url", type="string", require=true, desc="头像链接")
     */
    public function infoReturn()
    {
    }

    /**
     * @Apidoc\Field("avatar_id,username,nickname,password,phone,email,remark,sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("admin_user_id,avatar_id,username,nickname,phone,email,remark,sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("admin_user_id")
     * @Apidoc\AddField("admin_role_ids", type="array", require=true, desc="角色id,eg:[1,2]")
     * @Apidoc\AddField("admin_menu_ids", type="array", require=true, desc="菜单id,eg:[1,2]")
     */
    public function ruleParam()
    {
    }

    /**
     * @Apidoc\Field("username,password")
     * @Apidoc\AddField("username", type="string", require=true, desc="账号/手机/邮箱")
     * @Apidoc\AddField("password", type="string", require=true, desc="密码")
     */
    public function loginParam()
    {
    }

    /**
     * @Apidoc\Field("admin_user_id")
     * @Apidoc\AddField("admin_token", type="string", require=true, desc="AdminToken")
     */
    public function loginReturn()
    {
    }

    /**
     * @Apidoc\Field("username")
     */
    public function username()
    {
    }

    /**
     * @Apidoc\Field("password")
     */
    public function password()
    {
    }

    /**
     * @Apidoc\Field("is_super")
     */
    public function is_super()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }
}
