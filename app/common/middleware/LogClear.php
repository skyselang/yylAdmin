<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Event;

/**
 * 日志清除中间件
 */
class LogClear
{
    /**
     * 处理请求
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 用户日志清除
        Event::trigger('UserLog');

        // 会员日志清除
        Event::trigger('MemberLog');

        return $next($request);
    }
}
