<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-30
 * @LastEditTime : 2020-11-30
 */

namespace app\index\service;

use app\admin\service\MemberService;

class UserService
{
    /**
     * 注册
     * 
     * @param array $param 注册信息
     *
     * @return array
     */
    public static function register($param)
    {
        $member = MemberService::add($param);

        return $member;
    }
}
