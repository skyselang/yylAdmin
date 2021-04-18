<?php
/*
 * @Description  : 角色管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-04-10
 */

namespace app\common\service;

use think\facade\Db;
use app\common\cache\AdminRoleCache;
use app\common\cache\AdminUserCache;

class AdminRoleService
{
    /**
     * 角色列表
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        if (empty($field)) {
            $field = 'admin_role_id,role_name,role_desc,role_sort,is_disable,create_time,update_time';
        }

        if (empty($order)) {
            $order = ['role_sort' => 'desc', 'admin_role_id' => 'desc'];
        }

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

            $admin_menu_ids = str_trim($admin_role['admin_menu_ids']);

            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                $admin_menu_ids = explode(',', $admin_menu_ids);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }

            $admin_role['admin_menu_ids'] = $admin_menu_ids;

            AdminRoleCache::set($admin_role_id, $admin_role);
        }

        $data['admin_role'] = $admin_role;
        $data['menu_tree']  = AdminMenuService::list()['list'];

        return $admin_role;
    }

    /**
     * 角色添加
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function add($param)
    {
        sort($param['admin_menu_ids']);

        if (count($param['admin_menu_ids']) > 0) {
            if (empty($param['admin_menu_ids'][0])) {
                unset($param['admin_menu_ids'][0]);
            }
        }

        $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
        $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
        $param['create_time']    = datetime();

        $admin_role_id = Db::name('admin_role')
            ->insertGetId($param);

        if (empty($admin_role_id)) {
            exception();
        }

        $param['admin_role_id'] = $admin_role_id;

        return $param;
    }

    /**
     * 角色修改
     *
     * @param array $param 角色信息
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_role_id = $param['admin_role_id'];

        if (false) {
            $data['admin_role'] = self::info($admin_role_id);
            $data['menu_tree']  = AdminMenuService::list()['list'];

            return $data;
        } else {
            unset($param['admin_role_id']);

            sort($param['admin_menu_ids']);

            if (count($param['admin_menu_ids']) > 0) {
                if (empty($param['admin_menu_ids'][0])) {
                    unset($param['admin_menu_ids'][0]);
                }
            }

            $param['admin_menu_ids'] = implode(',', $param['admin_menu_ids']);
            $param['admin_menu_ids'] = str_join($param['admin_menu_ids']);
            $param['update_time']    = datetime();

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
        $update['delete_time'] = datetime();

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

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

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
     * 角色管理员
     *
     * @param array   $where 条件
     * @param integer $page  页数
     * @param integer $limit 数量
     * @param array   $order 排序
     * @param string  $field 字段
     * 
     * @return array 
     */
    public static function user($where = [], $page = 1, $limit = 10,  $order = [], $field = '')
    {
        $data = AdminUserService::list($where, $page, $limit, $order, $field);

        return $data;
    }

    /**
     * 角色管理员解除
     *
     * @param array $param 菜单管理员id
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
            $admin_role_ids = str_join('');
        } else {
            $admin_role_ids = str_join(implode(',', $admin_role_ids));
        }

        $update['admin_role_ids'] = $admin_role_ids;
        $update['update_time']    = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_role_id'] = $admin_role_id;
        $update['admin_user_id'] = $admin_user_id;

        AdminUserCache::upd($admin_user_id);

        return $update;
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
            $admin_role_id  = str_trim($admin_role_id);
            $admin_role_ids = explode(',', $admin_role_id);
        }

        $admin_menu_ids = [];
        foreach ($admin_role_ids as $k => $v) {
            $admin_role     = self::info($v);
            $admin_menu_ids = array_merge($admin_menu_ids, $admin_role['admin_menu_ids']);
        }
        $admin_menu_ids = array_unique($admin_menu_ids);

        sort($admin_menu_ids);

        return $admin_menu_ids;
    }
}
