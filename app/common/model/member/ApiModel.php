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
    /**
     * 表名
     * @var string
     */
    protected $name = 'member_api';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'api_id';
    /**
     * 上级id字段
     * @var string
     */
    public $pidk = 'api_pid';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     * @return string
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? lang('是') : lang('否');
    }

    /**
     * 获取是否免登名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unlogin_name", type="string", desc="是否免登名称")
     * @return string
     */
    public function getIsUnloginNameAttr($value, $data)
    {
        return ($data['is_unlogin'] ?? 0) ? lang('是') : lang('否');
    }

    /**
     * 获取是否免权名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unauth_name", type="string", desc="是否免权名称")
     * @return string
     */
    public function getIsUnauthNameAttr($value, $data)
    {
        return ($data['is_unauth'] ?? 0) ? lang('是') : lang('否');
    }

    /**
     * 获取是否免限名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_unrate_name", type="string", desc="是否免限名称")
     * @return string
     */
    public function getIsUnrateNameAttr($value, $data)
    {
        return ($data['is_unrate'] ?? 0) ? lang('是') : lang('否');
    }
}
