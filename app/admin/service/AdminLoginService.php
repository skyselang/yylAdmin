<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\AdminAdminCache;
use app\common\cache\AdminVerifyCache;
use app\common\service\IpInfoService;

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

        $field = 'admin_admin_id,username,nickname,login_num,is_disable';

        $where[] = ['username|email|phone', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $admin_admin = Db::name('admin_admin')
            ->field($field)
            ->where($where)
            ->find();

        if (empty($admin_admin)) {
            exception('账号或密码错误');
        }

        if ($admin_admin['is_disable'] == 1) {
            exception('账号已被禁用，请联系管理员');
        }

        $ip_info = IpInfoService::info();

        $admin_admin_id = $admin_admin['admin_admin_id'];

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        $update['login_num']    = $admin_admin['login_num'] + 1;
        Db::name('admin_admin')
            ->where('admin_admin_id', $admin_admin_id)
            ->update($update);

        $admin_log['admin_admin_id'] = $admin_admin_id;
        $admin_log['log_type']       = 1;
        $admin_log['response_code']  = 200;
        $admin_log['response_msg']   = '登录成功';
        AdminLogService::add($admin_log);

        AdminAdminCache::del($admin_admin_id);
        $admin_admin = AdminAdminService::info($admin_admin_id);

        $data['admin_admin_id'] = $admin_admin_id;
        $data['admin_token']    = $admin_admin['admin_token'];

        AdminVerifyCache::del($param['verify_id']);

        return $data;
    }

    /**
     * 退出
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return array
     */
    public static function logout($admin_admin_id)
    {
        $update['logout_time'] = datetime();

        Db::name('admin_admin')
            ->where('admin_admin_id', $admin_admin_id)
            ->update($update);

        $update['admin_admin_id'] = $admin_admin_id;

        AdminAdminCache::del($admin_admin_id);

        return $update;
    }
}
