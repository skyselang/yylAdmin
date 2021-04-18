<?php
/*
 * @Description  : 管理员模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-17
 * @LastEditTime : 2021-04-17
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class AdminUserLogModel extends Model
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
     */
    public function log()
    {
    }
}
