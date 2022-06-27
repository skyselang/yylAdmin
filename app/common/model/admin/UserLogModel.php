<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户日志模型
namespace app\common\model\admin;

use think\Model;
use hg\apidoc\annotation as Apidoc;

class UserLogModel extends Model
{
    // 表名
    protected $name = 'admin_user_log';
    // 表主键
    protected $pk = 'admin_user_log_id';

    /**
     * @Apidoc\Field("admin_user_log_id")
     */
    public function id()
    {
    }

    /**
     * 
     */
    public function listParam()
    {
    }

    /**
     * @Apidoc\Field("admin_user_log_id,admin_user_id,username,admin_menu_id,request_ip,request_region,request_isp,response_code,response_msg,create_time")
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
}
