<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 用户管理
namespace app\common\service\admin;

use think\facade\Db;
use think\facade\Config;
use app\common\utils\IpInfoUtils;
use app\common\cache\admin\UserCache;
use app\common\model\admin\MenuModel;
use app\common\model\admin\RoleModel;
use app\common\service\admin\TokenService;
use app\common\service\file\FileService;

class UserService
{
    // 表名
    protected static $t_name = 'admin_user';
    // 表主键
    protected static $t_pk = 'admin_user_id';

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
        if (empty($field)) {
            $field = self::$t_pk . ',username,nickname,phone,email,sort,is_disable,is_super,login_num,create_time,login_time';
        }

        if (empty($order)) {
            $order = ['sort' => 'desc', self::$t_pk => 'desc'];
        }

        $where[] = ['is_delete', '=', 0];

        $count = Db::name(self::$t_name)
            ->where($where)
            ->count(self::$t_pk);

        $pages = ceil($count / $limit);

        $list = Db::name(self::$t_name)
            ->field($field)
            ->where($where)
            ->page($page)
            ->limit($limit)
            ->order($order)
            ->select()
            ->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
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
            $admin_user = Db::name(self::$t_name)
                ->where(self::$t_pk, $admin_user_id)
                ->find();
            if (empty($admin_user)) {
                exception('用户不存在：' . $admin_user_id);
            }

