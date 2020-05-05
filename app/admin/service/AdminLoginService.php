<?php
/*
 * @Description  : 登录|退出
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-03-26 16:39:37
 */

namespace app\admin\service;

use app\admin\cache\AdminUserCache;
use think\facade\Db;

class AdminLoginService
{
    /**
     * 登录
     *
     * @param  array $param
     * @return array
     */
    public static function login($param)
    {
        $date = date('Y-m-d H:i:s');
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

        $update['login_ip']   = $param['login_ip'];
        $update['login_time'] = $date;
        $update['login_num']  = $admin_user['login_num'] + 1;
        Db::name('admin_user')->where('admin_user_id', $admin_user['admin_user_id'])->update($update);

        $admin_user = AdminUserCache::get($admin_user['admin_user_id']);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['admin_token'] = $admin_user['admin_token'];

        return $data;
    }

    /**
     * 退出
     *
     * @param  array $param
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
