<?php
/*
 * @Description  : 登录退出
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-11-24
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\AdminUserCache;
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

        $field = 'admin_user_id,username,nickname,login_num,is_disable';

        $where[] = ['username', '=', $username];
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
            exception('账号已被禁用');
        }

        $request_ip = $param['request_ip'];
        $ipinfo     = IpInfoService::info($request_ip);

        $admin_user_id = $admin_user['admin_user_id'];

        $update['login_ip']     = $request_ip;
        $update['login_region'] = $ipinfo['region'];
        $update['login_time']   = date('Y-m-d H:i:s');
        $update['login_num']    = $admin_user['login_num'] + 1;
        Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        AdminUserCache::del($admin_user_id);

        $menu_url   = request_pathinfo();
        $admin_menu = AdminMenuService::info($menu_url);

        $request_param['username'] = $username;
        if ($param['verify_id']) {
            $request_param['verify_id']   = $param['verify_id'];
            $request_param['verify_code'] = $param['verify_code'];
        }

        $admin_log['admin_log_type'] = 1;
        $admin_log['admin_user_id']  = $admin_user_id;
        $admin_log['admin_menu_id']  = $admin_menu['admin_menu_id'];
        $admin_log['request_ip']     = $request_ip;
        $admin_log['request_method'] = $param['request_method'];
        $admin_log['request_param']  = serialize($request_param);
        AdminLogService::add($admin_log);

        AdminVerifyCache::del($param['verify_id']);

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
        $update['logout_time'] = date('Y-m-d H:i:s');

        Db::name('admin_user')->where('admin_user_id', $admin_user_id)->update($update);

        AdminUserCache::del($admin_user_id);

        return $admin_user_id;
    }
}
