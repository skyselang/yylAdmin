<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace {$tables[0].namespace};

use think\Model;
use hg\apidoc\annotation as Apidoc;

class {$tables[0].model_name} extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = '{$tables[0].table_name}';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = '{$custom.field_pk}';

    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? lang('是') : lang('否');
    }
}
