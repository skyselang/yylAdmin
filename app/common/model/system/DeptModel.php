<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\system;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 部门管理模型
 */
class DeptModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_dept';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'dept_id';
    /**
     * 上级id字段
     * @var string
     */
    public $pidk = 'dept_pid';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     * @return string
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }
}
