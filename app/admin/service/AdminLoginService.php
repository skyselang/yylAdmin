<?php
/*
 * @Description  : 登录|退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-26
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Config;
use app\cache\AdminUserCache;
use app\cache\AdminVerifyCache;

class AdminLoginService
{
    /**
     * 登录
     *
     * @param array $param
     * @return array
     */
    public static function login($param)
    {
        $verify_id   = $param['verify_id'];
        $verify_code = $param['verify_code'];
        $is_verify   = Config::get('admin.is_verify', false);
        if ($is_verify) {
            if (empty($verify_code)) {
                error('请输入验证码');
            }

            $AdminVerifyService = new AdminVerifyService();
            $check_verify = $AdminVerifyService->check($verify_id, $verify_code);
            if (empty($check_verify)) {
                error('验证码错误');
            }
        }

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

        $update['login_ip']   = $param['request_ip'];
        $update['login_time'] = date('Y-m-d H:i:s');
        $update['login_num']  = $admin_user['login_num'] + 1;
        Db::name('admin_user')->where('admin_user_id', $admin_user['admin_user_id'])->update($update);

        AdminUserCache::del($admin_user['admin_user_id']);
        $admin_user = AdminUserService::info($admin_user['admin_user_id']);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['admin_token']   = $admin_user['admin_token'];

        $admin_log['admin_user_id']  = $admin_user['admin_user_id'];
        $admin_log['menu_url']       = $param['menu_url'];
        $admin_log['request_ip']     = $param['request_ip'];
        $admin_log['request_method'] = $param['request_method'];
        $admin_log['request_param']  = serialize([]);
        AdminLogService::add($admin_log);

        AdminVerifyCache::del($verify_id);

        return $data;
    }

    /**
     * 退出
     *
     * @param array $param
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
