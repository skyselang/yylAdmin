<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-09-27
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\AdminUserCache;
use app\common\cache\AdminVerifyCache;

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
        $field = 'admin_user_id,username,nickname,login_num,is_prohibit';

        $where[] = ['username', '=', $param['username']];
        $where[] = ['password', '=', md5($param['password'])];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name('admin_user')->field($field)->where($where)->find();
        if (empty($admin_user)) {
            error('账号或密码错误');
        }

        if ($admin_user['is_prohibit'] == 1) {
            error('账号已被禁用');
        }

        $ipinfo = AdminIpInfoService::info($param['request_ip']);
        $update['login_ip']     = $param['request_ip'];
        $update['login_region'] = $ipinfo['region'];
        $update['login_time']   = date('Y-m-d H:i:s');
        $update['login_num']    = $admin_user['login_num'] + 1;
        Db::name('admin_user')->where('admin_user_id', $admin_user['admin_user_id'])->update($update);

        AdminUserCache::del($admin_user['admin_user_id']);
        $admin_user = AdminUserService::info($admin_user['admin_user_id']);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['admin_token']   = $admin_user['admin_token'];

        $admin_menu_url = admin_menu_url();
        $admin_menu     = AdminMenuService::info($admin_menu_url, true);
        $request_param  = [];
        if ($param['verify_id']) {
            $request_param['verify_id']   = $param['verify_id'];
            $request_param['verify_code'] = $param['verify_code'];
        }

        $admin_log['admin_log_type'] = 1;
        $admin_log['admin_user_id']  = $admin_user['admin_user_id'];
        $admin_log['admin_menu_id']  = $admin_menu['admin_menu_id'];
        $admin_log['request_ip']     = $param['request_ip'];
        $admin_log['request_method'] = $param['request_method'];
        $admin_log['request_param']  = serialize($request_param);
        AdminLogService::add($admin_log);

        AdminVerifyCache::del($param['verify_id']);

        return $data;
    }

    /**
     * 退出
     *
     * @param array $param 账号信息
     * 
     * @return array
     */
    public static function logout($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $update['logout_time'] = date('Y-m-d H:i:s');

        Db::name('admin_user')->where('admin_user_id', $admin_user_id)->update($update);

        AdminUserCache::del($admin_user_id);

        return $param;
    }
}
