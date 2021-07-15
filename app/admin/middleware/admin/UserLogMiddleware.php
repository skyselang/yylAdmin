<?php
/*
 * @Description  : 日志记录中间件
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-06
 * @LastEditTime : 2021-07-14
 */

namespace app\admin\middleware\admin;

use Closure;
use think\Request;
use think\Response;
use app\common\service\admin\UserLogService;

class UserLogMiddleware
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

        // 日志记录是否开启
        if (admin_log_switch()) {
            $admin_user_id = admin_user_id();

            if ($admin_user_id) {
                $response_data = $response->getData();

                if (isset($response_data['code'])) {
                    $admin_user_log['response_code'] = $response_data['code'];
                }
                if (isset($response_data['msg'])) {
                    $admin_user_log['response_msg'] = $response_data['msg'];
                } else {
                    if (isset($response_data['message'])) {
                        $admin_user_log['response_msg'] = $response_data['message'];
                    }
                }

                $admin_user_log['admin_user_id'] = $admin_user_id;
                UserLogService::add($admin_user_log);
            }
        }

        return $response;
    }
}
