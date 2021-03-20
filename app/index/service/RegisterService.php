<?php
/*
 * @Description  : 注册
 * @Author       : https://github.com/skyselang
 * @Date         : 2021-03-20
 * @LastEditTime : 2021-03-20
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
        
        $user_log['log_type'] = 1;
        $user_log['user_id']  = $data['user_id'];
        UserLogService::add($user_log);

        return $data;
    }
}
