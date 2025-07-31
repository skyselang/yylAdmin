<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\setting;

use think\model\Pivot;

/**
 * 设置文件关联模型
 */
class SettingFilesModel extends Pivot
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'setting_files';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'id';
}
