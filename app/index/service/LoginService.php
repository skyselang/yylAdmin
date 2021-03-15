<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-11
 */

namespace app\index\service;

use think\facade\Db;
use app\common\cache\UserCache;
use app\common\cache\VerifyCache;
use app\common\service\IpInfoService;
use app\admin\service\UserLogService;
use app\admin\service\ApiService;
use app\admin\service\UserService;
use think\facade\Cache;

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

        $request_ip = $param['request_ip'];
        $ip_info    = IpInfoService::info($request_ip);

        $user_id = $user['user_id'];

        $update['login_ip']     = $request_ip;
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = date('Y-m-d H:i:s');
        $update['login_num']    = $user['login_num'] + 1;
        Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        UserCache::del($user_id);

        $api_url = request_pathinfo();
        $api     = ApiService::info($api_url);

        $request_param['username'] = $username;

        if ($param['verify_id']) {
            $request_param['verify_id']   = $param['verify_id'];
            $request_param['verify_code'] = $param['verify_code'];
        }

        $log['user_id']        = $user_id;
        $log['log_type']       = 1;
        $log['api_id']         = $api['api_id'];
        $log['request_ip']     = $request_ip;
        $log['request_method'] = $param['request_method'];
        $log['request_param']  = serialize($request_param);
        UserLogService::add($log);

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
        $update['logout_time'] = date('Y-m-d H:i:s');

        Db::name('user')
            ->where('user_id', $user_id)
            ->update($update);

        $update['user_id'] = $user_id;

        UserCache::del($user_id);

        return $update;
    }
}
