<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 接口管理模型
 */
class ApiModel extends Model
{
    // 表名
    protected $name = 'api';
    // 表主键
    protected $pk = 'api_id';

    /**
     * @Apidoc\Field("api_id")
     */
    public function id()
    {
    }

    /**
     * @Apidoc\Field("api_id,api_pid,api_name,api_url,api_sort,is_unlogin,is_disable")
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
     * @Apidoc\Field("api_pid,api_name,api_url,api_sort")
     */
    public function addParam()
    {
    }

    /**
     * @Apidoc\Field("api_id,api_pid,api_name,api_url,api_sort")
     */
    public function editParam()
    {
    }

    /**
     * @Apidoc\Field("api_pid")
     */
    public function api_pid()
    {
    }

    /**
     * @Apidoc\Field("api_url")
     */
    public function api_url()
    {
    }

    /**
     * @Apidoc\Field("is_disable")
     */
    public function is_disable()
    {
    }

    /**
     * @Apidoc\Field("is_unlogin")
     */
    public function is_unlogin()
    {
    }
}
