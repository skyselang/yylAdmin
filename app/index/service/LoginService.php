<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 登录退出
namespace app\index\service;

use app\common\service\MemberService;

class LoginService
{
    /**
     * 登录（账号）
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        return MemberService::login($param);
    }

    /**
     * 微信登录
     *
     * @param array $userinfo 微信用户信息
     *
     * @return array
     */
    public static function wechat($userinfo)
    {
        return MemberService::wechat($userinfo);
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
