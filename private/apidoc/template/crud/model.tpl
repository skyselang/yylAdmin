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
    // 表名
    protected $name = '{$tables[0].table_name}';
    // 表主键
    protected $pk = '{$custom.field_pk}';
}
