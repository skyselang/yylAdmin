<?php
/*
 * @Description  : 会员模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-10
 * @LastEditTime : 2021-04-17
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;
use hg\apidoc\annotation\WithoutField;

class MemberModel extends Model
{
    protected $name = 'member';

    /**
     * @Field("member_id")
     */
    public function id()
    {
    }

    /**
     * @Field("username")
     */
    public function username()
    {
    }

    /**
     * @Field("nickname")
     */
    public function nickname()
    {
    }

    /**
     * @Field("password")
     */
    public function password()
    {
    }
    
    /**
     * @Field("member_id,username,nickname,phone,email,avatar,remark,sort,create_time,login_time,is_disable")
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
     * @WithoutField("password,remark,sort,is_disable,is_delete,logout_time,delete_time")
     */
    public function infoIndex()
    {
    }

    /**
     * @Field("username,nickname,password,phone,email,region_id,remark,sort")
     */
    public function add()
    {
    }

    /**
     * @Field("member_id,username,nickname,phone,email,region_id,remark,sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("member_id,username,nickname,phone,email,region_id")
     */
    public function editIndex()
    {
    }

    /**
     * @Field("member_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("member_id")
     * @AddField("avatar_file", type="file", require=true, desc="头像文件")
     */
    public function avatar()
    {
    }

    /**
     * @Field("member_id,password")
     */
    public function pwd()
    {
    }

    /**
     * @Field("member_id,is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("member_id,username,nickname,phone,email,avatar,login_ip,login_time")
     * @AddField("menber_token", type="string", desc="MemberToken")
     */
    public function login()
    {
    }
}
