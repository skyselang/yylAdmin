<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2020-10-15
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Config;
use app\common\cache\AdminUserCache;
use app\admin\service\AdminTokenService;

class AdminUserService
{
    /**
     * 用户列表
     *
     * @param array   $where   条件
     * @param string  $field   字段
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * @param boolean $whereOr OR查询
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10, $field = '',  $order = [], $whereOr = false)
    {
        if ($field) {
            $withoutField = '';
        } else {
            $withoutField = 'admin_menu_id,password,is_delete,logout_time,delete_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'admin_user_id' => 'asc'];
        }

        if ($whereOr) {
            $count = Db::name('admin_user')
                ->whereOr($where)
                ->count('admin_user_id');

            $list = Db::name('admin_user')
                ->field($field)
                ->withoutField($withoutField)
                ->whereOr($where)
                ->page($page)
                ->limit($limit)
                ->order($order)
                ->select()
                ->toArray();
        } else {
            $where[] = ['is_delete', '=', 0];

            $count = Db::name('admin_user')
                ->where($where)
                ->count('admin_user_id');

            $list = Db::name('admin_user')
                ->field($field)
                ->withoutField($withoutField)
                ->where($where)
                ->page($page)
                ->limit($limit)
                ->order($order)
                ->select()
                ->toArray();
        }

        $pages = ceil($count / $limit);

        foreach ($list as $k => $v) {
            $admin_role_ids = explode(',', $v['admin_role_ids']);
            foreach ($admin_role_ids as $ka => $va) {
                $admin_role_ids[$ka] = (int) $va;
            }
            $list[$k]['admin_role_ids'] = $admin_role_ids;
        }

        $data['count'] = $count;
        $data['pages'] = $pages;
        $data['page']  = $page;
        $data['limit'] = $limit;
        $data['list']  = $list;

        return $data;
    }

    /**
     * 用户信息
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array
     */
    public static function info($admin_user_id)
    {
        $admin_user = AdminUserCache::get($admin_user_id);

        if (empty($admin_user)) {
            $admin_user = Db::name('admin_user')
                ->withoutField('password')
                ->where('admin_user_id', $admin_user_id)
                ->where('is_delete', 0)
                ->find();

            if (empty($admin_user)) {
                error('用户不存在');
            }

            $admin_user['avatar'] = file_url($admin_user['avatar']);

            if (super_admin($admin_user_id)) {
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->select()
                    ->toArray();

                $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
                $admin_menu_url = array_column($admin_menu, 'menu_url');
                $admin_menu     = array_filter($admin_menu_url);

                $admin_user['admin_menu_ids'] = $admin_menu_ids;
            } elseif ($admin_user['is_super_admin'] == 1) {
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->where('is_prohibit', 0)
                    ->select()
                    ->toArray();

                $admin_menu_ids = array_column($admin_menu, 'admin_menu_id');
                $admin_menu_url = array_column($admin_menu, 'menu_url');
                $admin_menu     = array_filter($admin_menu_url);

                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
                $admin_user['admin_menu_ids'] = $admin_menu_ids;
            } else {
                $admin_role = Db::name('admin_role')
                    ->field('admin_role_id')
                    ->where('admin_role_id', 'in', $admin_user['admin_role_ids'])
                    ->where('is_delete', 0)
                    ->where('is_prohibit', 0)
                    ->column('admin_menu_ids');

                $admin_menu_ids     = $admin_role;
                $admin_menu_ids[]   = $admin_user['admin_menu_id'];
                $admin_menu_ids_str = implode(',', $admin_menu_ids);
                $admin_menu_ids_arr = explode(',', $admin_menu_ids_str);
                $admin_menu_ids     = array_unique($admin_menu_ids_arr);
                $admin_menu_ids     = array_filter($admin_menu_ids);

                $admin_menu_ids_temp = [];
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids_temp[] = (int) $v;
                }
                $admin_menu_ids = $admin_menu_ids_temp;
                $admin_user['admin_menu_ids'] = $admin_menu_ids;

                $where[] = ['admin_menu_id', 'in', $admin_menu_ids];
                $where[] = ['is_delete', '=', 0];
                $where[] = ['is_prohibit', '=', 0];
                $where[] = ['menu_url', '<>', ''];

                $where_un[] = ['is_delete', '=', 0];
                $where_un[] = ['is_prohibit', '=', 0];
                $where_un[] = ['menu_url', '<>', ''];
                $where_un[] = ['is_unauth', '=', 1];

                $admin_menu = Db::name('admin_menu')
                    ->field('menu_url')
                    ->whereOr([$where, $where_un])
                    ->column('menu_url');
            }

            $admin_role_ids = explode(',', $admin_user['admin_role_ids']);
            foreach ($admin_role_ids as $k => $v) {
                $admin_role_ids[$k] = (int) $v;
            }

            $admin_menu_id = explode(',', $admin_user['admin_menu_id']);
            foreach ($admin_menu_id as $k => $v) {
                $admin_menu_id[$k] = (int) $v;
            }

            $api_white_list  = Config::get('admin.api_white_list', []);
            $role_white_list = Config::get('admin.role_white_list', []);
            $white_list      = array_merge($api_white_list, $role_white_list);
            $admin_menu      = array_merge($admin_menu, $white_list);

            sort($admin_menu);

            $admin_user['admin_role_ids'] = $admin_role_ids;
            $admin_user['admin_menu_id']  = $admin_menu_id;
            $admin_user['admin_token']    = AdminTokenService::create($admin_user);
            $admin_user['roles']          = $admin_menu;

            AdminUserCache::set($admin_user_id, $admin_user);
        }

        return $admin_user;
    }

    /**
     * 用户添加
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['is_prohibit']    = 0;
        $param['is_super_admin'] = 0;
        $param['password']       = md5($param['password']);
        $param['create_time']    = date('Y-m-d H:i:s');
        
        $admin_user_id = Db::name('admin_user')
            ->insertGetId($param);

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
     * 
     * @return array
     */
    public static function edit($param)
    {
        $admin_user_id = $param['admin_user_id'];

        unset($param['admin_user_id']);

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
     * 
     * @return array
     */
    public static function dele($admin_user_id)
    {
        $data['is_delete']   = 1;
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
     * 用户密码重置
     *
     * @param array $param 用户信息
     * 
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
     * 
     * @return array
     */
    public static function rule($param)
    {
        $admin_user_id  = $param['admin_user_id'];
        $admin_role_ids = $param['admin_role_ids'];
        $admin_menu_id  = $param['admin_menu_id'];

        sort($admin_role_ids);
        sort($admin_menu_id);

        $data['admin_role_ids'] = implode(',', $admin_role_ids);
        $data['admin_menu_id']  = implode(',', $admin_menu_id);
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
     * 
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
     * 
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

    /**
     * 用户模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'username|nickname')
    {
        $admin_user = Db::name('admin_user')
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $admin_user;
    }

    /**
     * 用户精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'username|nickname')
    {
        $admin_user = Db::name('admin_user')
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $admin_user;
    }
}
