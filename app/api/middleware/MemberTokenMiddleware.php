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
use app\common\utils\ReturnCodeUtils;

/**
 * 会员Token中间件
 */
class MemberTokenMiddleware
{
    /**
     * 处理请求
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 接口是否免登
        if (!api_is_unlogin()) {

            // 会员token获取
            $member_token = member_token();
            if (empty($member_token)) {
                exception(lang('请登录'), ReturnCodeUtils::LOGIN_INVALID);
            }

            // 会员token验证
            member_token_verify($member_token);
        }

        return $next($request);
    }
}
