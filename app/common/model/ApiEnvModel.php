<?php
/*
 * @Description  : 接口环境模型
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-04-09
 * @LastEditTime : 2021-04-17
 */

namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class ApiEnvModel extends Model
{
    protected $name = 'api_env';

    /**
     * @Field("api_env_id")
     */
    public function id()
    {
    }

    /**
     * @Field("api_env_id,env_name,env_host,env_sort,env_header,create_time,update_time")
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
     * @Field("env_name,env_host,env_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("api_env_id,env_name,env_host,env_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("api_env_id")
     */
    public function dele()
    {
    }
}
