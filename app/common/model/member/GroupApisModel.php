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
 * 会员分组接口关联模型
 */
class GroupApisModel extends Pivot
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'member_group_apis';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'id';
}
