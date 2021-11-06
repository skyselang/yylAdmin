<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 会员日志模型
namespace app\common\model;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class MemberLogModel extends Model
{
    // 表名
    protected $name = 'Member_log';
    // 表主键
    protected $pk = 'member_log_id';

    /**
     * @Apidoc\Field("member_log_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("member_log_id,member_id,api_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time")
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
     * @Apidoc\Field("member_log_id")
     */
    public function deleParam()
    {
    }

    /**
     * @Apidoc\Field("log_type")
     */
    public function log_type()
    {
    }
}
