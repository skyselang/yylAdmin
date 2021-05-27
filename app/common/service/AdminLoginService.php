<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-05-26
 */

namespace app\common\service;

use think\facade\Db;
use app\common\cache\AdminUserCache;
use app\common\utils\IpInfoUtils;

class AdminLoginService
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

        $field = 'admin_user_id,login_num,is_disable';

        $where[] = ['username|phone|email', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')
            ->field($field)
            ->where($where)
            ->find();

        if (empty($admin_user)) {
            exception('账号或密码错误');
        }

        if ($admin_user['is_disable'] == 1) {
            exception('账号已被禁用，请联系管理员');
        }

        $ip_info = IpInfoUtils::info();

        $admin_user_id = $admin_user['admin_user_id'];

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        $update['login_num']    = $admin_user['login_num'] + 1;
        Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        $admin_user_log['admin_user_id'] = $admin_user_id;
        $admin_user_log['log_type']      = 1;
        $admin_user_log['response_code'] = 200;
        $admin_user_log['response_msg']  = '登录成功';
        AdminUserLogService::add($admin_user_log);

        AdminUserCache::del($admin_user_id);
        $admin_user = AdminUserService::info($admin_user_id);

        $data['admin_user_id'] = $admin_user_id;
        $data['admin_token']   = $admin_user['admin_token'];

        return $data;
    }

    /**
     * 退出
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array
     */
    public static function logout($admin_user_id)
    {
        $update['logout_time'] = datetime();

        Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        $update['admin_user_id'] = $admin_user_id;

        AdminUserCache::del($admin_user_id);

        return $update;
    }
}
