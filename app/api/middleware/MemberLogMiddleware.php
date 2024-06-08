<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\member\LogService;

/**
 * 会员日志中间件
 */
class MemberLogMiddleware
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
        $response = $next($request);

        $member_id = member_id();
        if ($member_id) {
            $response_data = $response->getData();
            if (isset($response_data['code'])) {
                $member_log['response_code'] = $response_data['code'];
            }
            if (isset($response_data['msg'])) {
                $member_log['response_msg'] = $response_data['msg'];
            } else {
                if (isset($response_data['message'])) {
                    $member_log['response_msg'] = $response_data['message'];
                }
            }
            $member_log['member_id']   = $member_id;
            $member_log['platform']    = member_platform();
            $member_log['application'] = member_application();
            LogService::add($member_log);
        }

        return $response;
    }
}
