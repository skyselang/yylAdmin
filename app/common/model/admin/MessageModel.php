<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 消息管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class MessageModel extends Model
{
    // 表名
    protected $name = 'admin_message';
    // 表主键
    protected $pk = 'admin_message_id';

    /**
     * @Apidoc\Field("admin_message_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("admin_message_id,title,type,sort,is_open,create_time")
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
     * @Apidoc\Field("title,type,sort,is_open")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("admin_message_id,title,type,sort,is_open")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("admin_message_id")
     */
    public function deleParam()
    {
    }

    /**
     * @Apidoc\Field("is_open")
     */
    public function isopenParam()
    {
    }
}
