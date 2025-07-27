<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\member;

use think\model\Pivot;

/**
 * 会员属性关联模型
 */
class AttributesModel extends Pivot
{
    // 表名
    protected $name = 'member_attributes';
    // 表主键
    protected $pk = 'id';
}
