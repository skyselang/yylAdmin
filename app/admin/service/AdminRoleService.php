<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-12-11
 */

namespace app\admin\service;

use think\facade\Db;
use app\common\cache\AdminRoleCache;
use app\common\cache\AdminUserCache;

class AdminRoleService
{
    /**
     * 角色列表
     *
     * @param array   $where   条件
     * @param string  $field   字段
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * @param bool    $whereOr OR查询
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [], $whereOr = false)
    {
        if (empty($field)) {
            $field = 'admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time';
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

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 角色信息
     *
     * @param integer $admin_role_id 角色id
     * 
     * @return array
     */
    public static function info($admin_role_id)
    {
        $admin_role = AdminRoleCache::get($admin_role_id);

        if (empty($admin_role)) {
            $admin_role = Db::name('admin_role')
                ->where('admin_role_id', $admin_role_id)
                ->find();

            if (empty($admin_role)) {
                exception('角色不存在：' . $admin_role_id);
            }

            $admin_menu_ids = $admin_role['admin_menu_ids'];
            $admin_menu_ids = explode(',', $admin_menu_ids);
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }
            $admin_role['admin_menu_ids'] = $admin_menu_ids;

            AdminRoleCache::set($admin_role_id, $admin_role);
        }

        return $admin_role;
    }

    /**
     * 角色添加
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function add($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $data['menu_data'] = AdminMenuService::list()['list'];

            return $data;
        } else {
            sort($param['admin_menu_ids']);

            if (count($param['admin_menu_ids']) > 0) {
                if (empty($param['admin_menu_ids'][0])) {
                    unset($param['admin_menu_ids'][0]);
                }
            }

            $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
            $param['create_time']    = date('Y-m-d H:i:s');

            $admin_role_id = Db::name('admin_role')
                ->insertGetId($param);

            if (empty($admin_role_id)) {
                exception();
            }

            $param['admin_role_id'] = $admin_role_id;

            return $param;
        }
    }

    /**
     * 角色修改
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function edit($param = [], $method = 'get')
    {
        if ($method == 'get') {
            $data['admin_role'] = self::info($param['admin_role_id']);
            $data['menu_data']  = AdminMenuService::list()['list'];

            return $data;
        } else {
            $admin_role_id = $param['admin_role_id'];

            unset($param['admin_role_id']);

            sort($param['admin_menu_ids']);

            if (count($param['admin_menu_ids']) > 0) {
                if (empty($param['admin_menu_ids'][0])) {
                    unset($param['admin_menu_ids'][0]);
                }
            }

            $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
            $param['update_time']    = date('Y-m-d H:i:s');

            $res = Db::name('admin_role')
                ->where('admin_role_id', $admin_role_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['admin_role_id'] = $admin_role_id;

            AdminRoleCache::del($admin_role_id);

            return $param;
        }
    }

    /**
     * 角色删除
     *
     * @param array $admin_role_id 角色id
     * 
     * @return array
     */
    public static function dele($admin_role_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = date('Y-m-d H:i:s');

        $res = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_role_id'] = $admin_role_id;

        AdminRoleCache::del($admin_role_id);

        return $update;
    }

    /**
     * 角色禁用
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $admin_role_id = $param['admin_role_id'];

        $param['is_disable']  = $param['is_disable'];
        $param['update_time'] = date('Y-m-d H:i:s');

        $res = Db::name('admin_role')
            ->where('admin_role_id', $admin_role_id)
            ->update($param);

        if (empty($res)) {
            exception();
        }

        AdminRoleCache::del($admin_role_id);

        return $param;
    }

    /**
     * 角色用户
     *
     * @param array   $where 条件
     * @param string  $field 字段
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * 
     * @return array 
     */
    public static function user($where = [], $page = 1, $limit = 10, $field = '',  $order = [], $whereOr = false)
    {
        $data = AdminUserService::list($where, $page, $limit, $field, $order, $whereOr);

        return $data;
    }

    /**
     * 角色用户解除
     *
     * @param array $param 菜单用户id
     *
     * @return array
     */
    public static function userRemove($param)
    {
        $admin_role_id = $param['admin_role_id'];
        $admin_user_id = $param['admin_user_id'];

        $admin_user = AdminUserService::info($admin_user_id);

        $admin_role_ids = $admin_user['admin_role_ids'];
        foreach ($admin_role_ids as $k => $v) {
            if ($admin_role_id == $v) {
                unset($admin_role_ids[$k]);
            }
        }

        if (empty($admin_role_ids)) {
            $admin_role_ids = '';
        } else {
            $admin_role_ids = implode(',', $admin_role_ids);
        }

        $update['update_time']    = date('Y-m-d H:i:s');
        $update['admin_role_ids'] = $admin_role_ids;

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminUserCache::upd($admin_user_id);

        return $param;
    }

    /**
     * 角色菜单id
     *
     * @param mixed $admin_role_id 角色id
     *
     * @return array
     */
    public static function getMenuId($admin_role_id)
    {
        if (empty($admin_role_id)) {
            return [];
        }

        $admin_role_ids = [];

        if (is_numeric($admin_role_id)) {
            $admin_role_ids[] = $admin_role_id;
        } elseif (is_array($admin_role_id)) {
            $admin_role_ids = $admin_role_id;
        } else {
            $admin_role_ids = explode(',', $admin_role_id);
        }

        $admin_menu_ids = [];
        foreach ($admin_role_ids as $k => $v) {
            $admin_role = self::info($v);
            $admin_menu_ids = array_merge($admin_menu_ids, $admin_role['admin_menu_ids']);
        }
        $admin_menu_ids = array_unique($admin_menu_ids);

        sort($admin_menu_ids);

        return $admin_menu_ids;
    }
}
