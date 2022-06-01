<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理模型
namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class MemberModel extends Model
{
    // 表名
    protected $name = 'member';
    // 表主键
    protected $pk = 'member_id';
    // 性别
    public $gender_arr = [0 => '未知', 1 => '男', 2 => '女'];
    // 注册渠道
    public $reg_channel_arr = [1 => 'Web', 2 => '公众号', 3 => '小程序', 4 => '安卓', 5 => '苹果', 6 => '后台'];
    // 注册方式
    public $reg_type_arr = [1 => '用户名', 2 => '手机', 3 => '邮箱', 4 => '公众号', 5 => '小程序', 6 => '后台'];

    // 获取器：性别名称
    public function getGenderNameAttr($value, $data)
    {
        $arr = $this->gender_arr;
        return $arr[$data['gender']];
    }

    // 获取器：注册渠道名称
    public function getRegChannelNameAttr($value, $data)
    {
        $arr = $this->reg_channel_arr;
        return $arr[$data['reg_channel']];
    }

    // 获取器：注册方式名称
    public function getRegTypeNameAttr($value, $data)
    {
        $arr = $this->reg_type_arr;
        return $arr[$data['reg_type']];
    }

    /**
     * @Apidoc\Field("member_id")
     */
    public function id()
    {
    }

    /**
     * 后台列表
     * @Apidoc\Field("member_id,username,nickname,phone,email,remark,sort,create_time,login_time,is_disable")
     * @Apidoc\AddField("avatar_url", type="string", require=false, desc="头像链接")
     */
    public function listReturn()
    {
    }

    /**
     * 后台信息
     */
    public function infoReturn()
    {
    }

    /**
     * 后台添加
     * @Apidoc\Field("avatar_id,username,nickname,password,phone,email,name,gender,region_id,remark,sort")
     */
    public function addParam()
    {
    }

    /**
     * 后台修改
     * @Apidoc\Field("member_id,avatar_id,username,nickname,phone,email,name,gender,region_id,remark,sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("username")
     */
    public function username()
    {
    }

    /**
     * @Apidoc\Field("nickname")
     */
    public function nickname()
    {
    }

    /**
     * @Apidoc\Field("phone")
     */
    public function phone()
    {
    }

    /**
     * @Apidoc\Field("email")
     */
    public function email()
    {
    }

    /**
     * @Apidoc\Field("password")
     */
    public function password()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }

    /**
     * 头像链接
     * @Apidoc\Field("avatar_url")
     * @Apidoc\AddField("avatar_url", type="string", require=false, desc="头像链接")
     */
    public function avatar_url()
    {
    }

    /**
     * 账号注册
     * @Apidoc\Field("username,nickname,password")
     */
    public function registerReturn()
    {
    }

    /**
     * 登录
     * @Apidoc\Field("member_id,username,nickname,phone,email,avatar_id,login_ip,login_time")
     * @Apidoc\AddField("menber_token", type="string", require=true, desc="MemberToken")
     */
    public function loginReturn()
    {
    }

    /**
     * 会员信息
     * @Apidoc\WithoutField("password,remark,sort,is_disable,is_delete,logout_time,delete_time")
     * @Apidoc\AddField("wechat", type="object", default="", desc="微信信息")
     * @Apidoc\AddField("pwd_edit_type", type="int", default="0", desc="密码修改方式：0原密码设置新密码，1直接设置新密码")
     */
    public function indexInfoReturn()
    {
    }

    /**
     * 会员信息修改
     * @Apidoc\Field("avatar_id,username,nickname,region_id")
     */
    public function indexEditParam()
    {
    }
}
