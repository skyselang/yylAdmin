<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\utils\RetCodeUtils;

/**
 * 接口Token中间件
 */
class ApiTokenMiddleware
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
        // 菜单是否免登
        if (!menu_is_unlogin()) {

            // 用户token获取
            $user_token = user_token();
            if (empty($user_token)) {
                exception('请登录', RetCodeUtils::LOGIN_INVALID);
            }

            // 用户Token验证
            user_token_verify($user_token);
        }

        return $next($request);
    }
}