            $admin_user['avatar_url']     = FileService::fileUrl($admin_user['avatar_id']);
            $admin_user['admin_role_ids'] = str_trim($admin_user['admin_role_ids']);
            $admin_user['admin_menu_ids'] = str_trim($admin_user['admin_menu_ids']);
            if (admin_is_super($admin_user_id)) {
                $AdminMenu = new MenuModel();
                $admin_menu = $AdminMenu
                    ->field('admin_menu_id,menu_url')
                    ->where('is_delete', 0)
                    ->select()
                    ->toArray();

                $menu_ids = array_column($admin_menu, 'admin_menu_id');
                $menu_url = array_column($admin_menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } elseif ($admin_user['is_super'] == 1) {
                $AdminMenu = new MenuModel();
                $admin_menu = $AdminMenu
                    ->field('admin_menu_id,menu_url')
                    ->where('is_disable', 0)
                    ->where('is_delete', 0)
                    ->select()
                    ->toArray();

                $menu_ids = array_column($admin_menu, 'admin_menu_id');
                $menu_url = array_column($admin_menu, 'menu_url');
                $menu_url = array_filter($menu_url);
            } else {
                $AdminRole = new RoleModel();
                $menu_ids = $AdminRole
                    ->field('admin_role_id')
                    ->where('admin_role_id', 'in', $admin_user['admin_role_ids'])
                    ->where('is_disable', 0)
                    ->where('is_delete', 0)
                    ->column('admin_menu_ids');

                $menu_ids[]   = $admin_user['admin_menu_ids'];
                $menu_ids_str = implode(',', $menu_ids);
                $menu_ids_arr = explode(',', $menu_ids_str);
                $menu_ids     = array_unique($menu_ids_arr);
                $menu_ids     = array_filter($menu_ids);

                $where[] = ['admin_menu_id', 'in', $menu_ids];
                $where[] = ['menu_url', '<>', ''];
                $where[] = ['is_disable', '=', 0];
                $where[] = ['is_delete', '=', 0];

                $where_un[] = ['menu_url', '<>', ''];
                $where_un[] = ['is_unauth', '=', 1];
                $where_un[] = ['is_disable', '=', 0];
                $where_un[] = ['is_delete', '=', 0];

                $AdminMenu = new MenuModel();
                $menu_url = $AdminMenu
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

            $menu_is_unlogin = Config::get('admin.menu_is_unlogin', []);
            $menu_is_unauth  = Config::get('admin.menu_is_unauth', []);
            $unlogin_unauth  = array_merge($menu_is_unlogin, $menu_is_unauth);
            $menu_url        = array_merge($menu_url, $unlogin_unauth);
            $menu_url        = array_unique($menu_url);

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

        $admin_user_id = Db::name(self::$t_name)
            ->insertGetId($param);
        if (empty($admin_user_id)) {
            exception();
        }

        $param[self::$t_pk] = $admin_user_id;

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
    public static function edit($param)
    {
        $admin_user_id = $param[self::$t_pk];

        unset($param[self::$t_pk]);

        $param['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_user_id)
            ->update($param);
        if (empty($res)) {
            exception();
        }

        $param[self::$t_pk] = $admin_user_id;

        UserCache::upd($admin_user_id);

        return $param;
    }

    /**
     * 用户删除
     *
     * @param array $ids 用户id
     * 
     * @return array
     */
    public static function dele($ids)
    {
        $update['is_delete']   = 1;
        $update['delete_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::del($v);
        }

        $update['ids'] = $ids;

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
            $admin_user_id = $param[self::$t_pk];

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

            $data[self::$t_pk]  = $admin_user_id;
            $data['admin_menu_ids'] = $admin_menu_ids;
            $data['admin_role_ids'] = $admin_user['admin_role_ids'];
            $data['username']       = $admin_user['username'];
            $data['nickname']       = $admin_user['nickname'];
            $data['menu_ids']       = $menu_ids;
            $data['admin_role']     = $admin_role;
            $data['admin_menu']     = $admin_menu;

            return $data;
        } else {
            $admin_user_id  = $param[self::$t_pk];
            $admin_role_ids = $param['admin_role_ids'];
            $admin_menu_ids = $param['admin_menu_ids'];

            sort($admin_role_ids);
            sort($admin_menu_ids);

            $update['admin_role_ids'] = str_join(implode(',', $admin_role_ids));
            $update['admin_menu_ids'] = str_join(implode(',', $admin_menu_ids));
            $update['update_time']    = datetime();

            $res = Db::name(self::$t_name)
                ->where(self::$t_pk, $admin_user_id)
                ->update($update);
            if (empty($res)) {
                exception();
            }

            $update[self::$t_pk] = $admin_user_id;

            UserCache::upd($admin_user_id);

            return $update;
        }
    }

    /**
     * 用户重置密码
     *
     * @param array  $ids      用户id
     * @param string $password 新密码
     * 
     * @return array
     */
    public static function pwd($ids, $password)
    {
        $update['password']    = md5($password);
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否禁用
     *
     * @param array   $ids        用户id
     * @param integer $is_disable 是否禁用
     * 
     * @return array
     */
    public static function disable($ids, $is_disable)
    {
        $update['is_disable']  = $is_disable;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户是否超管
     *
     * @param array   $ids      用户id
     * @param integer $is_super 是否禁用
     * 
     * @return array
     */
    public static function super($ids, $is_super)
    {

        $update['is_super']    = $is_super;
        $update['update_time'] = datetime();

        $res = Db::name(self::$t_name)
            ->where(self::$t_pk, 'in', $ids)
            ->update($update);
        if (empty($res)) {
            exception();
        }

        foreach ($ids as $v) {
            UserCache::upd($v);
        }

        $update['ids'] = $ids;

        return $update;
    }

    /**
     * 用户登录
     *
     * @param array $param 登录信息
     * 
     * @return array
     */
    public static function login($param)
    {
        $username = $param['username'];
        $password = md5($param['password']);

        $where[] = ['username|phone|email', '=', $username];
        $where[] = ['password', '=', $password];
        $where[] = ['is_delete', '=', 0];

        $admin_user = Db::name(self::$t_name)
            ->field('admin_user_id,login_num,is_disable')
            ->where($where)
            ->find();
        if (empty($admin_user)) {
            exception('账号或密码错误');
        }
        if ($admin_user['is_disable'] == 1) {
            exception('账号已被禁用，请联系管理员');
        }

        $ip_info = IpInfoUtils::info();

        $admin_user_id = $admin_user[self::$t_pk];

        $update['login_ip']     = $ip_info['ip'];
        $update['login_region'] = $ip_info['region'];
        $update['login_time']   = datetime();
        $update['login_num']    = $admin_user['login_num'] + 1;
        Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_user_id)
            ->update($update);

        $admin_user_log[self::$t_pk] = $admin_user_id;
        $admin_user_log['log_type']      = 1;
        $admin_user_log['response_code'] = 200;
        $admin_user_log['response_msg']  = '登录成功';
        UserLogService::add($admin_user_log);

        UserCache::del($admin_user_id);
        $admin_user  = self::info($admin_user_id);
        $admin_token = $admin_user['admin_token'];

        return compact(self::$t_pk, 'admin_token');
    }

    /**
     * 用户退出
     *
     * @param integer $admin_user_id 用户id
     * 
     * @return array
     */
    public static function logout($admin_user_id)
    {
        $update['logout_time'] = datetime();

        Db::name(self::$t_name)
            ->where(self::$t_pk, $admin_user_id)
            ->update($update);

        $update[self::$t_pk] = $admin_user_id;

        UserCache::del($admin_user_id);

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
        $admin_user = Db::name(self::$t_name)
            ->where($field, 'like', '%' . $keyword . '%')
            ->where('is_delete', '=', 0)
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
        $admin_user = Db::name(self::$t_name)
            ->where($field, '=', $keyword)
            ->where('is_delete', '=', 0)
            ->select()
            ->toArray();

        return $admin_user;
    }
}
