<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员管理模型
namespace app\common\model;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class MemberWechatModel extends Model
{
    // 表名
    protected $name = 'member_wechat';
    // 表主键
    protected $pk = 'member_wechat_id';

    /**
     * @Apidoc\Field("member_wechat_id")
     */
    public function id()
    {
    }

    /**
     * 
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
     * @Apidoc\WithoutField("member_wechat_id")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("member_wechat_id,nickname")
     */
    public function editParam()
    {
    }
}
