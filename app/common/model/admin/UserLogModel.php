<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 日志管理模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation\Field;
use hg\apidoc\annotation\AddField;

class UserLogModel extends Model
{
    protected $name = 'admin_user_log';

    /**
     * @Field("admin_user_log_id")
     */
    public function id()
    {
    }

    /**
     * @Field("admin_user_log_id,admin_user_id,admin_menu_id,request_method,request_ip,request_region,request_isp,response_code,response_msg,create_time")
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
     * @Field("admin_user_log_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("log_type")
     * @AddField("log_type", type="int", desc="日志类型1登录2操作3退出")
     */
    public function log_type()
    {
    }
}
