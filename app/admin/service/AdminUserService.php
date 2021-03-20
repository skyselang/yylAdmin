<?php
/*
 * @Description  : 管理员管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-03-20
 */

namespace app\admin\service;

use think\facade\Db;
use think\facade\Config;
use think\facade\Filesystem;
use app\common\cache\AdminUserCache;
use app\admin\service\AdminTokenService;

class AdminUserService
{
    /**
     * 管理员列表
     *
     * @param array   $where   条件
     * @param integer $page    页数
     * @param integer $limit   数量
     * @param array   $order   排序
     * @param string  $field   字段
     * @param boolean $whereOr OR查询
     * 
     * @return array 
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $whereOr = false)
    {
        if (empty($field)) {
            $field = 'admin_user_id,username,nickname,email,sort,is_disable,is_admin,login_num,login_ip,login_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'admin_user_id' => 'desc'];
        }

        if ($whereOr) {
            $count = Db::name('admin_user')
                ->whereOr($where)
                ->count('admin_user_id');

            $list = Db::name('admin_user')
                ->field($field)
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
     * 管理员信息
     *
     * @param integer $admin_user_id 管理员id
     * 
     * @return array
     */
    public static function info($admin_user_id)
    {
        $admin_user = AdminUserCache::get($admin_user_id);

        if (empty($admin_user)) {
            $admin_user = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->find();

            if (empty($admin_user)) {
                exception('管理员不存在：' . $admin_user_id);
            }

            $admin_user['avatar'] = file_url($admin_user['avatar']);

            if (admin_is_system($admin_user_id)) {
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->select()
                    ->toArray();

                $menu_ids = array_column($admin_menu, 'admin_menu_id');
                $menu_url = array_column($admin_menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } elseif ($admin_user['is_admin'] == 1) {
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->where('is_disable', 0)
                    ->select()
                    ->toArray();

                $menu_ids = array_column($admin_menu, 'admin_menu_id');
                $menu_url = array_column($admin_menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } else {
                $menu_ids = Db::name('admin_role')
                    ->field('admin_role_id')
                    ->where('admin_role_id', 'in', $admin_user['admin_role_ids'])
                    ->where('is_delete', 0)
                    ->where('is_disable', 0)
                    ->column('admin_menu_ids');

                $menu_ids[]   = $admin_user['admin_menu_ids'];
                $menu_ids_str = implode(',', $menu_ids);
                $menu_ids_arr = explode(',', $menu_ids_str);
                $menu_ids     = array_unique($menu_ids_arr);
                $menu_ids     = array_filter($menu_ids);

                $where[] = ['admin_menu_id', 'in', $menu_ids];
                $where[] = ['is_delete', '=', 0];
                $where[] = ['is_disable', '=', 0];
                $where[] = ['menu_url', '<>', ''];

                $where_un[] = ['is_delete', '=', 0];
                $where_un[] = ['is_disable', '=', 0];
                $where_un[] = ['menu_url', '<>', ''];
                $where_un[] = ['is_unauth', '=', 1];

                $menu_url = Db::name('admin_menu')
                    ->field('menu_url')
                    ->whereOr([$where, $where_un])
                    ->column('menu_url');
            }

            $admin_role_ids = $admin_user['admin_role_ids'];
            if (empty($admin_role_ids)) {
                $admin_role_ids = [];
            } else {
                $admin_role_ids = explode(',', $admin_user['admin_role_ids']);
                foreach ($admin_role_ids as $k => $v) {
                    $admin_role_ids[$k] = (int) $v;
                }
            }

            $admin_menu_ids = $admin_user['admin_menu_ids'];
            if (empty($admin_menu_ids)) {
                $admin_menu_ids = [];
            } else {
                $admin_menu_ids = explode(',', $admin_user['admin_menu_ids']);
                foreach ($admin_menu_ids as $k => $v) {
                    $admin_menu_ids[$k] = (int) $v;
                }
            }

            if (empty($menu_ids)) {
                $menu_ids = [];
            } else {
                foreach ($menu_ids as $k => $v) {
                    $menu_ids[$k] = (int) $v;
                }
            }

            $api_white_list  = Config::get('admin.api_white_list', []);
            $rule_white_list = Config::get('admin.rule_white_list', []);
            $white_list      = array_merge($api_white_list, $rule_white_list);
            $menu_url        = array_merge($menu_url, $white_list);
            $menu_url        = array_unique($menu_url);

            sort($menu_url);

            $admin_user['admin_token']    = AdminTokenService::create($admin_user);
            $admin_user['admin_role_ids'] = $admin_role_ids;
            $admin_user['admin_menu_ids'] = $admin_menu_ids;
            $admin_user['menu_ids']       = $menu_ids;
            $admin_user['roles']          = $menu_url;

            AdminUserCache::set($admin_user_id, $admin_user);
        }

        return $admin_user;
    }

    /**
     * 管理员添加
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function add($param)
    {
        $param['password']    = md5($param['password']);
        $param['create_time'] = datetime();

        $admin_user_id = Db::name('admin_user')
            ->insertGetId($param);

        if (empty($admin_user_id)) {
            exception();
        }

        $param['admin_user_id'] = $admin_user_id;

        unset($param['password']);

        return $param;
    }

    /**
     * 管理员修改
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
    {
        $admin_user_id = $param['admin_user_id'];

        if ($method == 'get') {
            $admin_user = self::info($admin_user_id);

            unset($admin_user['admin_token'], $admin_user['password'], $admin_user['menu_ids'], $admin_user['roles']);

            $data['admin_user'] = $admin_user;

            return $data;
        } else {
            unset($param['admin_user_id']);

            $param['update_time'] = datetime();

            $res = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->update($param);

            if (empty($res)) {
                exception();
            }

            $param['admin_user_id'] = $admin_user_id;

            AdminUserCache::upd($admin_user_id);

            return $param;
        }
    }

    /**
     * 管理员删除
     *
     * @param integer $admin_user_id 管理员id
     * 
     * @return array
     */
    public static function dele($admin_user_id)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;

        AdminUserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 管理员修改头像
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
                return $admin_user_id . '/' . $admin_user_id . '_avatar';
            });

