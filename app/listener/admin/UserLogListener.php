<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\listener\admin;

use app\common\service\system\UserLogService;

/**
 * 用户日志事件
 */
class UserLogListener
{
    /**
     * 事件监听处理
     * 
     * @param mixed $event
     *
     * @return mixed
     */
    public function handle($event)
    {
        UserLogService::clearLog();
    }
}
