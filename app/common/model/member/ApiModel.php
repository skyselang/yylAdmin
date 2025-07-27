<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\member;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 会员接口模型
 */
class ApiModel extends Model
{
    // 表名
    protected $name = 'member_api';
    // 表主键
    protected $pk = 'api_id';
    /**
     * 上级id键
     * @var string
     */
    public $pidk = 'api_pid';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }

    /**
     * 获取是否免登名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unlogin_name", type="string", desc="是否免登名称")
     */
    public function getIsUnloginNameAttr($value, $data)
    {
        return ($data['is_unlogin'] ?? 0) ? '是' : '否';
    }

    /**
     * 获取是否免权名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unauth_name", type="string", desc="是否免权名称")
     */
    public function getIsUnauthNameAttr($value, $data)
    {
        return ($data['is_unauth'] ?? 0) ? '是' : '否';
    }

    /**
     * 获取是否免限名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unrate_name", type="string", desc="是否免限名称")
     */
    public function getIsUnrateNameAttr($value, $data)
    {
        return ($data['is_unrate'] ?? 0) ? '是' : '否';
    }
}