        $update['avatar']      = 'storage/' . $avatar_name . '?t=' . date('YmdHis');
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        AdminUserCache::upd($admin_user_id);
        $admin_user = AdminUserService::info($admin_user_id);

        $data['admin_user_id'] = $admin_user['admin_user_id'];
        $data['avatar']        = $admin_user['avatar'];
        $data['update_time']   = $admin_user['update_time'];

        return $data;
    }

    /**
     * 管理员密码重置
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function pwd($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $update['password']    = md5($param['password']);
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;
        $update['password']      = $res;

        AdminUserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 管理员权限分配
     *
     * @param array  $param  管理员信息
     * @param string $method 请求方式
     * 
     * @return array
     */
    public static function rule($param, $method = 'get')
    {
        if ($method == 'get') {
            $admin_user_id = $param['admin_user_id'];

            $admin_role = AdminRoleService::list([], 1, 999)['list'];
            $admin_menu = AdminMenuService::list('list')['list'];
            $admin_user = AdminUserService::info($admin_user_id);

            $menu_ids       = $admin_user['menu_ids'];
            $admin_menu_ids = $admin_user['admin_menu_ids'];
            $role_menu_ids  = AdminRoleService::getMenuId($admin_user['admin_role_ids']);

            foreach ($admin_menu as $k => $v) {
                $admin_menu[$k]['is_check'] = 0;
                $admin_menu[$k]['is_role']  = 0;
                $admin_menu[$k]['is_menu']  = 0;
                foreach ($menu_ids as $vmis) {
                    if ($v['admin_menu_id'] == $vmis) {
                        $admin_menu[$k]['is_check'] = 1;
                    }
                }
                foreach ($admin_menu_ids as $vami) {
                    if ($v['admin_menu_id'] == $vami) {
                        $admin_menu[$k]['is_menu'] = 1;
                    }
                }
                foreach ($role_menu_ids as $vrmi) {
                    if ($v['admin_menu_id'] == $vrmi) {
                        $admin_menu[$k]['is_role'] = 1;
                    }
                }
            }

            $admin_menu = AdminMenuService::toTree($admin_menu, 0);

            $data['admin_user_id']  = $admin_user_id;
            $data['admin_menu_ids'] = $admin_menu_ids;
            $data['admin_role_ids'] = $admin_user['admin_role_ids'];
            $data['username']       = $admin_user['username'];
            $data['nickname']       = $admin_user['nickname'];
            $data['menu_ids']       = $menu_ids;
            $data['admin_role']     = $admin_role;
            $data['admin_menu']     = $admin_menu;

            return $data;
        } else {
            $admin_user_id  = $param['admin_user_id'];
            $admin_role_ids = $param['admin_role_ids'];
            $admin_menu_ids = $param['admin_menu_ids'];

            sort($admin_role_ids);
            sort($admin_menu_ids);

            if (count($admin_role_ids) > 0) {
                if (empty($admin_role_ids[0])) {
                    unset($admin_role_ids[0]);
                }
            }

            if (count($admin_menu_ids) > 0) {
                if (empty($admin_menu_ids[0])) {
                    unset($admin_menu_ids[0]);
                }
            }

            $update['admin_role_ids'] = implode(',', $admin_role_ids);
            $update['admin_menu_ids'] = implode(',', $admin_menu_ids);
            $update['update_time']    = datetime();

            $res = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->update($update);

            if (empty($res)) {
                exception();
            }

            $update['admin_user_id'] = $admin_user_id;

            AdminUserCache::upd($admin_user_id);

            return $update;
        }
    }

    /**
     * 管理员是否禁用
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function disable($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $update['is_disable']  = $param['is_disable'];
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;

        AdminUserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 管理员是否超管
     *
     * @param array $param 管理员信息
     * 
     * @return array
     */
    public static function admin($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $update['is_admin']    = $param['is_admin'];
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;

        AdminUserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 管理员模糊查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function likeQuery($keyword, $field = 'username|nickname')
    {
        $admin_user = Db::name('admin_user')
            ->where('is_delete', '=', 0)
            ->where($field, 'like', '%' . $keyword . '%')
            ->select()
            ->toArray();

        return $admin_user;
    }

    /**
     * 管理员精确查询
     *
     * @param string $keyword 关键词
     * @param string $field   字段
     *
     * @return array
     */
    public static function etQuery($keyword, $field = 'username|nickname')
    {
        $admin_user = Db::name('admin_user')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $admin_user;
    }
}
