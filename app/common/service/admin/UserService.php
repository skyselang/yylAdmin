<?php
/*
 * @Description  : 用户管理
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-05-05
 * @LastEditTime : 2021-07-16
 */

namespace app\common\service\admin;

use think\facade\Db;
use think\facade\Config;
use app\common\cache\admin\UserCache;
use app\common\service\admin\TokenService;

class UserService
{
    /**
     * 用户列表
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
        if ($field) {
            $field = str_merge($field, 'admin_user_id,username');
        } else {
            $field = 'admin_user_id,username,nickname,phone,email,sort,is_disable,is_super,login_num,create_time,login_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', 'admin_user_id' => 'desc'];
        }

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

        $pages = ceil($count / $limit);

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
        $admin_user = UserCache::get($admin_user_id);

        if (empty($admin_user)) {
            $admin_user = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->find();

            if (empty($admin_user)) {
                exception('用户不存在：' . $admin_user_id);
            }

            $admin_user['avatar_url']     = file_url($admin_user['avatar']);
            $admin_user['admin_role_ids'] = str_trim($admin_user['admin_role_ids']);
            $admin_user['admin_menu_ids'] = str_trim($admin_user['admin_menu_ids']);

            if (admin_is_super($admin_user_id)) {
                $admin_menu = Db::name('admin_menu')
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->select()
                    ->toArray();

                $menu_ids = array_column($admin_menu, 'admin_menu_id');
                $menu_url = array_column($admin_menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } elseif ($admin_user['is_super'] == 1) {
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

                $where_un[] = ['is_unauth', '=', 1];
                $where_un[] = ['is_delete', '=', 0];
                $where_un[] = ['is_disable', '=', 0];
                $where_un[] = ['menu_url', '<>', ''];

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

            $api_whitelist  = Config::get('admin.api_whitelist', []);
            $rule_whitelist = Config::get('admin.rule_whitelist', []);
            $whitelist      = array_merge($api_whitelist, $rule_whitelist);
            $menu_url       = array_merge($menu_url, $whitelist);
            $menu_url       = array_unique($menu_url);

            sort($menu_url);

            $admin_user['admin_token']    = TokenService::create($admin_user);
            $admin_user['admin_role_ids'] = $admin_role_ids;
            $admin_user['admin_menu_ids'] = $admin_menu_ids;
            $admin_user['menu_ids']       = $menu_ids;
            $admin_user['roles']          = $menu_url;

            UserCache::set($admin_user_id, $admin_user);
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
     * 用户修改
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function edit($param, $method = 'get')
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
     * 用户删除
     *
     * @param integer $admin_user_id 用户id
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

        UserCache::del($admin_user_id);

        return $update;
    }

    /**
     * 用户重置密码
     *
     * @param array $param 用户信息
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
        $update['password']       = $res;

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 用户分配权限
     *
     * @param array  $param  用户信息
     * @param string $method 请求方式
     * 
     * @return array
     */
    public static function rule($param, $method = 'get')
    {
        if ($method == 'get') {
            $admin_user_id = $param['admin_user_id'];

            $admin_role = RoleService::list([], 1, 999)['list'];
            $admin_menu = MenuService::list();
            $admin_user = UserService::info($admin_user_id);

            $menu_ids       = $admin_user['menu_ids'];
            $admin_menu_ids = $admin_user['admin_menu_ids'];
            $role_menu_ids  = RoleService::getMenuId($admin_user['admin_role_ids']);

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

            $admin_menu = MenuService::toTree($admin_menu, 0);

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

            $update['admin_role_ids'] = str_join(implode(',', $admin_role_ids));
            $update['admin_menu_ids'] = str_join(implode(',', $admin_menu_ids));
            $update['update_time']    = datetime();

            $res = Db::name('admin_user')
                ->where('admin_user_id', $admin_user_id)
                ->update($update);

            if (empty($res)) {
                exception();
            }

            $update['admin_user_id'] = $admin_user_id;

            UserCache::upd($admin_user_id);

            return $update;
        }
    }

    /**
     * 用户是否禁用
     *
     * @param array $param 用户信息
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

        UserCache::upd($admin_user_id);

        return $update;
    }

    /**
     * 用户是否超管
     *
     * @param array $param 用户信息
     * 
     * @return array
     */
    public static function super($param)
    {
        $admin_user_id = $param['admin_user_id'];

        $update['is_super']    = $param['is_super'];
        $update['update_time'] = datetime();

        $res = Db::name('admin_user')
            ->where('admin_user_id', $admin_user_id)
            ->update($update);

        if (empty($res)) {
            exception();
        }

        $update['admin_user_id'] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $update;
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
            ->where('is_delete', '=', 0)
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
    public static function equQuery($keyword, $field = 'username|nickname')
    {
        $admin_user = Db::name('admin_user')
            ->where('is_delete', '=', 0)
            ->where($field, '=', $keyword)
            ->select()
            ->toArray();

        return $admin_user;
    }
}
