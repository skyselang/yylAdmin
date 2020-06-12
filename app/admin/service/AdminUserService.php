<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30
 */

namespace app\admin\service;

use think\facade\Db;
use app\cache\AdminUserCache;

class AdminUserService
{
    /**
     * 用户列表
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [])
    {
        if (empty($field)) {
            $field = 'admin_user_id,admin_rule_ids,username,nickname,email,remark,sort,is_prohibit,is_super_admin,login_num,login_ip,login_time,insert_time,update_time';
        }

        $where[] = ['is_delete', '=', 0];

        if (empty($order)) {
            $order = ['sort' => 'desc', 'admin_user_id' => 'asc'];
        }

        $count = Db::name('admin_user')
            ->where($where)
            ->count('admin_user_id');

        $list = Db::name('admin_user')
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            $admin_rule_ids = explode(',', $v['admin_rule_ids']);
            foreach ($admin_rule_ids as $ka => $va) {
                $admin_rule_ids[$ka] = (int) $va;
            }
            $list[$k]['admin_rule_ids'] = $admin_rule_ids;
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function add($param)
    {
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('username', $param['username'])
            ->where('is_delete', 0)
            ->find();
        if ($admin_user) {
            error('账号已存在');
        }

        $param['is_prohibit']    = 0;
        $param['is_super_admin'] = 0;
        $param['password']       = md5($param['password']);
        $param['insert_time']    = date('Y-m-d H:i:s');
        $admin_user_id = Db::name('admin_user')->insertGetId($param);

        if (empty($admin_user_id)) {
            error();
        }

        $param['admin_user_id'] = $admin_user_id;

        return $param;
    }

    /**
     * 用户修改
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function edit($param)
    {
        $admin_user_id = $param['admin_user_id'];
        unset($param['admin_user_id']);

        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('username', $param['username'])
            ->where('admin_user_id', '<>', $admin_user_id)
            ->where('is_delete', 0)
            ->find();

        if ($admin_user) {
            error('账号已存在');
        }

        $param['update_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($param);

        if (empty($update)) {
            error();
        }

        $param['admin_user_id'] = $admin_user_id;

        AdminUserCache::del($admin_user_id);

        return $param;
    }

    /**
     * 用户删除
     *
     * @param integer $admin_user_id 用户id
     * @return array
     */
    public static function dele($admin_user_id)
    {
        $data['is_delete'] = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $data['admin_user_id'] = $admin_user_id;

        AdminUserCache::del($admin_user_id);

        return $data;
    }

    /**
     * 用户信息
     *
     * @param integer $admin_user_id 用户id
     * @return array
     */
    public static function info($admin_user_id)
    {
        $admin_user = Db::name('admin_user')
            ->field('admin_user_id')
            ->where('admin_user_id', $admin_user_id)
            ->where('is_delete', 0)
            ->find();
        if (empty($admin_user)) {
            error('用户不存在');
        }

        $admin_user = AdminUserCache::set($admin_user_id);

        return $admin_user;
    }

    /**
     * 用户密码重置
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function pwd($param)
    {
        $admin_user_id = $param['admin_user_id'];
        $password      = $param['password'];

        $data['password']    = md5($password);
        $data['update_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $param['admin_user_id'] = $admin_user_id;

        AdminUserCache::del($admin_user_id);

        return $param;
    }

    /**
     * 用户权限分配
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function rule($param)
    {
        $admin_user_id  = $param['admin_user_id'];
        $admin_rule_ids = $param['admin_rule_ids'];
        sort($admin_rule_ids);

        $data['admin_rule_ids'] = implode(',', $admin_rule_ids);
        $data['update_time']    = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        AdminUserCache::del($admin_user_id);

        return $param;
    }

    /**
     * 用户是否禁用
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function prohibit($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $data['is_prohibit'] = $param['is_prohibit'];
        $data['update_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        AdminUserCache::del($admin_user_id);

        return $param;
    }

    /**
     * 用户是否超管
     *
     * @param array $param 用户信息
     * @return array
     */
    public static function superAdmin($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $data['is_super_admin'] = $param['is_super_admin'];
        $data['update_time']    = date('Y-m-d H:i:s');
        $update = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        AdminUserCache::del($admin_user_id);

        return $param;
    }
}
