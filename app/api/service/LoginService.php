<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\service;

use app\common\service\member\MemberService;

/**
 * 登录退出
 */
class LoginService
{
    /**
     * 登录
     *
     * @param array  $param 登录信息
     * @param string $type  登录方式
     * 
     * @return array
     */
    public static function login($param, $type = '')
    {
        if (!isset($param['platform'])) {
            $param['platform'] = member_platform();
        }
        if (!isset($param['application'])) {
            $param['application'] = member_application();
        }
        return MemberService::login($param, $type);
    }

    /**
     * 第三方登录
     *
     * @param array $user_info 第三方用户信息
     * platform，application，openid，headimgurl，nickname，unionid
     *
     * @return array
     */
    public static function thirdLogin($user_info)
    {
        return MemberService::thirdLogin($user_info);
    }

    /**
     * 退出
     *
     * @param int $member_id 会员id
     * 
     * @return array
     */
    public static function logout($member_id)
    {
        return MemberService::logout($member_id);
    }
}
