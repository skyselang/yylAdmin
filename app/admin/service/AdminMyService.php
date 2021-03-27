<?php
/*
 * @Description  : 个人中心
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-10-12
 * @LastEditTime : 2021-03-24
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Filesystem;
use app\common\cache\AdminAdminCache;

class AdminMyService
{
    /**
     * 我的信息
     *
     * @param integer $admin_admin_id 管理员id
     * 
     * @return array
     */
    public static function info($admin_admin_id)
    {
        $admin_admin = AdminAdminService::info($admin_admin_id);

        $data['admin_admin_id'] = $admin_admin['admin_admin_id'];
        $data['avatar']        = $admin_admin['avatar'];
        $data['username']      = $admin_admin['username'];
        $data['nickname']      = $admin_admin['nickname'];
        $data['email']         = $admin_admin['email'];
        $data['phone']         = $admin_admin['phone'];
        $data['create_time']   = $admin_admin['create_time'];
        $data['update_time']   = $admin_admin['update_time'];
        $data['login_time']    = $admin_admin['login_time'];
        $data['logout_time']   = $admin_admin['logout_time'];
        $data['is_delete']     = $admin_admin['is_delete'];
        $data['roles']         = $admin_admin['roles'];

        return $data;
    }

    /**
     * 修改信息
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $admin_admin_id = $param['admin_admin_id'];

        if ($method == 'get') {
            $admin_admin = self::info($admin_admin_id);

            $data['admin_admin_id'] = $admin_admin['admin_admin_id'];
            $data['username']       = $admin_admin['username'];
            $data['nickname']       = $admin_admin['nickname'];
            $data['email']          = $admin_admin['email'];
            $data['phone']          = $admin_admin['phone'];
            $data['is_delete']      = $admin_admin['is_delete'];

            return $data;
        } else {
            unset($param['admin_admin_id']);

            $param['update_time'] = datetime();

            $res = Db::name('admin_admin')
                ->where('admin_admin_id', $admin_admin_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['admin_admin_id'] = $admin_admin_id;

            AdminAdminCache::upd($admin_admin_id);

            return $param;
        }
    }

    /**
     * 修改密码
     *
     * @param array $param 管理员密码
     * 
     * @return array
     */
    public static function pwd($param)
    {
        $admin_admin_id = $param['admin_admin_id'];
        $password_old  = $param['password_old'];
        $password_new  = $param['password_new'];

        $admin_admin = AdminAdminService::info($admin_admin_id);

        if (md5($password_old) != $admin_admin['password']) {
            exception('旧密码错误');
        }

        $update['password']    = md5($password_new);
        $update['update_time'] = datetime();

        $res = Db::name('admin_admin')
            ->where('admin_admin_id', $admin_admin_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_admin_id'] = $admin_admin_id;
        $update['password']      = $res;

        AdminAdminCache::upd($admin_admin_id);

        return $update;
    }

    /**
     * 更换头像
     *
     * @param array $param 头像信息
     * 
     * @return array
     */
    public static function avatar($param)
    {
        $admin_admin_id = $param['admin_admin_id'];
        $avatar        = $param['avatar'];

        $avatar_name = Filesystem::disk('public')
            ->putFile('admin_admin', $avatar, function () use ($admin_admin_id) {
                return $admin_admin_id . '/' . $admin_admin_id . '_avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = datetime();

        $res = Db::name('admin_admin')
            ->where('admin_admin_id', $admin_admin_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminAdminCache::upd($admin_admin_id);
        $admin_admin = AdminAdminService::info($admin_admin_id);

        $data['admin_admin_id'] = $admin_admin['admin_admin_id'];
        $data['update_time']   = $admin_admin['update_time'];
        $data['avatar']        = $admin_admin['avatar'];

        return $data;
    }

    /**
     * 我的日志
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function log($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $data = AdminLogService::list($where, $page, $limit, $order, $field);

        return $data;
    }
}
