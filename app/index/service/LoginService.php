<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-20
 */

namespace app\index\service;

use think\facade\Db;
use app\common\cache\UserCache;
use app\common\cache\VerifyCache;
use app\common\service\IpInfoService;
use app\admin\service\UserLogService;
use app\admin\service\UserService;

class LoginService
{
    /**
     * 登录
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        $username = $param['username'];
        $password = md5($param['password']);

        $field = 'user_id,username,nickname,phone,email,login_num,is_disable';

        $where[] = ['username|phone|email', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $user = Db::name('user')
            ->field($field)
            ->where($where)
            ->find();

        if (empty($user)) {
            exception('账号或密码错误');
        }

        if ($user['is_disable'] == 1) {
            exception('账号已被禁用');
        }

        $ip_info = IpInfoService::info();
        $user_id = $user['user_id'];

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_num']    = $user['login_num'] + 1;
        $update['login_time']   = datetime();
        Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        $user_log['log_type'] = 2;
        $user_log['user_id']  = $user_id;
        UserLogService::add($user_log);

        UserCache::del($user_id);
        $user = UserService::info($user_id);

        VerifyCache::del($param['verify_id']);

        return $user;
    }

    /**
     * 退出
     *
     * @param integer $user_id 用户id
     * 
     * @return array
     */
    public static function logout($user_id)
    {
        $update['logout_time'] = datetime();

        Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        $update['user_id'] = $user_id;

        UserCache::del($user_id);

        return $update;
    }
}
