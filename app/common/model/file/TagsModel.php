<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\file;

use think\model\Pivot;

/**
 * 文件标签关联模型
 */
class TagsModel extends Pivot
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'file_tags';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'id';
}
