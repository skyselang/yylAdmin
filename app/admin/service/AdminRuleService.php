<?php
/*
 * @Description  : 权限管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-03-30 17:04:47
 */

namespace app\admin\service;

use think\facade\Db;

class AdminRuleService
{
    /**
     * 权限列表
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [], $whereOr = false)
    {
        if (empty($field)) {
            $field = 'admin_rule_id,admin_menu_ids,rule_name,rule_desc,rule_sort,is_prohibit,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['rule_sort' => 'desc', 'admin_rule_id' => 'asc'];
        }

        if ($whereOr) {
            $count = Db::name('admin_rule')
                ->whereOr($where)
                ->count('admin_rule_id');

            $list = Db::name('admin_rule')
                ->field($field)
                ->whereOr($where)
                ->page($page)
                ->limit($limit)
                ->order($order)
                ->select()
                ->toArray();
        } else {
            $where[] = ['is_delete', '=', 0];

            $count = Db::name('admin_rule')
                ->where($where)
                ->count('admin_rule_id');

            $list = Db::name('admin_rule')
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
     * 权限添加
     *
     * @param array $param 权限信息
     * @return array
     */
    public static function add($param)
    {
        $admin_rule = Db::name('admin_rule')
            ->field('admin_rule_id')
            ->where('rule_name', $param['rule_name'])
            ->where('is_delete', 0)
            ->find();

        if ($admin_rule) {
            error('权限已存在');
        }

        sort($param['admin_menu_ids']);
        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['create_time']    = date('Y-m-d H:i:s');
        $admin_rule_id = Db::name('admin_rule')->insertGetId($param);

        if (empty($admin_rule_id)) {
            error();
        }

        $param['admin_rule_id'] = $admin_rule_id;

        return $param;
    }

    /**
     * 权限修改
     *
     * @param array $param 权限信息
     * @return array
     */
    public static function edit($param)
    {
        $admin_rule_id = $param['admin_rule_id'];

        $admin_rule = Db::name('admin_rule')
            ->field('admin_rule_id')
            ->where('rule_name', $param['rule_name'])
            ->where('admin_rule_id', '<>', $admin_rule_id)
            ->where('is_delete', 0)
            ->find();

        if ($admin_rule) {
            error('权限已存在');
        }

        unset($param['admin_rule_id']);
        sort($param['admin_menu_ids']);
        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['update_time']    = date('Y-m-d H:i:s');
        $update = Db::name('admin_rule')
            ->where('admin_rule_id', $admin_rule_id)
            ->update($param);

        if (empty($update)) {
            error();
        }

        $param['admin_rule_id'] = $admin_rule_id;

        return $param;
    }

    /**
     * 权限删除
     *
     * @param array $admin_rule_id 权限id
     * @return array
     */
    public static function dele($admin_rule_id)
    {
        $data['is_delete']   = 1;
        $data['delete_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_rule')
            ->where('admin_rule_id', $admin_rule_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        $data['admin_rule_id'] = $admin_rule_id;

        return $data;
    }

    /**
     * 权限信息
     *
     * @param integer $admin_rule_id 权限id
     * @return array
     */
    public static function info($admin_rule_id = 0)
    {
        $admin_rule = Db::name('admin_rule')
            ->where('is_delete', 0)
            ->where('admin_rule_id', $admin_rule_id)
            ->find();

        if (empty($admin_rule)) {
            error('权限不存在');
        }

        return $admin_rule;
    }

    /**
     * 权限是否禁用
     *
     * @param array $param 权限信息
     * @return array
     */
    public static function prohibit($param)
    {
        $admin_rule_id = $param['admin_rule_id'];

        $data['is_prohibit'] = $param['is_prohibit'];
        $data['update_time'] = date('Y-m-d H:i:s');
        $update = Db::name('admin_rule')
            ->where('admin_rule_id', $admin_rule_id)
            ->update($data);

        if (empty($update)) {
            error();
        }

        return $param;
    }
}
