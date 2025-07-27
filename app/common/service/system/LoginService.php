<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use hg\apidoc\annotation as Apidoc;

/**
 * 登录退出
 */
class LoginService
{
    /**
     * 登录
     * @param array $param 登录信息
     * @Apidoc\Param(ref={UserService::class,"login"})
     * @Apidoc\Returned(ref={UserService::class,"login"})
     */
    public static function login($param)
    {
        return UserService::login($param);
    }

    /**
     * 退出
     * @param int $user_id 用户id
     */
    public static function logout($user_id)
    {
        return UserService::logout($user_id);
    }
}
