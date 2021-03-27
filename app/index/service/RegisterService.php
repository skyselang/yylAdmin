<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-20
 * @LastEditTime : 2021-03-27
 */

namespace app\index\service;

use app\admin\service\UserService;
use app\admin\service\UserLogService;

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
        $data = UserService::add($param, 'post');
        
        $user_log['log_type']      = 1;
        $user_log['user_id']       = $data['user_id'];
        $user_log['response_code'] = 200;
        $user_log['response_msg']  = '注册成功';
        UserLogService::add($user_log);

        return $data;
    }
}
