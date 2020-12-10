<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-14
 * @LastEditTime : 2020-12-10
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\AdminUserCache;

class AdminMyService
{
    /**
     * 我的信息
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array
     */
    public static function info($admin_user_id)
    {
        $admin_user = AdminUserService::info($admin_user_id);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['avatar']        = $admin_user['avatar'];
        $data['username']      = $admin_user['username'];
        $data['nickname']      = $admin_user['nickname'];
        $data['email']         = $admin_user['email'];
        $data['create_time']   = $admin_user['create_time'];
        $data['update_time']   = $admin_user['update_time'];
        $data['login_time']    = $admin_user['login_time'];
        $data['logout_time']   = $admin_user['logout_time'];
        $data['is_delete']     = $admin_user['is_delete'];
        $data['roles']         = $admin_user['roles'];

        return $data;
    }

    /**
     * 修改信息
     *
     * @param array  $param  用户信息
     * @param string $method 请求方式
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $admin_user_id = $param['admin_user_id'];

        if ($method == 'get') {
            $admin_user = self::info($admin_user_id);

            $data['admin_user_id'] = $admin_user['admin_user_id'];
            $data['username']      = $admin_user['username'];
            $data['nickname']      = $admin_user['nickname'];
            $data['email']         = $admin_user['email'];

            return $data;
        } else {
            $param['update_time'] = date('Y-m-d H:i:s');

            $update = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->update($param);

            if (empty($update)) {
                exception();
            }

            AdminUserCache::upd($admin_user_id);

            return $param;
        }
    }

    /**
     * 修改密码
     *
     * @param array $param 用户密码
     * 
     * @return array
     */
    public static function pwd($param)
    {
        $admin_user_id = $param['admin_user_id'];
        $password_old  = $param['password_old'];
        $password_new  = $param['password_new'];

        $admin_user = AdminUserService::info($admin_user_id);

        if (md5($password_old) != $admin_user['password']) {
            exception('旧密码错误');
        }

        $update['password']    = md5($password_new);
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminUserCache::upd($admin_user_id);

        return $res;
    }

    /**
     * 修改头像
     *
     * @param array $param 头像信息
     * 
     * @return array
     */
    public static function avatar($param)
    {
        $admin_user_id = $param['admin_user_id'];
        $avatar        = $param['avatar'];

        $avatar_name = Filesystem::disk('public')
            ->putFile('admin_user', $avatar, function () use ($admin_user_id) {
                return $admin_user_id . '/avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminUserCache::upd($admin_user_id);
        $admin_user = AdminUserService::info($admin_user_id);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['update_time']   = $admin_user['update_time'];
        $data['avatar']        = $admin_user['avatar'];

        return $data;
    }

    /**
     * 我的日志
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * 
     * @return array 
     */
    public static function log($where = [], $page = 1, $limit = 10, $field = '',  $order = [])
    {
        $data = AdminLogService::list($where, $page, $limit, $field, $order);

        return $data;
    }
}
