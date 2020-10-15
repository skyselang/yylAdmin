<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-10-15
 */

namespace app\admin\service;

use think\facade\Db;

class AdminRoleService
{
    /**
     * 权限列表
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [], $whereOr = false)
    {
        if (empty($field)) {
            $field = 'admin_role_id,admin_menu_ids,role_name,role_desc,role_sort,is_prohibit,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['role_sort' => 'desc', 'admin_role_id' => 'asc'];
        }

        if ($whereOr) {
            $count = Db::name('admin_role')
                ->whereOr($where)
                ->count('admin_role_id');

            $list = Db::name('admin_role')
                ->field($field)
                ->whereOr($where)
                ->page($page)
                ->limit($limit)
                ->order($order)
                ->select()
                ->toArray();
        } else {
            $where[] = ['is_delete', '=', 0];

            $count = Db::name('admin_role')
                ->where($where)
                ->count('admin_role_id');

            $list = Db::name('admin_role')
                ->field($field)
                ->where($where)
                ->page($page)
                ->limit($limit)
                ->order($order)
                ->select()
                ->toArray();
        }

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            $admin_menu_ids = explode(',', $v['admin_menu_ids']);
            foreach ($admin_menu_ids as $ka => $va) {
                $admin_menu_ids[$ka] = (int) $va;
            }
            $list[$k]['admin_menu_ids'] = $admin_menu_ids;
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 权限信息
     *
     * @param integer $admin_role_id 权限id
     * 
     * @return array
     */
    public static function info($admin_role_id = 0)
    {
        $admin_role = Db::name('admin_role')
            ->where('is_delete', 0)
            ->where('admin_role_id', $admin_role_id)
            ->find();

        if (empty($admin_role)) {
            error('权限不存在');
        }

        return $admin_role;
    }

    /**
     * 权限添加
     *
     * @param array $param 权限信息
     * 
     * @return array
     */
    public static function add($param)
    {
        sort($param['admin_menu_ids']);

        if (count($param['admin_menu_ids']) == 1) {
            if ($param['admin_menu_ids'][0] == 0) {
                $param['admin_menu_ids'] = [];
            }
        }

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['create_time']    = date('Y-m-d H:i:s');

        $admin_role_id = Db::name('admin_role')
            ->insertGetId($param);

        if (empty($admin_role_id)) {
            error();
        }

        $param['admin_role_id'] = $admin_role_id;

        return $param;
    }

    /**
     * 权限修改
     *
     * @param array $param 权限信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_role_id = $param['admin_role_id'];

        unset($param['admin_role_id']);

        sort($param['admin_menu_ids']);

        if (count($param['admin_menu_ids']) == 1) {
            if ($param['admin_menu_ids'][0] == 0) {
                $param['admin_menu_ids'] = [];
            }
        }

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['update_time']    = date('Y-m-d H:i:s');

        $update = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($param);

        if (empty($update)) {
            error();
        }

        $param['admin_role_id'] = $admin_role_id;

        return $param;
    }

    /**
     * 权限删除
     *
     * @param array $admin_role_id 权限id
     * 
     * @return array
     */
    public static function dele($admin_role_id)
    {
        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $data['admin_role_id'] = $admin_role_id;

        return $data;
    }

    /**
     * 权限是否禁用
     *
     * @param array $param 权限信息
     * 
     * @return array
     */
    public static function prohibit($param)
    {
        $admin_role_id = $param['admin_role_id'];

        $data['is_prohibit'] = $param['is_prohibit'];
        $data['update_time'] = date('Y-m-d H:i:s');

        $update = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        return $param;
    }
}
