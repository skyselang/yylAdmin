<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口管理模型
namespace app\common\model;

use think\Model;
use hg\apidoc\annotation\Field;

class ApiModel extends Model
{
    protected $name = 'api';

    /**
     * @Field("api_id")
     */
    public function id()
    {
    }

    /**
     * @Field("api_url")
     */
    public function api_url()
    {
    }

    /**
     * @Field("api_id,api_pid,api_name,api_url,api_sort,is_disable,is_unlogin,create_time,update_time")
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
     * @Field("api_pid,api_name,api_url,api_sort")
     */
    public function add()
    {
    }

    /**
     * @Field("api_id,api_pid,api_name,api_url,api_sort")
     */
    public function edit()
    {
    }

    /**
     * @Field("api_id")
     */
    public function dele()
    {
    }

    /**
     * @Field("api_id,is_disable")
     */
    public function disable()
    {
    }

    /**
     * @Field("api_id,is_unlogin")
     */
    public function unlogin()
    {
    }
}
