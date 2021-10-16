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
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class MemberLogModel extends Model
{
    // 表名
    protected $name = 'Member_log';
    // 主键
    protected $pk = 'member_log_id';

    /**
     * @Field("member_log_id")
     */
    public function id()
    {
    }

    /**
     * @Field("member_log_id,member_id,api_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time")
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
     * @Field("member_log_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("log_type")
     * @AddField("log_type", type="int", require=false, desc="日志类型1注册2登录3操作4退出")
     */
    public function log_type()
    {
    }
}
