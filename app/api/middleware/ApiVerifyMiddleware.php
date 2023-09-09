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
use think\facade\Config;
use app\common\service\member\ApiService;
use app\common\service\member\MemberService;
use app\common\service\member\SettingService;
use app\common\service\utils\RetCodeUtils;

/**
 * 接口校验中间件
 */
class ApiVerifyMiddleware
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
        $setting = SettingService::info();

        // 会员接口是否开启
        if ($setting['is_member_api']) {
            $api_url   = api_url();
            $member_id = member_id();

            // 会员是否超级会员
            if (!member_is_super($member_id)) {
                $debug = Config::get('app.app_debug');

                // 接口是否存在
                if (!api_is_exist($api_url)) {
                    $msg = 'api url error';
                    if ($debug) {
                        $msg .= '：' . $api_url;
                    }
                    exception($msg, RetCodeUtils::API_URL_ERROR);
                }

                // 接口是否已禁用
                if (api_is_disable($api_url)) {
                    $msg = 'api is disable';
                    if ($debug) {
                        $msg .= '：' . $api_url;
                    }
                    exception($msg, RetCodeUtils::API_URL_ERROR);
                }

                // 接口是否免权
                if (!api_is_unauth($api_url)) {
                    $member = MemberService::info($member_id);
                    if (empty($member)) {
                        exception('登录已失效，请重新登录', RetCodeUtils::LOGIN_INVALID);
                    }

                    if (!in_array($api_url, $member['api_urls'])) {
                        $api = ApiService::info($api_url);
                        $msg = 'No permission：' . $api['api_name'];
                        if ($debug) {
                            $msg .= '(' . $api_url . ')';
                        }
                        exception($msg, RetCodeUtils::NO_PERMISSION);
                    }
                }
            }
        }

        return $next($request);
    }
}
