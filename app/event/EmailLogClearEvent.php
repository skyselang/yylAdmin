<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace app\event;

use app\common\service\system\EmailLogService;

/**
 * 邮件日志清除事件
 */
class EmailLogClearEvent
{
    /**
     * 事件监听处理
     * @param mixed $args 参数
     * @return mixed
     */
    public function handle($args = null)
    {
        EmailLogService::clearLog();
    }
}
