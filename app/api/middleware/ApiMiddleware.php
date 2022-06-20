<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 接口校验中间件
namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use think\facade\Config;
use app\common\service\setting\SettingService;

class ApiMiddleware
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
        $debug = Config::get('app.app_debug');

        $setting = SettingService::info();

        if ($setting['api_manage']) {
            // 接口是否存在
            if (!api_is_exist()) {
                $msg = 'api url error';
                if ($debug) {
                    $msg .= '：' . api_url();
                }
                exception($msg, 404);
            }

            // 接口是否已禁用
            if (api_is_disable()) {
                $msg = 'api is disable';
                if ($debug) {
                    $msg .= '：' . api_url();
                }
                exception($msg, 404);
            }
        }

        return $next($request);
    }
}
