<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 个人中心
namespace app\common\service\admin;

use think\facade\Db;
use app\common\cache\admin\UserCache;

class UserCenterService
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
        $data = UserService::info($admin_user_id);

        unset($data['password'], $data['admin_token'], $data['menu_ids']);

        return $data;
    }

    /**
     * 修改信息
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_user_id = $param['admin_user_id'];

        unset($param['admin_user_id']);

        $param['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param['admin_user_id'] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $param;
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

        $admin_user = UserService::info($admin_user_id);
        if (md5($password_old) != $admin_user['password']) {
            exception('旧密码错误');
        }

        $update['password']    = md5($password_new);
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;
        $update['password']      = $res;

        UserCache::upd($admin_user_id);

        return $update;
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
        return UserLogService::list($where, $page, $limit, $order, $field);
    }
}
