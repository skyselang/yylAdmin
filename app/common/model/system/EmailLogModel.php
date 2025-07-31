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
 * 邮件日志模型
 */
class EmailLogModel extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $name = 'system_email_log';
    /**
     * 主键字段
     * @var string
     */
    protected $pk = 'log_id';
}
