<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-20
 * @LastEditTime : 2021-05-06
 */

namespace app\index\service;

use app\common\service\MemberService;
use app\common\service\MemberLogService;

class RegisterService
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
        $data = MemberService::add($param);

        $member_log['member_id'] = $data['member_id'];
        MemberLogService::add($member_log, 1);

        return $data;
    }
}
